<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once(dirname(__FILE__).'/../Requirements/Requirements.php');

use Bidding\Requirements;

class Particulars{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}



	public function view($id){
		$results=[];
		$SQL='SELECT * FROM particulars WHERE id = :id and status != 1';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	/**
	 * CREATE Supplier
	 */
	public function create($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$name=isset($params["name"])?$params["name"]:'';
		$deadline=isset($params["deadline"])?$params["deadline"]:null;

		//query
		$SQL='INSERT INTO particulars(bidding_id,name,deadline) values(:bidding_id,:name,:deadline)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':bidding_id',$id);
		$sth->bindParam(':deadline',$deadline);
		$sth->execute();

		return $this->DB->lastInsertId();

	}



	/**
	 * CREATE Supplier
	 */
	public function update($params=[]){
		//parameters
		$results=[];
		$id=isset($params["id"])?$params["id"]:null;
		$name=isset($params["name"])?$params["name"]:'';
		$deadline=isset($params["deadline"])?$params["deadline"]:null;

		//query
		$SQL='UPDATE particulars set name=:name, deadline=:deadline WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();

	}


	public function lists_by_parent($id, $includes_requirements = false){
		$results=[];
		$SQL='SELECT * FROM particulars WHERE bidding_id = :id and status != 1';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			// include requirementa
			if ($includes_requirements) {

				$Req = new Requirements($this->DB);
				$row->requirements = $Req->lists_by_parent($row->id,true);
			}

			$results[]=$row;
		}

		return $results;
	}

	public function set_status($id,$status){
		$SQL='UPDATE particulars set status=:status where id=:id';
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