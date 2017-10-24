<?php 
/**
 * @package Supplier
 * @description Handles company products
 * */
namespace Suppliers\Products;

class Templates{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}

	public function get_templates($page=0,$limit=20){
		$results=[];	
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM product_template ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;		
	}

	public function get_template_specs($id){
		$results=[];	
		$SQL='SELECT * FROM product_template_specifications WHERE product_template_id=:id ORDER BY id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;		
	}

	/**
	 * CREATE Template
	 */
	public function create($name,$account_id=NULL){
		$results=[];
		$SQL='INSERT INTO product_template(name,account_id) values(:name,:account_id)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':account_id',$account_id);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function add_specs($product_template_id,$name){
		$results=[];
		$SQL='INSERT INTO product_template_specifications(product_template_id,name) values(:product_template_id,:name)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':product_template_id',$product_template_id);
		$sth->bindParam(':name',$name);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

}

?>