<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../suppliers/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');

use Suppliers\Index as Index;
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

		#filter blocked or active companies
		if(isset($_GET['status'])){
			$status=trim(strip_tags(htmlentities(htmlspecialchars($_GET['status']))));
		}

		switch ($status) {
			case 'all':
				$status_code=0;
				break;
			case 'blocked':
				$status_code=2;
				break;
			
			default:
				$status_code=0;
				break;
		}
		
		echo @json_encode($index->lists($page,$LIMIT,$status_code));
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
		$id=isset($data->id)?$clean_str->clean($data->id):'';

		$res=@$index->remove($id);
		echo $res;
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
	$tagline=isset($data->tagline)?$clean_str->clean($data->tagline):'';
	$about=isset($data->about)?$clean_str->clean($data->about):'';
	$established_date=isset($data->established_date)?$clean_str->clean($data->established_date):'';
	$established_month=isset($data->established_month)?$clean_str->clean($data->established_month):'';
	$established_year=isset($data->established_year)?$clean_str->clean($data->established_year):'';
	$location=isset($data->location)?$clean_str->clean($data->location):'';
	$industry=isset($data->industry)?$clean_str->clean($data->industry):'';
	
	//required
	if(empty($name)) return 0;


	//update
	// ID is required
	if($action=='update'){

		$id=(int) isset($data->id)?$clean_str->clean($data->id):'';

		//must not be epty
		if(empty($id)) return 0;


		$result=$index->update([
			"name"=>$name,
			"tagline"=>$tagline,
			"about"=>$about,
			"established_date"=>$established_date,
			"established_month"=>$established_month,
			"established_year"=>$established_year,
			"location"=>$location,
			"industry"=>$industry,
			"id"=>$id
		]);

		$data=["data"=>$result];
		echo @json_encode($data);
		return 0;

	}

	$result=$index->create([
		"name"=>$name,
		"tagline"=>$tagline,
		"about"=>$about,
		"established_date"=>$established_date,
		"established_month"=>$established_month,
		"established_year"=>$established_year,
		"location"=>$location,
		"industry"=>$industry
	]);

	$data=["data"=>$result];
	echo @json_encode($data);
	
}

?>