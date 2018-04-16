<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../auth/Account.php');
require_once('../../auth/Session.php');
require_once('../../config/database/connections.php');

use Auth\Account as Account;
use Auth\Session as Session;

$method=($_SERVER['REQUEST_METHOD']);
$input =@ json_decode(file_get_contents("php://input"));

$acc = new Account($DB);
$Ses = new Session($DB);


//browsers , curl, etc...
$agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;

// token, salt
$token = ($Ses->generate_token(date('y-m-d h:i:s'),'bms-2/26/2018'));
$result = [];

if($method=="POST"){

	if (isset($input->username) && isset($input->password)) {
		$credential = $acc->login($input->username, $input->password);
		if(isset($credential->uid)){

			$sessionId = $Ses->set($token,$credential->uid,$agent);

			if($sessionId) {
				$result['id'] = $credential->uid;
				$result['token'] = $token;
				$result['role'] = 'supplier';
				echo json_encode($result);
				exit;
			}

		}

	}

}
?>