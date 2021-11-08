<?php
	
	require_once('../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	$error_response = [
		"response" => "error",
		"msg" => "Unknown Error"
	];
	
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}
	
	isAuthenticated($session,'user_module');
	

	$action = $_POST["action"];

	switch ($action) {
		case "getasof"		: getasof($_POST['processname'],$_POST['gradename'],$_POST['last'],$_POST['asof']);break;
		
		case "trace_forward": trace_forward($_POST['processid'])
		
		default: echo json_encode($error_response);
	}






function getasof($processname,$gradename,$last,$asof)
{

	global $GRADE_TITLE, $MASS_TITLE;

	if($processname =="Melting")
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime> NOW()- INTERVAL $last Month ORDER BY entrytime");
	}
	else
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime> NOW()- INTERVAL $last Month AND processid IN (SELECT processid from processentryparams WHERE step='OPERATIONAL' AND param='$GRADE_TITLE' AND value='$gradename') ORDER BY entrytime");
	}

	
	$remaining = 0;

    while($row=$result->fetch_assoc())
    {
    	$dum = [];
    	$dum["id"] = $row["processid"];
    	$dum["mass"] = 0;
    	$dum["remaining"] = 0;

    	$dum["entrydate"] = $row["entrytime"];
    	$dum["test"] = [];
    	$dum["child"] = [];
    	$dum["grade"] = $processname=="Melting"?"Default Grade":"No Grade Selected";
    	$currid = $row["processid"];
    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND (param = '$MASS_TITLE' OR param='$GRADE_TITLE')");




    	while($row2=$result2->fetch_assoc())
    	{

    		if($row2["param"]==$MASS_TITLE)
    		{
    			$dum["mass"] = $row2["value"];
    			$dum["remaining"] = $row2["value"];
    		}
    		if($row2["param"]==$GRADE_TITLE)
    			$dum["grade"] = $row2["value"];
    	}


    	
    	$start = $row["entrytime"];



    	$result2 = runQuery("SELECT * FROM processentryparams WHERE param='$currid' AND step='PARENT' AND processid in (SELECT processid from processentry WHERE (entrytime BETWEEN '$start' AND '$asof') )");

    	

    	while($row2=$result2->fetch_assoc())
    	{
    		$dumRaw = [$row2["processid"],$row2["value"]];

    		array_push($dum["child"],$dumRaw);

    		$dum["remaining"] -= $row2["value"];
    	}


    	


    	$remaining += $dum["remaining"];

    }

	$response = [
		"response" => true,
		"msg" => "",
		"remaining" => $remaining,
	];

	echo json_encode($response);
}



?>