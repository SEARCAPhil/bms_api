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



if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}


	/**
	 * GET
	 * get all company list from the database
	 * @param  $page page number
	 * @param  $limit default to 20 items
	 * @return json
	 */
	if(!isset($_GET['id'])){
		#instance
		$index=new Index($DB);
		$status_filter = ['drafts','closed'];

		#filter blocked or active companies
		if(isset($_GET['status'])){
			$status=trim(strip_tags(htmlentities(htmlspecialchars($_GET['status']))));
		}

		switch ($status) {
			case 'drafts':
				$status_code=0;
				break;
			case 'all':
				$status_code=null;
				break;
			case 'closed':
				$status_code=2;
				break;
			
			default:
				$status_code=null;
				break;
		}

		if(in_array($status, $status_filter)) {
			echo @json_encode($index->lists_by_status($page,$LIMIT,$status_code));
		}

		if(is_null($status_code)) {
			echo @json_encode($index->lists_all($page,$LIMIT));
		}
		
		
	}
	


	/**
	 * Preview
	 */  
	if(isset($_GET['id'])){
		$index=new Index($DB);
		$id=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
		$result=["data"=>@$index->view($id,1)];
		echo @json_encode($result);
	}
}



if($method=="POST"){
	/**
	 * POST product
	 */  
	$index=new Index($DB);
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';

	//remove
	if($action=='remove'){
		$id=(int)isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->remove($id);
		$data=["data"=>$res];
		echo @json_encode($data);
		return 0;
	}

	//block
	if($action=='block'){
		$id=isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->block($id);
		echo $res;

		//log to sytem
		if(!empty($res)){
			$logs->log($data->id,'Account has been blocked','account');
		}
	}

	//unblock
	if($action=='unblock'){
		$id=isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->unblock($id);
		echo $res;

		//log to sytem
		if(!empty($res)){
			$logs->log($data->id,'Account has been unblocked','account');
		}
	}

	//proceed to adding
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$description=isset($data->desc)?$clean_str->clean($data->desc):'';
	$deadline=isset($data->deadline)?$data->deadline:null;
	
	
	//required
	if(empty($name) || empty($description)) return 0;


	//update
	// ID is required
	if($action=='update'){

		$id=(int) isset($data->id)?$clean_str->clean($data->id):'';

		//must not be epty
		if(empty($id)) return 0;


		$result=$index->update($id,$name,$description,$deadline);

		$data=["data"=>$result];
		echo @json_encode($data);
		return 0;

	}

	if($action=='create'){

		$result=$index->create([
			"name"=>$name,
			"description"=>$description,
			"deadline"=>$deadline,
			"created_by"=> 1
		]);
	}

	$data=["data"=>$result];
	echo @json_encode($data);
	
}

?>