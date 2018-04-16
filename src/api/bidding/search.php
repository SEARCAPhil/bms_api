<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../bidding/Index/Index.php');
require_once('../../helpers/CleanStr/CleanStr.php');
require_once('../../config/database/connections.php');
require_once('../../suppliers/Logs/Logs.php');
require_once('../../auth/Session.php');

use Bidding\Index as Index;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

// DEFINE CURRENT Signatories
const APPROVED_BY = 'GIL C. SAGUIGUIT, JR.';
const RECOMMENDED_BY = 'ADORACION T. ROBLES';
const CERTIFIED_BY = 'ADORACION T. ROBLES';
const APPROVED_BY_POSITION = 'Director';
const RECOMMENDED_BY_POSITION = 'Vice Chair, CBA';
const CERTIFIED_BY_POSITION = 'Unit Head, Management Services and Executive Coordinator, OD';



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

	if(!@$current_session[0]->role) exit;


	/**
	 * GET
	 * get all company list from the database
	 * @param  $page page number
	 * @param  $limit default to 20 items
	 * @return json
	 */
	if(isset($_GET['param'])){
		#instance
		$index=new Index($DB);
		$param = htmlentities(htmlspecialchars(trim($_GET['param'])));


		// For ADMIN
		if ($current_session[0]->role === 'standard') {

			echo @json_encode($index->search_all_received($current_session[0]->pid,$param,$page,$LIMIT));
			
		}

		// CBA
		if ($current_session[0]->role === 'cba_assistant') {

			echo json_encode($index->search_all_received($current_session[0]->account_id,$param,$page,$LIMIT));
			
		}


		// CBA
		if ($current_session[0]->role === 'gsu') {
			
			echo json_encode($index->search_all_approved($page,$param,$LIMIT));
			
		}
		
		
	}
	



}


?>