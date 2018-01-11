<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../config/database/connections.php');

use Suppliers\Logs as Logs;

$LIMIT=30;
$page=1;

/**
 * GET
 * get all company list from the database
 * @param  $page page number
 * @param  $limit default to 20 items
 * @return json
 */

if(isset($_GET)){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	/**
	 * GET logs
	 * */
	if(isset($_GET['id'])){
		#instance
		$logs=new Logs($DB);

		$id=(int) utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['id'])))));

		#serve with page request
		if(isset($_GET['event'])){
			$event=utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['event'])))));
			$log=$logs->get_logs_event($id,$event,$page,$LIMIT);
		}else{
			$log=$logs->get_logs($id,$page,$LIMIT);
		}

		
		
		$data=["data"=>$log];

		echo json_encode($data);
	}


}


?>