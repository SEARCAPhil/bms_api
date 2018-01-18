<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../suppliers/Products/Products.php');
require_once('../../../config/database/connections.php');

use Suppliers\Products as Products;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$page=1;
$clean_str=new CleanStr();
$method=($_SERVER['REQUEST_METHOD']);

/**
 * GET
 * get all company list from the database
 * @param  $page page number
 * @param  $limit default to 20 items
 * @return json
 */

if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}


	/**
	 * GET product details
	 * */
	if(isset($_GET['id'])&&!isset($_GET['param'])){
		#instance
		$products=new Products($DB);
		$id=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
		$prod=$products->view($id);
		
		$data=["data"=>$prod];

		echo @json_encode($data);


	}


	/**
	 * SEARCH product details
	 * */
	if(isset($_GET['param'])){
		#instance
		$products=new Products($DB);

		$param=utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['param'])))));
		$prod=$products->search($param,$page,$LIMIT);
		
		$data=["data"=>$prod];

		echo json_encode($data);
	}


}


if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	/**
	 * GET products per category
	 **/
	if(isset($_GET['cid'])&&!isset($_GET['id'])){
		$cid=(int) htmlentities(htmlspecialchars($_GET['cid']));

		#instance
		$products=new Products($DB);
		
		$prod=$products->get_products_per_company($cid,$page,$LIMIT);
		
		$data=["data"=>$prod];

		echo @json_encode($data);
	} 
}


if($method=="POST"){
	/**
	 * POST product
	 * */

	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$products=new Products($DB);
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$description=isset($data->description)?$clean_str->clean($data->description):'';
	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$id=(int) isset($data->id)?$clean_str->clean($data->id):'';
	
	if(empty($id)) return 0;	

	//remove
	if($action=='remove'){
		$prod=$products->remove($id);
		$data=["data"=>$prod];
		echo @json_encode($data);
		return 0;
	}

	//create
	if(!empty($description)){
		$prod=$products->create($id,$name,['description'=>$description]);
	}else{
		$prod=$products->create($id,$name);
	}
		
	$data=["data"=>$prod];

	echo @json_encode($data);
	

}

?>