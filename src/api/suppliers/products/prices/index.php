<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../suppliers/Products/Prices/Prices.php');
require_once('../../../../config/database/connections.php');

use Suppliers\Products\Prices as Prices;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$page=1;
$clean_str=new CleanStr();
$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){
	/**
	 * POST product specs
	 * */
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));
	#instance
	$prices=new Prices($DB);
	$id=isset($data->id)?$clean_str->clean($data->id):'';
	$amount=isset($data->amount)?$clean_str->clean($data->amount):'';
	$currency=isset($data->currency)?$clean_str->clean($data->currency):'';

	$prod=$prices->add($id,$amount,$currency);
	
	$data=["data"=>$prod];

	echo @json_encode($data);
	
}

?>