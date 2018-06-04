<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../bidding/Logs.php');
require_once('../../../auth/Session.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Bidding\Logs as Logs;
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


	$data = (@json_decode($input));

	$action = isset($data->action)?$clean_str->clean($data->action):'';
	$id = (int) isset($data->id)?$data->id:null;
	$token = isset($data->token) ? $data->token : '';


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

	# session
	$current_session = $Ses->get($token);
	if(!@$current_session[0]->role) exit;


	if($action == 'remove') {
		$result = $req->remove($id);
		# log
		if ($result) {
			$logs->log($current_session[0]->account_id, 'delete', 'requirement', $id);
		}
		
		$data = ["data"=> $result];
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
				$supplier_id = $specs_ids[$x];
				$result = $req->send($id,$specs_ids[$x],0);
				// add to sent items
				if ($result) {
					$specs_sent[$specs_ids[$x]] = $result;
					#log
					$logs->log($current_session[0]->account_id, 'invite', 'supplier', $id, "{'supplier_id: {$supplier_id}, invitation_id:{$result}");
					
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
		$payload = [
			"name"=>$name,
			"quantity"=>$quantity,
			'unit'=>$unit,
			'budget_amount'=>$amount,
			'budget_currency'=>$currency,
			'bidding_excemption_request'=>$excempted,
			"id"=>$id
		];
		$result = $req->create($payload);
		
		# log
		if ($result) {
			$logs->log($current_session[0]->account_id, 'add', 'requirement', $result, json_encode($payload));
		}

	

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
				$payload = [
					'id'=>$result,
					'fund_type'=>$fund_type,
					'cost_center'=>$cost_center,
					'line_item'=>$line_item,
				];

				$req_result = $req->funds($payload);

				# log
				if ($req_result) {
					$logs->log($current_session[0]->account_id, 'add', 'fund', $req_result, json_encode($payload));
				}
			}
		}


		// specs
		if (count($specs) > 0 && $result > 0) {
			for ($i=0; $i < count($specs) ; $i++) { 
				if(!empty($specs[$i]->name) && !empty($specs[$i]->value)) {
					$payload = [
						'id' => $result,
						'name' => $specs[$i]->name,
						'value' => $specs[$i]->value,
					];

					$req_specs = $req->add_specs($result,$specs[$i]->name,$specs[$i]->value);
					# log
					if ($req_specs) {
						$logs->log($current_session[0]->account_id, 'add', 'spec', $req_specs, json_encode($payload));
					}	
				}
			}
		}

		$data=["data"=>$result];
		echo @json_encode($data);
	}


	// update
	if($action == 'update') {
		$payload = [
			"name"=>$name,
			"quantity"=>$quantity,
			'unit'=>$unit,
			'budget_amount'=>$amount,
			'budget_currency'=>$currency,
			'bidding_exemption_request'=>$excempted,
			"id"=>$id
		];
		$result = $req->update($payload);

		# log
		if ($result) {
			$logs->log($current_session[0]->account_id, 'update', 'requirement', $id, json_encode($payload));
		}
		

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
					$payload = [
						'id'=>$id,
						'fund_type'=>$fund_type,
						'cost_center'=>$cost_center,
						'line_item'=>$line_item,
					];

					$fund_res = $req->funds($payload);

					# log
					if ($fund_res) {
						$logs->log($current_session[0]->account_id, 'add', 'fund', $fund_res, json_encode($payload));
					}

				}else{
					// update
					$payload = [
						'id'=>$fund_id,
						'fund_type'=>$fund_type,
						'cost_center'=>$cost_center,
						'line_item'=>$line_item,
					];

					$fund_res = $req->fund_update($payload);

					# log
					if ($fund_res) {
						$logs->log($current_session[0]->account_id, 'update', 'fund', $fund_id, json_encode($payload));
					}	
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
				# log
				if ($fund_res) {
					$logs->log($current_session[0]->account_id, 'delete', 'fund', $key);
				}
			}
		}


		// remove specs
		if (!is_null($specsToRemove)) {
			foreach($specsToRemove as $key => $val) {
				$specs_res = $req->remove_specs($key);
				// proceed even if there is no changes in requirements
				if($specs_res) $specs_result = 1;
				# log
				if ($specs_res) {
					$logs->log($current_session[0]->account_id, 'delete', 'spec', $key);
				}
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
						$payload = ['id' => $id, 'name' =>$specs[$i]->name, 'value' => $specs[$i]->value];
						# log
						if ($specs_res) {
							$logs->log($current_session[0]->account_id, 'add', 'spec', $specs_res, json_encode($payload));
						}
						
					}else{
						// update
						$specs_res = $req->update_specs($specs_id,$specs[$i]->name,$specs[$i]->value);
						# log
						$payload = ['id' => $specs_id, 'name' =>$specs[$i]->name, 'value' => $specs[$i]->value];
						if ($specs_res) {
							$logs->log($current_session[0]->account_id, 'update', 'spec', $specs_id, json_encode($payload));
						}
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
	
	$res = $req->view($id);

	# clear recepients if request came from supplier
	if(is_null($current_session[0]->role) && $res[0]) {
		if(isset($res[0]->recepients)) $res[0]->recepients = [];
	}
	
	

	/*if(!$current_session[0]->role) {
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
	}*/

	echo @json_encode($res);
}

?>