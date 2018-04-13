<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Proposals.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');

use Bidding\Proposals as Proposals;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str = new CleanStr();
$logs = new Logs($DB);
$Ses = new Session($DB);
$Req = new Requirements($DB);

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
	if(isset($_GET['id']) && isset($_GET['status'])){
		#instance
		$Prop=new Proposals($DB);
		$status_filter = ['drafts','closed'];
		$id = (int) htmlentities(htmlspecialchars($_GET['id']));

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
					// echo @json_encode($Prop->lists_all($id,$page,$LIMIT,$status_code));
				} else {
					//echo @json_encode($Prop->lists_by_status($page,$LIMIT,$status_code));	
				}
				
			}

			if(is_null($status_code)) { 
				//var_dump($current_session[0]->company_id);
				// echo json_encode($Prop->lists_all($id,$page,$LIMIT));
				echo json_encode($Prop->lists_all_created($current_session[0]->company_id,$id,$page,200));
			}
		}


		if ($current_session[0]->role === 'cba_assistant') {
			// all received
			echo @json_encode($Prop->lists_all_received($id,$page,200,$status_code));	
		}



		if ($current_session[0]->role === 'gsu') {
			// all received
			echo @json_encode($Prop->lists_all_by_status($id,$page,200,3));	
		}

		
		exit;	
		
		
	}



	if(isset($_GET['id']) && !isset($_GET['status'])){
		#instance
		$Prop=new Proposals($DB);
		$status_filter = ['drafts','closed'];
		$id = (int) htmlentities(htmlspecialchars($_GET['id']));

		#
		
		//if (is_null($current_session[0]->role)) {
			
			echo json_encode($Prop->view($id));
			
		//}

		
		
		
	}
	
}

if($method=="POST"){
	/**
	 * POST product
	 */  
	$Prop=new Proposals($DB);
	
	$input=file_get_contents("php://input");


	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$token = isset($data->token)?trim($data->token):'';

	if(empty($token)) exit;

	// get privilege
	// this is IMPORTANT for checking privilege

	$current_session = $Ses->get($token);


	if(!@$current_session[0]->token) exit;

	

	// remove
	if ($action == 'remove') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';

		echo @$Prop->remove($id);
		exit;
	}

	// send
	if ($action == 'send') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';

		echo @$Prop->send($id);
		exit;
	}


	// send
	if ($action == 'change') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';
		$reason = (int) isset($data->reason) ? $clean_str->clean($data->reason) : '';

		if (empty($reason)) exit;

		echo @$Prop->request_for_changes($id,$reason);
		exit;
	}


		// send
	if ($action == 'award') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';
		$remarks = (int) isset($data->remarks) ? $clean_str->clean($data->remarks) : '';
		$original_proposal = $Prop->view($id);

		if (@$original_proposal[0]->company_id) {
			$lastId = $Req->award($original_proposal[0]->bidding_requirements_id,$original_proposal[0]->company_id,$remarks,$id);

			if ($lastId) {
				echo @$Prop->award($id);
			}
		}
		
		
		exit;
	}


	// create
	if ($action == 'create') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';
		$amount =  isset($data->amount) ? $clean_str->clean($data->amount) : 0;
		$discount = isset($data->discount) ? $clean_str->clean($data->discount) : 0;
		$remarks = isset($data->remarks) ? $clean_str->clean($data->remarks) : 0;
		$original = isset($data->original) ? $data->original : [];
		$others = isset($data->others) ? $data->others : [];

		if ($amount) {

			$original_requirements = $Req->view($id,1);
			$original_specs = [];

			if (@$original_requirements[0]->specs) {
				
				for($x = 0; $x < count($original_requirements[0]->specs); $x++) {
					for($y = 0; $y < count($original); $y++) {
						// change orig values that match on user POST data
						if($original[$y]->id == $original_requirements[0]->specs[$x]->id) {
							// change value
							$original_requirements[0]->specs[$x]->value = $original[$y]->value;
						}
					}

					// push to specs
					$original_specs[] = $original_requirements[0]->specs[$x];
					
				}
			}




			$lastId = $Prop->create($id, $current_session[0]->account_id, $amount, $discount, $remarks);	

			// proceed to adding specs
			if ($lastId) {
				for ($i=0; $i < count($original_specs) ; $i++) { 
					if(!empty($original_specs[$i]->name) && !empty($original_specs[$i]->value)) {
						$Prop->add_specs($lastId,$original_specs[$i]->name,$original_specs[$i]->value,$original_specs[$i]->id);	
					}
				}

				for ($o=0; $o < count($others) ; $o++) { 
					if (!empty($others[$o]->name) && !empty($others[$o]->value)) {
						$Prop->add_specs($lastId,$others[$o]->name,$others[$o]->value);	
					}
				}
			}

			echo $lastId;
		}

		
		exit;
	}




	// create
	if ($action == 'update') {
		$id = (int) isset($data->id) ? $clean_str->clean($data->id) : '';
		$amount =  isset($data->amount) ? $clean_str->clean($data->amount) : 0;
		$discount = isset($data->discount) ? $clean_str->clean($data->discount) : 0;
		$remarks = isset($data->remarks) ? $clean_str->clean($data->remarks) : 0;
		$original = isset($data->original) ? $data->original : [];
		$others = isset($data->others) ? $data->others : [];
		$otherSpecsToBeRemoved = isset($data->otherSpecsToBeRemoved) ? $data->otherSpecsToBeRemoved : [];
		$specs_update = 0;

		if ($amount) {

			$lastId = $Prop->update($id, $amount, $discount, $remarks);	


			//echo $lastId;

			for($x = 0; $x < count($original); $x++) {
				
				$spId = $Prop->update_specs_value($original[$x]->id,$original[$x]->value);	
				if ($spId) {
					$specs_update +=$spId;
				}	
			}

			for ($o=0; $o < count($others) ; $o++) { 

				if (!empty($others[$o]->name) && !empty($others[$o]->value)) {

					if ($others[$o]->id) {
						$spId = $Prop->update_specs($others[$o]->id,$others[$o]->name,$others[$o]->value);	
							
					} else {
						$spId = $Prop->add_specs($id,$others[$o]->name,$others[$o]->value);	
					}
					
					$specs_update +=$spId;
					
				}
			}


			for ($p=0; $p < count($otherSpecsToBeRemoved) ; $p++) { 

				if ($otherSpecsToBeRemoved[$p]->id) {
					$spId = $Prop->remove_specs($otherSpecsToBeRemoved[$p]->id);	
					
					$specs_update +=$spId;	
				}
			}

			echo $lastId || $specs_update;


		}

		
		exit;
	}

}




?>