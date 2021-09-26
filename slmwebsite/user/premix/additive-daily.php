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
        "Menu"		 => "premix-additivesdaily",
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

    	foreach ($period as $dt) {
    		$daily[$curr][strval($dt->format('d'))] = 0;
    	}

    	



    	$openingStock =0;
    	$currStock =0;

    	
    	$date = date(date('Y')."-$showmonthInt-01");

    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND internalid IN (SELECT internalid FROM additive_internal WHERE entrydate<'$date') GROUP BY additive");

    	if($result2->num_rows>0)
    	{
    		$openingStock = $result2->fetch_assoc()["qty"];
    	}

    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' GROUP BY additive");

    	if($result2->num_rows>0)
    	{
    		$currStock = $result2->fetch_assoc()["qty"];


    		foreach ($period as $dt) {

    			$dumdt = $dt->format('Y-m-d');

    			
    			
    			$result2 = runQuery("SELECT additive,sum(value)as qty FROM premix_batch_params LEFT JOIN additive_internal on additive_internal.internalid=premix_batch_params.param WHERE step = 'BATCH SELECTION' AND additive='$curr' AND premixid IN (SELECT premixid FROM premix_batch WHERE DATE(entrydate)='$dumdt') GROUP BY additive");
	    		if($result2->num_rows>0)
		    	{
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$daily[$curr][strval($dt->format('d'))] += -1*$dumqty;
		    		$currStock = $currStock - $dumqty;
		    	}

		    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE DATE(entrydate)='$dumdt' AND additive='$curr' GROUP BY additive");

		    	if($result2->num_rows>0)
		    	{
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$daily[$curr][strval($dt->format('d'))] += 1*$dumqty;
		    		
		    	}

		    	$result2 = runQuery("SELECT SUM(adjustment) as qty FROM stock_reconciliation WHERE internalid IN (SELECT internalid FROM additive_internal WHERE additive='$curr' AND status='NOTOVER') AND DATE(entrytime)='$dumdt' GROUP BY internalid");

		    	if($result2->num_rows>0)
		    	{
		    		if(!isset($allrecon[$curr][strval($dt->format('d'))]))
		    		{
		    			$allrecon[$curr][strval($dt->format('d'))] = 0;
		    		}
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$allrecon[$curr][strval($dt->format('d'))] += $dumqty;
		    		$currStock += $dumqty;
		    		
		    	}

    		}

    		    		
    	}
    	


    	array_push($allAdditive,[$row["additive"],$openingStock,$currStock]);


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

		<th> <?php echo $additive[0]; ?></th>
		<?php 
			}
		?>
		
		</tr>


		<tr>
		<th>Current Stock</th>


		<?php 
			foreach ($allAdditive as $additive) {
			
		?>

		<th> <?php echo $additive[2]; ?></th>
		<?php 
			}
		?>
		
		</tr>


		<tr>
		<th>Opening Stock</th>


		<?php 
			foreach ($allAdditive as $additive) {
			
		?>

		<th> <?php echo $additive[1]; ?></th>
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
			<td><?php echo intval($dumdt); ?></td>
			<?php 
			foreach ($allAdditive as $additive) {
				$dumDaily = $daily[$additive[0]][strval($dt->format('d'))];
			?>

			<td> <?php echo $dumDaily>0?"+".$dumDaily:$dumDaily ?>
				
			<?php
				if(isset($allrecon[$additive[0]][strval($dt->format('d'))]))
				{
					echo "<br>Reconciliation: ". $allrecon[$additive[0]][strval($dt->format('d'))];
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