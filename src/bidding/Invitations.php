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
		$page=$page<2?0:$page-1;

		$SQL='SELECT bidding_requirements_invitation.*, bidding_requirements.name, quantity, unit, deadline FROM  bidding_requirements_invitation LEFT JOIN bidding_requirements on bidding_requirements.id = bidding_requirements_invitation.bidding_requirements_id  WHERE supplier_id =:id and bidding_requirements.status!=1 AND bidding_requirements_invitation.status !=1 ORDER BY bidding_requirements_invitation.id DESC  LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$supplier_id,\PDO::PARAM_INT);
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

	public function view($id,$particulars=0){
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