<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../bidding/Index/Index.php');
require_once('../../../../bidding/Particulars/Particulars.php');
require_once('../../../../bidding/Requirements/Requirements.php');
require_once('../../../../bidding/Requirements/Mailer.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../config/constants/reports.php');
require_once('../../../../suppliers/Logs/Logs.php');
require_once('../../../../auth/Session.php');
require_once('../../../../auth/Account.php');

use Bidding\Index as Index;
use Bidding\Proposals\Mailer as Mailer;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;
use Auth\Account as Account;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$part = new Particulars($DB);
$req = new Requirements($DB);
$Ses = new Session($DB);
$Acc= new Account($DB);

$LIMIT=20;
$status='all'; 
$page=1;

$method=($_SERVER['REQUEST_METHOD']);


if($method=="POST"){

	$index=new Index($DB);
	# read from input stream
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));

	$action=isset($data->action)?$clean_str->clean($data->action):'';
	$id=(int) isset($data->id)?$data->id:null;
	$token = isset($data->token) ? $clean_str->clean($data->token) : '';

	# get privilege
	# this is IMPORTANT for checking privilege
	if(empty($token)){
		exit;
	}

	$current_session = $Ses->get($token);


	if(!@$current_session[0]->token) exit;

	# required
	if(empty($id)) return 0;

	if($action == 'remove') {
		$result = $req->remove_recepients($id);
		$data=["data"=> $result];
		echo @json_encode($data);	
	}

	if($action == 'send') {
		$specs = isset($data->suppliers)?$data->suppliers:[];
		$specs_ids = [];
		$specs_sent = [];
		$result = [];
		$specs_allowed_to_be_sent = 0;
		$requirements_profile = [];
		foreach ($specs as $key => $value) {
			if(!empty(trim($value))) {
				array_push($specs_ids, (int) $key);
			}
		}

		if (!empty($specs_ids)) {
			for ($x=0; $x < count($specs_ids); $x++) {

				# MUST not send an invitation if item has no deadline yet
				$res = $req->view($id);
				$requirements_profile[$id] = $res;

				if($res[0]->deadline != '0000-00-00' && !empty($res[0]->deadline) && ($res[0]->deadline)) {

					$specs_allowed_to_be_sent++;
				}
				
			}

			# send email to CBA the submit
			$MailerClass = new Mailer();

			# all items must have a deadline
			# if one of those are not, DO NOT send an invitation
			if(count($specs_ids) == $specs_allowed_to_be_sent) {

				for ($x=0; $x < count($specs_ids); $x++) {
					# email template
					$message = include_once('proposal_email_inv_template.php');

					if(!$requirements_profile[$id][0]) exit;

						$receivers = [];
						# get all username under this company and save to spooler
						foreach($Acc->get_accounts_per_supplier($specs_ids[$x]) as $key => $value){
							if(!is_null($value->username)) array_push($receivers, $value->username);

						}

						# send email before inserting to database
						if($MailerClass->send($message, $receivers)) {
							$result = $req->send($id,$specs_ids[$x],$current_session[0]->account_id,APPROVED_BY_INVITATIONS);
							# add to sent items
							if ($result) {
								$specs_sent[$specs_ids[$x]] = $result;
							}
						}			
				}
				# send data
				$data=["data"=> $specs_sent];
				echo @json_encode($data);
			}
			# stop script
			exit;	
		}

	}


	# general sending
	if($action == 'send_items') {
		$items = isset($data->items)?$data->items:[];
		# specs
		$specs = isset($data->suppliers)?$data->suppliers:[];
		$specs_ids = [];
		$specs_sent = [];
		$result = [];
		$specs_allowed_to_be_sent = 0;
		$item_ids = [];

		foreach ($specs as $key => $value) {
			if(!empty(trim($value))) {
				array_push($specs_ids, (int) $key);
			}
		}

		
		foreach ($items as $key => $value) {
			if(!empty(trim($value))) {
				array_push($item_ids, (int) $key);
			}
		}





		if (!empty($specs_ids)) {

			for ($a=0; $a < count($item_ids); $a++) {

				# MUST not send an invitation if item has no deadline yet
				$res = $req->view($item_ids[$a]);

				if($res[0]->deadline != '0000-00-00' && !empty($res[0]->deadline) && ($res[0]->deadline)) {

					$specs_allowed_to_be_sent++;
				}
				
			}

			# Mailer
			$MailerClass = new Mailer();

			# all items must have a deadline
			# if one of those are not, DO NOT send an invitation
			if(count($item_ids) == $specs_allowed_to_be_sent) {

				for ($a=0; $a < count($item_ids); $a++) { 
					for ($x=0; $x < count($specs_ids); $x++) {  

						$receivers = [];
						# get all username under this company and save to spooler
						foreach($Acc->get_accounts_per_supplier($specs_ids[$x]) as $key => $value){
							if(!is_null($value->username)) array_push($receivers, $value->username);

						}

						$message = include_once('proposal_email_inv_template.php');
						if($MailerClass->send($message, $receivers)) {
							$result = $req->send($item_ids[$a],$specs_ids[$x],$current_session[0]->account_id,APPROVED_BY_INVITATIONS);
							// add to sent items
							if ($result) {
								$specs_sent[$specs_ids[$x]] = $result;
							}
						}
					}
				}
				# send data			
				$data=["data"=> $specs_sent];
				echo @json_encode($data);
				exit;	
			}

			

		}

	}


	
	if($action == 'winner') {
		$remarks = isset($data->remarks)?$data->remarks:'';
		$sup = isset($data->suppliers)?$data->suppliers:[];
		$sup_ids = [];
		$sup_sent = [];
		$viewable = false;
		foreach ($sup as $key => $value) {
			if(!empty(trim($value))) {
				array_push($sup_ids, (int) $key);
			}
		}

		if (!empty($sup_ids)) {

			if ($sup_ids[0]) {
				
				// MUST not send an invitation if item has no deadline yet
				$res = $req->view($id);
				if($res[0]->deadline == '0000-00-00' || empty($res[0]->deadline) || (!$res[0]->deadline)) exit;
				// award($id,$supplier_id,$remarks)
				$data = $req->award($id,$sup_ids[0],$remarks);
				echo @json_encode($data);
				exit;	
			}
			

		}

	}


	if($action == 'award') {
		echo $req->award_winner($id);
		exit;
	}



	if($action == 'remove_awardee') {
		echo $req->remove_awardee($id);
		exit;
	}

	
	
}

?>