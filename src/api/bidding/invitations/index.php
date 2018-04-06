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
	if(!isset($_GET['id'])){
		#instance
		$Inv=new Invitations($DB);
		$status_filter = ['drafts','closed'];

		#filter blocked or active companies
		if(isset($_GET['status'])){
			$status=trim(strip_tags(htmlentities(htmlspecialchars($_GET['status']))));
		}

		switch ($status) {
			case 'drafts':
				$status_code=0;
				break;
			case 'all':
				$status_code=null;
				break;
			case 'closed':
				$status_code=2;
				break;
			
			default:
				$status_code=null;
				break;
		}


		
		if (is_null($current_session[0]->role)) {
			if(in_array($status, $status_filter)) {

				if($status == 'closed') {
					echo @json_encode($Inv->lists_all_received($current_session[0]->company_id,$page,$LIMIT,$status_code));
				} else {
					echo @json_encode($Inv->lists_by_status($page,$LIMIT,$status_code));	
				}
				
			}

			if(is_null($status_code)) { 
				echo json_encode($Inv->lists_all_received($current_session[0]->company_id,$page,$LIMIT));
			}
		}

		
		
		
	}
	
}



?>