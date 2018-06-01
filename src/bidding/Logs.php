<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding;

class Logs{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function log($id,$action,$event,$reference_id = null, $data = null){

		$SQL='INSERT INTO bidding_logs(account_id, action, event, reference_id, data) values (:id, :action, :event, :reference_id, :data)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':event',$event,\PDO::PARAM_STR);
        $sth->bindParam(':action',$action,\PDO::PARAM_STR);
        $sth->bindParam(':reference_id',$reference_id,\PDO::PARAM_INT);
        $sth->bindParam(':data',$data,\PDO::PARAM_STR);

		if($sth->execute())	return $this->DB->lastInsertId();
		return 0;

	}

}

?>