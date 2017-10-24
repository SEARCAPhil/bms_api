<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../suppliers/Accounts/Accounts.php');
require_once('../../../config/database/connections.php');

use Suppliers\Accounts as Accounts;

$LIMIT=20;
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
	 * GET accounts
	 * */
	if(isset($_GET['cid'])&&!isset($_GET['id'])&&!isset($_GET['param'])){
		#instance
		$accounts=new Accounts($DB);

		$cid=(int) utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['cid'])))));

		$acc=$accounts->lists($cid,$page,$LIMIT);
		
		
		$data=["data"=>$acc];

		echo json_encode($data);
	}

	if(isset($_GET['id'])&&!isset($_GET['cid'])){
		#instance
		$accounts=new Accounts($DB);

		$id=(int) utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['id'])))));

		$acc=$accounts->view($id);
		//$acc=$accounts->get_privilege($id);
		
		$data=["data"=>$acc];

		echo json_encode($data);
	}


	if(isset($_GET['param'])&&!isset($_GET['id'])){
		#instance
		$accounts=new Accounts($DB);

		$param=utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['param'])))));

		#search for all
		if(!isset($_GET['cid'])){
			$acc=$accounts->search($param,$page,$LIMIT);
		}else{
			#search under company
			$cid=(int) utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['cid'])))));
			$acc=$accounts->search_per_company($cid,$param,$page,$LIMIT);	
		}
		
		
		
		$data=["data"=>$acc];

		echo json_encode($data);
	}


}


?>