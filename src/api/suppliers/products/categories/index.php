<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../suppliers/Products/Categories/Categories.php');
require_once('../../../../suppliers/Products/Products.php');
require_once('../../../../config/database/connections.php');

use Suppliers\Products\Categories as Categories;
use Suppliers\Products as Products;

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
	if(!isset($_GET['cid'])) return 0;

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


?>