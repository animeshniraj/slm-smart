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
		case "getprocessid"		: getHeatid($_POST['processid']);break;
		case "getMeltingQuantity"	: getMeltingQuantity($_POST['processid']);break;
		case "getRawBagQuantity"    : getRawBagQuantity($_POST['processid']);break;
		case "getRawBlendQuantity"    : getRawBlendQuantity($_POST['processid']);break;
		case "getAllRemainingMelting"	: getAllRemainingMelting($_POST['furnance']);break;
		case "getAllRemainingAnnealing"	: getAllRemainingAnnealing($_POST['furnance']);break;
		case "getAnnealingQuantity"	: getAnnealingQuantity($_POST['processid']);break;
		default: echo json_encode($error_response);
	}

	
	function getAllRemainingMelting($fid)
	{
		$prefix = $fid."%";
		$allIds = [];

		$result = runQuery("SELECT * FROM processentry WHERE processname='Melting' AND processid LIKE '$prefix' AND islocked <> 'BLOCKED'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$total = getTotalQuantity($row["processid"]);
				$used = getChildProcessQuantity($row["processid"]);
				
				if(($total-$used)>0)
				{
					
					array_push($allIds,[$row["processid"],$row["entrytime"],getHeatNumber($row["processid"]),$total,($total-$used)]);
				}
			}

			$response = [
				"response" => true,
				"ids" => $allIds
			];
			
			
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "No Ids"
			];
		}
		
		echo json_encode($response);
	}	


	function getAllRemainingAnnealing($fid)
	{
		$prefix = $fid."%";
		$allIds = [];

		$result = runQuery("SELECT * FROM processentry WHERE processname='Annealing' AND processid LIKE '$prefix' AND islocked <> 'BLOCKED'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$total = getTotalQuantity($row["processid"]);
				$used = getChildProcessQuantity($row["processid"]);
				
				if(($total-$used)>0)
				{
					
					array_push($allIds,[$row["processid"],$row["entrytime"],0,$total,($total-$used)]);
				}
			}

			$response = [
				"response" => true,
				"ids" => $allIds
			];
			
			
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "No Ids"
			];
		}
		
		echo json_encode($response);
	}


	
	

	function getHeatid($processid)
	{
		$result = runQuery("SELECT * FROM processentry WHERE processid = '$processid'");
		if($result->num_rows==1)
		{
			$response = [
				"response" => true
			];
			
		}
		else
		{
			$response = [
				"response" => false
			];
		}
		echo json_encode($response);
	}


	function getMeltingQuantity($processid)
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='Melting' AND processid = '$processid'");
		if($result->num_rows==1)
		{
			$result = $result->fetch_assoc();
			$total = getTotalQuantity($processid);
			$used = getChildProcessQuantity($processid);
			
			if(($total-$used)<=0)
			{
				$response = [
					"response" => false,
					"msg" => "There is no remaining quantity in this batch."
				];
			}
			else if($result["islocked"]=="BLOCKED")
			{
				$response = [
					"response" => false,
					"msg" => "This batch is quarantined."
				];
			}
			else
			{
				$response = [
					"response" => true,
					"id" => strtoupper($processid),
					"entrytime" => getEntryTime($processid),
					"heatno" => getHeatNumber($processid),
					"total" =>$total,
					"available"=> $total-$used,


				];
			}
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "Melting ID not found."
			];
		}
		echo json_encode($response);
	}


	function getAnnealingQuantity($processid)
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='Annealing' AND processid = '$processid'");
		if($result->num_rows==1)
		{
			$result = $result->fetch_assoc();
			$total = getTotalQuantity($processid);
			$used = getChildProcessQuantity($processid);
			
			if(($total-$used)<=0)
			{
				$response = [
					"response" => false,
					"msg" => "There is no remaining quantity in this batch."
				];
			}
			else if($result["islocked"]=="BLOCKED")
			{
				$response = [
					"response" => false,
					"msg" => "This batch is quarantined."
				];
			}
			else
			{
				$response = [
					"response" => true,
					"id" => strtoupper($processid),
					"entrytime" => getEntryTime($processid),
					"heatno" => 0,
					"total" =>$total,
					"available"=> $total-$used,


				];
			}
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "Melting ID not found."
			];
		}
		echo json_encode($response);
	}

	function getRawBagQuantity($processid)
	{
		global $HOLD_QTY;
		$result = runQuery("SELECT * FROM processentry WHERE processname='Raw bag' AND processid = '$processid'");
		if($result->num_rows==1)
		{
			$result = $result->fetch_assoc();
			$total = getTotalQuantity($processid);
			$used = getChildProcessQuantity($processid);

			$hold = 0;

			$result2 = runQuery("SELECT * FROM processentryparams WHERE processid = '$processid' AND param='$HOLD_QTY'");

			if($result2->num_rows>0)
			{
				$hold = floatval($result2->fetch_assoc()["value"]);
			}
			
			if(($total-$used-$hold)<=0)
			{
				$response = [
					"response" => false,
					"msg" => "There is no remaining quantity in this batch."
				];
			}
			else if($result["islocked"]=="BLOCKED")
			{
				$response = [
					"response" => false,
					"msg" => "This batch is quarantined."
				];
			}
			else
			{
				$response = [
					"response" => true,
					"id" => strtoupper($processid),
					"entrytime" => getEntryTime($processid),
					"heatno" => getDryBagNo($processid),
					"total" =>$total,
					"available"=> $total-$used-$hold,
					"hold" => $hold,


				];
			}
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "Raw Bad ID not found."
			];
		}
		echo json_encode($response);
	}

	function getRawBlendQuantity($processid)
	{
		global $HOLD_QTY;
		$result = runQuery("SELECT * FROM processentry WHERE processname='Raw Blend' AND processid = '$processid'");
		if($result->num_rows==1)
		{
			$result = $result->fetch_assoc();
			$total = getTotalQuantity($processid);
			$used = getChildProcessQuantity($processid);

			

			

			
			
			if(($total-$used)<=0)
			{
				$response = [
					"response" => false,
					"msg" => "There is no remaining quantity in this batch."
				];
			}
			else if($result["islocked"]=="BLOCKED")
			{
				$response = [
					"response" => false,
					"msg" => "This batch is quarantined."
				];
			}
			else
			{
				$response = [
					"response" => true,
					"id" => strtoupper($processid),
					"entrytime" => getEntryTime($processid),
					"heatno" => getBlendID($processid),
					"total" =>$total,
					"available"=> $total-$used,
					


				];
			}
			
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "Raw Blend ID not found."
			];
		}
		echo json_encode($response);
	}


	
	


?>