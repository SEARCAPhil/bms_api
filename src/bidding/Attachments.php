<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

class Attachments{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function create($id, $bidding_id, $filename, $original_filename, $size, $type, $copy = 'original', $original_copy_id = NULL){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO bidding_attachments(account_id, bidding_id, filename, original_filename, size, type, copy, original_copy_id) values(:account_id, :bidding_id, :filename, :original_filename, :size, :type, :copy, :original_copy_id)';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':account_id',$id);
		$sth->bindParam(':bidding_id',$bidding_id);
		$sth->bindParam(':filename',$filename);
		$sth->bindParam(':original_filename',$original_filename);
		$sth->bindParam(':size',$size);
		$sth->bindParam(':type',$type);
		$sth->bindParam(':copy',$copy);
		$sth->bindParam(':original_copy_id',$original_copy_id);

		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function lists($id, $page=1){
		$results=[];

		$SQL='SELECT * FROM bidding_attachments where account_id = :id';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id);
		
		
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function get_attachments($id){
		$results=[];

		$SQL='SELECT * FROM bidding_attachments where bidding_id = :id and status != 1';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id);
		
		
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_original_copy_only($id, $page=1){
		$results=[];

		$SQL='SELECT * FROM bidding_attachments where account_id = :id and copy = "original" ';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id);
		
		
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function view($id){
		$results=[];

		$SQL='SELECT * FROM bidding_attachments where id = :id';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id);
		
		
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function set_status($id,$status){
		$SQL='UPDATE bidding_attachments set status=:status where id=:id';
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