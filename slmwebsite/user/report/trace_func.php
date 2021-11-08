<?php
	
	require_once('../../../requiredlibs/includeall.php');

	
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
	

	$action = $_GET["action"];

	switch ($action) {
		
		case "trace_forward": find_children($_GET['processid']); break;
		case "trace_forward2": find_children2($_GET['processid']); break;
		case "trace_backward": find_parent($_GET['processid']); break;
		case "trace_backward2": find_parent2($_GET['processid']); break;
		
		default: echo json_encode($error_response);
	}



	function find_parent($processid)
	{
		global $MASS_TITLE;
		$result1 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
		$mainqty  = $result1->fetch_assoc()['value'];

		$data = [
			"id" => $processid,
			"desc"=> "$processid ($mainqty kg)",
			"children"=> []
		];

		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND processid='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$newid = $row['param'];
				$qty  = $row['value'];				
				$result1 = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND processid='$newid'");
				$split = round($qty/$mainqty,2);
				$split_percent = round($split*100,3);
				if($result1->num_rows==0)
				{
					$dumdata = [
						"id" => strval($newid)."---".$processid."**0.0**",
						"desc"=> "$newid (Qty Used: $qty kg ($split_percent %))",
						"hasChild"=> false,
						];
				}
				else
				{
					$dumdata = [
						"id" => strval($newid)."---".$processid."**".$split."**",
						"desc"=> "$newid (Qty Used: $qty kg ($split_percent %))",
						"hasChild"=> true,
						];
				}

				array_push($data["children"],$dumdata);
			}
		}

		echo json_encode($data);
	}

	function find_parent2($processid)
	{
		global $MASS_TITLE;

		$old = $processid;
		$old = strstr($old, '**', true);

		$total = strstr($processid, '**');

		$total = preg_replace("/[^0-9.]/", "", $total );
		$total = floatval($total);




		$processid = strstr($processid, '---', true);

		
		$result1 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
		$mainqty  = $result1->fetch_assoc()['value'];

		$parent_percent = $total;

		$data = [
			
		];

		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND processid='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$newid = $row['param'];
				$qty  = $row['value'];	

				$result1 = runQuery("SELECT * FROM processentryparams WHERE processid='$newid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
				$subqty  = $result1->fetch_assoc()['value'];

				$toChild = round($qty/$subqty,2);
				$from_parent = $toChild*$total;
				$from_parent_percent = round($from_parent*100,3);



				$result1 = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND processid='$newid'");
				if($result1->num_rows==0)
				{
					$dumdata = [
						"id" => strval($newid)."---".$old."**0.0**",
						"desc"=> "$newid (Child Percent: $from_parent_percent %)",
						"hasChild"=> false,
						];
				}
				else
				{
					$dumdata = [
						"id" => strval($newid)."---".$old."**".$from_parent."**",
						"desc"=> "$newid (Child Percent: $from_parent_percent %)",
						"hasChild"=> true,
						];
				}

				array_push($data,$dumdata);
			}
			
			
			
		}

		echo json_encode(["result"=> $data]);

	}	




	function find_children($processid)
	{
		global $MASS_TITLE;
		$result1 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
		$mainqty  = $result1->fetch_assoc()['value'];

		$data = [
			"id" => $processid,
			"desc"=> "$processid ($mainqty kg)",
			"children"=> []
		];

		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND param='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$newid = $row['processid'];
				$qty  = $row['value'];				
				$result1 = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND param='$newid'");
				$split = round($qty/$mainqty,2);
				$split_percent = round($split*100,3);
				if($result1->num_rows==0)
				{
					$dumdata = [
						"id" => strval($newid)."---".$processid."**0.0**",
						"desc"=> "$newid (Qty Used: $qty kg ($split_percent %))",
						"hasChild"=> false,
						];
				}
				else
				{
					$dumdata = [
						"id" => strval($newid)."---".$processid."**".$split."**",
						"desc"=> "$newid (Qty Used: $qty kg ($split_percent %))",
						"hasChild"=> true,
						];
				}

				array_push($data["children"],$dumdata);
			}
			
			
			
		}

		echo json_encode($data);

	}


	function find_children2($processid)
	{
		global $MASS_TITLE;

		$old = $processid;
		$old = strstr($old, '**', true);

		$total = strstr($processid, '**');

		$total = preg_replace("/[^0-9.]/", "", $total );
		$total = floatval($total);




		$processid = strstr($processid, '---', true);

		
		$result1 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
		$mainqty  = $result1->fetch_assoc()['value'];

		$parent_percent = $total;

		$data = [
			
		];

		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND param='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$newid = $row['processid'];
				$qty  = $row['value'];	

				$toChild = round($qty/$mainqty,2);
				$from_parent = $toChild*$parent_percent;
				$from_parent_percent = round($from_parent*100,3);



				$result1 = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND param='$newid'");
				if($result1->num_rows==0)
				{
					$dumdata = [
						"id" => strval($newid)."---".$old."**0.0**",
						"desc"=> "$newid (Parent Percent: $from_parent_percent %)",
						"hasChild"=> false,
						];
				}
				else
				{
					$dumdata = [
						"id" => strval($newid)."---".$old."**".$from_parent."**",
						"desc"=> "$newid (Parent Percent: $from_parent_percent %)",
						"hasChild"=> true,
						];
				}

				array_push($data,$dumdata);
			}
			
			
			
		}

		echo json_encode(["result"=> $data]);

	}









?>