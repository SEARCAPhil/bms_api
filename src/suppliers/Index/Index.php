<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers; 

class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function lists($page=0,$limit=20,$status=0){
		$results=['data'=>[]];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM company WHERE status=:status ORDER BY name ASC LIMIT :offset,:lim';
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

	public function view($id){
		$results=['data'=>[]];	
		$SQL='SELECT * FROM company WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results['data'][]=$row;
		}

		return $results;
	}
}

?>