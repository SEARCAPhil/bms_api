<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers; 

class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function lists($page=1,$limit=20,$status=0){
		$results=['data'=>[]];
		$page=$page>1?$page:1;

		#set starting limit(page 1=10,page 2=20)
		$start_page=$page<2?0:(integer)($page-1)*$limit;

		$SQL='SELECT * FROM company WHERE status=:status ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results['data'][]=$row;
		}

		return $results;
	}

	public function view($id){
		$results=['data'=>[]];	
		$SQL='SELECT * FROM company WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results['data'][]=$row;
		}

		return $results;
	}

	public function search($param,$page = 1,$limit=2){
		$results=[];
		$page=$page>1?$page:1;

		#set starting limit(page 1=10,page 2=20)
		$start_page=$page<2?0:(integer)($page-1)*$limit;
		$par = '%'.$param.'%';

		$SQL='SELECT * FROM company WHERE (name LIKE :param or alias LIKE :param) and status!=4 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);

		$sth->bindValue(':param',$par);
		$sth->bindValue(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindValue(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	/**
	 * CREATE Supplier
	 */
	public function create($params=[]){
		//parameters
		$results=[];
		$name=isset($params["name"])?$params["name"]:'';
		$tagline=isset($params["tagline"])?$params["tagline"]:'';
		$about=isset($params["about"])?$params["about"]:'';
		$established_month=isset($params["established_month"])?$params["established_month"]:'00';
		$established_date=isset($params["established_date"])?$params["established_date"]:'00';	
		$established_year=isset($params["established_year"])?$params["established_year"]:'0000';
		$location=isset($params["location"])?$params["location"]:'';	
		$industry=isset($params["industry"])?$params["industry"]:'';
		//query
		$SQL='INSERT INTO company(name,tagline,about,established_month,established_date,established_year,location,industry) values(:name,:tagline,:about,:established_month,:established_date,:established_year,:location,:industry)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':tagline',$tagline);
		$sth->bindParam(':about',$about);
		$sth->bindParam(':established_month',$established_month);
		$sth->bindParam(':established_date',$established_date);
		$sth->bindParam(':established_year',$established_year);
		$sth->bindParam(':location',$location);
		$sth->bindParam(':industry',$industry);
		$sth->execute();

		return $this->DB->lastInsertId();

	}


	/**
	 * UPDATE Supplier
	 */
	public function update($params=[]){
		//parameters
		$results=[];
		$name=isset($params["name"])?$params["name"]:'';
		$tagline=isset($params["tagline"])?$params["tagline"]:'';
		$alias=isset($params["alias"])?$params["alias"]:'';
		$about=isset($params["about"])?$params["about"]:'';
		$established_month=isset($params["established_month"])?$params["established_month"]:'00';
		$established_date=isset($params["established_date"])?$params["established_date"]:'00';	
		$established_year=isset($params["established_year"])?$params["established_year"]:'0000';
		$location=isset($params["location"])?$params["location"]:'';	
		$industry=isset($params["industry"])?$params["industry"]:'';
		$id=isset($params["id"])?$params["id"]:'';
		//query
		$SQL='UPDATE company set name=:name,tagline=:tagline,about=:about,established_month=:established_month,established_date=:established_date,established_year=:established_year,location=:location,industry=:industry,alias=:alias where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':tagline',$tagline);
		$sth->bindParam(':about',$about);
		$sth->bindParam(':established_month',$established_month);
		$sth->bindParam(':established_date',$established_date);
		$sth->bindParam(':established_year',$established_year);
		$sth->bindParam(':location',$location);
		$sth->bindParam(':industry',$industry);
		$sth->bindParam(':alias',$alias);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();

	}

	public function set_status($id,$status){
		$SQL='UPDATE company set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove($id){
		return self::set_status($id,1);
	}

	public function block($id){
		return self::set_status($id,2);
	}

	public function unblock($id){
		return self::set_status($id,0);
	}
}

?>