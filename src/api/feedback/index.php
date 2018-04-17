<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../feedback/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');
require_once('../../auth/Session.php');

use Feedback\Index as Feedback;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;


$clean_str=new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);
$Feed = new Feedback($DB);

$result = [];

$method=($_SERVER['REQUEST_METHOD']);



if($method=="POST"){

	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action = isset($data->action)?$clean_str->clean($data->action):'';
	$token = isset($data->token)?$data->token:'';



	if($action=='create'){
		$tok = $Ses->get($token);

		if (@$tok[0]->pid) {

			$feedback = isset($data->feedback)?$clean_str->clean($data->feedback):'';

			if (!empty($feedback)) {
				echo $Feed->create($tok[0]->pid, $feedback);
			}


		}


	}


	
}

?>