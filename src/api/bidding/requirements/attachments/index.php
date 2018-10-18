<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../bidding/Requirements/Attachments.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../config/server.php');
require_once('../../../../bidding/Logs.php');
require_once('../../../../auth/Session.php');

use Bidding\Requirements\Attachments as Attachments;
use Bidding\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$att = new Attachments($DB);
$Ses = new Session($DB);

$dir = UPLOAD_ABSOLUTE_PATH;

/**
 * GET suppliers list
 */ 
$method = ($_SERVER['REQUEST_METHOD']);

if($method=="POST" && isset($_FILES['files'])){

	if (!isset($_POST['id']) || !isset($_POST['token'])) exit;


	// get privilege
	// this is IMPORTANT for checking privilege
	$token = htmlentities(htmlspecialchars($_POST['token']));

	$current_session = $Ses->get($token);

	if(!@$current_session[0]->role) exit;


	$id = $current_session[0]->pid; //author
	$bidding_requirements_id = (int) trim($_POST['id']);
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
		if(!is_dir($dir.''.$bidding_requirements_id)) mkdir($dir.''.$bidding_requirements_id,0777, true);

		// upload
		if(move_uploaded_file($tmp_name, $dir.''.$bidding_requirements_id.'/'.$new_file_name)){

			$last_id = $att->create($id, $bidding_requirements_id, $new_file_name, $name, $size, $ext, 'original');
			if ($last_id) {
				#log
				$payload = [
					'id' => $bidding_requirements_id,
					'original_filename' => $name,
					'filename' => $new_file_name,
					'size' => $size,
					'type' => $ext,
					'copy' => 'original'
				];
				$logs->log($current_session[0]->account_id, 'attach', 'requirement_attachment', $last_id, json_encode($payload));	
			}

			echo $last_id;
		}
	}
}

// remove or update attachments
if($method=="POST" && !isset($_FILES['files'])){
	$input = file_get_contents("php://input");
	$data = (@json_decode($input));
	$action = isset($data->action)?$clean_str->clean($data->action):'';
	$id = (int) isset($data->id)?$data->id:null;
	$token = isset($data->token)?$data->token:null;

	if (empty($id) || empty($action)|| empty($token)) return 0;

	$current_session = $Ses->get($token);

	if(!@$current_session[0]->role) exit;

	//remove
	if($action == 'remove'){

		$res = @$att->remove($id);
		if ($res) {
			$logs->log($current_session[0]->account_id, 'delete', 'requirement_attachment', $id);	
		}

		$data = ["data"=>$res];
		echo @json_encode($data);
		return 0;
	}
}

?>