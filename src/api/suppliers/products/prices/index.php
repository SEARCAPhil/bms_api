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
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	#instance
	$prices=new Prices($DB);


	$id=(int) isset($data->id)?$clean_str->clean($data->id):'';
	$action=isset($data->action)?$clean_str->clean($data->action):'';
	
	//update
	if($action=='update'){
		if(empty($id)) return 0;
		$name=isset($data->name)?$clean_str->clean($data->name):'';
		$value=isset($data->value)?$clean_str->clean($data->value):'';

		$pr_info = $prices->info($id);

		$cur = $pr_info[0]->currency;


		if($name=='currency'){
			$pr=$prices->update($id,$value);	
		}

		if($name=='amount'){
			$pr=$prices->add($pr_info[0]->product_id,$value,$cur);	
		}
		
	
		$data=["data"=>$pr];

		echo @json_encode($data);
		return 0;	
	}

	/**
	 * POST product specs
	 * */

	
	$id=isset($data->id)?$clean_str->clean($data->id):'';
	$amount=isset($data->amount)?$clean_str->clean($data->amount):'';
	$currency=isset($data->currency)?$clean_str->clean($data->currency):'';

	$prod=$prices->add($id,$amount,$currency);
	
	$data=["data"=>$prod];

	echo @json_encode($data);
	
}

?>