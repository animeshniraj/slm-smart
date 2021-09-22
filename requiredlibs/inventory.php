<?php
	
	require_once("variables.php");
	require_once("dbconfig.php");
	require_once("usermodel.php");


	
	function getTotalQuantity($processid)
	{
		global $MASS_TITLE;
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$MASS_TITLE'");
		$totalQuantity  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$totalQuantity += $row["value"];
			
		}

		return $totalQuantity;
	}

	function getHeatNumber($processid)
	{
		global $MASS_TITLE;
		global $HEATNO;
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='$HEATNO'");
		$heatnumber  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$heatnumber = $row["value"];
			
		}

		return $heatnumber;
	}

	function getDryBagNo($processid)
	{
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='Dry Bag No.'");
		$heatnumber  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$heatnumber = $row["value"];
			
		}

		return $heatnumber;
	}

	function getBlendID($processid)
	{
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='Blend ID'");
		$heatnumber  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$heatnumber = $row["value"];
			
		}

		return $heatnumber;
	}

	function getEntryTime($processid)
	{
		global $MASS_TITLE;
		$result = runQuery("SELECT * FROM processentry WHERE processid='$processid'");
		$entrytime  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$entrytime = $row["entrytime"];
			
		}

		return $entrytime;
	}

	function getChildProcessQuantity($processid)
	{
		global $MASS_TITLE;
		$totalQuantity = 0;
		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND param='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$totalQuantity += $row["value"];
			}
			
			
			
		}
		return $totalQuantity;
	}

	function getChildPremixQuantity($processid)
	{
		global $MASS_TITLE;
		$totalQuantity = 0;
		$result = runQuery("SELECT * FROM premix_batch_params WHERE STEP='BATCH SELECTION' AND param='$processid'");
		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$totalQuantity += $row["value"];
			}
			
			
			
		}
		return $totalQuantity;
	}


	function getAllParents($processid)
	{
		global $HOLD_QTY;
		$result = runQuery("SELECT * FROM processentryparams WHERE STEP='PARENT' AND processid='$processid'");
		$allParents = [];
		$totalQuantity = 0;
		$hold =0;

		

		if($result->num_rows>0)
		{
			while($row=$result->fetch_assoc())
			{
				$dumid = $row["param"];
				$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$dumid' AND param='$HOLD_QTY'");
				if($result2->num_rows>0)
				{
					$hold = $result2->fetch_assoc()["value"];
				}
				$totalQuantity += $row["value"];
				array_push($allParents,["id"=>$row["param"],"quantity"=>$row["value"],"total quantity"=> getTotalQuantity($row["param"]),"quantity left"=>(getTotalQuantity($row["param"])-getChildProcessQuantity($row["param"]))-$hold,"hold quantity"=>$hold]);

			}
			
		}

		return ["Parents"=>$allParents,"Total"=>$totalQuantity];
	}



	function getAverageTest($processid,$param)
	{
		$result = runQuery("SELECT AVG(value) as avg FROM processtestparams WHERE processid='$processid' AND param='$param'");

		return round($result->fetch_assoc()["avg"],2);
	}


	function getAllBlendmasterGrades($processid,$processname)
	{
		global $GRADE_TITLE;

		if($processname=="Raw Blend")
		{
			$processname = "Raw Bag";
		}

		if($processname=="Final Blend")
		{
			$processname = "Semi Finished";
		}

		$result = runQuery("SELECT * FROM blendmastergrade WHERE processid='$processid'");

		$grades = [];

		while($row=$result->fetch_assoc())
		{
			array_push($grades,$row["gradename"]);
		}



		$params = [];
		$allids = [];
		for($i=0;$i<count($grades);$i++)
		{
			$gradename = $grades[$i];
			$result = runQuery("SELECT * FROM gradeproperties WHERE processname='$processname'  AND gradename='$gradename' ORDER BY ordering");
			while($row=$result->fetch_assoc())
			{
				array_push($params,$row["properties"]);
			}

			$result = runQuery("SELECT * FROM processentryparams WHERE param='$GRADE_TITLE' AND value='$gradename' AND step='OPERATIONAL'");
			
			while($row=$result->fetch_assoc())
			{
				array_push($allids,[$row["processid"],$gradename]);
			}

		}
		$params = array_unique($params);
		$allids = array_unique($allids,SORT_REGULAR);

		return getFullBlendData($allids,$params,$processname,$processid);
		

	}


	function getFullBlendData($processid,$params,$processname,$childid)
	{
		global $HOLD_QTY;
		$allData = [];

		$dumData = [];

		array_push($dumData,"checked","Bag ID","Date","Grade");

		

		for($j=0;$j<count($params);$j++)
		{
			array_push($dumData,$params[$j]);

		}

		array_push($dumData,"Bal Qty");

		array_push($allData,$dumData);

		

		for($i=0;$i<count($processid);$i++)
		{
			$dumData = [];

			

			$dumId = $processid[$i][0];


			$result = runQuery("SELECT * FROM processentryparams WHERE processid = '$childid' AND step = 'PARENT' and param='$dumId'");

			if($result->num_rows==1)
			{
				array_push($dumData,"checked",$result->fetch_assoc()["value"]);
			}
			else
			{
				array_push($dumData,"",0);
			}

			array_push($dumData,$processid[$i][0]);

			$result = runQuery("SELECT * FROM processentry WHERE processid = '$dumId'");

			$result = $result->fetch_assoc();

			if($result["islocked"] =="BLOCKED")
			{
				continue;
			}


			$hold = 0;

			if($processname=="Raw Bag")
			{
				if($result["processname"] == "Raw Blend")
				{
					if($result["islocked"] != "FAILED_ALLOWED")
					{
						continue;
					}
				}
				
			}


			$result2 = runQuery("SELECT * FROM processentryparams WHERE processid = '$dumId' AND param='$HOLD_QTY'");

			if($result2->num_rows>0)
			{
				$hold = floatval($result2->fetch_assoc()["value"]);
			}

			array_push($dumData,Date('d-M-Y',strtotime($result["entrytime"])));
			array_push($dumData,$processid[$i][1]);

			for($j=0;$j<count($params);$j++)
			{
				array_push($dumData,getAverageTest($processid[$i][0],$params[$j]));
			}
			$total = getTotalQuantity($processid[$i][0]);
			$used = getChildProcessQuantity($processid[$i][0]);
			array_push($dumData,$total-$used-$hold);
			array_push($allData,$dumData);
		}

		return [$allData,$params];
		
	}
	










	

?>