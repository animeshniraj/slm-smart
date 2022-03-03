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
        "Page Title" => "SLM | Batch Stock Report",
        "Home Link"  => "/user/",
        "Menu"		 => "process-batch-stock",
        "MainMenu"	 => "process_batch",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Batch";

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

    if(isset($_GET['currgrade']))
    {
    	$currgrade = $_GET['currgrade'];
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

    if(isset($_GET['propmin']))
    {
    	$propmin = $_GET['propmin'];
    	

    }

    $rtype = "prod";
    if(isset($_GET['rtype']))
    {
    	$rtype = $_GET['rtype'];
    	

    }

    if(isset($_GET['propmax']))
    {
    	$propmax = $_GET['propmax'];
    	

    }

    $allowed_grade =[];

    if($currgrade!="all" && substr($currgrade,0,3)=="all")
    {
    	$dclass = explode("all",$currgrade)[1];
    	
    	$result = runQuery("SELECT * FROM gradeclass WHERE processname='Final Blend' AND class='$dclass'");

    	while($row=$result->fetch_assoc())
    	{
    		array_push($allowed_grade,$row['gradename']);
    	}
    	$currgrade="all";
    	
    }




    $allData = [];

    $asof = 0;

    $heading = [];

	if($currgrade=="all")
    {
    	$result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime>= '$starttime' AND entrytime <= '$stoptime' ORDER BY entrytime");
    }
    else
    {
    	$dumgradepattern = $currgrade."#%";
    	$result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND entrytime>= '$starttime' AND entrytime <= '$stoptime' AND processid IN (SELECT processid FROM processentryparams WHERE param='$GRADE_TITLE' AND (value='$currgrade' OR value LIKE '$dumgradepattern')) ORDER BY entrytime");

    }
    

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


    	if(count($allowed_grade)>0)
    	{

    		if(!in_array($dum["grade"],$allowed_grade))
    		{
    			continue;
    		}
    	}


    	$start= $row["entrytime"];


    	$result2 = runQuery("SELECT * FROM processentryparams WHERE param='$currid' AND step='PARENT' AND param <>'approved-by' AND processid in (SELECT processid from processentry WHERE (entrytime BETWEEN '$start' AND '$stoptime') )");



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

    		if(is_numeric($row2["value"]))
    		{
    			$avg[getpropShortname($processname,$row2["param"])][0] +=  $row2["value"];
    			$avg[getpropShortname($processname,$row2["param"])][1]++;
    		}
    		
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
    	
    	$flag = true;

    	foreach ($avg as $key => $value) {
    		

    		if(isset($propmin["'$key'"]) && $propmin["'$key'"])
    		{	
    			if($value[0]<=$propmin["'$key'"])
    			{
    				
    				$flag = false;
    				//continue;
    			}
    		}

    		if(isset($propmax["'$key'"]) && $propmax["'$key'"])
    		{
    			
    			if($value[0]>=$propmax["'$key'"])
    			{
    				$flag = false;
    				//continue;
    			}
    		}
    	}

    	


    	$asof += $dum["remaining"];


    	if($flag)
    	{
    		if($rtype=="bal" && $dum["remaining"]<=0)
    		{
    			continue;
    		}
    		array_push($allData,$dum);
    	}

    	
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
	
	function titleicontoRefresh()
	{
		var titleicon = document.getElementById('titleicon');
		titleicon.classList.remove("fa-signal");
		titleicon.classList.add("fa-refresh");

	}
	function titleicontonormal()
	{
		var titleicon = document.getElementById('titleicon');
		titleicon.classList.remove("fa-refresh");
		titleicon.classList.add("fa-fire");
		
	}
	function reloadCurrPage()
	{
		window.location = window.location.href.split("?")[0];
	}

</script>



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i id="titleicon" onmouseenter="titleicontoRefresh()" onmouseleave="titleicontonormal()" onclick="reloadCurrPage()" style="cursor: pointer;" class="fa fa-signal bg-c-blue"></i>
				<div class="d-inline">
					<h3>Batch Stock</h3>
					<span>View Batch stock details</span>
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
	<?php
if($show != "yes")
{
?>
<div class="form-group">
<h5>Select Date Range to see Batch report</h5>
<hr>
<div class="row">
			<label class="col-sm-2 col-form-label">Start Date: </label>
			<div class="col-sm-3">
				<input required name="starttime" id='starttime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">			
			</div>
			
			<label class="col-sm-2 col-form-label">End Date: </label>
			<div class="col-sm-3">
				<input required name="stoptime" id='stoptime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $asofdate; ?>">
				<input type="hidden" name="show" value="yes">
			</div>
</div>
<hr>
<div class="row">
			<label class="col-sm-2 col-form-label ">Select Grade:</label>
			<div class="col-sm-3">
				<select class="form-control" required name="currgrade" id="currgrade">
					<option value="all">All Grades</option>
					<option value="allsip">All SIP</option>
					<option value="allaip">All AIP</option>
					<option value="allbronze">All Bronze</option>
					<option value="allrsp">All RSP</option>
					<?php  

						$result = runQuery("SELECT * FROM processgrades WHERE processname='$processname' ORDER BY entrytime DESC");
						$allgradelist = [];
						while($row=$result->fetch_assoc())
						{

								if(in_array(explode('#',$row["gradename"])[0], $allgradelist))
								{
									continue;
								}

								echo "<option value='".explode('#',$row["gradename"])[0]."'>".explode('#',$row["gradename"])[0]."</option>";
								array_push($allgradelist,explode('#',$row["gradename"])[0]);
								
						}

					?>
				</select>



				<script type="text/javascript">
					document.getElementById('currgrade').value='<?php echo $currgrade; ?>';
				</script>
			</div>

			<label class="col-sm-2 col-form-label ">Type</label>
			<div class="col-sm-3">
				<select class="form-control" required name="rtype" id="rtype">
					<option value="prod">Production Quantity</option>
					<option value="bal">Balance Quantity</option>
					
				</select>

				

				<script type="text/javascript">
					document.getElementById('currgrade').value='<?php echo $currgrade; ?>';
				</script>
			</div>

			

</div>
			<hr>
			
			<h5>Select desired Properties to show:</h5>

			<div class="col-sm-8 table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th></th>
							<th>Property</th>
							<th>Min</th>
							<th>Max</th>
						</tr>
					</thead>
					<tbody>
				<?php
				
					foreach ($heading as $head) {

						$dchecked = "";
							if(in_array($head,$props))
							{
								$dchecked = "checked";
							}

						?>

						


						<tr>
							<td><input id="prop-<?php echo $head?>" type="checkbox" <?php echo $dchecked; ?> name="prop[]" value="<?php echo $head; ?>"></td>
							<td><?php echo $head; ?></td>
							<td><input id="propmin-<?php echo $head?>" type="number" min="0" step="0.001" name="propmin['<?php echo $head?>']" value=""></td>
							<td><input id="propmax-<?php echo $head?>" type="number" min="0" step="0.001" name="propmax['<?php echo $head?>']" value=""></td>

						</tr>

						<?php
					}


				?>
				</tbody>
				</table>
			</div>
			<div class="col-sm-12">
				<button class="btn btn-primary pull-right" type="submit"><i class="fa fa-refresh"></i>Generate Report</button>
			</div>
			
			</div>
			<?php
}
?>

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



<br>
<br>

<?php
if($show == "yes")
{
?>
<div class="row" style="margin:1rem;">

<div class="table-responsive dt-responsive table-responsive">
<table id="stockdatatable" class="table table-striped table-bordered table-xs" style="width:100%;">
	
<thead>
	

	<tr style="font-size:11px;font-weight:bold;background-color:#990000;color:#fff;text-align:center;padding:0.25em!important;">
		<th scope="col">Sl.<br>No.</th>
		<th>Batch ID</th>
		<th>Entry Time</th>
		<th>Grade</th>
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


<tr  style="font-size:14px;">
	<td width="2%" style="text-align:center;"><?php echo $k++; ?>.</td>
	<td width="5%"><a target="_blank" href="/user/report/basic-batch.php?id=<?php echo $data["id"]; ?>"><?php echo $data["id"]; ?></a></td></td>
	<td width="5%"><?php echo $data["entrydate"]; ?></td>
	<td width="5%"><?php echo $data["grade"]; ?></td>
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
<td style="font-weight:bold;font-size:16px;color:#990000;text-align:right;" ><div style="display:inline;"><?php echo $asof; ?></div> kg</td>

	

</tr>
</tfoot>



</tbody>
</table>

<script type="text/javascript">
	$(document).ready( function () {
    $('.table').DataTable(

    {
    	dom: 'Bfrtip',
    	buttons: [
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':visible'
                }
            },
        ],
    	 "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false
            },


            <?php 
            $startnumber = 4;
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

<?php
	if($show=="yes")
	{
?>
<div class="col-sm-12">
	<button onclick="printreport()" class="btn btn-primary pull-right mr-1 mb-1"><i class="fa fa-print"></i> Print</button>
</div>

<?php
}
?>


</div>
</div>

<script type="text/javascript">
	
function printreport()
{
					var form  = document.createElement('form');
			  		form.setAttribute('method','POST');
			  		form.setAttribute('action','/user/report/printtable.php');
			  		form.setAttribute('target','_blank');

			  		var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"print");
						i.setAttribute('value',"<?php echo $processname ?>");

						form.appendChild(i);


						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"data");
						i.setAttribute('value',document.getElementById('stockdatatable').innerHTML);

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"startdate");
						i.setAttribute('value','<?php echo $starttime ?>');

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"enddate");
						i.setAttribute('value','<?php echo $stoptime ?>');

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"grade");
						i.setAttribute('value','<?php echo $currgrade ?>');

						form.appendChild(i);

					

						document.body.appendChild(form);
						form.submit();
}


</script>

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