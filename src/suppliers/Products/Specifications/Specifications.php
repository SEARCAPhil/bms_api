<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers\Products;

class Specifications{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function view($id){
		$results=[];
		$SQL='SELECT * FROM specifications WHERE product_id=:id and status=0 ORDER BY position';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;	
	}

	public function add($product_id,$name,$value){
		$results=[];
		$SQL='INSERT INTO specifications(product_id,name,value) values(:product_id,:name,:value)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':product_id',$product_id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$value); 
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function update($id,$name,$val){

	
		$SQL='UPDATE specifications set name=:name,value=:value where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':value',$val);
		
		
		$sth->execute();

		return $sth->rowCount();
	}

	public function set_status($id,$status){
		$SQL='UPDATE specifications set status=:status where id=:id';
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