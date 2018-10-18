<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../suppliers/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');

use Suppliers\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$clean_str=new CleanStr();
$logs = new Logs($DB);


/**
 * GET suppliers list
 */ 
$method=($_SERVER['REQUEST_METHOD']);

if($method=="GET"){
  $index = new Index($DB);
  echo @json_encode($index->stat());
}

?>