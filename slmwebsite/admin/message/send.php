<?php
    
	require_once('../../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'admin_module');
	$myuserid = $session->user->getUserid();

   
   	if(!isset($_POST["sendMessage"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    if(!isset($_POST["recipients"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $recipients = $_POST["recipients"];


    if(!isset($_POST["subject"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $subject = $_POST["subject"];


    if(!isset($_POST["message"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $message = $_POST["message"];


    $bytes = random_bytes(20);
	$uniq = bin2hex($bytes);

	runQuery("INSERT INTO mail VALUES(NULL,'$myuserid','$subject','$uniq')");

	$result = runQuery("SELECT mailid FROM mail where uniqueid='$uniq'");
	$mailid = $result->fetch_assoc()["mailid"];

    runQuery("INSERT INTO mailinfo VALUES('$mailid','$myuserid',CURRENT_TIMESTAMP)");

	runQuery("INSERT INTO mailrecipients VALUES(NULL,'$mailid','$myuserid','NEW')");
    
	foreach ($recipients as $dumuser) {
		runQuery("INSERT INTO mailrecipients VALUES(NULL,'$mailid','$dumuser','NEW')");
		$dumObj = new user($dumuser);
        $dumObj->pullInfo();
        if($dumObj->getRoleid()=="ADMIN")
        {
            $url  = "/admin/message/inbox.php";
        }
        else
        {
            $url  = "/user/message/inbox.php";
        }
		$dumObj->setNotification("New Message from ".getFirstName($myuserid),"Subject: ".$subject,"MAIL",$url);
	}
    $message = addslashes($message);
	runQuery("INSERT INTO mailmessage VALUES(NULL,'$mailid','$myuserid','$message',CURRENT_TIMESTAMP)");
	
	header("Location: inbox.php");
	die();


    

?>