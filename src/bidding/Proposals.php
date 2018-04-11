<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 


class Proposals{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function create($id, $account_id, $amount, $discount = 0, $remarks =''){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO bidding_requirements_proposals(bidding_requirements_id, account_id, amount, discount, remarks) values(:bidding_requirements_id, :account_id, :amount, :discount, :remarks)';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_id',$id);
		$sth->bindParam(':account_id',$account_id);
		$sth->bindParam(':amount',$amount);
		$sth->bindParam(':discount',$discount);
		$sth->bindParam(':remarks',$remarks);

		$sth->execute();

		return $this->DB->lastInsertId();

	}



	public function update($id, $amount, $discount = 0, $remarks =''){
		//parameters
		$results=[];
		//query
		$SQL='UPDATE bidding_requirements_proposals SET  amount = :amount, discount = :discount, remarks =:remarks WHERE id = :id';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':amount',$amount);
		$sth->bindParam(':discount',$discount);
		$sth->bindParam(':remarks',$remarks);
		$sth->bindParam(':id',$id);

		$sth->execute();

		return $sth->rowCount();

	}

	public function lists_all($req_id,$page=0,$limit=200){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT bidding_requirements_proposals.*, bidding_requirements.name, quantity, unit, username, company_id FROM  bidding_requirements_proposals LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_proposals.bidding_requirements_id LEFT JOIN account on account.id = bidding_requirements_proposals.account_id WHERE bidding_requirements_proposals.bidding_requirements_id = :id AND bidding_requirements_proposals.status !=4 LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$req_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_all_created($company_id,$req_id,$page=0,$limit=200){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT bidding_requirements_proposals.*, bidding_requirements.name, quantity, unit, username, company_id FROM  bidding_requirements_proposals LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_proposals.bidding_requirements_id LEFT JOIN account on account.id = bidding_requirements_proposals.account_id WHERE bidding_requirements_proposals.bidding_requirements_id = :id AND bidding_requirements_proposals.status !=4 AND company_id = :company_id LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$req_id,\PDO::PARAM_INT);
		$sth->bindValue(':company_id',$company_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_all_received($req_id,$page=0,$limit=200){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT bidding_requirements_proposals.*, bidding_requirements.name, quantity, unit, username, company_id FROM  bidding_requirements_proposals LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_proposals.bidding_requirements_id LEFT JOIN account on account.id = bidding_requirements_proposals.account_id WHERE bidding_requirements_proposals.bidding_requirements_id = :id AND bidding_requirements_proposals.status !=4 AND bidding_requirements_proposals.status !=0 ORDER BY bidding_requirements_proposals.date_created DESC LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$req_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}



	public function lists_by_status($page=0,$limit=20,$status=0){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.status =:status ORDER BY bidding.name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function view($id){
		$results=[];	

		$SQL = 'SELECT bidding_requirements_proposals.*, bidding_requirements.name, quantity, unit, username, company_id, company.name as company_name FROM  bidding_requirements_proposals LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_proposals.bidding_requirements_id LEFT JOIN account on account.id = bidding_requirements_proposals.account_id LEFT JOIN company on company.id = company_id WHERE bidding_requirements_proposals.id = :id';

		$SQL2 = 'SELECT bidding_requirements_proposals_specs.*, bidding_requirements_specs.name as orig_name, bidding_requirements_specs.value as orig_value FROM bidding_requirements_proposals_specs LEFT JOIN bidding_requirements_specs on bidding_requirements_specs.id = bidding_requirements_proposals_specs.bidding_requirements_specs_id  WHERE bidding_requirements_proposals_id = :id and bidding_requirements_proposals_specs.status = 0 ';

		$sth=$this->DB->prepare($SQL);
		$sth2=$this->DB->prepare($SQL2);

		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();


		while($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$row->specs = [];
			$row->orig_specs = [];
			$row->other_specs = [];

			$sth2->bindParam(':id',$row->id,\PDO::PARAM_INT);
			$sth2->execute();

			// specs
			while ($row2 = $sth2->fetch(\PDO::FETCH_OBJ)) {

				if (!$row2->orig_name) {
					$row->other_specs[] = $row2;
				} else {
					$row->orig_specs[] = $row2;
				}

				$row->specs[] = $row2;
			}


			$results[]=$row;
		}

		return $results;
	}

	public function add_specs($id, $name, $value, $parent_id = 0){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_requirements_proposals_specs(bidding_requirements_proposals_id,name,value,bidding_requirements_specs_id) values(:bidding_requirements_proposals_id,:name,:value,:bidding_requirements_specs_id)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_requirements_proposals_id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value);
		$sth->bindParam(':bidding_requirements_specs_id',$parent_id);
		$sth->execute();

		return $this->DB->lastInsertId();

	}


	public function update_specs($id, $name, $value){
		//parameters
		$results=[];
		
		//query
		$SQL='UPDATE bidding_requirements_proposals_specs set name =:name, value =:value WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value);

		$sth->execute();

		return $sth->rowCount();

	}


	public function update_specs_value($id, $value){
		//parameters
		$results=[];
		
		//query
		$SQL='UPDATE bidding_requirements_proposals_specs set value =:value WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':value',$value);

		$sth->execute();

		return $sth->rowCount();

	}


	public function set_specs_status($id,$status){
		$SQL='UPDATE bidding_requirements_proposals_specs set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove_specs($id){
		return (int) self::set_specs_status($id, 1);
	}



	public function set_status($id,$status){
		$SQL='UPDATE bidding_requirements_proposals set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}


	public function remove($id){
		return (int) self::set_status($id, 4);
	}

	public function send($id){
		return (int) self::set_status($id, 1);
	}

	public function award($id){
		return (int) self::set_status($id, 3);
	}

	public function request_for_changes($id,$reason){
		$SQL='UPDATE bidding_requirements_proposals set status=2, bidders_remarks =:reason where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':reason',$reason,\PDO::PARAM_STR);
		$sth->execute();

		return $sth->rowCount();
	}


}



?>