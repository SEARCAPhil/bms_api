<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../../bidding/Requirements/Attachments.php');
require_once('../../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../../config/database/connections.php');
require_once('../../../../../suppliers/Logs/Logs.php');

use Bidding\Requirements\Attachments as Attachments;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);


/**
 * Attach recent file
 * */
function attach($DB,$id) {
	$att=new Attachments($DB);
	return $att->view($id);
}

/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);



if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	/**
	 * Preview
	 */  

	$att=new Attachments($DB);
	$id=1;
	$result=["data"=>$att->lists_original_copy_only($id,$page)];
	echo @json_encode($result);
	
}


if($method=="POST"){
	/**
	 * POST product
	 */  
	$att=new Attachments($DB);
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$atts=isset($data->attachments)?$data->attachments:null;

	// profile id here
	$id =1;
	$result = [];

	if($action === 'create') {
		foreach ($data->attachments as $key => $value) {
			if(!empty(trim($value))) {
				// attach
				$preview = $att->view($key);
				if ($preview[0]) {

					$lastId = $att->create($id, $preview[0]->bidding_requirements_id, $preview[0]->filename, $preview[0]->original_filename, $preview[0]->size, $preview[0]->type, 'duplicate', $preview[0]->id);
					
					// success
					if( $lastId > 0) $result[] = array('id' => $lastId, 'filename' => $preview[0]->filename, 'original_filename' => $preview[0]->original_filename, 'type' => $preview[0]->type);
				}
			}
		}


		$data=["data"=>$result];
		echo @json_encode($data);
		return 0;

	}
}

?>