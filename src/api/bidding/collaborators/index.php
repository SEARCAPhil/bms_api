<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');

use Bidding\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);

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

	$emails=isset($data->emails)?$data->emails:[];

	$id=(int) isset($data->id)?$clean_str->clean($data->id):'';

	$result = [];

	if(empty($id)) return 0;

	if($action=='create'){

		for($x = 0; $x < count($emails); $x++) {
			$res=$index->set_collaborators($id, $emails[$x]);

			if($res > 0) array_push($result, $res);
		}
		
	}

	$data=["data"=>$result];
	echo @json_encode($data);
	
}

?>