<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../Auth/Account.php');
require_once('../../Auth/Session.php');
require_once('../../config/database/connections.php');

use Auth\Account as Account;
use Auth\Session as Session;

$method=($_SERVER['REQUEST_METHOD']);
$original_input =@ json_decode(file_get_contents("php://input"));

$acc = new Account($DB);
$Ses = new Session($DB);

$page = 1;
$result = [];

/**
 * list
 */ 
$method=($_SERVER['REQUEST_METHOD']);

if($method=="GET"){

	#serve with page request
	if(isset($_GET['page'])){
		$page=(int) htmlentities(htmlspecialchars($_GET['page']));
	}

	#serve with page request
	if(!isset($_GET['token'])){
		exit;
	}

	// get privilege
	// this is IMPORTANT for checking privilege
	$token=htmlentities(htmlspecialchars($_GET['token']));

	$current_session = $Ses->get($token);

	if(!@$current_session[0]->role) exit;

	echo json_encode($acc->get_cba_assts('cba_assistant',$page));


}


?>