<?php
	
	$mysql_server = "localhost";
	$mysql_user = $_SERVER['DBUSER'];
	$mysql_pass = $_SERVER['DBPASS'];
	$mysql_db = $_SERVER['DBNAME'];

	function startSqlconn() // starts an sql connection based on credentials Return $sqlconn
	{
		global $mysql_server;
		global $mysql_user;
		global $mysql_pass;
		global $mysql_db;


		$sqlconn = new mysqli($mysql_server,$mysql_user,$mysql_pass,$mysql_db);

		if($sqlconn->connect_errno)
		{
			echo "Failed to connect" . $sqlconn->connect_error;
			exit(); 
		}

		return $sqlconn;

	}


	// SQL Query Function - starts and connection and executes a sql command. Return Results
	function runQuery($sql)
	{

		$sqlconn = startSqlconn();
		$result = $sqlconn->query($sql);

		$sqlconn->close();

		return $result;

	}

	

?>