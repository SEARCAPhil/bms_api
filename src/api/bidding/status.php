<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../bidding/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');

use Bidding\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);

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

	$id=(int)isset($data->id)?$clean_str->clean($data->id):'';
	$status=(int)isset($data->status)?$clean_str->clean($data->status):'';


	if (!empty($id) && !empty($status)) {
		switch ($status) {
			case 2:
				$res = @$index->open($id);
				break;
			case 5:
				$res = @$index->closed($id);
				break;
			case 3:
				$res = @$index->approve($id);
				break;
			case 6:
				$res = @$index->failed($id);
				break;
			case 1:
				$res = @$index->send($id);
				break;
			
			default:
				$res = 0;
				break;
		}
		
		$data=["data"=>$res];
		echo @json_encode($data);
		return 0;	
	}
}

?>