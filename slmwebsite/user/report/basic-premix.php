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


    $processname = "Premix";


    if(!isset($_GET["id"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $premixid = $_GET["id"];



    $allData = [];


    $result = runQuery("SELECT * FROM premix_batch WHERE premixid='$premixid'");

    if($result->num_rows!=1)
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "Id Not found";
    	include("../../pages/error.php");
    	die();
    }

    $result = $result->fetch_assoc();
    $allData['mass'] = $result['mass'];
    $allData['remaining'] = $result['mass'];
    $allData['grade'] = $result['gradename'];
    $allData['entrydate'] = $result['entrydate'];
    $allData['batch'] = [];
    $allData['feed'] = [];
    $allData["dispatch"]=[];

    $result2 = runQuery("SELECT * FROM dispatch_invoices WHERE batch='$premixid'");



	while($row2=$result2->fetch_assoc())
	{
		$dumRaw = [$row2["cid"],$row2["qty"]];

		array_push($allData["dispatch"],$dumRaw);

		$dum["remaining"] -= $row2["qty"];
	}


    $result = runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' ORDER BY tag");

    while($row=$result->fetch_assoc())
    {
    	if($row['step']=='BATCH SELECTION')
    	{
    		if(!isset($allData['batch'][$row['tag']]))
    		{
    			$allData['batch'][$row['tag']] = [];
    		}

    		array_push($allData['batch'][$row['tag']],[$row['param'],$row['value']]);
    	}
    	else if($row['step']=='FEED SEQUENCE')
    	{
    		array_push($allData['feed'],[$row['param'],$row['value']]);
    	}
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
					<h5>Report - <?php echo $premixid; ?>(<?php echo $processname; ?>)</h5>
					<span>View Premix report</span>
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



<table class="table table-bordered">
	
	<tr>
		<th>Entry Date</th>
		<th><?php echo Date('d-M-Y',strtotime($allData['entrydate'])) ?></th>
	</tr>
	<tr>
		<th>Grade</th>
		<th><?php echo $allData['grade'] ?></th>
	</tr>

	<tr>
		<th>Production Quantity (kg)</th>
		<th><?php echo $allData['mass'] ?></th>
	</tr>

	<tr>
		<th>Remaining Quantity (kg)</th>
		<th><?php echo $allData['remaining'] ?></th>
	</tr>
</table>
<br><hr style="border-top: 3px solid"><br>


<big style="font-weight: bold;">Batches</big>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Additive</th>
			<th>Batch</th>
		</tr>
	</thead>

	<tbody>
		<?php 

		foreach ($allData['batch'] as $additive => $batches) {
			

		?>

		<tr>
			<td><?php echo $additive;?></td>
			<td>
			<?php 
			foreach ($batches as $value) {
				echo $value[0]." (".$value[1]." kg) <br>";
			}
			?>
			</td>
		</tr>
		<?php
		}

		?>
	</tbody>
</table>


<br><hr style="border-top: 3px solid"><br>


<big style="font-weight: bold;">Feed Sequence</big>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Batch</th>
			<th>Quantity (kg)</th>
		</tr>
	</thead>

	<tbody>
		<?php 

		foreach ($allData['feed'] as  $batches) {
			

		?>

		<tr>
			<th><?php echo $batches[0];?></th>
			<td><?php echo $batches[1];?></td>
		</tr>
		<?php
		}

		?>
	</tbody>


</table>


<big style="font-weight: bold;">Dispatch</big>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Consignment ID</th>
			<th>Quantity (kg)</th>
		</tr>
	</thead>

	<tbody>
		<?php 

		foreach ($allData['dispatch'] as  $batches) {
			

		?>

		<tr>
			<th><?php echo $batches[0];?></th>
			<td><?php echo $batches[1];?></td>
		</tr>
		<?php
		}

		?>
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