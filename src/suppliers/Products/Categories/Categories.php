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

	public function get_parent_categories($company_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product_category WHERE parent_id IS NULL and is_deleted=0 and company_id=:id ORDER BY name ASC LIMIT :offset,:lim';
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
		$SQL='SELECT * FROM product_category WHERE parent_id=:id and is_deleted=0 ORDER BY name ASC LIMIT :offset,:lim';
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
}

?>