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


	if(!isset($_POST["daterange"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }


    $daterange = $_POST["daterange"];

   
    
    $startDate = Date("Y-m-d",strtotime(explode(" - ", $daterange)[0]));
    $endDate = Date("Y-m-d",strtotime(explode(" - ", $daterange)[1]));




    $result = runQuery("SELECT * FROM loginlog WHERE currtime BETWEEN '$startDate' AND '$endDate' ORDER BY currtime DESC");
    
    $allLogs = [];

    if($result->num_rows>0)
    {
    	$k = 0;
    	while($row=$result->fetch_assoc())
    	{
    		array_push($allLogs, [++$k,$row["currtime"],getFullName($row["userid"])]);
    	}
    }

    $daterange = str_replace("/","_", $daterange);
    $daterange = str_replace(" - ","_to_", $daterange);

	header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="loginlogs_from_'.$daterange.'.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
      
	$fp = fopen('php://output', 'w');
	
	fputcsv($fp, ["Sl. No.","Log Time","Name"]);
    foreach ($allLogs as $log) 
    {
        
        fputcsv($fp, $log);
    }	

  

?>