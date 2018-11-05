<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers;
require_once('../../config/server.php'); 


class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->dir = UPLOAD_DIR.'logo/';
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
			$row->logo = "{$this->dir}{$row->logo}";
			$results['data'][]=$row;
		}

		return $results;
	}

	public function view($id){
		$results=[];	
		$SQL='SELECT * FROM company WHERE id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->logo = "{$this->dir}{$row->logo}";
			$row->about = nl2br(mb_convert_encoding($row->about, "UTF-8"));
			$row->contact_info = $this->contact_info($row->id);
			$results[]=$row;
		}

		return $results;
	}


	public function contact_info($id){
		$results=[];	
		$SQL='SELECT * FROM company_contact_info WHERE company_id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function stat(){
		$results=[];

		$SQL = 'SELECT status, count(*) as total FROM `company` WHERE status != 1 GROUP BY status';
		$sth = $this->DB->prepare($SQL);
		$sth->execute();

		while($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$results[] = $row;
		}

		return $results;
	}


	public function search($param,$page = 1,$limit=50){
		$results=[];
		$page=$page>1?$page:1;

		#set starting limit(page 1=10,page 2=20)
		$start_page=$page<2?0:(integer)($page-1)*$limit;
		$par = '%'.$param.'%';

		$SQL='SELECT * FROM company WHERE (name LIKE :param or alias LIKE :param) and status!=1 ORDER BY name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);

		$sth->bindValue(':param',$par);
		$sth->bindValue(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindValue(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->logo = "{$this->dir}{$row->logo}";
			$row->about = nl2br(mb_convert_encoding($row->about, "UTF-8"));
			$row->contact_info = $this->contact_info($row->id);
			$results[] = $row;
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
		$website=isset($params["website"])?$params["website"]:'';
		//query
		$SQL='INSERT INTO company(name,tagline,about,established_month,established_date,established_year,location,industry,website) values(:name,:tagline,:about,:established_month,:established_date,:established_year,:location,:industry,:website)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':tagline',$tagline);
		$sth->bindParam(':about',$about);
		$sth->bindParam(':established_month',$established_month);
		$sth->bindParam(':established_date',$established_date);
		$sth->bindParam(':established_year',$established_year);
		$sth->bindParam(':location',$location);
		$sth->bindParam(':industry',$industry);
		$sth->bindParam(':website',$website);
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
		$website=isset($params["website"])?$params["website"]:'';
		$id=isset($params["id"])?$params["id"]:'';
		//query
		$SQL='UPDATE company set name=:name,tagline=:tagline,about=:about,established_month=:established_month,established_date=:established_date,established_year=:established_year,location=:location,industry=:industry,alias=:alias, website=:website where id=:id';
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
		$sth->bindParam(':website',$website);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();

	}

		/**
	 * CREATE Supplier
	 */
	public function add_contact($id, $type, $value){
	
		$SQL='INSERT INTO company_contact_info(company_id, type, value) values (:id, :type, :value)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':type',$type);
		$sth->bindParam(':value',$value);
		$sth->execute();

		return $this->DB->lastInsertId();
	}


	/**
	 * removeContacst
	 */
	public function remove_contact($id){
	
		$SQL='DELETE FROM company_contact_info WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();
	}

	/**
	 * update Contact
	 */
	public function update_contact($id, $type, $value){
	
		$SQL='UPDATE company_contact_info SET type =:type, value = :value WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':type',$type);
		$sth->bindParam(':value',$value);
		$sth->execute();

		return $sth->rowCount();
	}

		/**
	 * UPDATE Supplier
	 */
	public function update_logo($id, $logo){
		$SQL='UPDATE company set logo = :logo where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':logo',$logo);
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