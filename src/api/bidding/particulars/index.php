<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../bidding/Logs.php');
require_once('../../../auth/Session.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Bidding\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$part = new Particulars($DB);
$Ses = new Session($DB);

/**
 * GET suppliers list
 */ 
$method = ($_SERVER['REQUEST_METHOD']);


if($method=="POST"){
	/**
	 * POST product
	 */  
	$index=new Index($DB);
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action) ? $clean_str->clean($data->action) : '';
	$name = isset($data->name) ? $clean_str->clean($data->name) : '';
	$deadline = isset($data->deadline) ? $data->deadline : null;
	$id = (int) isset($data->id) ? $data->id : null;
	$token = isset($data->token) ? $data->token : '';
	
	if(empty($id)) return 0;

	# session
	$current_session = $Ses->get($token);
	if(!@$current_session[0]->role) exit;



	if ($action === 'create') {
		//required
		if(empty($name) || empty($id)) return 0;

		$payload = [	
			"name"=>$name,
			"deadline"=>$deadline,
			"id"=>$id
		];

		$result = $part->create($payload);

		if ($result) {
			$logs->log($current_session[0]->account_id, 'add', 'particular', $result, json_encode($payload));
		}

	}

	if ($action === 'update') {
		//required
		if(empty($name) || empty($id)) return 0;
		$payload = [
			"name"=>$name,
			"deadline"=>$deadline,
			"id"=>$id
		];
		
		$result = $part->update($payload);
		if ($result) {
			$logs->log($current_session[0]->account_id, 'update', 'particular', $id, json_encode($payload));
		}
	}

	if ($action === 'remove') {

		$result = $part->remove($id);
		if ($result) {
			$logs->log($current_session[0]->account_id, 'delete', 'particular', $id);
		}
	}


	$data=["data"=>$result];
	echo @json_encode($data);
	
}


if($method=="GET"){

	if(!isset($_GET['token'])){
		exit;
	}

	// get privilege
	// this is IMPORTANT for checking privilege
	$token=htmlentities(htmlspecialchars($_GET['token']));

	$current_session = $Ses->get($token);

	if(!@$current_session[0]->role) exit;


	/**
	 * GET
	 * get all company list from the database
	 * @param  $page page number
	 * @param  $limit default to 20 items
	 * @return json
	 */
	if(isset($_GET['id'])){
		$id = (int) htmlentities(htmlspecialchars($_GET['id']));

		echo json_encode($part->view($id));
	}
}

?>