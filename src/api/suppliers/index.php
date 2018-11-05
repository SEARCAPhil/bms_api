<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../suppliers/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');
require_once('../../config/server.php');

use Suppliers\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=50;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$dir = UPLOAD_ABSOLUTE_PATH.'logo/';

/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);

/**
 * Insert new company profile
 */
function create ($pdo, $data, $file , $dir) {
	$clean_str = new CleanStr();

	//proceed to adding
	$name=isset($data['name'])?$clean_str->clean($data['name']):'';
	$tagline=isset($data['tagline'])?$clean_str->clean($data['tagline']):'';
	$about=isset($data['about'])?$clean_str->clean($data['about']):'';
	$established_date=isset($data['established_date'])?$clean_str->clean($data['established_date']):'';
	$established_month=isset($data['established_month'])?$clean_str->clean($data['established_month']):'';
	$established_year=isset($data['established_year'])?$clean_str->clean($data['established_year']):'';
	$location=isset($data['location'])?$clean_str->clean($data['location']):'';
	$industry=isset($data['industry'])?$clean_str->clean($data['industry']):'';
	$alias=isset($data['alias'])?$clean_str->clean($data['alias']):'';
	$website=isset($data['website'])?$clean_str->clean($data['website']):'';
	$action = isset($data['action'])?$clean_str->clean($data['action']):'';
	$id = isset($data['id'])?$clean_str->clean($data['id']):'';
	$contacts = isset($data['contacts'])?$data['contacts']:[];

	$is_contact_removed = $is_contact_added = 0;
	//required
	if(empty($name)) return 0;

	if ($action== 'create') {
		$result=$pdo->create([
			"name"=>$name,
			"tagline"=>$tagline,
			"alias" => $alias,
			"about"=>$about,
			"established_date"=>$established_date,
			"established_month"=>$established_month,
			"established_year"=>$established_year,
			"location"=>$location,
			"industry"=>$industry,
			"website"=>$website
		]);

		$id = $result;
	}

	if($action =='update' && $id) {
		$result = $pdo->update([
			"id" => $id,
			"name" => $name,
			"tagline" => $tagline,
			"alias" => $alias,
			"about" => $about,
			"established_date" => $established_date,
			"established_month" => $established_month,
			"established_year" => $established_year,
			"location" => $location,
			"industry" => $industry,
			"website" => $website
		]);

		if ($result) $result = $id;
	}
	
	if ($id) {
		// add contact information
		foreach(@$contacts as $key => $value) {
			$__con = explode(',', $value);

			// add new
			if(empty(@$__con[2])&&(!empty(@$__con[1]))) { 
				$is_contact_added = $pdo->add_contact($id,$__con[0],@$__con[1]) > 0 ? $is_contact_added+1 : $is_contact_added; 
			} else {
				// update
				if(isset($__con[2])&&!empty(@$__con[1])) {
					$is_contact_added = $pdo->update_contact($__con[2],$__con[0],@$__con[1]) > 0 ? $is_contact_added+1 : $is_contact_added;
				 }  
			}
		}

		// remove contacts
		$contacts_to_remove = explode(',', $data['contacts_to_remove']);

		foreach($contacts_to_remove as $key => $value) {
			$is_contact_removed = $pdo->remove_contact($value) > 0 ? $is_contact_removed+1 : $is_contact_removed;
		}

	}

	//add logo
	if (isset($file['name']) && !empty($id)) {
		$is_uploaded = uploadImage ($pdo, $id, $file, $dir);
		$result = $is_uploaded ? $id : $result;
	} 

	// changes marker
	$__something_changed = ($result + $is_contact_added + $is_contact_removed);
	if($action =='update' && $__something_changed) $result = $id;

	$data=["data" => $result];
	echo @json_encode($data);
}

/**
 * Upload Logo
 */
function uploadImage ($pdo, $id, $file, $dir) {
	
	if(!isset($file['name'])) return 0;

	$allowed_format = array('png','jpg','jpeg','pdf','PDF','doc','docx','xls','xlsx');
	$allowed_size = 1943040;#40MB

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
		if(move_uploaded_file($tmp_name, $dir.'/'.$new_file_name)){

			return $pdo->update_logo($id, $new_file_name);

		}
	}
}

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
	if(!isset($_GET['id'])&&!isset($_GET['param'])){
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

	// search
	if(!isset($_GET['id'])&&isset($_GET['param'])){
		#instance
		$index=new Index($DB);
		$param=trim(strip_tags(htmlentities(htmlspecialchars($_GET['param']))));
		$result=["data"=>@$index->search($param, $page)];
		echo @json_encode($result);
		exit;
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
	$index = new Index($DB);
	
	$input = file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';

	if(@$_POST['action'] == 'create' ||  @$_POST['action'] == 'update') create($index, $_POST, $_FILES['logo'], $dir);

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
	$alias=isset($data->alias)?$clean_str->clean($data->alias):'';
	
	//required
	if(empty($name)) return 0;


	//update
	// ID is required
	//if(@$_POST['action'] == 'update') create($index, $_POST, $_FILES['logo'], $dir);

	/*$result=$index->create([
		"name"=>$name,
		"tagline"=>$tagline,
		"about"=>$about,
		"established_date"=>$established_date,
		"established_month"=>$established_month,
		"established_year"=>$established_year,
		"location"=>$location,
		"industry"=>$industry
	]);*/

	$data=["data"=>$result];
	echo @json_encode($data);
	
}

?>