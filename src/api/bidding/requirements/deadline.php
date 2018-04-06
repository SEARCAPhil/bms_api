<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');

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
	$deadline=isset($data->deadline)?$clean_str->clean($data->deadline):'0000-00-00';

	//required
	if(empty($id)) return 0;

	if($action == 'create') {
		echo $req->set_deadline($id, $deadline);

	}

}

?>