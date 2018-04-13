<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../bidding/Invitations.php');
require_once('../../../../bidding/Proposals.php');
require_once('../../../../bidding/Proposals/Attachments.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../suppliers/Logs/Logs.php');
require_once('../../../../Auth/Session.php');

use Bidding\Proposals\Attachments as Attachments;
use Bidding\Proposals as Proposals;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$Att = new Attachments($DB);
$Ses = new Session($DB);
$Prop= new Proposals($DB);

$dir = './../../../../../public/uploads/proposals/';


/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);

// for uploading
if($method=="POST" && isset($_FILES['files'])){
	if (!isset($_POST['id']) || !isset($_POST['token'])) exit;


	// get privilege
	// this is IMPORTANT for checking privilege
	$token=htmlentities(htmlspecialchars($_POST['token']));

	$current_session = $Ses->get($token);

	if(!@$current_session[0]->account_id) exit;


	$id = $current_session[0]->pid; //author
	$bidding_id = (int) trim($_POST['id']); //author

	// get requirements info

	$res = $Prop->view($bidding_id);

	if(!isset($res[0]->id)) exit;



	$file = ($_FILES['files']);
	if(!isset($file['name'])) return 0;

	$allowed_format=array('png','jpg','jpeg','pdf','PDF','doc','docx','xls','xlsx');
	$allowed_size=41943040;#40MB

	$name = $file['name'];
	$type = $file['type'];
	$size= $file['size'];
	$tmp_name = $file['tmp_name'];
	$base_name = basename($name);
	$ext = pathinfo($name, PATHINFO_EXTENSION);
	#new file name to be unique
	$new_file_name=date('mdyhsi').rand().'.'.$ext;

	#check extension && file size
	if(in_array($ext, $allowed_format) && $size<$allowed_size){

		// create directory if not exist
		if(!is_dir($dir)) mkdir($dir,0777, true);

		// upload
		if(move_uploaded_file($tmp_name, $dir.''.$new_file_name)){


			$last_id = $Att->create($current_session[0]->account_id, $bidding_id, $new_file_name, $name, $size, $ext);

			echo $last_id;
		}
		
	}
}




// remove or update attachments
if($method=="POST" && !isset($_FILES['files'])){
	$input=file_get_contents("php://input");

	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';

	$id=(int) isset($data->id)?$data->id:null;

	if (empty($id) || empty($action)) return 0;

	//remove
	if($action == 'remove'){

		$res = $Att->remove($id);
		$data = ["data"=>$res];
		echo @json_encode($data);
		exit;
	}
}


?>