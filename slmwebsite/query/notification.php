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
		case "getNotification" 	:  getNotification($_POST["userid"]); break;
		case "readNotification" :  readNotification($_POST["userid"],$_POST["ids"]); break;
		case "getTime" 			:  getTime(); break;
		default: echo json_encode($error_response);
	}

	function getTime()
	{
		$time = strtotime("now");

		echo json_encode(["response"=>true,"time"=>Date('d-M-Y H:i',$time)]);

	}

	function readNotification($userid,$ids)
	{
		$ids =  json_decode($ids);
		
		for($i=0;$i<count($ids);$i++)
		{
			$currid = $ids[$i];
			runQuery("UPDATE usernotification SET status='READ' WHERE userid='$userid' AND id='$currid'");
		}

		echo json_encode(["response"=>true]);

	}	

	function getNotification($userid)
	{
		$result = runQuery("SELECT *,TIMESTAMPDIFF(SECOND,time, NOW()) AS ago FROM usernotification WHERE (userid = '$userid' AND (status='NEW' OR time >= NOW()- INTERVAL 5 DAY)) ORDER BY ago");

		$newNotif = 0;
		$notifications = [];
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				if($row["status"]=="NEW")
				{
					$newNotif++;
				}

				$currAgo = $row["ago"];
				if($currAgo>3600)
				{
					$currAgo = floor($currAgo/3600);
					$agomessage = "about ".$currAgo." hour ago.";
				}
				else if($currAgo>60)
				{
					$currAgo = floor($currAgo/60);
					$agomessage = "about ".$currAgo." mins ago.";
				}
				else
				{

					$agomessage = "about ".$currAgo." secs ago.";
				}

				$dumNotification = [
					"id"    => $row["id"],
					"title" => $row["notiftitle"],
					"msg"   => $row["notifmsg"],
					"agomessage" => $agomessage,
					"status"     => $row["status"],
					"type"     	 => $row["type"],
					"url"     	 => $row["url"],
				];

				array_push($notifications,$dumNotification);
			}
		}

		$response = [
			"response" => true,
			"notifications" => $notifications,
			"newNotif"     => $newNotif, 
		];

		echo json_encode($response);
	}
	


?>