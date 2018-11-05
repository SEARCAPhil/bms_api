<?php
namespace Auth;
class Session{
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}
	public function generate_token($text,$salt){
		return sha1($text.''.md5($salt));
	}
	public function set($token,$account_id,$user_agent){
		$SQL = 'INSERT INTO account_session(token,account_id,user_agent) values(:token,:account_id,:user_agent)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':token',$token,\PDO::PARAM_STR);
		$sth->bindParam(':account_id',$account_id,\PDO::PARAM_STR);
		$sth->bindParam(':user_agent',$user_agent,\PDO::PARAM_STR);
		$sth->execute();
		return $this->DB->lastInsertId();
	}

	public function setO365($token,$uid,$user_agent,$account_id){
		$SQL = 'INSERT INTO session(token,uuid,user_agent,account_id) values(:token,:uuid,:user_agent,account_id)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':token',$token,\PDO::PARAM_STR);
		$sth->bindParam(':uuid',$uid,\PDO::PARAM_STR);
		$sth->bindParam(':uccount_id',$uid,\PDO::PARAM_STR);
		$sth->bindParam(':user_agent',$user_agent,\PDO::PARAM_STR);
		$sth->execute();
		return $this->DB->lastInsertId();
	}

	public function get($token){
		$SQL = 'SELECT account_session.* , profile.id as pid, account_role.role, profile.email, profile.department, account.company_id FROM account_session LEFT JOIN profile on account_session.account_id = profile.account_id LEFT JOIN account_role on account_role.account_id = account_session.account_id LEFT JOIN account on account.id = account_session.account_id  WHERE account_session.token =:token ORDER BY profile.id DESC LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':token',$token,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while ($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_all_sessions($id, $page = 0 , $limit = 20){
		$results = [];	
		$start_page = $page<2?0:(integer)($page-1)*$limit;
		$SQL='SELECT * FROM account_session WHERE account_id = :id ORDER BY date_created DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_STR);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

}
?>