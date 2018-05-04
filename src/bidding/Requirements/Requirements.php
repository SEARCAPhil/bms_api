<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once('Attachments.php');
require_once(dirname(__FILE__).'/../Particulars/Particulars.php');

use Bidding\Requirements\Attachments as Attachments;
use Bidding\Particulars as Particulars;


class Requirements{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->Att = new Attachments($this->DB);
	}

	public function view($id){
		$results=[];
		$SQL='SELECT bidding_requirements.*, particulars.bidding_id, bidding.status as bidding_status FROM bidding_requirements LEFT JOIN particulars on particulars.id = bidding_requirements.particular_id LEFT JOIN bidding on bidding.id = particulars.bidding_id WHERE bidding_requirements.id = :id and bidding_requirements.status != 1';
		$SQL2='SELECT * FROM bidding_requirements_specs WHERE bidding_requirements_id = :id AND status != 1';
		$SQL3='SELECT bidding_requirements_invitation.*, company.name, company.alias FROM bidding_requirements_invitation LEFT JOIN company on company.id = bidding_requirements_invitation.supplier_id WHERE bidding_requirements_invitation.bidding_requirements_id = :id and bidding_requirements_invitation.status = 0';

		$SQL4 = 'SELECT bidding_requirements_funds.* FROM bidding_requirements_funds WHERE bidding_requirements_funds.bidding_requirements_id = :id AND bidding_requirements_funds.status !=1';
		$SQL5 = 'SELECT bidding_requirements_awardees.* , company.name, company.alias, bidding_requirements_proposals.status as proposal_status FROM bidding_requirements_awardees LEFT JOIN bidding_requirements_proposals on bidding_requirements_proposals.id = bidding_requirements_awardees.proposal_id   LEFT JOIN company on company.id = bidding_requirements_awardees.company_id  WHERE bidding_requirements_awardees.bidding_requirements_id = :id AND bidding_requirements_awardees.status !=1';



		$sth=$this->DB->prepare($SQL);
		$sth2=$this->DB->prepare($SQL2);
		$sth3=$this->DB->prepare($SQL3);
		$sth4=$this->DB->prepare($SQL4);
		$sth5=$this->DB->prepare($SQL5);

		$sth->bindParam(':id', $id);
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			// specs
			$row->specs = [];
			$sth2->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth2->execute();

			while($row2 =$sth2->fetch(\PDO::FETCH_OBJ)){
				$row->specs[] = $row2; 
			}

			// recepients
			$row->recepients = [];
			$sth3->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth3->execute();
			while($row3 =$sth3->fetch(\PDO::FETCH_OBJ)){
				$row->recepients[] = $row3; 
			}


			// recepients
			$row->funds = [];
			$sth4->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth4->execute();
			while($row4 =$sth4->fetch(\PDO::FETCH_OBJ)){
				$row->funds[] = $row4; 
			}


			// recepients
			$row->awardees = [];
			$sth5->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth5->execute();
			while($row5 =$sth5->fetch(\PDO::FETCH_OBJ)){
				$row->awardees[] = $row5; 
			}

			// attachments
			$row->attachments = $this->Att->get_attachments($row->id);

			$results[]=$row;
		}
		return $results;
	}


	public function lists_by_parent($id){
		$results=[];

		$SQL='SELECT * FROM bidding_requirements WHERE particular_id = :id and status !=1';
		$SQL2='SELECT * FROM bidding_requirements_funds WHERE bidding_requirements_id = :id AND status != 1';
		$SQL3='SELECT * FROM bidding_requirements_specs WHERE bidding_requirements_id = :id';
		$SQL4='SELECT bidding_requirements_invitation.*, company.name, company.alias FROM bidding_requirements_invitation LEFT JOIN company on company.id = bidding_requirements_invitation.supplier_id WHERE bidding_requirements_invitation.bidding_requirements_id = :id and bidding_requirements_invitation.status = 0';


		$sth=$this->DB->prepare($SQL);
		$sth2=$this->DB->prepare($SQL2);
		$sth3=$this->DB->prepare($SQL3);
		$sth4=$this->DB->prepare($SQL4);

		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			// funding
			$row->funds = [];
			$sth2->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth2->execute();
			while($row2 =$sth2->fetch(\PDO::FETCH_OBJ)){
				$row->funds[] = $row2; 
			}

			// specs
			$row->specs = [];
			$sth3->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth3->execute();
			while($row3 =$sth3->fetch(\PDO::FETCH_OBJ)){
				$row->specs[] = $row3; 
			}


			// recepients
			$row->recepients = [];
			$sth4->bindValue(':id',$row->id,\PDO::PARAM_INT);
			$sth4->execute();
			while($row4 =$sth4->fetch(\PDO::FETCH_OBJ)){
				$row->recepients[] = $row4; 
			}

			$row->awardees = $this->get_awardees($row->id);
			$row->attachments = $this->Att->get_attachments($row->id);
			
			$results[]=$row;
		}

		return $results;
	}

	public function get_awardees($id) {
		$results=[];

		$SQL='SELECT * FROM bidding_requirements_awardees WHERE bidding_requirements_id = :id and status !=1';
		
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {

			
			$results[]=$row;
		}

		return $results;
	}

	public function create($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$name=isset($params["name"])?$params["name"]:'';
		$quantity=isset($params["quantity"])?$params["quantity"]:0;
		$unit=isset($params["unit"])?$params["unit"]:'';
		$budget_amount=isset($params["budget_amount"])?$params["budget_amount"]:0;
		$budget_currency=isset($params["budget_currency"])?$params["budget_currency"]:'PHP';
		$bidding_excemption_request=(int) isset($params["bidding_excemption_request"])?$params["bidding_excemption_request"]:0;

		//query
		$SQL='INSERT INTO bidding_requirements(particular_id,name,quantity,unit,budget_amount,budget_currency,bidding_excemption_request) values(:particular_id,:name,:quantity,:unit,:budget_amount,:budget_currency,:bidding_excemption_request)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':particular_id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':quantity',$quantity);
		$sth->bindParam(':unit',$unit);
		$sth->bindParam(':budget_amount',$budget_amount);
		$sth->bindParam(':budget_currency',$budget_currency);
		$sth->bindParam(':bidding_excemption_request',$bidding_excemption_request);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function update($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$name=isset($params["name"])?$params["name"]:'';
		$quantity=isset($params["quantity"])?$params["quantity"]:0;
		$unit=isset($params["unit"])?$params["unit"]:'';
		$budget_amount=isset($params["budget_amount"])?$params["budget_amount"]:0;
		$budget_currency=isset($params["budget_currency"])?$params["budget_currency"]:'PHP';
		$bidding_excemption_request=(int) isset($params["bidding_excemption_request"])?$params["bidding_excemption_request"]:0;

		//query
		$SQL='UPDATE bidding_requirements SET name=:name,quantity=:quantity,unit=:unit,budget_amount=:budget_amount,budget_currency=:budget_currency,bidding_excemption_request=:bidding_excemption_request WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':quantity',$quantity);
		$sth->bindParam(':unit',$unit);
		$sth->bindParam(':budget_amount',$budget_amount);
		$sth->bindParam(':budget_currency',$budget_currency);
		$sth->bindParam(':bidding_excemption_request',$bidding_excemption_request);
		$sth->execute();

		return $sth->rowCount();

	}


	public function funds($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$fund_type=isset($params["fund_type"])?$params["fund_type"]:'';
		$cost_center=isset($params["cost_center"])?$params["cost_center"]:0;
		$line_item=isset($params["line_item"])?$params["line_item"]:'';

		//query
		$SQL='INSERT INTO bidding_requirements_funds(bidding_requirements_id,fund_type,cost_center,line_item) values(:bidding_requirements_id,:fund_type,:cost_center,:line_item)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_id',$id);
		$sth->bindParam(':fund_type',$fund_type);
		$sth->bindParam(':cost_center',$cost_center);
		$sth->bindParam(':line_item',$line_item);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function fund_update($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$fund_type=isset($params["fund_type"])?$params["fund_type"]:'';
		$cost_center=isset($params["cost_center"])?$params["cost_center"]:0;
		$line_item=isset($params["line_item"])?$params["line_item"]:'';

		//query
		$SQL='UPDATE bidding_requirements_funds SET fund_type=:fund_type,cost_center=:cost_center,line_item=:line_item WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':fund_type',$fund_type);
		$sth->bindParam(':cost_center',$cost_center);
		$sth->bindParam(':line_item',$line_item);
		$sth->execute();

		return $sth->rowCount();

	}

	public function add_specs($id, $name, $value){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_requirements_specs(bidding_requirements_id,name,value) values(:bidding_requirements_id,:name,:value)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function update_specs($id, $name, $value){
		//parameters
		$results=[];
		
		//query
		$SQL='UPDATE bidding_requirements_specs SET name = :name,value = :value WHERE id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value);
		$sth->execute();

		return $sth->rowCount();

	}

	public function send($id, $supplier_id, $account_id, $approved_by = ''){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO bidding_requirements_invitation(bidding_requirements_id,supplier_id,account_id, approved_by) values(:bidding_requirements_id,:supplier_id,:account_id, :approved_by)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_id', $id);
		$sth->bindParam(':supplier_id',$supplier_id);
		$sth->bindParam(':account_id', $account_id);
		$sth->bindParam(':approved_by', $approved_by);
		$sth->execute();

		return $this->DB->lastInsertId();

	}


	public function award($id,$supplier_id,$remarks,$proposal_id = 0){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO bidding_requirements_awardees(bidding_requirements_id,company_id,remarks,proposal_id) values(:bidding_requirements_id,:company_id,:remarks,:proposal_id)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_id',$id);
		$sth->bindParam(':company_id',$supplier_id);
		$sth->bindParam(':proposal_id',$proposal_id);
		$sth->bindParam(':remarks',$remarks,\PDO::PARAM_STR);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function set_deadline($id, $deadline = '0000-00-00'){
		//parameters
		$results=[];
		//query
		$SQL='UPDATE bidding_requirements set deadline = :deadline where id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':deadline',$deadline);
		$sth->execute();

		return $sth->rowCount();

	}

	public function set_recepient_status($id, $status){
		//parameters
		$results=[];
		//query
		$SQL='UPDATE bidding_requirements_invitation set status=:status where id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();

	}

	// awardee
	public function set_awardee_status($id, $status){
		//parameters
		$results=[];
		//query
		$SQL='UPDATE bidding_requirements_awardees set status=:status where id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();

	}

	public function remove_recepients($id){
		return self::set_recepient_status($id,1);
	}

	public function set_status($id,$status){
		$SQL='UPDATE bidding_requirements set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove_fund($id,$status = 1){
		$SQL='UPDATE bidding_requirements_funds set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}


	public function remove_specs($id,$status = 1){
		$SQL='UPDATE bidding_requirements_specs set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove_awardee($id){
		return self::set_awardee_status($id,1);
	}

	public function remove($id){
		return self::set_status($id,1);
	}


	public function feedback($id, $account_id, $feedback){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_requirements_awardees_feedback(account_id,bidding_requirements_awardees_id,feedback) values(:account_id,:bidding_requirements_awardees_id,:feedback)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_awardees_id',$id);
		$sth->bindParam(':account_id',$account_id);
		$sth->bindParam(':feedback',$feedback);
		$sth->execute();

		return $this->DB->lastInsertId();

	}



	public function get_feedback_per_awardee($id){
		
		//query
		$SQL='SELECT bidding_requirements_awardees_feedback.*, bidding_requirements_awardees.company_id, company.name as company_name FROM bidding_requirements_awardees_feedback LEFT JOIN bidding_requirements_awardees ON bidding_requirements_awardees.id = bidding_requirements_awardees_feedback.bidding_requirements_awardees_id LEFT JOIN company on company.id = bidding_requirements_awardees.company_id WHERE bidding_requirements_awardees_feedback.bidding_requirements_awardees_id = :id ';

		$SQL2 = 'SELECT profile_name, department from profile WHERE account_id = :id ORDER BY profile.id DESC LIMIT 1';
		$SQL3 = 'SELECT * from bidding_requirements_awardees_feedback_ratings WHERE bidding_requirements_awardees_feedback_ratings.bidding_requirements_awardees_feedback_id = :id ORDER BY name ASC';

		$sth = $this->DB->prepare($SQL);
		$sth2 = $this->DB->prepare($SQL2);
		$sth3 = $this->DB->prepare($SQL3);

		$sth->bindParam(':id',$id);
		$sth->execute();

		$results = [];

		while ($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$row->feedback = nl2br($row->feedback);

			# profile
			$sth2->bindParam(':id',$row->account_id);
			$sth2->execute();
			$row->author = [];
			while($row2 = $sth2->fetch(\PDO::FETCH_OBJ)) {
				$row->author[] = $row2;
			}


			# ratings
			$sth3->bindParam(':id',$row->id);
			$sth3->execute();
			$row->ratings = [];
			while($row3 = $sth3->fetch(\PDO::FETCH_OBJ)) {
				$row->ratings[] = $row3;
			}

			$results[] = $row;
		}

		return $results;

	}


	public function get_feedback_per_bidding_request($id){
		
		//query
		$SQL='SELECT bidding_requirements_awardees_feedback.*, bidding_requirements_awardees.company_id, company.name as company_name, particulars.bidding_id , bidding_requirements.name as product_name FROM bidding_requirements_awardees_feedback LEFT JOIN bidding_requirements_awardees ON bidding_requirements_awardees.id = bidding_requirements_awardees_feedback.bidding_requirements_awardees_id LEFT JOIN company on company.id = bidding_requirements_awardees.company_id LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_awardees.bidding_requirements_id LEFT JOIN particulars on particulars.id = bidding_requirements.particular_id WHERE particulars.bidding_id = :id ';

		$SQL2 = 'SELECT profile_name, department from profile WHERE account_id = :id ORDER BY profile.id DESC LIMIT 1';
		$SQL3 = 'SELECT * from bidding_requirements_awardees_feedback_ratings WHERE bidding_requirements_awardees_feedback_ratings.bidding_requirements_awardees_feedback_id = :id ORDER BY name ASC';

		$sth = $this->DB->prepare($SQL);
		$sth2 = $this->DB->prepare($SQL2);
		$sth3 = $this->DB->prepare($SQL3);

		$sth->bindParam(':id',$id);
		$sth->execute();

		$results = [];

		while ($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$row->feedback = nl2br($row->feedback);

			# profile
			$sth2->bindParam(':id',$row->account_id);
			$sth2->execute();
			$row->author = [];
			while($row2 = $sth2->fetch(\PDO::FETCH_OBJ)) {
				$row->author[] = $row2;
			}


			# ratings
			$sth3->bindParam(':id',$row->id);
			$sth3->execute();
			$row->ratings = [];
			while($row3 = $sth3->fetch(\PDO::FETCH_OBJ)) {
				$row->ratings[] = $row3;
			}

			$results[] = $row;
		}

		return $results;

	}


	public function get_feedback($id){
		
		//query
		$SQL='SELECT bidding_requirements_awardees_feedback.*, bidding_requirements_awardees.company_id, company.name as company_name, particulars.bidding_id , bidding_requirements.name as product_name, bidding_requirements.deadline , bidding_requirements_proposals.amount, bidding_requirements_proposals.currency FROM bidding_requirements_awardees_feedback LEFT JOIN bidding_requirements_awardees ON bidding_requirements_awardees.id = bidding_requirements_awardees_feedback.bidding_requirements_awardees_id LEFT JOIN bidding_requirements_proposals on bidding_requirements_proposals.id = bidding_requirements_awardees.proposal_id LEFT JOIN company on company.id = bidding_requirements_awardees.company_id LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_awardees.bidding_requirements_id LEFT JOIN particulars on particulars.id = bidding_requirements.particular_id WHERE bidding_requirements_awardees_feedback.id = :id ';

		$SQL2 = 'SELECT profile_name, department from profile WHERE account_id = :id ORDER BY profile.id DESC LIMIT 1';
		$SQL3 = 'SELECT * from bidding_requirements_awardees_feedback_ratings WHERE bidding_requirements_awardees_feedback_ratings.bidding_requirements_awardees_feedback_id = :id ORDER BY name ASC';

		$sth = $this->DB->prepare($SQL);
		$sth2 = $this->DB->prepare($SQL2);
		$sth3 = $this->DB->prepare($SQL3);

		$sth->bindParam(':id',$id);
		$sth->execute();

		$results = [];

		while ($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$row->feedback = nl2br($row->feedback);

			# profile
			$sth2->bindParam(':id',$row->account_id);
			$sth2->execute();
			$row->author = [];
			while($row2 = $sth2->fetch(\PDO::FETCH_OBJ)) {
				$row->author[] = $row2;
			}


			# ratings
			$sth3->bindParam(':id',$row->id);
			$sth3->execute();
			$row->ratings = [];
			while($row3 = $sth3->fetch(\PDO::FETCH_OBJ)) {
				$row->ratings[] = $row3;
			}

			$results[] = $row;
		}

		return $results;

	}


	public function rate($id, $name, $value){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_requirements_awardees_feedback_ratings(bidding_requirements_awardees_feedback_id, name, value) values(:bidding_requirements_awardees_feedback_id, :name, :value)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_awardees_feedback_id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value);
		$sth->execute();

		return $this->DB->lastInsertId();

	}
}

?>