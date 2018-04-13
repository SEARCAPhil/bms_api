<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Proposals.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');

use Bidding\Proposals as Proposals;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str = new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);
$Req = new Requirements($DB);
$Prop = new Proposals($DB);

/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){
	/**
	 * POST product
	 */  
	
	$input=file_get_contents("php://input");


	$data = (@json_decode($input));

	$action = isset($data->action)?$clean_str->clean($data->action):'';
	$id = (int) isset($data->id)?$data->id:null;
	$ref = isset($data->ref)?$clean_str->clean($data->ref):null;

	//required
	if(empty($id) || !($ref)) return 0;

	if($action == 'create') {
		echo $Prop->set_reference_no($id, $ref);

	}

}

?>