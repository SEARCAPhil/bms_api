<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../suppliers/Accounts/Accounts.php');
require_once('../../../config/database/connections.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../auth/Session.php');
require_once('../../../config/database/connections.php');

use Suppliers\Accounts as Accounts;
use Helpers\CleanStr as CleanStr;
use Suppliers\Logs as Logs;
use Auth\Session as Session;

$clean_str = new CleanStr();
$Ses = new Session ($DB);

$LIMIT = 20;
$page =1 ;

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


	if(isset($_GET['id'])){
		#instance
		$accounts = new Accounts($DB);

		$id = (int) utf8_encode(trim(strip_tags(htmlentities(htmlspecialchars($_GET['id'])))));

		$sessions = $Ses->get_all_sessions($id, $page, $LIMIT);
		
		
		$data=["data"=>$sessions];

		echo json_encode($data);
	}

}

?>