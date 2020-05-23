<?php
//他のページからrequireする用です
function db_connect(){
	// $dsn = 'mysql:host=◯◯◯;dbname=◯◯◯;charset=utf8';
	// $user = '◯◯◯';
	// $password = '◯◯◯';

	try{
		$dbh = new PDO($dsn, $user, $password);
		return $dbh;
	}catch (PDOException $e){
	    	print('Error:'.$e->getMessage());
	    	die();
	}
}

?>
