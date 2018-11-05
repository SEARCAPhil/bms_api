<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../suppliers/Accounts/Accounts.php');
require_once('../../../suppliers/Accounts/Profile.php');
require_once('../../../config/database/connections.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../suppliers/Logs/Logs.php');


use Suppliers\Accounts as Accounts;
use Suppliers\Accounts\Profile as Profile;
use Helpers\CleanStr as CleanStr;
use Suppliers\Logs as Logs;

$clean_str=new CleanStr();

$LIMIT=20;
$page=1;
$result = 0;
$message = '';

/** Update profile */
function update ($DB,$id, $last_name, $first_name, $department, $department_alias, $position) {
  #instance
  $profile = new Profile($DB);
  return $profile->update($id, $last_name, $first_name, $department, $department_alias, $position);	
}

/** create account */
function create($DB, $username, $password, $password2, $id, $last_name, $first_name, $department, $department_alias, $position) {
  global $message;
  #instance
  $acc = new Accounts($DB);
  $is_exists = $acc->exists($username);
  if($is_exists) {
    $message = 'Account already exists';
    return $is_exists;
  }

  if($password !== $password2) {
    $message = "Password doesn't match";
    return 0;
  }

  return $acc->create($id, $username, sha1($password));

  //var_dump($acc->create($id, $username, $password, $password2));	
}


if(isset($_POST)){
	/**
	 * POST Account
	 * */
	
	$last_name = isset($_POST['name'])?$clean_str->clean($_POST['name']):'';
  $first_name = isset($_POST['first_name'])?$clean_str->clean($_POST['first_name']):'';
  $position = isset($_POST['position'])?$clean_str->clean($_POST['position']):'';
  $department = isset($_POST['department'])?$clean_str->clean($_POST['department']):'';
  $department_alias = isset($_POST['department_alias'])?$clean_str->clean($_POST['department_alias']):'';
  $id = isset($_POST['id'])?$clean_str->clean($_POST['id']):'';
  $action = isset($_POST['action']) ? $clean_str->clean($_POST['action']):'';
  $username= isset($_POST['username']) ? $clean_str->clean($_POST['username']):'';
  $password = isset($_POST['password']) ? $clean_str->clean($_POST['password']):'';
  $password2 = isset($_POST['confirm_password']) ? $clean_str->clean($_POST['confirm_password']):'';
  

	//prevent empty primary key
  if(empty($id)) return 0;
  
  if($action === 'update') $result = update($DB, $id, $last_name, $first_name, $department, $department_alias, $position);
  if($action === 'create') $result = create($DB, $username, $password, $password2, $id, $last_name, $first_name, $department, $department_alias, $position);	


  $data = ["data" => $result];
  // check for errors
  if(!empty($message)) $data['message'] = $message;
    echo @json_encode($data);
}

?>