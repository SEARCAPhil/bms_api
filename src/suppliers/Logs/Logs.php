<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers;

class Logs{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function get_logs($acc_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT logs.*,account.username,account.company_id FROM logs LEFT JOIN account on logs.account_id=account.id  WHERE account.company_id=:id ORDER BY id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$acc_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function get_logs_event($acc_id,$event,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT logs.*,account.username,account.company_id FROM logs LEFT JOIN account on logs.account_id=account.id  WHERE account.company=:id and event=:event ORDER BY id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$acc_id,\PDO::PARAM_INT);
		$sth->bindParam(':event',$event,\PDO::PARAM_STR);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function log($id,$message,$event){

		$SQL='INSERT INTO logs(account_id,message,event) values (:id,:message,:event)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':event',$event,\PDO::PARAM_STR);
		$sth->bindParam(':message',$message,\PDO::PARAM_STR);

		if($sth->execute())	return $this->DB->lastInsertId();
		return 0;

	}

}

?>