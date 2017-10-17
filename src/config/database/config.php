<?php
/**
 * DATABASE CONFIGURATION FILE
 */
$config=[];

#Activate main configuration
#change value with your [DSN_CUSTOM_NAME]
$ACTIVE_CONFIG='default';



/**
 * DSN (Data Source Name) configuration
 * Please use the template below in adding new DSN
 * 
 $config[DSN_CUSTOM_NAME]=array(
 	'dms'=>'mysql|pgsql' #please refer to PDO documentation for supported RDBMS
  	'name'=>'database_name',
  	'name'=>'test',
	'host'=>'localhost',
	'port'=>'',
	'username'=>'root',
	'password'=>'',
	'charset'=>'UTF8',
	'debug'=>true	
 );
 */
$config['default']=array(
	'dbms'=>'mysql',
	'name'=>'test',
	'host'=>'localhost',
	'port'=>'',
	'username'=>'root',
	'password'=>'',
	'charset'=>'UTF8',
	'debug'=>true	
	);

$config['local']=array(
	'dbms'=>'mysql',
	'name'=>'bms',
	'host'=>'localhost',
	'port'=>'',
	'username'=>'root',
	'password'=>'',
	'charset'=>'UTF8',
	'debug'=>true	
	);

?>