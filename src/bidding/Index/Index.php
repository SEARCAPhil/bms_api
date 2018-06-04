<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Bidding; 

require_once(dirname(__FILE__).'/../Particulars/Particulars.php');
require_once(dirname(__FILE__).'/../Attachments.php');


use Bidding\Particulars;
use Bidding\Attachments;



class Index{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}


	/**
	 * CREATE Supplier
	 */
	public function create($params=[]){
		//parameters
		$results=[];
		$name=isset($params["name"])?$params["name"]:'';
		$description=isset($params["description"])?$params["description"]:'';
		$deadline=isset($params["deadline"])?$params["deadline"]:null;
		$created_by=isset($params["created_by"])?$params["created_by"]:null;
		$exemption = (int) isset($params["exemption"])?$params["exemption"]:0;

		//query
		$SQL='INSERT INTO bidding(name,description,deadline,created_by,excemption,approved_by,recommended_by,requested_by,approved_by_position,recommended_by_position,requested_by_position) values(:name,:description,:deadline,:created_by,:exemption,:approved_by,:recommended_by,:requested_by,:approved_by_position,:recommended_by_position,:requested_by_position)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':description',$description);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':created_by',$created_by);
		$sth->bindParam(':exemption',$exemption);
		$sth->bindValue(':approved_by',@$params["approved_by"]);
		$sth->bindValue('recommended_by',@$params["recommended_by"]);
		$sth->bindValue(':requested_by',@$params["requested_by"]);
		$sth->bindValue(':approved_by_position',@$params["approved_by_position"]);
		$sth->bindValue(':recommended_by_position',@$params["recommended_by_position"]);
		$sth->bindValue(':requested_by_position',@$params["requested_by_position"]);
		$sth->execute();

		return $this->DB->lastInsertId();

	}

	public function update($id, $name, $description, $deadline, $excemption){
		//parameters
		$results=[];

		//query
		$SQL='UPDATE bidding SET  name=:name, description=:description, deadline=:deadline, excemption =:excemption WHERE id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':name',$name);
		$sth->bindParam(':description',$description);
		$sth->bindParam(':deadline',$deadline);
		$sth->bindParam(':excemption',$excemption);
		$sth->bindParam(':id',$id);
		$sth->execute();

		return $sth->rowCount();

	}

	public function lists_all_received($account_id,$page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		//$SQL='SELECT bidding.*, profile.profile_name, profile.email, bidding_collaborators.*  FROM bidding LEFT JOIN profile on profile.id = bidding.created_by LEFT JOIN bidding_collaborators on bidding_collaborators.account_id = profile.account_id WHERE (bidding.status !=4 and bidding.status !=0) AND (profile.account_id = :account_id) OR (account.id =:account_id) ORDER BY bidding.name ASC LIMIT :offset,:lim';

		$SQL='SELECT bidding.*, bidding_collaborators.account_id,profile.profile_name FROM bidding_collaborators LEFT JOIN bidding on bidding.id = bidding_collaborators.bidding_id LEFT JOIN profile on profile.id = bidding.created_by  WHERE (bidding.status !=4 and bidding.status != 0) AND ((bidding.created_by = :account_id) OR ( bidding_collaborators.account_id = :account_id)) ORDER BY bidding.id DESC LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':account_id',$account_id);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_all_approved($page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		//$SQL='SELECT bidding.*, profile.profile_name, profile.email, bidding_collaborators.*  FROM bidding LEFT JOIN profile on profile.id = bidding.created_by LEFT JOIN bidding_collaborators on bidding_collaborators.account_id = profile.account_id WHERE (bidding.status !=4 and bidding.status !=0) AND (profile.account_id = :account_id) OR (account.id =:account_id) ORDER BY bidding.name ASC LIMIT :offset,:lim';

		$SQL='SELECT bidding.*, bidding_collaborators.account_id,profile.profile_name FROM bidding_collaborators LEFT JOIN bidding on bidding.id = bidding_collaborators.bidding_id LEFT JOIN profile on profile.id = bidding.created_by  WHERE bidding.status = 3 OR bidding.status = 5 ORDER BY bidding.id DESC LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function lists_all_drafts($pid, $page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE (bidding.status !=4 and bidding.status = 0) AND bidding.created_by = :pid ORDER BY bidding.id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':pid',$pid,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_by_status($page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.status =:status ORDER BY bidding.id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_by_status_admin($page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$SQL='SELECT bidding.*, profile.profile_name FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.status =:status ORDER BY bidding.name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':status',$status,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function lists_all_between_dates($from, $to,$page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;

		$SQL='SELECT bidding.*, CAST(bidding.date_created as DATE) as date_created, profile.profile_name FROM  bidding  LEFT JOIN profile on profile.id = bidding.created_by  WHERE (bidding.status !=4 and bidding.status != 0) AND CAST(bidding.date_created as DATE) BETWEEN CAST(:froms AS DATE) and CAST(:tos AS DATE) ORDER BY bidding.date_created ASC LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':froms',$from);
		$sth->bindValue(':tos',$to);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			# get particulars
			$part = new Particulars($this->DB);
			$row->particulars = $part->lists_by_parent($row->id,true);
	
			$results[]=$row;
		}

		return $results;
	}

	public function view($id,$particulars=0){
		$results=[];	
		$SQL='SELECT bidding.*, profile.profile_name , profile.position FROM bidding LEFT JOIN profile on profile.id = bidding.created_by WHERE bidding.id=:id AND bidding.status != 4';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);
		$sth->execute();

		$att = new Attachments($this->DB);

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			//get particulars
			if($particulars){
				$part = new Particulars($this->DB);
				$row->particulars = $part->lists_by_parent($row->id,true);
			}

			$row->collaborators = $this->get_collaborators($row->id);

			$row->attachments = $att->get_attachments($row->id);


			$results[]=$row;
		}

		return $results;
	}


	public function search_all_received($account_id,$param,$page=0,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		//$SQL='SELECT bidding.*, profile.profile_name, profile.email, bidding_collaborators.*  FROM bidding LEFT JOIN profile on profile.id = bidding.created_by LEFT JOIN bidding_collaborators on bidding_collaborators.account_id = profile.account_id WHERE (bidding.status !=4 and bidding.status !=0) AND (profile.account_id = :account_id) OR (account.id =:account_id) ORDER BY bidding.name ASC LIMIT :offset,:lim';

		$SQL='SELECT bidding.*, bidding_collaborators.account_id,profile.profile_name FROM bidding_collaborators LEFT JOIN bidding on bidding.id = bidding_collaborators.bidding_id LEFT JOIN profile on profile.id = bidding.created_by  WHERE bidding.id LIKE :param AND ((bidding.status !=4 and bidding.status != 0) AND (bidding.created_by = :account_id OR bidding_collaborators.account_id = :account_id))  ORDER BY bidding.id DESC LIMIT :offset,:lim';

		$params = '%'.$param.'%';
		$sth = $this->DB->prepare($SQL);

		$sth->bindParam(':param',$params);
		$sth->bindParam(':account_id',$account_id);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}


	public function search_all_approved($page=0,$param,$limit=20,$status=0){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$params = '%'.$param.'%';
		//$SQL='SELECT bidding.*, profile.profile_name, profile.email, bidding_collaborators.*  FROM bidding LEFT JOIN profile on profile.id = bidding.created_by LEFT JOIN bidding_collaborators on bidding_collaborators.account_id = profile.account_id WHERE (bidding.status !=4 and bidding.status !=0) AND (profile.account_id = :account_id) OR (account.id =:account_id) ORDER BY bidding.name ASC LIMIT :offset,:lim';

		$SQL='SELECT bidding.*, bidding_collaborators.account_id,profile.profile_name FROM bidding_collaborators LEFT JOIN bidding on bidding.id = bidding_collaborators.bidding_id LEFT JOIN profile on profile.id = bidding.created_by  WHERE (bidding.status = 3 OR bidding.status = 5) AND bidding.id LIKE :param ORDER BY bidding.id DESC LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':param',$params);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}



	public function change_signatories($id,$recommended_by, $recommended_by_position, $requested_by, $requested_by_position, $approved_by, $approved_by_position) {

		$SQL='UPDATE bidding set recommended_by =:recommended_by, recommended_by_position=:recommended_by_position, requested_by =:requested_by, requested_by_position=:requested_by_position, approved_by=:approved_by, approved_by_position =:approved_by_position where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$id,\PDO::PARAM_INT);
		$sth->bindValue(':recommended_by',$recommended_by);
		$sth->bindValue(':recommended_by_position',$recommended_by_position);
		$sth->bindValue(':requested_by',$requested_by);
		$sth->bindValue(':requested_by_position',$requested_by_position);
		$sth->bindValue(':approved_by',$approved_by);
		$sth->bindValue(':approved_by_position',$approved_by_position);
		$sth->execute();

		return $sth->rowCount();
	}


	public function view_signatories($department_name) {
		$results = [];
		$SQL='SELECT * FROM signatories where department=:department_name and status = 0';
		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':department_name',$department_name);
		$sth->execute();

		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

	public function set_status($id,$status){
		$SQL='UPDATE bidding set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove($id){
		return self::set_status($id,4);
	}

	public function send($id){
		return self::set_status($id,1);
	}

	public function closed($id){
		return self::set_status($id,5);
	}

	public function open($id){
		return self::set_status($id,2);
	}
	public function failed($id){
		return self::set_status($id,6);
	}
	public function approve($id){
		return self::set_status($id,3);
	}


	/** COLLABORATORS **/
	public function set_collaborators($id, $account_id){
		//parameters
		$results=[];
		
		//query
		$SQL='INSERT INTO bidding_collaborators(bidding_id, account_id) values(:bidding_id, :account_id)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':bidding_id',$id);
		$sth->bindParam(':account_id',$account_id);

		$sth->execute();

		return $this->DB->lastInsertId();

	}


	public function get_collaborators($id){
		$results=[];
		$SQL='SELECT bidding_collaborators.*, profile.profile_name FROM bidding_collaborators LEFT JOIN profile on profile.account_id = bidding_collaborators.account_id WHERE bidding_id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id,\PDO::PARAM_INT);

		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}

		return $results;
	}

}



?>