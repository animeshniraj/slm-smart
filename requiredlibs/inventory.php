<?php
	
	require_once("variables.php");
	require_once("dbconfig.php");
	require_once("usermodel.php");


	function getfinalbatchqty($did)
	{
		$dqty = getTotalQuantity($did);

			$result2 = runQuery("SELECT SUM(value) as val FROM premix_batch_params WHERE param='$did' AND tag = 'Iron' AND step = 'BATCH SELECTION'");

			$result2 = $result2->fetch_assoc()['val'];

			if($result2)
			{
				$dqty -= $result2;
			}


			$result2 = runQuery("SELECT SUM(qty) as val FROM dispatch_invoices WHERE batch='$did'");

			$result2 = $result2->fetch_assoc()['val'];

			if($result2)
			{
				$dqty -= $result2;
			}


			return $dqty;
	}


	function getProcessGrade($did)
	{
		global $GRADE_TITLE;

		$grade = "";

		$result = runQuery("SELECT * FROM processentryparams WHERE param='$GRADE_TITLE' AND processid='$did'");

		if($result->num_rows==1)
		{
			$grade = $result->fetch_assoc()['value'];
		}

		return $grade;
	}
	
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

	function getRawBagNo($processid)
	{
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='Raw Bag No.'");
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
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='Blend Number'");
		$heatnumber  = "-";
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$heatnumber = $row["value"];
			
		}

		return $heatnumber;
	}


	function getBinNo($processid)
	{
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='GENERIC' AND param='Bin Number'");
		$heatnumber  = "-";
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			$heatnumber = $row["value"];
			
		}

		return $heatnumber;
	}

	function getBlendID_annealing($processid)
	{
		
		
		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND STEP='PARENT' AND param <>'$processid'");
		$heatnumber  = 0;
		if($result->num_rows>0)
		{
			$row=$result->fetch_assoc();
			
			#$heatnumber = getBlendID($row["param"]);
			$heatnumber = $row["param"];
			
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



	function getAverageTest($processid,$params)
	{
		$result = runQuery("SELECT param,AVG(value) as avg FROM processtestparams WHERE processid='$processid' GROUP BY param");

		$dum_prop = [];
		$dumData = [];

		while($row=$result->fetch_assoc())
		{
			$dum_prop[$row["param"]] = $row["avg"];
		}

		for($j=0;$j<count($params);$j++)
		{
			if(isset($dum_prop[$params[$j]]))
			{
				array_push($dumData,$dum_prop[$params[$j]]);
			}
			else
			{
				array_push($dumData,"");
				
			}
			
		}
		
		return $dumData;
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
			$result = runQuery("SELECT * FROM processentryparams WHERE param='$GRADE_TITLE' AND value='$gradename' AND step='OPERATIONAL' AND processid in (SELECT processid FROM processentry WHERE processname='$processname' AND (DATE(entrytime) >= Date(NOW()- INTERVAL 60 day)))");
			
			while($row=$result->fetch_assoc())
			{
				array_push($allids,[$row["processid"],$gradename]);
			}

		}



		$params = array_unique($params);

		$d1 = [];
		$d2= [];
		$dspan = [];

		foreach($params as $value)
		{
			if($value =="Sieve PAN")
			{
				array_push($dspan,$value);
			}
			elseif(substr($value, 0,5) =="Sieve")
			{
				array_push($d1,$value);
			}
			else
			{
				array_push($d2,$value);
			}


		}

		sort($d1,SORT_NATURAL | SORT_FLAG_CASE);
		$params= array_merge($d2,$d1,$dspan);

		$allids = array_unique($allids,SORT_REGULAR);



		return getFullBlendData($allids,$params,$processname,$processid);
			
		

	}


	function getFullBlendData($processid,$params,$processname,$childid)
	{
		global $HOLD_QTY;
		$allData = [];
		$all_num = [];
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


			$result = runQuery("SELECT value FROM processentryparams WHERE processid = '$childid' AND step = 'PARENT' and param='$dumId'");

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

			//	$result2 = runQuery("SELECT value FROM processentryparams WHERE processid = '$dumId' AND param='$HOLD_QTY'");

			//	if($result2->num_rows>0)
			//	{
			//		$hold = floatval($result2->fetch_assoc()["value"]);
			//	}
			
			$hold = 0;	
				
			}
			else
			{
				$hold = 0;
			}


			

			if($processname=="Raw Bag")
			{
				array_push($dumData,Date('d-M-Y',strtotime($result["entrytime"])));
			}
			else
			{
				array_push($dumData,Date('d-M-Y',strtotime($result["entrytime"])));
			}



			
			array_push($dumData,$processid[$i][1]);

			$avg_data = getAverageTest($processid[$i][0],$params);
			
			

			for($j=0;$j<count($avg_data);$j++)
			{
				array_push($dumData,$avg_data[$j]);
			}
			$total = getTotalQuantity($processid[$i][0]);
			$used = getChildProcessQuantity($processid[$i][0]);



			if(($total-$used-$hold)==0 && $dumData[0]!== "checked"){
				continue;
			}

			array_push($dumData,$total-$used-$hold);

			$dumNo = "";
			$dumid = $processid[$i][0];
			
			if($processname=="Raw Bag")
			{
				$result_d = runQuery("SELECT value FROM processentryparams WHERE processid = '$dumid' AND  param='Raw Bag No.'");
			}
			else
			{
				
				$result_d = runQuery("SELECT value FROM processentryparams WHERE processid = '$dumid' AND  param='Bin Number'");
			}

			if($result_d->num_rows!=0)
			{
				$dumNo = $result_d->fetch_assoc()['value'];
			}

			$all_num[$processid[$i][0]] = $dumNo;
			

			

			array_push($allData,$dumData);

			
			
		}



		return [$allData,$params,$all_num];

		
	}
	










	

?>