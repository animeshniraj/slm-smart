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
		case "getMails" :  getMails($_POST["userid"],$_POST["limit"]); break;
		case "readMail" :  readMail($_POST["userid"],$_POST["id"]); break;
		case "sendMail" :  sendMail($_POST["userid"],$_POST["id"],$_POST["newmessage"]); break;
		default: echo json_encode($error_response);
	}

	

	function getMails($userid,$limit)
	{
		$result = runQuery("SELECT * FROM maildetails WHERE mailid IN (SELECT mailid FROM mailrecipients WHERE mailrecipients.recipient='$userid' ) ORDER BY sendtime DESC LIMIT ".$limit);

		$mailData = [];
		if($result->num_rows>0)
		{
			while($row = $result->fetch_assoc())
			{
				$dumUser = new user($row["lastsender"]);
				$dumUser->pullInfo();
				$dumMailid = $row["mailid"];
				$result2 = runQuery("SELECT alert FROM mailrecipients WHERE mailid='$dumMailid' AND recipient='$userid'");
				$alert = $result2->fetch_assoc()["alert"];
				if($alert=="NEW")
				{
					$alert = true;
				}
				else
				{
					$alert = false;
				}
				$dumData = [
					"sender"=> $dumUser->getFname()." ".$dumUser->getLname(),
					"subject"=>$row["subject"],
					"code"   => $row["uniqueid"],
					"sendtime"=> date("M d, Y H:m",strtotime($row["sendtime"])),
					"new" => $alert,

				];

				array_push($mailData, $dumData);
			}
		}

		echo json_encode(["response"=> true, "mails"=>$mailData,]);
	}

	function readMail($userid,$mailid)
	{
		$result = runQuery("SELECT * FROM mailmessage WHERE mailid='$mailid' ORDER BY time");
		runQuery("UPDATE mailrecipients SET alert='READ' WHERE mailid='$mailid' AND recipient='$userid'");
		$messages = [];
		if($result->num_rows>0)
		{
			while($row = $result->fetch_assoc())
			{
				if($row["sender"] == $userid)
				{
					$flag = true;
				}
				else
				{
					$flag = false;
				}

				$dumUser = new user($row["sender"]);
				$dumUser->pullInfo();

				$dumData = [
					"sender"=> $dumUser->getFname()." ".$dumUser->getLname(),
					"message"=> $row["message"],
					"sendtime"=> date("M d, Y H:m",strtotime($row["time"])),
					"isyou"=> $flag,

				];

				array_push($messages,$dumData);
			}
		}

		echo json_encode(["response"=> true, "messages"=>$messages]);
	}


	function sendMail($userid,$mailid,$newmessage)
	{
		global $session;
		$myuser = $session->user->getUserid();
		$newmessage = addslashes($newmessage);
		runQuery("INSERT INTO mailmessage VALUES(NULL,'$mailid','$myuser','$newmessage',CURRENT_TIMESTAMP)");
		runQuery("UPDATE mailrecipients SET alert ='NEW' WHERE mailid='$mailid'");
		runQuery("UPDATE mailinfo SET lastsender ='$myuser', sendtime=CURRENT_TIMESTAMP WHERE mailid='$mailid'");
		$result = runQuery("SELECT * FROM mail WHERE mailid='$mailid'");
		$subject = $result->fetch_assoc()["subject"];

		$result = runQuery("SELECT * FROM mailrecipients WHERE recipient<>'$myuser' AND mailid='$mailid'");

		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$dumUser = new user($row["recipient"]);
				$dumUser->pullInfo();
				if($dumUser->getRoleid()=="ADMIN")
				{
					$url  = "/admin/message/inbox.php";
				}
				else
				{
					$url  = "/user/message/inbox.php";
				}
				$dumUser->setNotification("New Message from ".getFirstName($myuser),"Subject: ".$subject,"MAIL",$url);
			}
		}
			

		echo json_encode(["response"=> true,"message"=> "INSERT INTO mailmessage VALUES(NULL,'$mailid','$myuser','$newmessage',CURRENT_TIMESTAMP)"]);
	}
	


?>