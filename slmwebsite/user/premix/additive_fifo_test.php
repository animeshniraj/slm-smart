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

	

    $PAGE = [
        "Page Title" => "View all Additive Daily Stock | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-additivesfifo",
        "MainMenu"	 => "premix_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

	$showmonth = date('F');
	$showmonthInt = date('m');
	$showyear = date('Y');


    if(isset($_POST['deleteProcess']))
    {
    	$externalid = $_POST['externalid'];
    	runQuery("DELETE FROM additive_external WHERE externalid='$externalid'");
    }


    $startdate =  new DateTime(date('Y')."-$showmonthInt-01");
    $endDate = new DateTime(date('Y-m-d',strtotime("tomorrow")));
   

  	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($startdate, $interval, $endDate);

    $allAdditive = [];
    $daily = [];
    $allrecon = [];


    $result = runQuery("SELECT * FROM premix_additives WHERE additive<>'Iron'");

    while($row=$result->fetch_assoc())
    {	
    	$curr = $row["additive"];

    	$daily[$curr] = [];
    	$allrecon[$curr] =[];

    	$isfirst = false;
    	
    	foreach ($period as $dt) {
    		


    		$dumdt = $dt->format('Y-m-d');

    		

    		$result2 = runQuery("SELECT * FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND DATE(entrydate) <='$dumdt' ORDER BY entrydate");
    		
	    	if($result2->num_rows>0)
	    	{
	    		$result_new = $result2;
	    		while($result2 = $result_new->fetch_assoc())
		    	{
		    		$openingStock =0;
	    			$currStock =0;

	    			$currintid = $result2["internalid"];

	    			$result3 = runQuery("SELECT additive,entrydate,mass as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND internalid ='$currintid'");
	    			$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;
			    	$daily[$curr][$currintid][strval($dt->format('d'))]["opening"] = $openingStock;
			    	$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "As it is";

			    	if($dumdt == $dt->format('Y-m-01'))
		    		{
		    				$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Carry Forward";
		    				$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = "-";
		    				//$k++;
		    		}
		    		

			    	if($result3->num_rows>0)
			    	{
			    		$result3 = $result3->fetch_assoc();
			    		$openingStock = $result3["qty"];
			    		$currStock = $result3["qty"];
			    		$daily[$curr][$currintid][strval($dt->format('d'))]["opening"] = $openingStock;
			    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;

			    		
			    		if($dumdt==Date('Y-m-d',strtotime($result3['entrydate'])))
			    		{
			    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "New Addition";
			    		}


			    		$result4 = runQuery("SELECT sum(value)as qty FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$currintid' AND premixid in (SELECT premixid FROM premix_batch WHERE DATE(entrydate) <= '$dumdt') GROUP BY param");

			    	
			    		if($result4->num_rows>0)
				    	{
				    		$dumqty = $result4->fetch_assoc()["qty"];
				    		$currStock = $currStock - $dumqty;
				    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;

				    		
				    		if($currStock<$openingStock)
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Consumed";
				    		}
				    		else
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "As it is";
				    		}

				    		
				    	}



			    	}



			    }

	    	}



    		$result2 = runQuery("SELECT * FROM premix_additives WHERE additive<>'Iron'");

    	}

    	



    	


    	array_push($allAdditive,[$curr]);


    }

     echo "<pre>";

    $fifo_data = [];

    foreach ($period as $dt) {
    	$fifo_data[$dt->format('d')] =[];
    	$fifo_data[$dt->format('d')]['max_row'] =0;
    	foreach ($allAdditive as $additive) {

    		$fifo_data[$dt->format('d')][$additive[0]] = [];

    	}
    }

    foreach ($allAdditive as $additive) {

    	$curr = $daily[$additive[0]];
		foreach ($curr as $curradditive => $additivedata) {


			
			foreach ($additivedata as $key => $value) {
				

				$fifo_data[$key][$additive[0]][$curradditive] = $daily[$additive[0]][$curradditive][$key];
			}


		}

	}

	foreach ($period as $dt) {
    	foreach ($allAdditive as $additive) {

    		
    		$dumCount = count($fifo_data[$dt->format('d')][$additive[0]]);

    		if($fifo_data[$dt->format('d')]['max_row']< $dumCount)
    		{
    			$fifo_data[$dt->format('d')]['max_row']= $dumCount;
    		}
    		
    		

    	}
    }
    

   
    print_r($fifo_data);
   
    die();
    


    $deletePermission = false;
    
	if($myrole =='ADMIN')
	{
		
			$deletePermission = true;
		

	}
 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

