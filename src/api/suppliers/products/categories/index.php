<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../suppliers/Products/Categories/Categories.php');
require_once('../../../../suppliers/Products/Products.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');


use Suppliers\Products\Categories as Categories;
use Suppliers\Products as Products;
use Helpers\CleanStr as CleanStr;

$clean_str=new CleanStr();

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

$method=($_SERVER['REQUEST_METHOD']);

if($method=='GET'&&isset($_GET['cid'])){
	

	$cid=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['cid']))));

	#instance
	$categories=new Categories($DB);
	$products=new Products($DB);

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	
	$category=$categories->get_parent_categories($_GET['cid'],$page,$LIMIT);
	$category=is_array($category)?$category:[];

	for($x=0;$x<count($category);$x++){
		#sub-categories
		if(!isset($category[$x]->sub_categories)) $category[$x]->sub_categories=[];
		if(!isset($category[$x]->products)) $category[$x]->products=[];

		#sub-categories
		if(isset($_GET['sub'])){
			$id=$category[$x]->id;
			$category[$x]->sub_categories[]=$categories->get_children_categories($_GET['cid'],$page,$LIMIT);
		}
		
		#products
		if(isset($_GET['prod'])){
			#get initial product list
			$category[$x]->products[]=$products->get_products($_GET['cid'],$page,$LIMIT);
		}
	}

	
	$data=["data"=>$category];

	echo @json_encode($data);
	
}


if($method=='GET'&&isset($_GET['id'])){


	$id=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
	
	#instance
	$categories=new Categories($DB);

	$category=$categories->view($id);

	$data=["data"=>$category];

	echo @json_encode($data);


}

if($method=='POST'){
	/**
	 * POST Account
	 * */

	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$categories=new Categories($DB);
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$description=isset($data->description)?$clean_str->clean($data->description):'';
	$id=(int) isset($data->id)?$clean_str->clean($data->id):'';
	$action=isset($data->action)?$clean_str->clean($data->action):'';

	//prevent empty primary key
	if(empty($id)) return 0;


	if($action=="remove"){

		
		$cat=$categories->remove($id);	
		$data=["data"=>$cat];

		echo @json_encode($data);
		return 0;
	}

	if($action=="update"){
		
		$cat=$categories->update($id,$name,$description);	
		$data=["data"=>$cat];

		echo @json_encode($data);
		return 0;
	}


	if(empty($id)||empty($name)||empty($description)||empty($action)) return 0;


	$cat=$categories->create($id,$name,$description);	
	$data=["data"=>$cat];

	echo @json_encode($data);
}


?>