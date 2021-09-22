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

	isAuthenticated($session,'admin_module');


	$action = $_POST["action"];

	switch ($action) {
		case "checkUserid" :  checkUserid($_POST["userid"]); break;
		default: echo json_encode($error_response);
	}



	function checkUserid($userid)
	{
		$result = runQuery("SELECT * FROM users WHERE userid='$userid'");
		if($result->num_rows==0)
		{
			$response = [
				"response" => true,
			];
		}
		else
		{
			$response = [
				"response" => false,
			];
		}

		echo json_encode($response);
		return true;

	}


?>