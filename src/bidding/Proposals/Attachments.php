<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding\Proposals; 

class Attachments{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function create($id, $bidding_requirements_proposals_id, $filename, $original_filename, $size, $type){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO bidding_requirements_proposals_attachments(account_id, bidding_requirements_proposals_id, filename, original_filename, size, type) values(:account_id, :bidding_requirements_proposals_id, :filename, :original_filename, :size, :type)';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':account_id',$id);
		$sth->bindParam(':bidding_requirements_proposals_id',$bidding_requirements_proposals_id);
		$sth->bindParam(':filename',$filename);
		$sth->bindParam(':original_filename',$original_filename);
		$sth->bindParam(':size',$size);
		$sth->bindParam(':type',$type);

		$sth->execute();

		return $this->DB->lastInsertId();

	}

	

	public function set_status($id,$status){
		$SQL='UPDATE bidding_requirements_proposals_attachments set status=:status where id=:id';
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