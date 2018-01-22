<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers\Products;

class Prices{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	
	public function view($id,$page=0,$limit=20){
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

	public function info($id){
		$results=[];
		$SQL='SELECT * FROM price WHERE id=:id ORDER BY id DESC LIMIT 1';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;	
	}


	public function add($product_id,$amount,$currency){
		$results=[];
		$SQL='INSERT INTO price(product_id,amount,currency) values(:product_id,:amount,:currency)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':product_id',$product_id,\PDO::PARAM_INT);
		$sth->bindParam(':amount',$amount,\PDO::PARAM_INT);
		$sth->bindParam(':currency',$currency,\PDO::PARAM_INT);
		$sth->execute();
		
		return $this->DB->lastInsertId();	
	}

	//update field based on name
	public function update($id,$currency='',$amount=''){

		if(!empty($currency)&&empty($amount)){
			$SQL='UPDATE price set currency=:currency where id=:id';
			$sth=$this->DB->prepare($SQL);
			$sth->bindParam(':id',$id);
			$sth->bindParam(':currency',$currency);
		}
		if(empty($currency)&&!empty($amount)){
			$SQL='UPDATE price set amount=:amount where id=:id';
			$sth=$this->DB->prepare($SQL);
			$sth->bindParam(':id',$id);
			$sth->bindParam(':amount',$amount);
		}

		if(!empty($currency)&&!empty($amount)){
			$SQL='UPDATE price set currency=:currency , amount=:amount where id=:id';
			$sth=$this->DB->prepare($SQL);
			$sth->bindParam(':id',$id);
			$sth->bindParam(':currency',$currency);
			$sth->bindParam(':amount',$amount);
		}
		
		$sth->execute();

		return $sth->rowCount();
	}


}

?>