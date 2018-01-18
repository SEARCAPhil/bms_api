<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers;

require_once('Prices/Prices.php');
require_once('Specifications/Specifications.php');

use Suppliers\Products\Prices as Prices;
use Suppliers\Products\Specifications as Specs;

class Products{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
		$this->prices=new Prices($this->DB);
		$this->specs=new Specs($this->DB);
	}

	public function create($cid,$name,$specs=[]){
		$results=[];
		$SQL='INSERT INTO product(name,company_id) values(:name,:company_id)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':company_id',$cid); 
		$sth->execute();
		$lastId=$this->DB->lastInsertId();

		if($lastId&&count($specs)>0){
			foreach ($specs as $key => $value) {
				$this->specs->add($lastId,$key,$value);
			}
		}

		return $lastId;

	}

	public function get_products_per_category($category_id,$page=0,$limit=30){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product WHERE product_category_id=:id and status!=1 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$category_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->prices=$this->prices->view($row->id,0,10);
			$row->specs=$this->specs->view($row->id);
			$results[]=$row;
		}

		return $results;	
	}

	public function get_products_per_company($cid,$page=0,$limit=30){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product WHERE company_id=:id and status!=1 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$cid,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->prices=$this->prices->view($row->id,0,10);
			$row->specs=$this->specs->view($row->id);
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
			$row->prices=$this->prices->view($row->id,0,10);
			$row->specs=$this->specs->view($row->id);
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

	public function set_status($id,$status){
		$SQL='UPDATE product set status=:status where id=:id';
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