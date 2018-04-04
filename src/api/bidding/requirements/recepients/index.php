<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../bidding/Index/Index.php');
require_once('../../../../bidding/Particulars/Particulars.php');
require_once('../../../../bidding/Requirements/Requirements.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../suppliers/Logs/Logs.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$part = new Particulars($DB);
$req= new Requirements($DB);

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


	//required
	if(empty($id)) return 0;

	if($action == 'remove') {
		$result = $req->remove_recepients($id);
		$data=["data"=> $result];
		echo @json_encode($data);	
	}

	if($action == 'send') {
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


	
	if($action == 'award') {
		$remarks = isset($data->remarks)?$data->remarks:'';
		$sup = isset($data->suppliers)?$data->suppliers:[];
		$sup_ids = [];
		$sup_sent = [];
		foreach ($sup as $key => $value) {
			if(!empty(trim($value))) {
				array_push($sup_ids, (int) $key);
			}
		}

		if (!empty($sup_ids)) {

			if ($sup_ids[0]) {
				// award($id,$supplier_id,$remarks)
				$data = $req->award($id,$sup_ids[0],$remarks);
				echo @json_encode($data);
				exit;	
			}
			

		}

	}

	
	
}

?>