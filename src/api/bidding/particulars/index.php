<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$part = new Particulars($DB);

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

	//proceed to adding
	$name=isset($data->name)?$clean_str->clean($data->name):'';

	$deadline=isset($data->deadline)?$data->deadline:null;
	$id=(int) isset($data->id)?$data->id:null;
	
	if(empty($id)) return 0;
	



	if ($action === 'create') {
		//required
		if(empty($name) || empty($id)) return 0;

		$result=$part->create([	
			"name"=>$name,
			"deadline"=>$deadline,
			"id"=>$id
		]);
	}

	if ($action === 'update') {
		//required
		if(empty($name) || empty($id)) return 0;
		$result=$part->update([
			"name"=>$name,
			"deadline"=>$deadline,
			"id"=>$id
		]);
	}

	if ($action === 'remove') {

		$result=$part->remove($id);
	}


	$data=["data"=>$result];
	echo @json_encode($data);
	
}

?>