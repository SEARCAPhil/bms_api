<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Feedback; 


class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function create($id, $feedback){
		//parameters
		$results=[];
		//query
		$SQL='INSERT INTO feedback(account_id, feedback) values(:account_id, :feedback)';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':account_id',$id);
		$sth->bindParam(':feedback',$feedback);

		$sth->execute();

		return $this->DB->lastInsertId();

	}



}



?>