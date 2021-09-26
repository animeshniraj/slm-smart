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

    	$isfirst = true;
    	
    	foreach ($period as $dt) {
    		


    		$dumdt = $dt->format('Y-m-d');

    		

    		$result2 = runQuery("SELECT * FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND DATE(entrydate) <='$dumdt' ORDER BY entrydate");
    		
	    	if($result2->num_rows>0)
	    	{
	    		$result2 = $result2->fetch_assoc();
	    		$openingStock =0;
    			$currStock =0;

    			$currintid = $result2["internalid"];

    			$result3 = runQuery("SELECT additive,mass as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND internalid ='$currintid'");
    			$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;
		    	$daily[$curr][$currintid][strval($dt->format('d'))]["opening"] = $openingStock;
		    	$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "As it is";

		    	if($dumdt == $dt->format('Y-m-01'))
	    		{
	    				$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "Carry Forward";
	    				$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = "-";
	    				$k++;
	    		}
	    		if($isfirst)
	    		{
	    			$daily[$curr][$currintid][strval($dt->format('d'))]["msg"] = "New Addition";
	    			$isfirst = false;
	    		}

		    	if($result3->num_rows>0)
		    	{
		    		$result3 = $result3->fetch_assoc();
		    		$openingStock = $result3["qty"];
		    		$currStock = $result3["qty"];
		    		$daily[$curr][$currintid][strval($dt->format('d'))]["opening"] = $openingStock;
		    		$daily[$curr][$currintid][strval($dt->format('d'))]["current"] = $currStock;


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



    		$result2 = runQuery("SELECT * FROM premix_additives WHERE additive<>'Iron'");

    	}

    	



    	


    	array_push($allAdditive,[$curr]);


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
		<tr>
		<th>Date</th>


		<?php 
			foreach ($allAdditive as $additive) {
			
		?>

		<th style="border-left: 2px solid;"> <?php echo $additive[0]; ?></th>

		<th> Activity</th>
		<th> Quantity</th>
		<th>Openign Stock</th>
		<?php 
			}
		?>
		
		</tr>





	</thead>
	<tbody>

		<?php 

		foreach ($period as $dt) {

    			$dumdt = $dt->format('d');



		?>

		<tr>
			<td ><?php echo intval($dumdt); ?></td>
			<?php 
			foreach ($allAdditive as $additive) {
				
			?>

			
				

				<?php 

				
					foreach($daily[$additive[0]] as $key => $currbatch)
					{



				?>

				
						
						<?php 
						if(isset($currbatch[strval($dt->format('d'))]["opening"] ))
						{
							?>


							<td style="border-left: 2px solid;"><?php echo $key ?></td>
						<?php
						}
						else
						{
							?>


							<td style="border-left: 2px solid;">-</td>
						<?php
						}

					?></td>

						<td><?php 
						if(isset($currbatch[strval($dt->format('d'))]["msg"] ))
						{
							echo $currbatch[strval($dt->format('d'))]["msg"] ;
						}
						else
						{
							echo "No Batch" ;
						}

					?></td>
					<td><?php 
						if(isset($currbatch[strval($dt->format('d'))]["current"] ))
						{
							echo $currbatch[strval($dt->format('d'))]["current"] ;
						}
						else
						{
							echo "-" ;
						}

					?></td>
					<td><?php 
						if(isset($currbatch[strval($dt->format('d'))]["opening"] ))
						{
							echo $currbatch[strval($dt->format('d'))]["opening"] ;
						}
						else
						{
							echo "-" ;
						}

					?></td>


				
				


				<?php 

					}

				?>



			</td>
			<?php 
				}
			?>

		</tr>


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