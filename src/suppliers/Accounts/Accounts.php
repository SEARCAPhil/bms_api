<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers; 

class Accounts{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function lists($company_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT account.username,profile.* FROM account LEFT JOIN profile on profile.uid=account.id WHERE is_deleted=0 and company_id=:id ORDER BY profile.first_name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$company_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function view($id){
		$results=[];	
		$SQL='SELECT account.username,profile.*,company.name as company FROM account LEFT JOIN profile on profile.uid=account.id LEFT JOIN company on company.id=account.company_id WHERE account.id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function get_privilege($id){
		$results=[];	
		$SQL='SELECT * FROM privilege WHERE account_id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function search($param,$page=0,$limit=20){
		$results=[];	
		$page=$page<2?0:$page-1;
		$SQL='SELECT account.username,profile.* FROM account LEFT JOIN profile on profile.uid=account.id WHERE username LIKE :param OR profile_name LIKE :param OR department LIKE :param ORDER BY profile_name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$name='%'.$param.'%';
		$sth->bindParam(':param',$name,\PDO::PARAM_STR);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function search_per_company($cid,$param,$page=0,$limit=20){
		$results=[];	
		$page=$page<2?0:$page-1;
		$SQL='SELECT account.username,profile.* FROM account LEFT JOIN profile on profile.uid=account.id WHERE (username LIKE :param OR profile_name LIKE :param OR department LIKE :param) and account.company_id=:cid ORDER BY profile_name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$name='%'.$param.'%';
		$sth->bindParam(':param',$name,\PDO::PARAM_STR);
		$sth->bindParam(':cid',$cid,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}
}

?>