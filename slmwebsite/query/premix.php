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

		
		default: echo json_encode($error_response);
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
					"msg" => "This id do not have enought quantity"
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