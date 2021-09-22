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
        "Menu"		 => "process-rawbag-stock",
        "MainMenu"	 => "process_rawbag",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Raw Bag";

    $showMonth =6;
    $asofdate =Date("Y-m-d H:i",strtotime("now"));

    if(isset($_GET['lastmonths']))
    {
    	$showMonth = $_GET['lastmonths'];
    }

    if(isset($_GET['asofdate']))
    {
    	$asofdate = $_GET['asofdate'];
    }



    $allData = [];
    $heading = [];
    $asof = [];


    $result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime> NOW()- INTERVAL $showMonth Month AND entrytime < '$asofdate' ORDER BY entrytime");

    while($row=$result->fetch_assoc())
    {
    	$dum = [];
    	$dum["id"] = $row["processid"];
    	$dum["entrydate"] = $row["entrytime"];
    	$dum["mass"] = 0;

    	$dum["remaining"] = 0;
    	$dum["rawmaterial"] = [];
    	$dum["child"] = [];
    	$dum["grade"] = $processname=="Melting"?"Default Grade":"No Grade Selected";
    	$dum["test"] = [];
    	$currid = $row["processid"];
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

    	$start= $row["entrytime"];

    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND step='PARENT' AND processid in (SELECT processid from processentry WHERE (entrytime BETWEEN '$start' AND '$asofdate') )");



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


    	$avg = [];
    	
    	$result2 = runQuery("SELECT DISTINCT(param) FROM processtestparams WHERE processid = '$currid'");

    	while($row2=$result2->fetch_assoc())
    	{
    		array_push($heading,$row2["param"]);
    		$avg[$row2["param"]] = [0,0];
    	}

    	$result2 = runQuery("SELECT * FROM processtestparams WHERE processid='$currid'");
    	while($row2=$result2->fetch_assoc())
    	{
    		
    		$avg[$row2["param"]][0] +=  $row2["value"];
    		$avg[$row2["param"]][1]++;
    	}


    	foreach ($avg as $key => $value) {
    		$dumSum = $value[0];
    		$dumCount = $value[1];

    		if($dumCount!=0)
    		{
    			$avg[$key][0] = round($dumSum/$dumCount,3);
    		}
    		else{
    			$avg[$key][0] = "-";
    		}
    	}

    	$dum["test"] = $avg;

    	if(!isset($allData[$dum["grade"]]))
    	{
    		$allData[$dum["grade"]] = [];
    		$asof[$dum["grade"]] = 0;
    	}

    	$asof[$dum["grade"]] += $dum["remaining"];
    	array_push($allData[$dum["grade"]],$dum);
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
					<h5>Raw Bag Stock</h5>
					<span>View Raw Bag process stock</span>
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

<form  method="GET" >
<div class="form-group row">
			<label class="col-sm-2 col-form-label">Show Last (Months): </label>
			<div class="col-sm-2">
			
				<input required name="lastmonths" type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $showMonth; ?>">

			
			</div>
			<div class="col-sm-2"></div>
			<label class="col-sm-1 col-form-label">As Of: </label>
			<div class="col-sm-2">
			
				<input required name="asofdate" type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">

			
			</div>

			<button class="btn btn-primary" type="submit"><i class="fa fa-refresh"></i>Reload</button>
			</div>

</div>
</form>



<script>
					$(function() {
					  $('input[name="asofdate"]').daterangepicker({
					    singleDatePicker: true,
					    timePicker: true,
					    timePicker24Hour: true,
					    showDropdowns: true,
					    locale: 
					    {    
					    	format: 'YYYY-MM-DD HH:mm',
					    },
					  	
					    minYear: 1901,
					    maxYear: parseInt(moment().format('YYYY'),10)
					  }, function(start, end, label) {
					    
					  });


					});
					$('#creation-date').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>

<br>
<br>

<?php

foreach ($allData as $key => $currData) {


?>
<div style="display:flex; justify-content:center;">
	<big style="font-weight: bold;">Grade Name: <?php echo $key ?></big>
</div>
<div class="table-responsive">
<table class="table table-striped table-bordered" >
	
<thead>
	

	<tr>
		<th>Sl. No </th>
		<th>Heat ID</th>
		<th>Entry Time</th>
		<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo $head; ?></td>


			<?php 
		}

	?>
		<th>Starting Quantity(kg)</th>
		<th>Remaining Quantity(kg)</th>
		
		

	</tr>
</thead>
<tbody>

<?php 
	
	$k=1;
	foreach ($currData as $data) {
		
?>


<tr>
	<td><?php echo $k++; ?></td>
	<td><a target="_blank" href="/user/report/basic-rawbag.php?id=<?php echo $data["id"]; ?>"><?php echo $data["id"]; ?></a></td>
	<td><?php echo $data["entrydate"]; ?></td>
<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo isset($data["test"][$head][0])?$data["test"][$head][0]:"-"; ?></td>


			<?php 
		}

	?>

	<td><?php echo $data["mass"]; ?></td>
	<td><?php echo $data["remaining"]; ?></td>

</tr>


<?php

}
?>


<tr>

<?php

		foreach ($heading as $head) {
			

			?>

				<td></td>


			<?php 
		}

	?>
<td></td>
<td></td>
<td></td>
<td></td>
<td style="font-weight:bold;" ><div style="display:inline;"><?php echo $asof[$key]; ?></div> kg</td>




</tr>








</tbody>
</table>
</div>

<br><br>
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