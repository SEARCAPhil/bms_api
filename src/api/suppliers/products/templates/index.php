<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../suppliers/Products/Templates/Templates.php');
require_once('../../../../config/database/connections.php');

use Suppliers\Products\Templates as Templates;

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
	 * GET templates list
	 * */
	if(!isset($_GET['id'])){
		#instance
		$templates=new Templates($DB);
		$prod=$templates->get_templates($page,$LIMIT);
		
		$data=["data"=>$prod];

		echo @json_encode($data);
	}

	/**
	 * View Template
	 */
	if(isset($_GET['id'])){
		#instance
		$templates=new Templates($DB);

		$id=(int) trim(strip_tags(htmlentities(htmlspecialchars($_GET['id']))));
		$prod=$templates->get_template_specs($id);
		
		$data=["data"=>$prod];

		echo @json_encode($data);
	}


}


/**
 * POST Template
 */  
if(isset($_GET['test_post'])){
	
	#instance
	$templates=new Templates($DB);
	$input=file_get_contents("php://input");
	$temp=$templates->create('TEST_TEMPLATE');

	$data=["data"=>$temp];
	echo @json_encode($data);
}

/**
 * POST template specs
 * */
if(isset($_GET['test_post_specs'])){
#instance
	$templates=new Templates($DB);
	$input=file_get_contents("php://input");
	$temp=$templates->add_specs(1,'width');

	$data=["data"=>$temp];
	echo @json_encode($data);
}

?>