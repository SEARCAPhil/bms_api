<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../bidding/Requirements/Attachments.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../suppliers/Logs/Logs.php');

use Bidding\Requirements\Attachments as Attachments;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$att = new Attachments($DB);

$dir = './../../../../public/uploads/';


/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);

if($method=="POST"){
	$id = 1; //authot
	$bidding_requirements_id = 1; //sample bidding requirements ID only
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

			$last_id = $att->create($id, $bidding_requirements_id, $new_file_name, $name, $size, $ext, 'original');

			echo $last_id;
		}
	}
}

?>