<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once('Attachments.php');

use Bidding\Requirements\Attachments as Attachments;

class Requirements{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->Att = new Attachments($this->DB);
	}


	public function lists_by_parent($id){
		$results=[];

		$SQL='SELECT * FROM bidding_requirements WHERE particular_id = :id and status !=1';
		$SQL2='SELECT * FROM bidding_requirements_funds WHERE bidding_requirements_id = :id AND status != 1';
		$SQL3='SELECT * FROM bidding_requirements_specs WHERE bidding_requirements_id = :id';

		$sth=$this->DB->prepare($SQL);
		$sth2=$this->DB->prepare($SQL2);
		$sth3=$this->DB->prepare($SQL3);

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

			$row->attachments = $this->Att->get_attachments($row->id);
			
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

	public function remove($id){
		return self::set_status($id,1);
	}
}

?>