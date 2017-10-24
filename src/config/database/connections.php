<?php 
/**
 * load database configuration
 */
require_once('config.php');

#override config
$ACTIVE_CONFIG='local';

#Load default database instance
try{
	$DB=new PDO(
			//dbms
			$config[$ACTIVE_CONFIG]['dbms'].
			//host
			':host='.$config[$ACTIVE_CONFIG]['host'].';'.
			//port
			(!empty($config[$ACTIVE_CONFIG]['port'])?'port='.$config[$ACTIVE_CONFIG]['port'].';':'').
			//dbname
			'dbname='.$config[$ACTIVE_CONFIG]['name'].';'.
			//charset
			'charset='.$config[$ACTIVE_CONFIG]['charset'],
			//username
			$config[$ACTIVE_CONFIG]['username'],
			//password
			!empty($config[$ACTIVE_CONFIG]['password'])?','.$config[$ACTIVE_CONFIG]['password']:''
		);
}catch(PDOException $e){
	print 'ERROR :'.$e->getMessage();
	die();
}

?>
