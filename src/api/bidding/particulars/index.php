<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');

use Bidding\Index as Index;
use Bidding\Particulars as Particulars;
use Suppliers\Logs as Logs;
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