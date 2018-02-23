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

	public function lists($id, $page=1){
		$results=[];

		$SQL='SELECT * FROM bidding_attachments';

		$sth=$this->DB->prepare($SQL);
		
		
		$sth->execute();
		
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}
}

?>