<?php
	
	require_once('../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	$error_response = [
		"response" => "error"
	];
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'user_module');


	$action = $_POST["action"];

	switch ($action) {
		case "searchProcess" 	:  searchProcess($_POST["searchkey"]); break;
		
		default: echo json_encode($error_response);
	}

	function searchProcess($searchkey)
	{

		$result = runQuery("SELECT * FROM processentry WHERE processid='$searchkey'");

		$response = [
			"response" => false,

		];

		if($result->num_rows==1)
		{
			$result = $result->fetch_assoc();
			$process = $result["processname"];

			if($process=="Melting")
			{
				$response = [
					"response" => true,
					"url" => "/user/process/melting-edit.php",
					"name"=> "processid",

				];
			}

		}

		

		echo json_encode($response);
	}

?>