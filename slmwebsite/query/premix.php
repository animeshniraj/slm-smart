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
		case "checkSupplier"		: checkSupplier($_POST['externalid'],$_POST['supplier'],$_POST['additive']);break;
		case "checkExternalId"		: checkExternalId($_POST['externalid']);break;
		case "checkPremixId"		: checkPremixId($_POST['premixid']);break;
		case "getBatches"			: getBatches($_POST['additive']); break;
		case "checkFinalBlend"		: checkFinalBlend($_POST['id'],$_POST['quantity']); break;
		case "getfifobatch"			: getfifobatch($_POST['additive'],$_POST['quantity']); break;
		case "getfinalbatch"		: getfinalbatch($_POST['grade']); break;
		
		default: echo json_encode($error_response);
	}



	function getfinalbatch($grade)
	{

		$result = runQuery("SELECT * FROM premix_grades WHERE gradename='$grade'");

		$allowed_grades = unserialize($result->fetch_assoc()['finishedgrade']);

		$monthsago = Date('Y-m-d',strtotime('-6 months'));
		$result = runQuery("SELECT * FROM processentry WHERE processname='BATCH' AND islocked ='BATCHED' AND entrytime>='$monthsago'");

		

		$alldata = [];
		while($row = $result->fetch_assoc())
		{
			$did = $row['processid'];
			$remQty = getfinalbatchqty($did);

			if(in_array(getProcessGrade($did),$allowed_grades) && $remQty>0)
			{
				array_push($alldata,[$did,$remQty]);
			}
			
			
			
		}

		$response = [
			"response" => true,
			"data" => $alldata,
			"msg" => ""
		];

		 echo json_encode($response);
		 die();
	}



	function getfifobatch($additive,$quantity)
	{

		$step = 0.01;
	if($additive=="Iron")
	{
		echo "";
		die();
	}
	else
	{

		$allids = [];



		$result2 = runQuery("SELECT * FROM additive_internal WHERE additive in (SELECT additive FROM premix_additives_group_member WHERE groupname='$additive') AND status='NOTOVER' ORDER BY entrydate LIMIT 20");
		while($row2=$result2->fetch_assoc())
		{
			array_push($allids,[$row2["internalid"],$row2["mass"]]);
		}
		

		if(count($allids)==0)
		{

			$result = runQuery("SELECT * FROM additive_internal WHERE additive='$additive' AND status='NOTOVER' ORDER BY entrydate LIMIT 20");
			while($row=$result->fetch_assoc())
			{
				array_push($allids,[$row["internalid"],$row["mass"]]);
			}

		}

		$selectedBatch = [];
		$required = $quantity;
		$flag = true;

		for($i=0;$i<count($allids);$i++)
		{

			$currid = $allids[$i][0];
			$currmass = $allids[$i][1];

			$result = runQuery("SELECT * FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$currid'");

			while($row=$result->fetch_assoc())
			{
				$currmass -= $row["value"];
			}

			if($currmass>=$step)
			{
				if($currmass>=$required)
				{

					array_push($selectedBatch,[$currid,$required]);
					$required -= $currmass;
					break;
				}
				else
				{
					
					$required -= $currmass;
					array_push($selectedBatch,[$currid,$currmass]);
				}
			}

		}

		if($required>0)
		{
			array_push($selectedBatch,["Error","No Batch Available"]);
			$flag = false;

			
		}


		$aa = "";

		for($i=0;$i<count($selectedBatch);$i++)
		{
			if($selectedBatch[$i][0] !="Error")
			{
				$aa = $aa." ".$selectedBatch[$i][0]." (".$selectedBatch[$i][1]." kg)<br>";
				

			}
			else
			{
				$aa = $aa." ".$selectedBatch[$i][0]." (".$selectedBatch[$i][1]." )<br>";
			}
			
		}



		$response = [
			"response" => true,
			"result" => $aa,
			"additive"=>$additive,
		];

		 echo json_encode($response);
		 die();
		//$result = runQuery("SELECT * FROM additive_internal WHERE additive = ")
	}

	}

	function checkFinalBlend($id,$qty)
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='Final Blend' AND processid = '$id' AND islocked <> 'BLOCKED'");
		if($result->num_rows>0)
		{
			$total = getTotalQuantity($id);

			$used = getChildProcessQuantity($id) + getChildPremixQuantity($id) ;

			if(($total-$used)>=$qty)
			{
				$response = [
					"response" => true,
					"msg" => ""
				];
			}
			else
			{
				$response = [
					"response" => false,
					"msg" => "This id do not have enough quantity"
				];
			}
		}
		else
		{
			$response = [
				"response" => false,
				"msg" => "Cannot find the id"
			];
		}
		
		echo json_encode($response);
	}

	function checkSupplier($eid,$supplier,$additive)
	{	
		$result = runQuery("SELECT * FROM external_param WHERE externalid='$supplier' AND param='Additives' AND value='$additive'");
		if($result->num_rows==0)
		{
			$error_response = [
				"response" => "error",
				"msg" => "This supplier does not provide ".$additive,
				
			];
			echo json_encode($error_response);
			die();
		}


		$result = runQuery("SELECT * FROM additive_external WHERE externalid='$eid'");
		if($result->num_rows!=0)
		{
			$error_response = [
				"response" => "error",
				"msg" => "This external id already exists",
				
			];
			echo json_encode($error_response);
			die();
		}



		$response = [
			"response" => "yes",
			"msg" => ""
		];

		echo json_encode($response);
			die();

	}


	function checkExternalId($eid)
	{
		$result = runQuery("SELECT * FROM additive_external WHERE externalid='$eid'");
		if($result->num_rows==0)
		{
			$error_response = [
				"response" => "error",
				"msg" => "This external id doesnot exists",
				
			];
			echo json_encode($error_response);
			die();
		}

		$response = [
			"response" => "yes",
			"msg" => ""
		];

		echo json_encode($response);
			die();
	}


	function checkPremixId($eid)
	{
		$result = runQuery("SELECT * FROM premix_batch WHERE premixid='$eid'");
		if($result->num_rows==0)
		{
			$error_response = [
				"response" => "error",
				"msg" => "This premix id doesnot exists",
				
			];
			echo json_encode($error_response);
			die();
		}

		$response = [
			"response" => "yes",
			"msg" => ""
		];

		echo json_encode($response);
			die();
	}


	function getBatches($additive)
	{
		$result = runQuery("SELECT * FROM additive_internal WHERE additive='$additive' AND status = 'NOTOVER' ORDER BY entrydate LIMIT 5");

		$alldata = [];

		while($row=$result->fetch_assoc())
		{
			array_push($alldata,[$row["internalid"],$row["entrydate"],$row["mass"]]);
		}

		$response = [
			"response" => "yes",
			"batch" => $alldata
		];

		echo json_encode($response);
			die();
	}





?>