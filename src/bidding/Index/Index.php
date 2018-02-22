<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once(dirname(__FILE__).'/../Particulars/Particulars.php');


use Bidding\Particulars;


class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}


	/**
	 * CREATE Supplier
	 */
	public function create($params=[]){
		//parameters
		$results=[];
		$name=isset($params["name"])?$params["name"]:'';
		$description=isset($params["description"])?$params["description"]:'';
		$deadline=isset($params["deadline"])?$params["deadline"]:null;
		$created_by=isset($params["created_by"])?$params["created_by"]:null;

		//query
		$SQL='INSERT INTO bidding(name,description,deadline,created_by) values(:name,:description,:deadline,:created_by)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':description',$description);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':created_by',$created_by);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function update($id, $name, $description, $deadline){
		//parameters
		$results=[];

		//query
		$SQL='UPDATE bidding SET  name=:name, description=:description, deadline=:deadline WHERE id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':description',$description);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();

	}

	public function lists_all($page=0,$limit=20,$status=0){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM bidding WHERE status = 1 OR status = 2 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function lists_by_status($page=0,$limit=20,$status=0){
		$results=['data'=>[]];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM bidding WHERE status=:status ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results['data'][]=$row;
		}

		return $results;
	}

	public function view($id,$particulars=0){
		$results=[];	
		$SQL='SELECT * FROM bidding WHERE id=:id AND status != 4';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			//get particulars
			if($particulars){
				$part = new Particulars($this->DB);
				$row->particulars = $part->lists_by_parent($row->id,true);
			}

			$row->collaborators = $this->get_collaborators($row->id);


			$results[]=$row;
		}

		return $results;
	}

	public function set_status($id,$status){
		$SQL='UPDATE bidding set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove($id){
		return self::set_status($id,4);
	}

	public function send($id){
		return self::set_status($id,1);
	}


	/** COLLABORATORS **/
	public function set_collaborators($id, $email){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_collaborators(bidding_id, email) values(:bidding_id, :email)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_id',$id);
		$sth->bindParam(':email',$email);

		$sth->execute();

		return $this->DB->lastInsertId();

	}


	public function get_collaborators($id){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM bidding_collaborators WHERE bidding_id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);

		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}
}



?>