<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../suppliers/Accounts/Accounts.php');
require_once('../../../config/database/connections.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../suppliers/Logs/Logs.php');



use Suppliers\Accounts as Accounts;
use Helpers\CleanStr as CleanStr;
use Suppliers\Logs as Logs;

$clean_str=new CleanStr();

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


if(isset($_POST)){
	/**
	 * POST Account
	 * */

	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$accounts=new Accounts($DB);
	$username=isset($data->username)?$clean_str->clean($data->username):'';
	$password=isset($data->password)?$clean_str->clean($data->password):'';
	$id=(int) isset($data->id)?$clean_str->clean($data->id):'';
	$action=isset($data->action)?$clean_str->clean($data->action):'';


	if($action=="remove"){

		if(empty($id)) return 0;
		
		//ID in the body is not the company ID
		//account's primary id is used for deleting account
		$acc=$accounts->remove($id);	
		$data=["data"=>$acc];

		echo @json_encode($data);
		return 0;
	}


	if($action=="block"){

		if(empty($id)) return 0;
		
		//ID in the body is not the company ID
		//account's primary id is used for deleting account
		$acc=$accounts->block($id);	
		$data=["data"=>$acc];

		echo @json_encode($data);
		return 0;
	}



	if($action=="unblock"){

		if(empty($id)) return 0;
		
		//ID in the body is not the company ID
		//account's primary id is used for deleting account
		$acc=$accounts->unblock($id);	
		$data=["data"=>$acc];

		echo @json_encode($data);
		return 0;
	}


	if(empty($id)||empty($username)||empty($password)||empty($action)) return 0;


	$acc=$accounts->create($id,$username,$password);	
	$data=["data"=>$acc];

	echo @json_encode($data);
}

?>