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
        "Page Title" => "SLM | Semi Finished Stock Report",
        "Home Link"  => "/user/",
        "Menu"		 => "process-semifinished-stock",
        "MainMenu"	 => "process_semifinished",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Semi Finished";

    $showMonth =6;
    $stoptime =Date("Y-m-d H:i",strtotime("now"));
    $starttime =Date("Y-m-d H:i",strtotime("-6 months"));



    if(isset($_GET['starttime']))
    {
    	$starttime = $_GET['starttime'];
    }

    if(isset($_GET['stoptime']))
    {
    	$stoptime = $_GET['stoptime'];
    }



    $allData = [];

    $asof = 0;

    $heading = [];
    $result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime>= '$starttime' AND entrytime <= '$stoptime' ORDER BY entrytime");

    while($row=$result->fetch_assoc())
    {

    	$dum = [];
    	$dum["id"] = $row["processid"];
    	$dum["mass"] = 0;
    	$dum["remaining"] = 0;

    	$dum["entrydate"] = $row["entrytime"];
    	$dum["test"] = [];
    	$dum["child"] = [];
    	$dum["grade"] = $processname=="Melting"?"Default Grade":"No Grade Selected";
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


    	$result2 = runQuery("SELECT * FROM processentryparams WHERE param='$currid' AND step='PARENT' AND processid in (SELECT processid from processentry WHERE (entrytime BETWEEN '$start' AND '$stoptime') )");



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
    		
    		array_push($heading,getpropShortname($processname,$row2["param"]));
    		$avg[getpropShortname($processname,$row2["param"])] = [0,0];


    	}

    	$result2 = runQuery("SELECT * FROM processtestparams WHERE processid='$currid'");
    	while($row2=$result2->fetch_assoc())
    	{
    		
    		$avg[getpropShortname($processname,$row2["param"])][0] +=  $row2["value"];
    		$avg[getpropShortname($processname,$row2["param"])][1]++;
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


    	$asof += $dum["remaining"];

    	array_push($allData,$dum);
    }



    $heading = array_unique($heading);


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
					<h3>Semi Finished Stock</h3>
					<span>View Semi Finished process stock</span>
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
			<label class="col-sm-2 col-form-label">Start Date: </label>
			<div class="col-sm-2">
			
				<input required name="starttime" id='starttime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">

			
			</div>
			<div class="col-sm-2"></div>
			<label class="col-sm-1 col-form-label">Stop Date: </label>
			<div class="col-sm-2">
			
				<input required name="stoptime" id='stoptime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">

			
			</div>

			<button class="btn btn-primary" type="submit"><i class="fa fa-refresh"></i>Reload</button>
			</div>

</div>
</form>


<script>
					$(function() {
					  $('input[name="starttime"]').daterangepicker({
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
					$('#starttime').val('<?php echo DATE('Y-m-d H:i',strtotime("-6 months")) ?>');


					$(function() {
					  $('input[name="stoptime"]').daterangepicker({
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
					$('#stoptime').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>



<br>
<br>



<div class="table-responsive">
<table class="table table-striped table-bordered table-xsm" >
	
<thead>
	

	<tr style="font-size:11px;font-weight:bold;background-color:#990000;color:#fff;text-align:center;padding:0.25em!important;">
		<th scope="col">Sl.<br>No.</th>
		<th>Semi Finished ID</th>
		<th>Entry Time</th>
		<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo $head; ?></td>


			<?php 
		}

	?>
		<th>Starting<br>Quantity(kg)</th>
		<th>Remaining<br>Quantity(kg)</th>
		
		

	</tr>
</thead>
<tbody>

<?php 
	
	$k=1;
	foreach ($allData as $data) {
		
?>


<tr  style="font-size:14px;">
	<td style="text-align:center;"><?php echo $k++; ?>.</td>
	<td><a target="_blank" href="/user/report/basic-semifinished.php?id=<?php echo $data["id"]; ?>"><?php echo $data["id"]; ?></a></td></td>
	<td><?php echo $data["entrydate"]; ?></td>
<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo isset($data["test"][$head][0])?$data["test"][$head][0]:"-"; ?></td>


			<?php 
		}

	?>

	<td style="text-align:right;"><?php echo $data["mass"]; ?></td>
	<td style="text-align:right;"><?php echo $data["remaining"]; ?></td>

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
<td style="font-weight:bold;font-size:16px;color:#990000;text-align:right;" ><div style="display:inline;"><?php echo $asof; ?></div> kg</td>

	

</tr>



</tbody>
</table>
</div>

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