<?php
namespace Auth;
class Account{
	public function __construct($DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function create($company_id, $username, $password, $uid){
		$SQL = 'INSERT INTO account(company_id, username, password, uid) VALUES (:company_id, :username, :password, :uid)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':username',$username,\PDO::PARAM_STR);
		$sth->bindParam(':password',$password,\PDO::PARAM_STR);
		$sth->bindParam(':uid',$uid,\PDO::PARAM_STR);
		$sth->bindParam(':company_id',$company_id,\PDO::PARAM_INT);
		$sth->execute();
		
		return $this->DB->lastInsertId();
	}

	public function create_profile($id, $profile_name, $last_name, $first_name, $middle_name, $email, $department, $department_alias, $position){
		$SQL = 'INSERT INTO profile(account_id, profile_name, last_name, first_name, middle_name, email, department, department_alias, position) VALUES (:account_id, :profile_name, :last_name, :first_name, :middle_name, :email, :department, :department_alias, :position)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':account_id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':profile_name',$profile_name);
		$sth->bindParam(':last_name',$last_name);
		$sth->bindParam(':first_name',$first_name);
		$sth->bindParam(':middle_name',$middle_name);
		$sth->bindParam(':email',$email);
		$sth->bindParam(':department',$department);
		$sth->bindParam(':department_alias',$department_alias);
		$sth->bindParam(':position',$position);

		$sth->execute();
		
		return $this->DB->lastInsertId();
	}

	public function login($username, $password){
		$SQL = 'SELECT account.username,account.id as uid,account_profile.* FROM account LEFT JOIN account_profile on account_profile.uid = account.id WHERE username = :username and password = :password LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':username',$username,\PDO::PARAM_STR);
		$sth->bindParam(':password',$password,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$result = $row;
		}
		return $result;
	}
	public function loginO365($uid){
		$SQL = 'SELECT account.username,account.id as uid,profile.*, account_role.role FROM account LEFT JOIN profile on profile.account_id = account.id LEFT JOIN account_role on account_role.account_id = account.id WHERE account.uid = :uid LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':uid',$uid,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$result = $row;
		}
		return $result;
	}
	public function view($id){
		$results=[];

		$SQL='SELECT * FROM account_profile WHERE id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}

	public function get_cba_assts($role, $page=0, $limit=20){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT account.username,account.id as id,account.status, account_role.role, profile.profile_name FROM account_role LEFT JOIN account ON account.id = account_role.account_id LEFT JOIN profile on profile.account_id=account.id WHERE account.status!=1 and account_role.role = :role  ORDER BY profile.first_name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		
		$sth->bindParam(':role',$role);
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