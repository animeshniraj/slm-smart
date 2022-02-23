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
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "process-batch-stock",
        "MainMenu"	 => "process_batch",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Batch";


    if(!isset($_GET["id"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $processid = $_GET["id"];



    $allData = [];



    $result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND processid ='$processid'");

    $isBlocked = "NO";

    while($row=$result->fetch_assoc())
    {


    	$dum = [];
    	$dum["id"] = $row["processid"];
    	$dum["mass"] = 0;
    	$dum["remaining"] = 0;
    	$dum["rawmaterial"] = [];
    	$dum["generic"] = [];
    	$dum["operational"] = [];
    	$dum["test"] = [];
    	$dum["child"] = [];
    	$dum["grade"] = $processname=="Melting"?"Default Grade":"No Grade Selected";
    	$currid = $row["processid"];
    	$isBlocked = $row["islocked"];
    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND (param = '$MASS_TITLE' OR param='$GRADE_TITLE')");





    	while($row2=$result2->fetch_assoc())
    	{

    		if($row2["param"]==$MASS_TITLE)
    		{
    			$dum["mass"] = $row2["value"];
    			$dum["remaining"] = $row2["value"];
    		}
    		if($row2["param"]==$GRADE_TITLE)
    			$dum["grade"] = $row2["value"];
    	}


    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND step='GENERIC'");

    	while($row2=$result2->fetch_assoc())
    	{
    		$dumRaw = [$row2["param"],$row2["value"]];

    		array_push($dum["generic"],$dumRaw);
    	}


    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND step='OPERATIONAL'");

    	while($row2=$result2->fetch_assoc())
    	{
    		$dumRaw = [$row2["param"],$row2["value"]];

    		array_push($dum["operational"],$dumRaw);
    	}



    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND step='PARENT'");



    	while($row2=$result2->fetch_assoc())
    	{
    		$dumRaw = [$row2["param"],$row2["value"]];

    		array_push($dum["rawmaterial"],$dumRaw);
    	}


    	$result2 = runQuery("SELECT * FROM processentryparams WHERE param='$currid' AND step='PARENT'");



    	while($row2=$result2->fetch_assoc())
    	{
    		$dumRaw = [$row2["processid"],$row2["value"]];

    		array_push($dum["child"],$dumRaw);

    		$dum["remaining"] -= $row2["value"];
    	}


    	$allTest = [];
    	$heading = [];

    	


    	$result2 = runQuery("SELECT DISTINCT(param) FROM processtestparams WHERE processid = '$currid'");

    	while($row2=$result2->fetch_assoc())
    	{
    		array_push($heading,$row2["param"]);
    	}

    	$result2 = runQuery("SELECT * FROM processtestparams WHERE processid='$currid'");
    	while($row2=$result2->fetch_assoc())
    	{
    		if(!isset($allTest[$row2["testid"]] ))
    		{
    			$allTest[$row2["testid"]] = [];
    		}

    		$allTest[$row2["testid"]]["id"] = $row2["testid"];
    		$allTest[$row2["testid"]][$row2["param"]] = $row2["value"];

    	}

    	$dum["test"]["header"] = $heading;
    	$dum["test"]["data"] = $allTest;

    	array_push($allData,$dum);
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
					<h5>Report - <?php echo $processid; ?>(<?php echo $processname; ?>)</h5>
					<span>View Batch report</span>
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



	<?php
	if($isBlocked=="BLOCKED_ALLOWED")
	{
?>
<div class="alert alert-info background-danger">This batch was quarantined and allowed by Admin.


	



</div>
<?php
}
else if($isBlocked=="BLOCKED")
	{
?>
<div class="alert alert-danger background-danger">This batch is quarantined.


	



</div>
<?php
}

?>



<br>
<br>


<?php 

	foreach ($allData as $data) {
		
?>

<big style="font-weight: bold;">
	<div style="display:flex; justify-content:space-between; ">
	<span>Batch ID: <?php echo $data["id"] ?></span>
	
	<span>Grade Name: <?php echo $data["grade"] ?> </span>

	<span >Total Quantity: <?php echo $data["mass"] ?> kg</span>

	</div>

</big>
<br>
<br>




<big style="font-weight: bold;">Generic Property</big>
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>
		<th>Sl. No </th>
		<th>Parameter</th>
		<th>Value</th>
		
		

	</tr>
</thead>
<tbody>

<?php 

$currDataList = $data["generic"];
$k=0;
foreach($currDataList as $datalist)
{




?>

<tr>
	

	<td><?php echo ++$k; ?></td>
	<td><?php echo $datalist[0]; ?></td>
	<td><?php echo $datalist[1]; ?></td>
</tr>


<?php 

}

?>



</tbody>
</table>



<br><hr style="border-top: 3px solid"><br>


<big style="font-weight: bold;">Operational Property</big>
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>
		<th>Sl. No </th>
		<th>Parameter</th>
		<th>Value</th>
		
		

	</tr>
</thead>
<tbody>

<?php 

$currDataList = $data["operational"];
$k=0;
foreach($currDataList as $datalist)
{




?>

<tr>
	

	<td><?php echo ++$k; ?></td>
	<td><?php echo $datalist[0]; ?></td>
	<td><?php echo $datalist[1]; ?></td>
</tr>


<?php 

}

?>



</tbody>
</table>



<br><hr style="border-top: 3px solid"><br>



<big style="font-weight: bold;">Test</big>
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>

		<th>Test Id: </th>
		<?php

		$testheading = $data["test"]["header"];

		foreach ($testheading as $head) {
			




		?>

		
				<th><?php echo $head ?></th>

		<?php

			}


		?>
		
		

	</tr>
</thead>
<tbody>

<?php 

$currDataList = $data["test"]["data"];
$k=0;
foreach($currDataList as $datalist)
{




?>

<tr>
	

	<td><?php echo $datalist["id"]; ?></td>


	<?php

		foreach ($testheading as $head) {

	?>
		<td><?php echo $datalist[$head]; ?></td>

	<?php

		}

	?>

</tr>


<?php 

}

?>



</tbody>
</table>



<br><hr style="border-top: 3px solid"><br>



<big style="font-weight: bold;">Premix Usage</big>
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>
		<th>Sl. No </th>
		<th>Premix ID</th>
		<th>Used Quantity(kg)</th>
		
		

	</tr>
</thead>
<tbody>

<?php 

$currDataList = $data["child"];
$k=0;
foreach($currDataList as $datalist)
{




?>

<tr>
	

	<td><?php echo ++$k; ?></td>
	<td><?php echo $datalist[0]; ?></td>
	<td><?php echo $datalist[1]; ?></td>
</tr>


<?php 

}

?>

<tr style="font-weight:bold">
	

	<td></td>
	<td>Remaining Quantity (kg)</td>
	<td><?php echo $data["remaining"]; ?></td>
</tr>

</tbody>
</table>


<br><hr style="border-top: 3px solid"><br>

<big style="font-weight: bold;">Final Blend Usage</big>
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>
		<th>Sl. No </th>
		<th>Final Blend ID</th>
		<th>Used Quantity(kg)</th>
		
		

	</tr>
</thead>
<tbody>

<?php 

$currDataList = $data["rawmaterial"];
$k=0;
foreach($currDataList as $datalist)
{




?>

<tr>
	

	<td><?php echo ++$k; ?></td>
	<td><?php echo $datalist[0]; ?></td>
	<td><?php echo $datalist[1]; ?></td>
</tr>


<?php 

}

?>



</tbody>
</table>




<br><hr style="border-top: 3px solid"><br>
<br>
<br>
<br>

<button onclick="window.open('backwardtrace.php?id=<?php echo $processid; ?>','_blank').focus();" class="btn waves-effect waves-light btn-primary"><i class="icofont icofont-ui-previous"></i>Backward Tracking</button>

<?php

}
?>



</div>
</div>









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
  	






  // Creation

  	

  		

  	

});








</script>