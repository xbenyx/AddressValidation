<?php
include_once("conf.php");

$db = PARAMS['database'];
$db_user = PARAMS['database_user'];
$db_pwd = PARAMS['database_pwd'];

try
{
	
$conn = new PDO("sqlsrv:Server=127.0.0.1,1433;Database=$db",$db_user,$db_pwd);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e)
{
die(print_r($e->getMessage()));
}

