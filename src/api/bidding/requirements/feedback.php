<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../bidding/Logs.php');
require_once('../../../auth/Session.php');

use Bidding\Index as Index;
use Bidding\Requirements as Requirements;
use Bidding\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();

$Req = new Requirements($DB);
$Ses = new Session($DB);
$logs = new Logs($DB);

/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action = isset($data->action) ? $clean_str->clean($data->action) : '';
	$feedback = isset($data->feedback) ? $clean_str->clean($data->feedback) : '';
	$ratings = isset($data->ratings) ? $data->ratings : [];

	# bidding requirements
	$id = (int) isset($data->id) ? $data->id : null;

	# award_id
	$awardee_id = (int) isset($data->supplier_id) ? $data->supplier_id : null;

	# get session
	$token = @$data->token;
	if(empty($token) || empty($awardee_id)) return 0;

	
	$current_session = $Ses->get($token);
	if(!@$current_session[0]->role) exit;

	# required
	if(empty($id)) return 0;

	if($action == 'create') {
			
			$feedbackId = $Req->feedback($awardee_id,$current_session[0]->account_id,$feedback);
			if ($feedbackId) {
				# save ratings
				foreach ($ratings as $key => $value) {
					$Req->rate($feedbackId,$key,$value);
				}

				# log
				$payload = ['id' =>$feedbackId, 'feedback' =>$feedback,'ratings' => $ratings];
				$logs->log($current_session[0]->account_id, 'feedback', 'bidding_requirement_invitation', $feedbackId, json_encode($payload));

				echo $feedbackId;
			}
		

	}

}

if($method=="GET"){
	if (!isset($_GET['id']) || !isset($_GET['token'])) exit;
	# bidding requirements
	$id = (int) $clean_str->clean($_GET['id']);

	if (isset($_GET['bidding_request'])) {
		$res = $Req->get_feedback_per_bidding_request($id);
	} else {
		$res = $Req->get_feedback_per_awardee($id);
	}
	echo json_encode($res);
}

?>