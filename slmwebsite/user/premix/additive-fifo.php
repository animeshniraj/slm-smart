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
    $olddate = "";

    while($row=$result->fetch_assoc())
    {	
    	$curr = $row["additive"];

    	$daily[$curr] = [];
    	$allrecon[$curr] =[];

    	$isfirst = false;
    	
    	foreach ($period as $dt) {
    		


    		$dumdt = $dt->format('Y-m-d');

    		


    		$olddate = clone $dt;
    		$olddate->modify("-1 day");
    				
    		

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
			    	$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "";

			    	if($dumdt == $dt->format('Y-m-01'))
		    		{
		    				$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Carry Forward";
		    				$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = "-";
		    				//$k++;
		    		}
		    		

			    	if($result3->num_rows>0)
			    	{
			    		$result3 = $result3->fetch_assoc();
			    		//$openingStock = $result3["qty"];

			    		if($dumdt == $dt->format('Y-m-01'))
			    		{
			    				$openingStock = $result3["qty"];
			    				
			    		}
			    		else
			    		{
			    				if(is_null($daily[$curr][$currintid][strval($olddate->format('d'))]))
			    				{
			    					$openingStock = "";
			    				}
			    				else
			    				{
			    					$openingStock = $daily[$curr][$currintid][strval($olddate->format('d'))]["current"];
			    				}	
			    			 	
			    			 
			    			 
			    			 	
			    			 
			    			 
			    			 //$openingStock = $olddate->format('d');

			    			 
			    		}
			    		$currStock = $result3["qty"];
			    		$daily[$curr][$currintid][strval($dt->format('d'))]["opening"] = $openingStock;
			    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;

			    		
			    		if($dumdt==Date('Y-m-d',strtotime($result3['entrydate'])))
			    		{
			    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "New Addition";
			    		}


			    		$dumtime = $dt->format('Y-m-d');
				    	$result5 = runQuery("SELECT internalid,SUM(adjustment) as qty FROM stock_reconciliation WHERE internalid='$currintid' AND DATE(entrytime) <= '$dumdt'  GROUP BY internalid");

				    	$rFlag = false;
			    		while($row5=$result5->fetch_assoc())
			    		{
			    			$currStock += $row5["qty"];
			    			$rFlag = true;
			    			//$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Reconciled";

			    		}


			    		$result5 = runQuery("SELECT internalid,SUM(adjustment) as qty FROM stock_reconciliation WHERE internalid='$currintid' AND DATE(entrytime) = '$dumdt'  GROUP BY internalid");

				    	$rFlag = false;
			    		while($row5=$result5->fetch_assoc())
			    		{
			    			
			    			$rFlag = true;
			    			//$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Reconciled";

			    		}


			    		$result4 = runQuery("SELECT sum(value)as qty FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$currintid' AND premixid in (SELECT premixid FROM premix_batch WHERE DATE(entrydate) <= '$dumdt') GROUP BY param");

			    	
			    		if($result4->num_rows>0)
				    	{
				    		$dumqty = $result4->fetch_assoc()["qty"];
				    		$currStock = $currStock - $dumqty;
				    		

				    		

				    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;

				    		if($dumdt==Date('Y-m-d',strtotime($result3['entrydate'])))
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "New Addition";
				    		}
				    		elseif($rFlag)
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Reconciled";
				    		}
				    		elseif($currStock<$openingStock)
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Consumed";
				    		}
				    		else
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "As it is";
				    		}

				    		
				    		
				    	}
				    	else
				    	{
				 
				    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;
				    		if($dumdt==Date('Y-m-d',strtotime($result3['entrydate'])))
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "New Addition";
				    		}
				    		elseif($rFlag)
				    		{
				    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Reconciled";
				    		}
				    		elseif($currStock<$openingStock)
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
    		$olddate = clone $dt;

    	}

    	



    	


    	array_push($allAdditive,[$curr]);


    }

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
    

    


    $deletePermission = false;
    
	if($myrole =='ADMIN')
	{
		
			$deletePermission = true;
		

	}
 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		inobj.value = val;
	}


</script>



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>View all Additives</h5>
					<span>Select additive to edit</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

<div class="page-body">
<div class="row">
<div class="col-lg-12">






<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">

<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th style="text-align: center;" colspan="100%">Month of <?php echo $showmonth; ?> (All Qty in Kgs)</th>
		</tr>
			
		</tr>
		<tr style='border: 2px solid'>
		<th>Date</th>


		<?php 
			foreach ($allAdditive as $additive) {
			
		?>

		<th style="border-left: 2px solid;"> <?php echo $additive[0]; ?></th>

		<th> Activity</th>
		<th> Quantity</th>
		<th>Opening Stock</th>
		<?php 
			}
		?>
		
		</tr>





	</thead>
	<tbody>

		<?php 
			$olddt = "";
			$currdt = "";
			foreach ($period as $dt) {

				$currdt = $dt->format('d');
			
		?>
		


		
		<?php 
			for($i=0;$i<$fifo_data[$currdt]['max_row'];$i++)
			{
				
				if($currdt==$olddt)
				{
					echo "<tr>";
				}
				else
				{
					echo "<tr style='border-top: 2px solid'>";
				}


			if($currdt!=$olddt)
			{
				$olddt =$currdt;
				echo "<th rowspan='".$fifo_data[$currdt]['max_row']."'>".$currdt."</th>";
			}


			foreach ($fifo_data[$currdt] as $key => $value) {
				if($key=="max_row"){continue;}

				$dumKey = array_key_first($value);
				if(!$dumKey)
				{
					echo "<td></td><td></td><td></td><td></td>";
					continue;
				}
				
		?>
			


			

			<td><?php echo $dumKey ?></td>
			<td><?php echo $value[$dumKey]['msg'] ?></td>
			<td><?php echo $value[$dumKey]['current'] ?></td>

			<?php if($dumKey=="01"){ ?>

			<td><?php echo $value[$dumKey]['opening'] ?></td>

			<?php } else{ ?>

				<td><?php echo $value[$dumKey]['opening'] ?></td>
			<?php } ?>
			

		<?php
			unset($fifo_data[$currdt][$key][$dumKey]);
			}
			echo "</tr>";
		}

		?>

		

		<?php

		}
		?>
		

	</tbody>
	</table>


</div>


</div>
</div>



<form method="POST" id="deleteprocessform">
	<input type="hidden" name="externalid" id="deleteprocessid">
	<input type="hidden" name="deleteProcess" >

</form>

<form method="POST" id="redirectform" action="additive-edit.php">
	<input type="hidden" name="externalid" id="deleteprocessid">


</form>



</div>
</div>
</div>

</div>
</div>
</div>
</div>


<?php
    
    include("../../pages/endbody.php");

?>

<script type="text/javascript">





$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	




});





</script>