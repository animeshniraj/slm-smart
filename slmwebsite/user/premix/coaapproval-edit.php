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
        "Page Title" => "View all Premix Batches | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "coa-premix",
        "MainMenu"	 => "coa_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


	 if(!isset($_POST["premixid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $premixid = $_POST["premixid"];


   if(isset($_POST['approvecoa']))
   {
   	

   	 
   	runQuery("DELETE FROM coa_test_data WHERE processid='$premixid'");

   	 for ($i=0; $i <count($_POST['allparam']) ; $i++) { 
   	 	
   	 	$dumparam = $_POST['allparam'][$i];
   	 	$dumval = $_POST['allvalues'][$i];
   	 	$dummin = $_POST['allmin'][$i];
   	 	$dummax = $_POST['allmax'][$i];
   	 	$dumtype = $_POST['type'][$i];
   	 	$dummpif = $_POST['allmpif'][$i];


   	 	runQuery("INSERT INTO coa_test_data VALUES(NULL,'$premixid','$dumparam','$dummpif','$dummin','$dummax','$dumval','$dumtype')");

   	 	}

   	 	 runQuery("INSERT INTO premix_coa_approval VALUES('$premixid','$myuserid',CURRENT_TIMESTAMP)");

   	


   	

   }
   



   $result = runQuery("SELECT * FROM premix_coa_approval WHERE premixid='$premixid'");


   $isapproved = false;
   $approvedby ="";

   if($result->num_rows==1)
   {
   		$result = $result->fetch_assoc();
   		$approvedby = $result['approvedby'];
   		$approveddate = $result['approvaldate'];
   		$isapproved = true;
   		$result2 = runQuery("SELECT * FROM coa_test_data WHERE processid='$premixid'");
   		while($row2=$result2->fetch_assoc())
   		{
   			$allapproved[$row2['param']] = $row2['value'];
   		}
   }

   

   $alltestData = [];
   $allheader = ["Test Id"];
   $allaverage = [];

   $allphysical = [];
   $allchemical = [];

   $result = runQuery("SELECT param FROM `premix_batch_testparams` WHERE premixid='$premixid' GROUP by param");

   while($row = $result->fetch_assoc())
   {
   	array_push($allheader,$row['param']);

   	$allaverage[$row['param']] = [];

   	$allchemical[$row['param']] = [];
   	$allchemical[$row['param']]['min'] = "";
   	$allchemical[$row['param']]['max'] = "";
   	$allchemical[$row['param']]['mpif'] = "";
   	$allchemical[$row['param']]['value'] = "";
   }

   $result = runQuery("SELECT * FROM `premix_batch_testparams` WHERE premixid='$premixid'");

   while($row = $result->fetch_assoc())
   {
   		if(!isset($alltestData[$row['testid']]))
   		{
   			$alltestData[$row['testid']] =[];

   		}

   		$alltestData[$row['testid']][$row['param']] = $row['value'];

   		array_push($allaverage[$row['param']],$row['value']);
   }

   $result = runQuery("SELECT * FROM premix_batch WHERE premixid='$premixid'")->fetch_assoc();
   $gradename = $result['gradename'];
   $proddate = $result['entrydate'];
   $result = runQuery("SELECT * FROM premix_grade_physical WHERE gradename = '$gradename'");

   while($row=$result->fetch_assoc())
   {
   		$allphysical[$row['parameter']] = [];
   		$allphysical[$row['parameter']]['min'] = $row['min'];
   		$allphysical[$row['parameter']]['mpif'] = $row['mpif'];
   		$allphysical[$row['parameter']]['max'] = $row['max'];
   		$allphysical[$row['parameter']]['value'] = "";

   		if(isset($allchemical[$row['parameter']])) {unset($allchemical[$row['parameter']]);}
   }

   foreach ($allchemical as $param => $paramarray) {
   		$result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$gradename' AND additive='$param'");
   		
   		if($result->num_rows==1)
   		{
   			$result = $result->fetch_assoc();
   			$allchemical[$param]['min'] = $result['mintol'];
   			$allchemical[$param]['max'] = $result['maxtol'];

   		}
   }

   
   foreach ($allaverage as $param => $value) {
   		
   		if(count($value)>=0)
   		{
   			$allaverage[$param] = array_sum($value)/count($value);
   		}
   		else
   		{
   			$allaverage[$param] = "-";
   		}

   		if (array_key_exists($param,$allchemical)) {
   			$allchemical[$param]['value'] =  $allaverage[$param];
   		}
   		else{
   			$allphysical[$param]['value'] =  $allaverage[$param];
   		}
   }





    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>




<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>Pending Approval (<?php echo $premixid; ?>)</h5>
					<span>Approve COA</span>
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
	
	$result = runQuery("SELECT * FROM premix_prodcode WHERE premixid='$premixid'")->fetch_assoc();



?>

<table class="table table-bordered">
	<tr>
		<th>Batch Number: <?php echo $result['batchnumber']; ?></th>
		<th>Production Date: <?php echo Date('d-M-Y',strtotime($proddate)); ?></th>
		<th>Final Production Quantity: <?php echo $result['finalqty']; ?> kg</th>

	</tr>
</table>

<table class="table table-striped">
	<thead>
		<tr>
			<?php 

				foreach ($allheader as $header) {
					echo "<th>".$header."</th>";
				}

			?>
		</tr>
	</thead>
	<tbody>
		<?php 

				foreach ($alltestData as $key => $value) {
					
					echo "<tr><td>".$key."</td>";

					foreach ($allheader as $header) {

						if($header !="Test Id" && isset($value[$header]))
						{
							echo "<td>".$value[$header]."</td>";
						}
						elseif($header !="Test Id")
						{
							echo "<td>-</td>";
						}
						
					}

					echo "</tr>";

				}

				echo "<tr style='border-top: solid 2px; font-weight:bold'><td>Average</td>";

				foreach ($allheader as $header) {

					if($header =="Test Id") {continue;}
					echo "<td>".$allaverage[$header]."</td>";
				}


				echo "</tr>"
		?>




	</tbody>
	
</table>
<br>
<br>

	<form method="POST" id="approvalform">



		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Property</th>
						<th>Standard</th>
						<th>Min</th>
						<th>Max</th>
						<th>Observation</th>
						<th>Final COA Value</th>
					</tr>

				</thead>

				<tbody>
					<tr>
						<th colspan="6" style="text-align:center; font-weight: bold; border-top: 2px solid;">Physical Properties</th>
					</tr>

					<?php 

						foreach ($allphysical as $param => $paramarray) {
							
						

					?>

					<tr>
						<td><?php echo $param; ?></td>
						<td><?php echo $paramarray['mpif']; ?></td>
						<td><?php echo $paramarray['min']; ?></td>
						<td><?php echo $paramarray['max']; ?></td>
						<td><?php echo $paramarray['value']; ?></td>
						<td><input <?php if($isapproved){echo "readonly";} ?>  required type="number" name="allvalues[]" value="<?php if($isapproved){echo $allapproved[$param];}else{echo $paramarray['value'];} ?>">
							<input type="hidden" name="allmpif[]" value="<?php echo $paramarray['mpif']; ?>">
							<input type="hidden" name="allmin[]" value="<?php echo $paramarray['min']; ?>">
							<input type="hidden" name="allmax[]" value="<?php echo $paramarray['max']; ?>">
							<input type="hidden" name="allparam[]" value="<?php echo $param; ?>">
							<input type="hidden" name="type[]" value="Physical">

						</td>
					</tr>

					<?php 
						}
					?>


					<tr>
						<th colspan="6" style="text-align:center; font-weight: bold; border-top: 2px solid;">Chemical Properties</th>
					</tr>


					<?php 

						foreach ($allchemical as $param => $paramarray) {
							
						

					?>

					<tr>
						<td><?php echo $param; ?></td>
						<td><?php echo $paramarray['mpif']; ?></td>
						<td><?php echo $paramarray['min']; ?></td>
						<td><?php echo $paramarray['max']; ?></td>
						<td><?php echo $paramarray['value']; ?></td>
						<td><input <?php if($isapproved){echo "readonly";} ?> required type="number" name="allvalues[]" value="<?php echo $paramarray['value']; ?>">
							<input type="hidden" name="allmpif[]" value="<?php echo $paramarray['mpif']; ?>">
							<input type="hidden" name="allmin[]" value="<?php echo $paramarray['min']; ?>">
							<input type="hidden" name="allmax[]" value="<?php echo $paramarray['max']; ?>">
							<input type="hidden" name="allparam[]" value="<?php echo $param; ?>">
							<input type="hidden" name="type[]" value="Chemical">

						</td>
					</tr>

					<?php 
						}
					?>
				</tbody>


			</table>
			
		</div>
		
		

		<?php 

			if($isapproved)
			{
				echo "<big>Approved by ". getFullName($approvedby). " on ". $approveddate."</big>";	
			}
			else
			{

		?>

		<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
		<input type="hidden" name="approvecoa" value="">


		<div class="col-sm-12">
		<button type="button" class="btn btn-primary m-b-0 pull-right" onclick="approvealert()"><i class="feather icon-check"></i>Approve Test</button>
		</div>


		<?php 

			}
		?>


	</form>

	<script type="text/javascript">
		function approvealert()
		{
			Swal.fire({
		  icon: 'info',
		  title: 'Approve COA',
		  html: 'Are you sure you want to approve these test.',
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			  	document.getElementById('approvalform').submit();
			  	return true;
			  }
			  else
			  {
			  	return false;
			  }
			});
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