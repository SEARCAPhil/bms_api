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


//browsers , curl, etc...
$agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;

// token, salt
$token = ($Ses->generate_token(date('y-m-d h:i:s'),'bms-2/26/2018'));
$result = [];

if($method=="POST"){

	if(!isset($original_input->data)) return 0;
	$input = $original_input->data;

	$credential = $acc->loginO365($input->id);


	// register
	if(!isset($credential->uid)) {

		// create($company_id, $username, $password, $uid)
		// This is for creating Office365 account
		/*------------------------------------------------------
		// DEFAULT : 1 = SEARCA
		// username = @email
		-------------------------------------------------------*/
		$accountId = (int) @$acc->create(1, isset($input->mail) ? $input->mail : null, null, $input->id);

		// if account successfully created, save profile to DB
		if($accountId > 0) {

			// create_profile($id, $profile_name, $last_name, $first_name, $middle_name, $email, $department, $department_alias, $position)
			$profileId = (int) @$acc->create_profile($accountId, $input->displayName, $input->surname, $input->givenName, $input->givenName, $input->mail, $input->department, $input->department, $input->jobTitle);

			$sessionId = $Ses->set($token,$accountId,$agent);

			

			if($sessionId) {
				$result['token'] = $token;
				$result['role'] = @$credential->role;
				echo json_encode($result);
				exit;
			}
			
		}

	} else {
		// proceed to login
		// no need to register again
		$sessionId = $Ses->set($token,$credential->uid,$agent);

		if($sessionId) {
			$result['token'] = $token;
			$result['role'] = $credential->role;
			echo json_encode($result);
			exit;
		}	
	}

	

	/*if(isset($credential->uid)){
		$session = $Ses->set($token,$credential->uid,$agent);
		//return token
		if($session>0){
			$credential->token = $token;
			echo @json_encode($credential);
		}
	}*/
}
?>