<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once(dirname(__FILE__).'/../Particulars/Particulars.php');
require_once(dirname(__FILE__).'/../Attachments.php');


use Bidding\Particulars;
use Bidding\Attachments;


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
		$excemption = (int) isset($params["excemption"])?$params["excemption"]:0;

		//query
		$SQL='INSERT INTO bidding(name,description,deadline,created_by,excemption) values(:name,:description,:deadline,:created_by,:excemption)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':description',$description);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':created_by',$created_by);
		$sth->bindParam(':excemption',$excemption);
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

	public function lists_all_received($email,$page=0,$limit=20,$status=0){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT bidding.*, profile.profile_name, profile.email FROM bidding LEFT JOIN profile on profile.id = bidding.created_by LEFT JOIN account on account.id = profile.id LEFT JOIN bidding_collaborators on bidding_collaborators.email = profile.email WHERE (bidding.status !=4 and bidding.status !=0) AND (profile.email = :email) OR (bidding.created_by =:email) ORDER BY bidding.name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':email',$email);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function lists_all_drafts($pid, $page=0,$limit=20,$status=0){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE (bidding.status !=4 and bidding.status = 0) AND bidding.created_by = profile.id ORDER BY bidding.name ASC LIMIT :offset,:lim';
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


	public function lists_by_status_admin($page=0,$limit=20,$status=0){
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

	public function view($id,$particulars=0){
		$results=[];	
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.id=:id AND bidding.status != 4';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();

		$att = new Attachments($this->DB);

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			//get particulars
			if($particulars){
				$part = new Particulars($this->DB);
				$row->particulars = $part->lists_by_parent($row->id,true);
			}

			$row->collaborators = $this->get_collaborators($row->id);

			$row->attachments = $att->get_attachments($row->id);



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

	public function closed($id){
		return self::set_status($id,5);
	}

	public function open($id){
		return self::set_status($id,2);
	}
	public function failed($id){
		return self::set_status($id,6);
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