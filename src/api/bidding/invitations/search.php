<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Invitations.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');

use Bidding\Invitations as Invitations;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);
$Inv=new Invitations($DB);

$result = [];
/**
 * GET suppliers list
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


	if(!@$current_session[0]->token) exit;


	/**
	 * GET
	 * get all company list from the database
	 * @param  $page page number
	 * @param  $limit default to 20 items
	 * @return json
	 */
	if(isset($_GET['param'])){
		#instance
		$Inv=new Invitations($DB);
		$param = htmlentities(htmlspecialchars(trim($_GET['param'])));

		
		if (is_null($current_session[0]->role)) {

			echo json_encode($Inv->search_all_received($current_session[0]->company_id,$param,$page,$LIMIT));
			
		}

		
		
		
	}
	
}



?>