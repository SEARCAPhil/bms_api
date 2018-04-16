<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../bidding/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');
require_once('../../auth/Session.php');

use Bidding\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);
$Index=new Index($DB);

$result = [];
/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){

	$input=file_get_contents("php://input");

	$data = (@json_decode($input));
	$id = (int) isset($data->id)?$data->id:'';
	$token = isset($data->token)?$data->token:'';
	$approved = isset($data->approved) ? trim($data->approved) : '';
	$approved_position = isset($data->approving_position) ? trim($data->approving_position) :'';
	$recommended = isset($data->recommended) ? trim($data->recommended) : '';
	$recommended_position = isset($data->recommended_position) ? trim($data->recommended_position) : '';
	$certified = isset($data->certified) ? trim($data->certified) : '';
	$certified_position = isset($data->certified_position) ? trim($data->certified_position) : '';

	if ($id) {
		$res = $Index->view($id);
		if ($res[0]) {
			
			$orig_recommended_by = empty($recommended) ? $res[0]->recommended_by : $recommended;
			$orig_certified_by = empty($certified) ? $res[0]->certified_by : $certified;
			$orig_approved_by = empty($approved) ? $res[0]->approved_by : $approved;


			$orig_recommended_by_position = empty($recommended_position) ? $res[0]->recommended_by_position : $recommended_position;
			$orig_certified_by_position = empty($certified_position) ? $res[0]->certified_by_position : $certified_position;
			$orig_approved_by_position = empty($approved_position) ? $res[0]->approved_by_position : $approved_position;

			echo $Index->change_signatories($id, $orig_recommended_by, $orig_recommended_by_position, $orig_certified_by, $orig_certified_by_position, $orig_approved_by, $orig_approved_by_position);

		}
	}


	
}

?>