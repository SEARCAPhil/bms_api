<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers\Products;

class Categories{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function create($id,$name,$description){
	
		$SQL='INSERT INTO product_category(company_id,name,description) values (:id,:name,:description)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':name',$name,\PDO::PARAM_STR);
		$sth->bindParam(':description',$description,\PDO::PARAM_STR);

		if($sth->execute())	return $this->DB->lastInsertId();
		return 0;

	}


	public function get_parent_categories($company_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product_category WHERE parent_id IS NULL and status=0 and company_id=:id ORDER BY name ASC LIMIT :offset,:lim';
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

	public function get_children_categories($parent_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product_category WHERE parent_id=:id and status=0 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$parent_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


		public function set_status($id,$status){
		$SQL='UPDATE product_category set status=:status where id=:id';
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