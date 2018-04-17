<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$part = new Particulars($DB);
$req= new Requirements($DB);
$Ses = new Session($DB);

/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){
	/**
	 * POST product
	 */  
	$index=new Index($DB);
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$id=(int) isset($data->id)?$data->id:null;


	//proceed to adding
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$quantity=isset($data->quantity)?$clean_str->clean($data->quantity):'';
	$unit=isset($data->unit)?$clean_str->clean($data->unit):'';

	$amount=isset($data->amount)?$clean_str->clean($data->amount):0;
	$currency=isset($data->currency)?$clean_str->clean($data->currency):'PHP';

	$excempted=(int) isset($data->excempted)?$clean_str->clean($data->excempted):0;

	$funds = isset($data->funds)?$data->funds:[];
	$fundsToRemove = isset($data->fundsToRemove)?$data->fundsToRemove:null;

	$specsToRemove = isset($data->specsToRemove)?$data->specsToRemove:null;


	$specs = isset($data->specs)?$data->specs:[];

	$extras = [];

	//required
	if(empty($id)) return 0;

	if($action == 'remove') {
		$result = $req->remove($id);
		$data=["data"=> $result];
		echo @json_encode($data);	
	}

	if($action == 'send') {
		$items = isset($data->items)?$data->items:[];
		// specs
		$specs = isset($data->suppliers)?$data->suppliers:[];
		$specs_ids = [];
		$specs_sent = [];
		foreach ($specs as $key => $value) {
			if(!empty(trim($value))) {
				array_push($specs_ids, (int) $key);
			}
		}

		if (!empty($specs_ids)) {
			for ($x=0; $x < count($specs_ids); $x++) {
				$result = $req->send($id,$specs_ids[$x],0);
				// add to sent items
				if ($result) {
					$specs_sent[$specs_ids[$x]] = $result;
				}
			}
			

			$data=["data"=> $specs_sent];
			echo @json_encode($data);
			exit;	
		}

	}



	
	
	//required
	if(empty($name)) return 0;

	if($action == 'create') {

		$result=$req->create([
			"name"=>$name,
			"quantity"=>$quantity,
			'unit'=>$unit,
			'budget_amount'=>$amount,
			'budget_currency'=>$currency,
			'bidding_excemption_request'=>$excempted,
			"id"=>$id
		]);

	

		// funding
		if (count($funds) > 0 && $result > 0) {
			for ($i=0; $i < count($funds) ; $i++) { 
				// vars
				$fund_type = '';
				$cost_center = '';
				$line_item = '';

				foreach($funds[$i] as $key => $val) {
					if($key === 'fund-type') $fund_type = $val;
					if($key === 'cost-center') $cost_center =$val;
					if($key === 'line-item') $line_item =$val;
				}

				// insert
				$req->funds([
					'id'=>$result,
					'fund_type'=>$fund_type,
					'cost_center'=>$cost_center,
					'line_item'=>$line_item,
				]);
			}
		}


		// specs
		if (count($specs) > 0 && $result > 0) {
			for ($i=0; $i < count($specs) ; $i++) { 
				if(!empty($specs[$i]->name) && !empty($specs[$i]->value)) {
					$req->add_specs($result,$specs[$i]->name,$specs[$i]->value);	
				}
			}
		}

		$data=["data"=>$result];
		echo @json_encode($data);
	}


	// update
	if($action == 'update') {

		$result=$req->update([
			"name"=>$name,
			"quantity"=>$quantity,
			'unit'=>$unit,
			'budget_amount'=>$amount,
			'budget_currency'=>$currency,
			'bidding_excemption_request'=>$excempted,
			"id"=>$id
		]);

		$fund_result = 0;
		$specs_result = 0;

		//** This still proceeds with updating funds **
		// funding
		if (count($funds) > 0 ) {
			for ($i=0; $i < count($funds) ; $i++) { 
				// vars
				$fund_type = '';
				$cost_center = '';
				$line_item = '';
				$fund_id = 0;

				foreach($funds[$i] as $key => $val) {
					if($key === 'fund-type') $fund_type = $val;
					if($key === 'cost-center') $cost_center = $val;
					if($key === 'line-item') $line_item = $val;
					if($key === 'id') $fund_id = $val;
				}


				// insert
				if (!$fund_id) {
					$fund_res = $req->funds([
						'id'=>$id,
						'fund_type'=>$fund_type,
						'cost_center'=>$cost_center,
						'line_item'=>$line_item,
					]);

				}else{
					// update
					$fund_res = $req->fund_update([
						'id'=>$fund_id,
						'fund_type'=>$fund_type,
						'cost_center'=>$cost_center,
						'line_item'=>$line_item,
					]);
					
				}
				// proceed even if there is no changes in requirements
				if($fund_res) $fund_result = 1;
			}
		}




		// remove funds

		if (!is_null($fundsToRemove)) {
			foreach($fundsToRemove as $key => $val) {
				$fund_res = $req->remove_fund($key);
				// proceed even if there is no changes in requirements
				if($fund_res) $fund_result = 1;
			}
		}


		// remove specs

		if (!is_null($specsToRemove)) {
			foreach($specsToRemove as $key => $val) {
				$specs_res = $req->remove_specs($key);
				// proceed even if there is no changes in requirements
				if($specs_res) $specs_result = 1;
			}
		}




		//** This still proceeds with updating specs **
		// specs
		if (count($specs) > 0) {
			for ($i=0; $i < count($specs) ; $i++) { 
				$specs_id = 0;
				if(!empty($specs[$i]->name) && !empty($specs[$i]->value)) {

					if(isset($specs[$i]->id)) $specs_id = $specs[$i]->id;

					// insert
					if (!$specs_id) {
						$specs_res = $req->add_specs($id,$specs[$i]->name,$specs[$i]->value);
						
					}else{
						// update
						$specs_res = $req->update_specs($specs_id,$specs[$i]->name,$specs[$i]->value);
					}
					// proceed even if there is no changes in requirements
					if($specs_res) {
						$specs_result = 1;
						$extras[] = array('id'=>$specs_res,'name'=>$specs[$i]->name,'value'=>$specs[$i]->value);
					} 	
				}
			}
		}

		$data=["data"=> ((int) ($result || $fund_result || $specs_result)),'extras'=>$extras];

		echo @json_encode($data);
	}
	
}

if($method=="GET" && isset($_GET['id'])){
	$id=(int) htmlentities(htmlspecialchars($_GET['id']));
	$res = [];

	#serve with page request
	if(!isset($_GET['token'])){
		exit;
	}

	// get privilege
	// this is IMPORTANT for checking privilege
	$token=htmlentities(htmlspecialchars($_GET['token']));

	$current_session = $Ses->get($token);


	if(!$current_session[0]->role) {
		$res = $req->view($id);
		$viewable = false;

		if (!$res[0]->recepients) exit;
		// match recepients
		foreach ($res[0]->recepients as $key => $value) {
			if ($value->supplier_id == $current_session[0]->company_id) {
				$viewable = true;
			}
		}
		// if one of the recepients
		if ($viewable) {
			$res = $req->view($id);	
		}
		
	} else {
		$res = $req->view($id);
	}



	

	echo @json_encode($res);
}

?>