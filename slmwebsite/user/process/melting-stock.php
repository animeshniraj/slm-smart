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
        "Page Title" => "Melting Stock Report | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "process-melting-stock",
        "MainMenu"	 => "process_melting",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Melting";

    $showMonth =6;
    $stoptime =Date("Y-m-d 23:59:59",strtotime("now"));
    $starttime =Date("Y-m-d",strtotime("-6 months"));
    $currgrade = "all";
    $show = "no";




    if(isset($_GET['starttime']))
    {
    	$starttime = $_GET['starttime'];
    }

    if(isset($_GET['stoptime']))
    {
    	$stoptime = $_GET['stoptime']." 23:59:59";
    }

    if(isset($_GET['show']))
    {
    	$show = "yes";

    }

    $props = [];
    if(isset($_GET['prop']))
    {
    	$props = $_GET['prop'];
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
					<h3>Melting Stock</h3>
					<span>View Melting stock details</span>
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
	<div class="form-group">
		<h5>Select Date Range to see Melting report</h5>
		<div class="row">
				<label class="col-md-1 col-form-label">Start Date: </label>
				<div class="col-md-2">
					<input required name="starttime" id='starttime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">
				</div>

				<label class="col-md-1 col-form-label">End Date: </label>
				<div class="col-md-2">
						<input required name="stoptime" id='stoptime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">
						<input type="hidden" name="show" value="yes">
				</div>
		</div>
				
				<h5>Properties to show:</h5>

				<div class="col-sm-12">

					<?php
					
						foreach ($heading as $head) {

								$dchecked = "";
								if(in_array($head,$props))
								{
									$dchecked = "checked";
								}

							?>

							<div class="checkbox-color checkbox-primary">
							<input id="prop-<?php echo $head?>" type="checkbox" <?php echo $dchecked; ?> name="prop[]" value="<?php echo $head; ?>">
							<label for="prop-<?php echo $head;?>">
							<?php echo $head; ?>
							</label>
							</div>

							<?php
						}

					?>
				</div>
				<div class="col-sm-12">
					<button class="btn btn-primary pull-right" type="submit"><i class="fa fa-refresh"></i>Generate Report</button>
				</div>
				</div>

	</div>
	</form>


	<script>
		$(function() {
			$('input[name="starttime"]').daterangepicker({
			singleDatePicker: true,
			timePicker: false,
			showDropdowns: true,
			locale: 
			{    
				format: 'YYYY-MM-DD',
			},
			
			minYear: 1901,
			maxYear: parseInt(moment().format('YYYY'),10)
			}, function(start, end, label) {
			
			});


		});
		$('#starttime').val('<?php echo $starttime ?>');


		$(function() {
			$('input[name="stoptime"]').daterangepicker({
			singleDatePicker: true,
			timePicker: false,
			showDropdowns: true,
			locale: 
			{    
				format: 'YYYY-MM-DD',
			},
			
			minYear: 1901,
			maxYear: parseInt(moment().format('YYYY'),10)
			}, function(start, end, label) {
			
			});


		});
		$('#stoptime').val('<?php echo $stoptime ?>');

	</script>

<hr>


<?php
if($show == "yes")
{
?>


<div class="row" style="margin:1rem;">
<div class="table-responsive dt-responsive">
<table class="table table-striped table-bordered table-xs" style="width:100%;">
	
<thead>
	

	<tr style="font-size:11px;font-weight:bold;background-color:#990000;color:#fff;text-align:center;padding:0.25em!important;">
		<th scope="col">Sl.<br>No.</th>
		<th>Heat ID</th>
		<th>Date</th>
		<th>Day<br>Heat No.</th>
		<th>Furnace<br> Heat No.</th>
		<th>Heat On<br>Time</th>
		<th>Heat Off<br>Time</th>

		<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo $head; ?></td>


			<?php 
		}

	?>
		<th>Produced<br>Quantity(kg)</th>
		<th>Balance<br>Quantity(kg)</th>
		
		

	</tr>
</thead>
<tbody>

<?php 
	
	$k=1;
	foreach ($allData as $data) {
		
?>


<tr  style="font-size:14px;text-align:center;">
	<td width="2%"><?php echo $k++; ?>.</td>
	<td width="5%"><a target="_blank" href="/user/report/basic-melting.php?id=<?php echo $data["id"]; ?>"><?php echo $data["id"]; ?></a></td></td>
	<td width="5%"><?php echo Date('d-M-Y',strtotime($data["entrydate"])); ?></td>


	<?php 

		$dheatno ="";
		$dfheatno ="";
		$dfheatontime ="";
		$dfheatofftime ="";


		$did = $data["id"];

		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$did' AND param='Heat No.'");
		if($result->num_rows==1)
		{
			$dheatno = $result->fetch_assoc()['value'];
		}

		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$did' AND param='Furnace Heat No.'");
		if($result->num_rows==1)
		{
			$dfheatno = $result->fetch_assoc()['value'];
		}

		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$did' AND param='Heat On Time'");
		if($result->num_rows==1)
		{
			$dfheatontime = Date('H:i',strtotime($result->fetch_assoc()['value']));
		}


		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$did' AND param='Heat Off Time'");
		if($result->num_rows==1)
		{
			$dfheatofftime = Date('H:i',strtotime($result->fetch_assoc()['value']));
		}


	?>

	<td width="3%"><?php echo $dheatno; ?></td>
	<td width="3%"><?php echo $dfheatno; ?></td>
	<td><?php echo $dfheatontime; ?></td>
	<td><?php echo $dfheatofftime; ?></td>
<?php

		foreach ($heading as $head) {
			

			?>

				<td><?php echo isset($data["test"][$head][0])?$data["test"][$head][0]:"-"; ?></td>


			<?php 
		}

	?>

	<td width="5%" style="text-align:right;"><?php echo $data["mass"]; ?></td>
	<td width="5%" style="text-align:right;"><?php echo $data["remaining"]; ?></td>

</tr>


<?php

}
?>


<tfoot>
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
<td></td>
<td></td>
<td></td>
<td></td>
<td style="font-weight:bold;font-size:16px;color:#990000;text-align:right;" ><div style="display:inline;"><?php echo $asof; ?></div> kg</td>

	

</tr>
</tfoot>



</tbody>
</table>

<script type="text/javascript">
	$(document).ready( function () {
    $('.table').DataTable(

    {
    	 "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false
            },


            <?php 
            $startnumber = 7;
            $i=0;
            	foreach ($heading as $head) {

            		$flag = "false";

            		if(in_array($head, $props))
            		{
            			$flag = "true";
            		}

            		
            ?>

            {
                "targets": [ <?php  echo $startnumber+$i;?> ],
                "visible": <?php echo $flag; ?>,
                "searchable": <?php echo $flag; ?>
            },
            <?php
            	$i++;
        			}
            ?>
            
        ]
    }


    	);
} );
</script>
</div>

<?php
}
?>


<div class="col-sm-12">
	<button class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download</button>
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