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


class Report{

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}


	public function lists_all_between_dates($from, $to){
		$results=[];

		$SQL='SELECT bidding.*, CAST(bidding.date_created as DATE) as date_created, profile.profile_name, profile.department, profile.department_alias FROM  bidding  LEFT JOIN profile on profile.id = bidding.created_by  WHERE (bidding.status !=4 and bidding.status != 0) AND CAST(bidding.date_created as DATE) BETWEEN CAST(:froms AS DATE) and CAST(:tos AS DATE) ORDER BY bidding.date_created ASC';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':froms',$from);
		$sth->bindValue(':tos',$to);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			# get particulars
			$part = new Particulars($this->DB);
			$row->particulars = $part->lists_by_parent($row->id,true);
	
			$results[]=$row;
		}

		return $results;
	}

}



?>