<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers\Accounts; 

class Profile{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}


	public function update($id, $last_name, $first_name, $department, $department_alias, $position){
		$profile_name = "{$first_name} {$last_name}";
		$SQL='UPDATE profile set last_name = :last_name, first_name = :first_name, department = :department, department_alias = :department_alias, position = :position, profile_name =:profile_name where id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':last_name', $last_name,\PDO::PARAM_STR);
    $sth->bindParam(':first_name', $first_name,\PDO::PARAM_STR);
    $sth->bindParam(':profile_name', $profile_name,\PDO::PARAM_STR);
    $sth->bindParam(':department', $department,\PDO::PARAM_STR);
    $sth->bindParam(':department_alias', $department_alias,\PDO::PARAM_STR);
    $sth->bindParam(':position', $position, \PDO::PARAM_STR);

		if($sth->execute())	return $sth->rowCount();
		return 0;

	}
}

?>