<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once(dirname(__FILE__).'/Particulars/Particulars.php');
require_once(dirname(__FILE__).'/Attachments.php');


use Bidding\Particulars;
use Bidding\Attachments;



class Invitations{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}


	public function lists_all_received($supplier_id,$page=0,$limit=20){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;

		$SQL='SELECT bidding_requirements_invitation.*, bidding_requirements.name, quantity, unit, deadline FROM  bidding_requirements_invitation LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_invitation.bidding_requirements_id  WHERE supplier_id =:id and bidding_requirements.status!=1 AND bidding_requirements_invitation.status !=1 ORDER BY bidding_requirements_invitation.id DESC  LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$supplier_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_all_received_per_bidding($supplier_id,$bidding_id,$page=0,$limit=20){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;

		$SQL='SELECT bidding_requirements_invitation.*, bidding_requirements.name, particulars.bidding_id, quantity, unit, bidding_requirements.deadline FROM  bidding_requirements_invitation LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_invitation.bidding_requirements_id LEFT JOIN particulars on particulars.id = bidding_requirements.particular_id  WHERE supplier_id =:id AND  particulars.bidding_id = :bidding_id  AND (bidding_requirements.status!=1 AND bidding_requirements_invitation.status !=1) ORDER BY bidding_requirements_invitation.id DESC  LIMIT :offset,:lim';
		$SQL2 = 'SELECT * FROM profile where account_id = :account_id ORDER BY profile.id DESC LIMIT 1';

		$sth = $this->DB->prepare($SQL);
		$sth->bindValue(':bidding_id',$bidding_id,\PDO::PARAM_INT);
		$sth->bindValue(':id',$supplier_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();

		$sth2=$this->DB->prepare($SQL2);

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			// the one who send an invitation
			$row->profile_name ='';
			$sth2->bindValue(':account_id',$row->account_id,\PDO::PARAM_INT);
			$sth2->execute();

			while ($row2 = $sth2->fetch(\PDO::FETCH_OBJ)) {
				$row->profile_name = $row2->profile_name;
			}

			$results[]=$row;
		}

		return $results;
	}




	public function lists_by_status($page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;

		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.status =:status ORDER BY bidding.name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function search_all_received($supplier_id,$param,$page=0,$limit=50){

		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$params = '%'.$param.'%';

		$SQL='SELECT bidding_requirements_invitation.*, bidding_requirements.name, quantity, unit, deadline FROM  bidding_requirements_invitation LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_invitation.bidding_requirements_id  WHERE (supplier_id =:id and bidding_requirements.status!=1 AND bidding_requirements_invitation.status !=1) AND (bidding_requirements_invitation.id LIKE :param OR bidding_requirements_invitation.bidding_requirements_id LIKE :param OR bidding_requirements.name LIKE :param) ORDER BY bidding_requirements_invitation.id DESC  LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':param',$params);
		$sth->bindParam(':id',$supplier_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	/*public function view($id,$particulars=0){
		$results=[];	
		$SQL='SELECT bidding.*, profile.profile_name , profile.position FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.id=:id AND bidding.status != 4';
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
	}*/

	public function view($id){
		$results=[];	

		$SQL='SELECT bidding_requirements_invitation.*, profile.profile_name from bidding_requirements_invitation LEFT JOIN profile on profile.account_id = bidding_requirements_invitation.account_id where bidding_requirements_invitation.id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
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

}



?>