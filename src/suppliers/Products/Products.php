<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers;

class Products{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function get_products($category_id,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product WHERE product_category_id=:id and is_deleted=0 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$category_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->prices=$this->get_product_prices($row->id,0,10);
			$row->specs=$this->get_products_specifications($row->id);
			$results[]=$row;
		}

		return $results;	
	}

	public function get_products_specifications($id){
		$results=[];
		$SQL='SELECT * FROM specifications WHERE product_id=:id and is_deleted=0 ORDER BY position';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;	
	}

	public function get_product_prices($id,$page=0,$limit=20){
		$results=[];
		$SQL='SELECT * FROM price WHERE product_id=:id ORDER BY id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
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
		$SQL='SELECT * FROM product WHERE product.id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->prices=$this->get_product_prices($row->id,0,10);
			$row->specs=$this->get_products_specifications($row->id);
			$results[]=$row;
		}

		return $results;
	}

	public function search($param,$page=0,$limit=20){
		$results=[];	
		$page=$page<2?0:$page-1;
		$SQL='SELECT product.*, price.amount, price.currency FROM product LEFT JOIN price on price.product_id=product.id WHERE name LIKE :name ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$name='%'.$param.'%';
		$sth->bindParam(':name',$name,\PDO::PARAM_STR);
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