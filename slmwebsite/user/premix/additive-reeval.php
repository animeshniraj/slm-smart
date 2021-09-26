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
        "Page Title" => "Additives | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-additivesinternalview",
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


    if(isset($_POST["reevaluate"]))
    {

    	
    	$revalids = $_POST["reeval"];
    	

    	foreach ($revalids as $id) {


    		$result = runQuery("SELECT param,SUM(value) as qty FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$id' GROUP BY param");

    		$dumused = 0;
    		while($row=$result->fetch_assoc())
    		{
    			$dumused = $row["qty"];
    		}

    		$result = runQuery("INSERT INTO additive_external (SELECT internalid,additive,supplier,CURRENT_TIMESTAMP,mass-$dumused,'PENDING' FROM additive_internal  WHERE internalid='$id')");

    		if($result)
    		{
    			runQuery("UPDATE additive_internal SET status='RESHELF' WHERE internalid='$id'");
    		}
    	}

    	
    	

    	header("Location: /user/premix/additive-view.php");



    	


    }


    $startdate =  new DateTime(date('Y')."-$showmonthInt-01");
    $endDate = new DateTime(date('Y-m-d',strtotime("today")));
   

  	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($startdate, $interval, $endDate);

    $allAdditive = [];
    $data = [];


    $result = runQuery("SELECT * FROM premix_additives");

    while($row=$result->fetch_assoc())
    {	
    	$curr = $row["additive"];

    	
    	$shelf = $row["shelflife"];
    	

    	$result2 = runQuery("SELECT * FROM additive_internal WHERE additive='$curr' AND status='NOTOVER' ORDER BY entrydate");

    	while($row2=$result2->fetch_assoc())
    	{

    		$elapsed = round((strtotime("now") -strtotime($row2["entrydate"]))/(3600*24)) ;

    		if($elapsed>($shelf-10))
    		{
    			$checked = "";
    			//$color = "#ffc40c";
    			$color = "black";

    			$indays = abs($elapsed-$shelf);

    			if($elapsed>=$shelf)
    			{
    				$row2["entrydate"] = $row2["entrydate"] ." (Expired ".$indays." days ago) ";
    				$checked = "checked";
    				$color = "red";
    			}
    			else
    			{
    				$row2["entrydate"] = $row2["entrydate"] ." (Expiring in ".$indays." days) ";
    			}
    			array_push($data,[$row2["internalid"],$row2["entrydate"],$curr,$shelf,$checked,$color]);
    		}



    		
    		
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
					<h5>Reevaluate Additives</h5>
					<span>Select additive to reeval</span>
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


	
<form method="POST">
<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<thead>
		
			
		</tr>
		<tr>
		<th></th>
		<th>Internal Id</th>
		<th>Entry Date</th>
		<th>Additive</th>
		<th>Shelf Life(Days)</th>
		

		</tr>

	</thead>
	<tbody>


		<?php

			foreach ($data as $additive) {
				
		?>
		<tr style="color: <?php echo $additive[5] ?>;">
			
			<td><input type="checkbox" name="reeval[]" <?php echo $additive[4] ?> value="<?php echo $additive[0] ?>"></td>
			<td><?php echo $additive[0] ?></td>
			<td><?php echo $additive[1] ?></td>
			<td><?php echo $additive[2] ?></td>
			<td><?php echo $additive[3] ?></td>

		</tr>

		<?php


				}

		?>
	</tbody>
	</table>
</div>

	<div class="col-sm-12">
				<button type="submit" name="reevaluate" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-check"></i>Confirm</button>
				</div>
</form>

</div>
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