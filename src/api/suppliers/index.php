<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../suppliers/Index/Index.php');
require_once('../../config/database/connections.php');

use Suppliers\Index as Index;

$LIMIT=20;
$status='all'; 
$page=1;

/**
 * GET
 * get all company list from the database
 * @param  $page page number
 * @param  $limit default to 20 items
 * @return json
 */

/**
 * GET suppliers list
 */ 
if(isset($_GET)){
	#instance
	$index=new Index($DB);
	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	if(!isset($_GET['id'])){

		#filter blocked or active companies
		if(isset($_GET['status'])){
			$status=trim(strip_tags(htmlentities(htmlspecialchars($_GET['status']))));
		}

		switch ($status) {
			case 'all':
				$status_code=0;
				break;
			case 'blocked':
				$status_code=1;
				break;
			
			default:
				$status_code=0;
				break;
		}
		
		echo @json_encode($index->lists($page,$LIMIT,$status_code));
	}
	
}

/**
 * GET supplier's profile
 */  
if(isset($_GET['id'])){
	$index=new Index($DB);
	$id=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
	$result=["data"=>@$index->view($id)];
	echo @json_encode($result);
}


?>