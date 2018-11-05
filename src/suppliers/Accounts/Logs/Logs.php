<?php 
/**
 * @namespace Index
 * @description Handles company information
 * */
namespace Suppliers\Accounts;

require_once('../../../../bidding/Index/Index.php');

use Bidding\Index as Bidding;

class Logs{

	const BIDDING_STAT_CREATED = 0;
	const BIDDING_STAT_SENT = 1;
	const BIDDING_STAT_OPEN = 2;
	const BIDDING_STAT_APPROVED = 3;
	const BIDDING_STAT_REMOVED = 4;
	const BIDDING_STAT_CLOSED = 5;
	const BIDDING_STAT_FAILED = 6;

	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING);
		$this->bid = new Bidding($this->DB); 
	}

	function get_bidding_details ($id) {
		return @$this->bid->view($id)[0];
	}

	function map_bidding_status ($stat, $row) {
		switch ($stat) {
			case self::BIDDING_STAT_SENT:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'sent');
				break;
			
			case self::BIDDING_STAT_OPEN:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'open');
				break;
			
			case self::BIDDING_STAT_APPROVED:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'approved');
				break;
			case self::BIDDING_STAT_REMOVED:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'removed');
				break;

			case self::BIDDING_STAT_CLOSED:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'closed');
				break;
			
			case self::BIDDING_STAT_FAILED:
				return self::bidding_status_message($row->reference_id, $row->date_created, 'failed');
				break;

		}
	}

	function bidding_created_message ($id, $date) {
		return "Created bidding request <b>#{$id}</b> - {$date} ";
	}

	function bidding_send_message ($id, $date) {
		return "Send bidding request <b>#{$id}</b> - {$date} ";
	}

	function bidding_deleted_message ($id, $date) {
		return "Deleted bidding request <b>#{$id}</b> - {$date} ";
	}

	function bidding_message ($id, $date, $action ='Created') {
		return "{$action} bidding request <b>#{$id}</b> - {$date} ";
	}

	function bidding_status_message ($id, $date, $status = '') {
		return "Changed bidding request <b>#{$id}</b> 's status to <b>{$status}</b> <br/><small class='text-muted'>{$date}</small> ";
	}

	function bidding_attachment_message ($name = '', $date, $filename='', $action = 'Uploaded') {
		return "{$action} <b class='text-success'>{$filename}</b> on bidding request <b>#{$name}</b> <br/> <small class='text-muted'>{$date}</small> ";
	}


	function requirement_message ($id, $date, $name, $action ='Created') {
		return "{$action} <b class='text-info'>{$name}</b> on bidding request <b>#{$id}</b> - {$date} ";
	}

	function requirement_attachment_message ($id, $date, $name, $action ='Uploaded') {
		return "{$action} <b class='text-info'>{$name}</b> on item <b>#{$id}</b> - {$date} ";
	}


	function particular_message ($id, $date, $name, $action ='Uploaded') {
		return "{$action} <b class='text-info'>{$name}</b> on bidding request <b>#{$id}</b> <br/> <small class='text-muted'>{$date}</small> ";
	}


	function specs_message ($id, $date, $name, $action ='Uploaded') {
		return "{$action} specs <b class='text-warning'>{$name}</b>  in particular <b>#{$id}</b> <br/> <small class='text-muted'>{$date}</small> ";
	}


	function funding_message ($id, $date, $name, $action ='Uploaded') {
		return "{$action} funding <b class='text-warning'>{$name}</b> in particular <b>#{$id}</b> <br/> <small class='text-muted'>{$date}</small> ";
	}


	function bidding_proposal_created_message ($id, $date, $action = 'Created') {
		return "{$action} bidding proposal <b>#{$id}</b> - {$date} ";
	}

	function facade_bidding_message ($row) { 
		$__message = '';
		if($row->action === 'status')  $__message = self::map_bidding_status (json_decode($row->data)->status, $row);
		if($row->action === 'deleted')  $__message = self::bidding_message($row->reference_id, $row->date_created , 'Removed');
		if($row->action === 'send')  $__message = self::bidding_message($row->reference_id, $row->date_created , 'Send');
		if($row->action === 'update')  $__message = self::bidding_message($row->reference_id, $row->date_created , 'Update');
		return $__message;
	}

	function facade_bidding_attachment_message ($row) { 
		$__message = '';
		if($row->action === 'attach')  $__message = self::bidding_attachment_message($row->reference_id, $row->date_created, @json_decode($row->data)->original_filename);
		if($row->action === 'delete')  $__message = self::bidding_attachment_message($row->reference_id, $row->date_created, @json_decode($row->data)->original_filename, 'Removed');
		return $__message;
	}


	function facade_requirement_message ($row) { 
		$__message = '';
		if($row->action === 'add' || $row->action === 'delete')  $__message = $__message = self::requirement_message($row->reference_id, $row->date_created, @json_decode($row->data)->name, $row->action === 'delete' ? 'Removed' : 'Added');
		return $__message;
	}

	function facade_requirement_attachment_message ($row) { 
		$__message = '';
		if($row->action === 'attach' || $row->action === 'delete')  $__message = $__message = self::requirement_attachment_message($row->reference_id, $row->date_created, @json_decode($row->data)->original_filename, $row->action === 'delete' ? 'Removed' : 'Uploaded');
		return $__message;
	}



	function facade_particular_message ($row) { 
		$__message = '';
		if($row->action === 'add' || $row->action === 'delete')  $__message = $__message = self::particular_message(@json_decode($row->data)->id, $row->date_created, @json_decode($row->data)->name, $row->action === 'delete' ? 'Removed' : 'Added');
		return $__message;
	}

	function facade_funding_message ($row) { 
		$__message = '';
		$__fund = @json_decode($row->data);
		$__src = "{$__fund->fund_type} - {$__fund->cost_center} - {$__fund->line_item}";
		if($row->action === 'add' || $row->action === 'delete')  $__message = $__message = self::funding_message(@json_decode($row->data)->id, $row->date_created, $__src, $row->action === 'delete' ? 'Removed' : 'Added');
		return $__message;
	}


	function facade_specs_message ($row) { 
		$__message = '';
		if($row->action === 'add' || $row->action === 'delete')  $__message = $__message = self::specs_message(@json_decode($row->data)->id, $row->date_created, @json_decode($row->data)->name.'('.@json_decode($row->data)->value.')', $row->action === 'delete' ? 'Removed' : 'Added');
		return $__message;
	}


	function facade_bidding_proposal_message ($row) { 
		$__message = '';
		if($row->action === 'create' || $row->action === 'delete')  $__message = self::bidding_proposal_created_message($row->reference_id, $row->date_created, $row->action === 'delete' ? 'Removed' : 'Created');
		if($row->action === 'send' || $row->action === 'return')  $__message = self::bidding_proposal_created_message($row->reference_id, $row->date_created, $row->action === 'send' ? 'Send' : 'Returned');
		return $__message;
	}

	public function get_logs($acc_id,$page=0,$limit=20){
		$results=[];
		$page = $page > 1 ? $page : 1;
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$SQL='SELECT * FROM bidding_logs WHERE account_id=:id ORDER BY id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$acc_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page ,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->message = '';
			if($row->event == 'bidding_request')  $row->message = $this->facade_bidding_message($row);
			if($row->event == 'bidding_attachment')  $row->message = $this->facade_bidding_attachment_message($row);
			if($row->event == 'bidding_proposal')  $row->message = $this->facade_bidding_proposal_message($row);
			if($row->event == 'requirement')  $row->message = $this->facade_requirement_message($row);
			if($row->event == 'requirement_attachment')  $row->message = $this->facade_requirement_attachment_message($row);
			if($row->event == 'particular')  $row->message = $this->facade_particular_message($row);		
			if($row->event == 'spec')  $row->message = $this->facade_specs_message($row);	
			if($row->event == 'fund')  $row->message = $this->facade_funding_message($row);	
			$results[]=$row;
		}

		return $results;
	}

	public function get_logs_event($acc_id,$event,$page=0,$limit=20){
		$results=[];
		$page=$page<2?0:$page-1;
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;
		$SQL='SELECT * FROM bidding_logs WHERE account_id=:id and event=:event ORDER BY id DESC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$acc_id,\PDO::PARAM_INT);
		$sth->bindParam(':event',$event,\PDO::PARAM_STR);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {

			$results[]=$row;
		}

		return $results;
	}

	public function get_account_logs($id,$page=0,$limit=20){
		$results=[];
		$page = $page > 1 ? $page : 1;
		#set starting limit(page 1=10,page 2=20)
		$start_page = $page < 2 ? 0 :(integer)($page-1) * $limit;

		$SQL='SELECT * FROM bidding_logs WHERE account_id =:id  ORDER BY bidding_logs.id DESC  LIMIT :offset,:lim';

		$sth=$this->DB->prepare($SQL);
		$sth->bindValue(':id',$supplier_id,\PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$start_page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			if($row->event == 'bidding_request')  $row->message = $this->facade_bidding_message($row);
				
			
			$results[]=$row;
		}

		return $results;
	}

}

?>