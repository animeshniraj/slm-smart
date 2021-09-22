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
		case "checkInvoiceId"		: checkInvoiceId($_POST['invoiceid']);break;
		case "loadbatch"		: loadbatch($_POST['type']);break;


		
		default: echo json_encode($error_response);
	}




	function loadbatch($type)
	{

		$data = [];


		if($type=="premix")
		{
			$allbatch = [];

			$result  = runQuery("SELECT * FROM premix_batch");

			while($row=$result->fetch_assoc())
			{
				$curr = $row["premixid"];
				$currQty = $row["mass"];

				$result2 = runQuery("SELECT * FROM dispatch_params WHERE step='BATCH' AND param='$curr'");
				while($row2=$result2->fetch_assoc())
				{
					$currQty -= $row2["value"];

					if($currQty<=0)
					{

						break;
					}
				}

				if($currQty>0)
				{
					array_push($data,[$curr,$currQty]);
				}


			}
		}
		elseif($type=="final")
		{
			$allbatch = [];

			$result  = runQuery("SELECT * FROM processentry WHERE processname='Final Blend'");
			while($row=$result->fetch_assoc())
			{
				$curr = $row["processid"];
				$result2  = runQuery("SELECT * FROM processentryparams WHERE processid='$curr' AND param='Quantity (in Kg)'");
				$currQty = 0;

				if($result2->num_rows>0)
				{
					$currQty = $result2->fetch_assoc()["value"];
				}


				

				$result2 = runQuery("SELECT * FROM dispatch_params WHERE step='BATCH' AND param='$curr'");
				while($row2=$result2->fetch_assoc())
				{
					$currQty -= $row2["value"];

					if($currQty<=0)
					{

						break;
					}
				}

				$result2 = runQuery("SELECT * FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$curr'");
				while($row2=$result2->fetch_assoc())
				{
					$currQty -= $row2["value"];

					if($currQty<=0)
					{

						break;
					}
				}

				if($currQty>0)
				{
					array_push($data,[$curr,$currQty]);
				}


			}
		}


		$response = [
			"response" => "yes",
			"msg" => "",
			"data" => $data
		];

		echo json_encode($response);
		die();
	}


	function checkInvoiceId($id)
	{
		$result = runQuery("SELECT * FROM dispatch_order WHERE invoiceid='$id'");
		if($result->num_rows==0)
		{
			$error_response = [
				"response" => "error",
				"msg" => "This invoice id doesnot exists",
				
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