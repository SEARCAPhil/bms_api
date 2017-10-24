<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../suppliers/Products/Specifications/Specifications.php');
require_once('../../../../config/database/connections.php');

use Suppliers\Products\Specifications as Specs;
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


if($method=="POST"){

	/**
	 * POST product specs
	 * */
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$specs=new Specs($DB);
	#parameters
	$id=isset($data->id)?$clean_str->clean($data->id):'';
	$name=isset($data->name)?$clean_str->clean($data->name):'';
	$val=isset($data->value)?$clean_str->clean($data->value):'';

	#required
	if(empty($id)||empty($name)||empty($val)) return 0;

	$prod_specs=$specs->add($id,$name,$val);
		
	$data=["data"=>$prod_specs];

	echo @json_encode($data);
	

}

?>