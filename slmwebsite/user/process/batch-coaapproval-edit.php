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
        "Page Title" => "View all Final Batches | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "coa-batch",
        "MainMenu"	 => "coa_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


	if(!isset($_POST["processid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $processid = $_POST["processid"];


   if(isset($_POST['approvecoa']))
   {
   	
   	runQuery("DELETE FROM coa_test_data WHERE processid='$processid'");

   	 for ($i=0; $i <count($_POST['allparam']) ; $i++) { 
   	 	
   	 	$dumparam = $_POST['allparam'][$i];
   	 	$dumval = $_POST['allvalues'][$i];
   	 	$dummin = $_POST['allmin'][$i];
   	 	$dummax = $_POST['allmax'][$i];
   	 	$dumtype = $_POST['type'][$i];
   	 	$dummpif = $_POST['allmpif'][$i];


 		runQuery("INSERT INTO coa_test_data VALUES(NULL,'$processid','$dumparam','$dummpif','$dummin','$dummax','$dumval','$dumtype')");
 	
 	}
 	 runQuery("INSERT INTO batch_coa_approval VALUES('$processid','$myuserid',CURRENT_TIMESTAMP)");


   }


  


   $isapproved = false;
   $approvedby ="";


   $alltestData = [];
   $allpheader = ["Test Id"];
   $allsheader = ["Test Id"];
   $allaverage = [];
   $propdata =[];

   $result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='$GRADE_TITLE'");

   if($result->num_rows==0)
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "No grade found";
    	include("../../pages/error.php");
    	die();

    }
    else
    {
    	$gradename = $result->fetch_assoc()['value'];
    }




   
    $allapproved = [];

   $result = runQuery("SELECT * FROM batch_coa_approval WHERE processid='$processid'");
   if($result->num_rows==1)
   {
   		$result = $result->fetch_assoc();
   		$approvedby = $result['approvedby'];
   		$approveddate = $result['approvaldate'];
   		$isapproved = true;

   		$result2 = runQuery("SELECT * FROM coa_test_data WHERE processid='$processid'");
   		while($row2=$result2->fetch_assoc())
   		{
   			$allapproved[$row2['param']] = $row2['value'];
   		}
   }


   		$result = runQuery("SELECT * FROM final_coa_grade_settings  WHERE gradename='$gradename' ORDER BY ordering");

		   while($row = $result->fetch_assoc())
		   {

		   	$allaverage[$row['property']] = [];
		   	$propdata[$row['property']] =[];
		   	if($row['type']=='Sieve')
		   	{
		   		
		   		array_push($allsheader,$row['property']);
		   	}
		   	else
		   	{
		   		
		   		array_push($allpheader,$row['property']);
		   	}

		   }


		   $result = runQuery("SELECT * FROM processtest WHERE processid='$processid' AND processname='Batch'");

		   while($row = $result->fetch_assoc())
		   {
		   		$testid = $row['testid'];
		   		$alltestData[$testid] =[];

		   		foreach ($allpheader as $value) {

		   			if($value == "Test Id"){continue;}
		   			$alltestData[$testid][$value] =[];

		   		}

		   		foreach ($allsheader as $value) {
		   			
		   			if($value == "Test Id"){continue;}
		   			$alltestData[$testid][$value] ="-";

		   		}
		   		
		   		$result2 = runQuery("SELECT * FROM processtestparams WHERE testid='$testid'");

		   		while($row2=$result2->fetch_assoc())
		   		{
		   			$alltestData[$testid][$row2['param']]  =  $row2['value'];
		   			array_push($allaverage[$row2['param']],$row2['value']);
		   			
		   		}
		   }

		   foreach ($allaverage as $key => $value) {
		   		
		   		if(count($value)>0)
		   		{
					$allaverage[$key] = array_sum($value)/count($value);
		   		}
		   		else
		   		{
		   			$allaverage[$key] =  "-";
		   		}
		   }

   



   foreach ($propdata as $key => $value) {
   		
   		$result = runQuery("SELECT * FROM gradeproperties WHERE gradename='$gradename' AND properties='$key'")->fetch_assoc();
   		$propdata[$key]['min'] = $result['min'];
   		$propdata[$key]['max'] = $result['max'];

   		$result = runQuery("SELECT * FROM processgradesproperties WHERE  gradeparam='$key'");

   		$propdata[$key]['class'] = "";
   		if($result->num_rows==1)
   		{
   			$propdata[$key]['class'] = $result->fetch_assoc()['class'];
   		}
   		
   		


   		

   		if(substr($key,0,5)=="Sieve")
   		{
   			$propdata[$key]['mpif'] = "MPIF- 05";
   		}
   		else
   		{
   			$result = runQuery("SELECT * FROM processgradesproperties WHERE processname='Final Blend' AND gradeparam='$key'")->fetch_assoc();
   			if($result)
   			{
   				$propdata[$key]['mpif'] = $result['mpif'];
   			}
   			else
   			{
   				$propdata[$key]['mpif'] = "";
   			}
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
					<h5>Pending Approval (<?php echo $processid; ?>)</h5>
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
	
	$result = runQuery("SELECT * FROM processentry WHERE processid='$processid'")->fetch_assoc();

	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='$MASS_TITLE'")->fetch_assoc();



?>

<table class="table table-bordered">
	<tr>
		<th>Processid: <?php echo $processid; ?></th>
		<th>Production Date: <?php echo Date('d-M-Y',strtotime($result['entrytime'])); ?></th>
		<th>Final Production Quantity: <?php echo $result2['value']; ?> kg</th>
		<th>Grade: <?php echo $gradename; ?></th>

	</tr>
</table>

<br><br>
<h4>All Properties</h4>
<br>
<table class="table table-striped">
	<thead>
		<tr>
			<?php 

				foreach ($allpheader as $header) {
					echo "<th>".$header."</th>";
				}

			?>
		</tr>
	</thead>
	<tbody>
		
		<?php 

			foreach ($alltestData as $ctestid => $cdata) {
				
				echo "<tr>";

				echo "<td>".$ctestid."</td>";
			

				foreach ($allpheader as $header) {

					if($header == "Test Id"){continue;}
					echo "<th>".$cdata[$header]."</th>";
				}

				echo "</tr>";

			}

		?>

		<tr style="border-top:2px solid;">
			<td>Average</td>

			<?php

				foreach ($allpheader as $header) {

					if($header == "Test Id"){continue;}
					echo "<th>".$allaverage[$header]."</th>";
				}



			?>
		</tr>

	</tbody>
	
</table>


<br><br>
<h4>Sieve Properties</h4>
<br>
<table class="table table-striped">
	<thead>
		<tr>
			<?php 

				foreach ($allsheader as $header) {
					echo "<th>".$header."</th>";
				}

			?>
		</tr>
	</thead>
	<tbody>
		
		<?php 

			foreach ($alltestData as $ctestid => $cdata) {
				
				echo "<tr>";

				echo "<td>".$ctestid."</td>";
			

				foreach ($allsheader as $header) {

					if($header == "Test Id"){continue;}
					echo "<th>".$cdata[$header]."</th>";
				}

				echo "</tr>";

			}

		?>

		<tr style="border-top:2px solid;">
			<td>Average</td>

			<?php

				foreach ($allsheader as $header) {

					if($header == "Test Id"){continue;}
					echo "<th>".$allaverage[$header]."</th>";
				}



			?>
		</tr>

	</tbody>
	
</table>





<br>
<br>

	<form method="POST" id="approvalform">



	<h4>Final COA Values</h4>
	<br>
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
				<th colspan="6" style="text-align:center; font-weight: bold; border-top: 2px solid;">All Properties</th>
			</tr>

			<?php

				foreach ($allpheader as $header) {


					if($header == "Test Id"){continue;}
					$step = 0.01;
					$round = 2;
					if($propdata[$header]['class']=="Chemical")
					{
						$step = 0.001;
						$round = 3;
					}



			?>

				<tr>
					<td><?php echo $header; ?></td>
					<td><?php echo $propdata[$header]['mpif']; ?></td>
					<td><?php echo $propdata[$header]['min']; ?></td>
					<td><?php echo $propdata[$header]['max']; ?></td>
					<td><?php echo round($allaverage[$header],$round) ?></td>
					<td><input <?php if($isapproved){echo "readonly";} ?> required step="<?php echo $step; ?>" type="number" name="allvalues[]" value="<?php if(!$isapproved){echo round($allaverage[$header],$round);}else{echo $allapproved[$header];} ?>">
							<input type="hidden" name="allmpif[]" value="<?php echo $propdata[$header]['max']; ?>">
							<input type="hidden" name="allmin[]" value="<?php echo $propdata[$header]['min']; ?>">
							<input type="hidden" name="allmax[]" value="<?php echo $propdata[$header]['max']; ?>">
							<input type="hidden" name="allparam[]" value="<?php echo $header; ?>">
							<input type="hidden" name="type[]" value="Property">
						</td>

				</tr>
			
			<?php

					
				}

			?>

			<?php

				foreach ($allsheader as $header) {

					if($header == "Test Id"){continue;}


			?>

				<tr>
					<td><?php echo $header; ?></td>
					<td><?php echo $propdata[$header]['mpif']; ?></td>
					<td><?php echo $propdata[$header]['min']; ?></td>
					<td><?php echo $propdata[$header]['max']; ?></td>
					<td><?php echo round($allaverage[$header],2) ?></td>
					<td><input <?php if($isapproved){echo "readonly";} ?> required type="number" step="0.01"  name="allvalues[]" value="<?php echo round($allaverage[$header],2); ?>">
							<input type="hidden" name="allmpif[]" value="<?php echo $propdata[$header]['max']; ?>">
							<input type="hidden" name="allmin[]" value="<?php echo $propdata[$header]['min']; ?>">
							<input type="hidden" name="allmax[]" value="<?php echo $propdata[$header]['max']; ?>">
							<input type="hidden" name="allparam[]" value="<?php echo $header; ?>">
							<input type="hidden" name="type[]" value="Sieve">
						</td>

				</tr>
			
			<?php

					
				}

			?>


		</tbody>


	</table>
		
		

		<?php 

			if($isapproved)
			{
				echo "<big>Approved by ". getFullName($approvedby). " on ". $approveddate."</big>";	
			}
			else
			{

		?>

		<input type="hidden" name="processid" value="<?php echo $processid; ?>">
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

