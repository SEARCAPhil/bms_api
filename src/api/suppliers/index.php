<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../suppliers/Index/Index.php');
require_once('../../config/database/connections.php');

use Index\Index as Index;

$LIMIT=20;
$status=0; //default
$page=1;

/**
 * GET
 * get all company list from the database
 * @param  $page page number
 * @param  $limit default to 20 items
 * @return json
 */

if(isset($_GET)){
	#instance
	$index=new Index($DB);
	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}
	#filter blocked or active companies
	if(isset($_GET['status'])){
		$status=(int) htmlentities(htmlspecialchars($_GET['status']));
	}
	
	echo @json_encode($index->lists($page,$LIMIT,$status));
}


?>