<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../bidding/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../bidding/Logs.php');
require_once('../../auth/Session.php');
require_once('../../config/constants/reports.php');

# Namespace
use Bidding\Index as Index;
use Bidding\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

# Defaults
$LIMIT = 20;
$status = 'all'; 
$page = 1;
$result = [];

# Global classes
$index=new Index($DB);
$clean_str=new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);

# GET , POST , PUT etc.
$method=($_SERVER['REQUEST_METHOD']);



if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	# Session checking
	# this is IMPORTANT for checking privilege
	if(!isset($_GET['token'])){
		exit;
	}
	$token=htmlentities(htmlspecialchars($_GET['token']));
	$current_session = $Ses->get($token);
	if(!@$current_session[0]->role) exit;


	if(!isset($_GET['id'])){
		$status_filter = ['drafts','closed'];

		#filter blocked or active companies
		if(isset($_GET['status'])){
			$status=trim(strip_tags(htmlentities(htmlspecialchars($_GET['status']))));
		}

		switch ($status) {
			case 'drafts':
				$status_code=0;
				break;
			case 'all':
				$status_code=null;
				break;
			case 'closed':
				$status_code=2;
				break;
			
			default:
				$status_code=null;
				break;
		}


		# Get all bidding requests
		# the values returned differs depending on their current role
		# STANDARD Account
		if ($current_session[0]->role === 'standard') {
			if(in_array($status, $status_filter)) {

				if($status == 'drafts') {
					echo @json_encode($index->lists_all_drafts($current_session[0]->pid,$page,$LIMIT,$status_code));
				} else {
					echo @json_encode($index->lists_by_status($page,$LIMIT,$status_code));	
				}
			}

			if(is_null($status_code)) {
				echo @json_encode($index->lists_all_received($current_session[0]->pid,$page,$LIMIT));
			}
		}

		# CBA Secretary Account
		if ($current_session[0]->role === 'cba_assistant' || $current_session[0]->role === 'admin') {
			if(in_array($status, $status_filter)) {

				if($status == 'drafts') {
					echo @json_encode($index->lists_all_drafts($current_session[0]->pid,$page,$LIMIT,$status_code));
				} else {
					echo @json_encode($index->lists_by_status($page,$LIMIT,$status_code));	
				}
				
			}

			if(is_null($status_code)) {
				echo @json_encode($index->lists_all_received($current_session[0]->account_id,$page,$LIMIT));
			}
		}


		// General Services Account
		if ($current_session[0]->role === 'gsu') {
			if(in_array($status, $status_filter)) {

				if($status == 'drafts') {
					echo @json_encode($index->lists_all_drafts($current_session[0]->pid,$page,$LIMIT,$status_code));
				} else {
					echo @json_encode($index->lists_all_approved($page,$LIMIT));
				}
				
			}

			if(is_null($status_code)) {
				echo @json_encode($index->lists_all_approved($page,$LIMIT));
			}
		}
		
		
	}
	

	# Bidding Request preview
	if(isset($_GET['id'])){
		$is_viewable = false;
		$id = (int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
		$details = $index->view($id,1);

		/* -----------------------------------------
		| check details if viewable
		| Only allow if permission meets the ff:
		| OWNER, Collaborator / CBA asst, Admin, GSU
		| -----------------------------------------*/
		# OWNER
		if ($current_session[0]->pid == $details[0]->created_by)  $is_viewable = true;

		# SENT TO
		$sent_to = [];
		foreach ($details[0]->collaborators as $key => $value) {
			array_push($sent_to, $value->account_id);
		}
		if (in_array($current_session[0]->account_id, $sent_to)) $is_viewable = true;

		# GSU or ADMIN
		if ($current_session[0]->role === 'gsu' || $current_session[0]->role === 'admin') $is_viewable = true;

		# show results
		if ($is_viewable) {
			$result=["data"=>@$details];
			echo @json_encode($result);
		}
	}
}



if($method=="POST"){

	# parameters
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));
	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$token = isset($data->token)?$data->token:'';

	# parameters for creating NEW or Updating bidding request only
	$name = '';
	$description = '';
	$deadline = '';
	$excemption = isset($data->excemption)?$data->excemption:0;

	# session
	$current_session = $Ses->get($token);
	if(!@$current_session[0]->role) exit;

	# signatories
	# Unit Head must be the default value
	$sign = $index->view_signatories(trim($current_session[0]->department));
	$requested_by = '';
	$requested_by_position = '';
	
	if(isset($sign[0])) {
		$requested_by = $sign[0]->name;
		$requested_by_position = $sign[0]->position;
	}


	# remove
	if($action=='remove'){ 
		$id = (int)isset($data->id)?$clean_str->clean($data->id):'';
		$res = @$index->remove($id);
		# log
		if ($res) {
			$logs->log($current_session[0]->account_id, 'delete', 'bidding_request', $id);
		}
		$data = ["data"=>$res];
		echo json_encode($data);
		return 0;
	}

	/*# block
	if($action=='block'){
		$id=isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->block($id);
		echo $res;

		//log to sytem
		if(!empty($res)){
			$logs->log($data->id,'Account has been blocked','account');
		}
	}

	# unblock
	if($action=='unblock'){
		$id=isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->unblock($id);
		echo $res;

		//log to sytem
		if(!empty($res)){
			$logs->log($data->id,'Account has been unblocked','account');
		}
	}*/


	# update
	if($action=='update'){
		# ID is required
		$id = (int) isset($data->id)?$clean_str->clean($data->id):'';
		if(empty($id)) return 0;

		# update
		$result = $index->update($id,$name,$description,$deadline,$excemption);

		if ($result) {
			$logs->log($current_session[0]->account_id, 'update', 'bidding_request', $id);
		}

		#result in JSON format
		$data=["data"=>$result];
		echo @json_encode($data);
		return 0;

	}

	# create
	if($action=='create'){
		$tok = $Ses->get($token);

		# insert to DB
		if (@$tok[0]->pid) {
			$result = $index->create([
				"name"=>$name,
				"description"=>$description,
				"deadline"=>$deadline,
				"excemption" => $excemption,
				"created_by"=> $tok[0]->pid,
				"approved_by" => APPROVED_BY,
				"recommended_by" => RECOMMENDED_BY, 
				"requested_by" => $requested_by,
				"approved_by_position" => APPROVED_BY_POSITION,
				"recommended_by_position" => RECOMMENDED_BY_POSITION,
				"requested_by_position" => $requested_by_position
			]);

			if ($result) {
				$logs->log($current_session[0]->account_id, 'add', 'bidding_request', $result);
			}
		}

		# results
		$data = ["data"=>$result];
		echo @json_encode($data);
	}
	
}

?>
