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

	isAuthenticated($session,'user_module');

	$myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

	$editidPermission = false;

	if($myrole =="ADMIN" OR $myrole =="Production_Supervisor")
	{
		$editidPermission = true;
	}



	if(!isset($_GET["id"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $id = $_GET["id"];

   if(!isset($_GET["cid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $cid = $_GET["cid"];

  

   $data = [];

   $data['basic'] = [];
   $data['basic']['batch'] = $id;
   $data['basic']['cid'] = $cid;


   $result = runQuery("SELECT * FROM dispatch WHERE cid='$cid'");

   if($result->num_rows==1)
   {
   		$result = $result->fetch_assoc();
   		$data['basic']['laid'] = $result['laid'];
   		$dlaid =  $result['laid'];
		$dumC = $result["customer"];
		$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
		$result2 = $result2->fetch_assoc(); 
		$data['basic']['customerid'] = $dumC;
		$data['basic']['customer'] = $result2['value'];

		$result2 = runQuery("SELECT * FROM loadingadvice_batches WHERE laid='$dlaid' AND batch='$id'");
		$result2 = $result2->fetch_assoc(); 
		$data['grade'] = $result2['grade'];
		$result2 = runQuery("SELECT * FROM loading_advice WHERE laid='$dlaid'");
		$result2 = $result2->fetch_assoc(); 
		$data['basic']['company'] = $result2['company'];
		$data['basic']['date'] = $result2['entrydate'];
   }
   else
   {
   		$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
   }



   $result = runQuery("SELECT * FROM processentry WHERE processid='$id'");

   if($result->num_rows==1)
   {
   		$data['batch'] = getDataFinal($id,$data['grade']);
   }
   else
   {
   		$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
   }





   echo "<pre>";
   print_r($data);



?>



<?php 

	function getDataFinal($id)
	{

		global $GRADE_TITLE;

		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$id' AND param='$GRADE_TITLE'");
		$result = $result->fetch_assoc();

		$cgrade = $result['value'];



		$result = runQuery("SELECT * FROM gradeproperties WHERE processname='Final Blend' AND gradename = '$cgrade'");


		$allproperties = [];
		$allSieve = [];

		$isSievecum = false;

		while($row=$result->fetch_assoc())
		{

			if(substr($row['properties'],0,5)=="Sieve")
			{
				$allSieve[$row['properties']] =[];
				$allSieve[$row['properties']]['printed'] = $row['max']==1?true:false;
				$allSieve[$row['properties']]["value"] =[];
				$isSievecum = $row['quarantine']==1?true:false;
			}
			else
			{
				$allproperties[$row['properties']] = [];
				$cproperty = $row['properties'];
				$result2 = runQuery("SELECT * FROM processgradesproperties WHERE processname='Final Blend' AND gradeparam='$cproperty'");
				$result2 = $result2->fetch_assoc();
				$allproperties[$row['properties']]['mpif'] = $result2['mpif'];
				$allproperties[$row['properties']]['class'] = $result2['class'];
				$allproperties[$row['properties']]["value"] = [];
			}
		}



		$result = runQuery("SELECT * FROM processtest WHERE processid='$id'");

		while($row=$result->fetch_assoc())
		{
			$dumtestid = $row["testid"];
			$result2 = runQuery("SELECT * FROM processtestparams WHERE testid='$dumtestid'");
			while($row2 = $result2->fetch_assoc())
			{
				if(substr($row2['param'],0,5)=="Sieve")
				{
					array_push($allSieve[$row2['param']]["value"],$row2['value']);
				}
				else
				{
					array_push($allproperties[$row2['param']]["value"],$row2['value']);
				}
			}
		}


		foreach ($allproperties as $key => $property) {
			
			$allproperties[$key]["value"] = array_sum($property["value"])/count($property["value"]);

		}

		foreach ($allSieve as $key => $property) {
			
			$allSieve[$key]["value"] = array_sum($property["value"])/count($property["value"]);

		}

		$carryover = 0;

		foreach ($allSieve as $key => $property) {
			
			if(!$property['printed'])
			{
				$carryover += $property['value'];
				//$allSieve[$key] = [];
			}

			if($carryover!=0 && $property['printed'])
			{
				$allSieve[$key]['value'] += $carryover;
				$carryover = 0;
			}

		}






		return [$allproperties,$allSieve];
	}

?>