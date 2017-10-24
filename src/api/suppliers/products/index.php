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
	 * GET products per category
	 **/
	if(!isset($_GET['id'])&&!isset($_GET['param'])&&(isset($_GET['cid'])&&$_GET['cat'])){
		$cid=(int) htmlentities(htmlspecialchars($_GET['cid']));

		#instance
		$products=new Products($DB);


		#serve with page request
		if(isset($_GET['cat'])){
			$category=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['cat']))));
		}

		
		$prod=$products->get_products($category,$page,$LIMIT);
		
		

		$data=["data"=>$prod];

		echo @json_encode($data);
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


if($method=="POST"){
	/**
	 * POST product
	 * */

	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$products=new Products($DB);
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$prod=$products->create($name);	
	$data=["data"=>$prod];

	echo @json_encode($data);

}

?>