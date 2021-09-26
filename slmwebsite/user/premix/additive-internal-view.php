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

    if(isset($_POST["reconciliation_adjustment"]))
    {
    	$id = $_POST["internalid"];
    	$adjustment = round( floatval($_POST["newqty"])-floatval($_POST["qty"]),2);

    	runQuery("INSERT INTO stock_reconciliation VALUES(NULL,'$id',CURRENT_TIMESTAMP,'$adjustment')");

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

    	$data[$curr] = [];

    	

    	$result2 = runQuery("SELECT * FROM additive_internal WHERE additive='$curr' AND status='NOTOVER' ORDER BY entrydate");

    	while($row2=$result2->fetch_assoc())
    	{

    		$currid = $row2["internalid"];
    		$initmass = $row2["mass"];
    		$currmass = $initmass;
    		$entrydate = $row2["entrydate"];

    		$result3 = runQuery("SELECT param,SUM(value) as qty FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$currid' GROUP BY param");

    		while($row3=$result3->fetch_assoc())
    		{
    			$currmass -= $row3["qty"];
    		}

    		$result3 = runQuery("SELECT internalid,SUM(adjustment) as qty FROM stock_reconciliation WHERE internalid='$currid' GROUP BY internalid");

    		while($row3=$result3->fetch_assoc())
    		{
    			$currmass += $row3["qty"];
    		}

    		array_push($data[$curr],[$currid,$initmass,$currmass,$entrydate]);
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


	<?php 
			foreach ($allAdditive as $additive) {
			
		?>

<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th style="text-align: center;" colspan="100%"><?php echo $additive[0]; ?></th>
		</tr>
			
		</tr>
		<tr>
		<th>Sl.No</th>
		<th>Internal Id</th>
		<th>Entry Date</th>
		<th>Total Quantity(kg)</th>
		<th>Balance Quantity(kg)</th>
		<th>Reconciliation (kg)</th>

		</tr>

	</thead>
	<tbody>

		<?php 
			foreach($data[$additive[0]] as $entry)
			{

				$k=1;
		?>


		<tr>
			<td> <?php echo $k++; ?></td>
			<td> <?php echo $entry[0]; ?></td>
			<td> <?php echo $entry[3]; ?></td>
			<td> <?php echo $entry[1]; ?></td>
			<td> <?php echo $entry[2]; ?></td>

			<td>
				<form method="POST">
				<div class="input-group input-group-button">

					<input type="hidden" name="reconciliation_adjustment" value="">	
					<input type="hidden" name="internalid" value="<?php echo $entry[0]; ?>">		
					<input type="hidden" name="qty" value="<?php echo $entry[2]; ?>">				
					<input type="text" name="newqty" class="form-control col-sm-3" value="<?php echo $entry[2]; ?>">
					<button class="btn btn-primary">Adjust</button>

				</div>
				</form>
			</td>

			
		</tr>


		<?php 
			}
		?>
		

	</tbody>
	</table>


</div>

<br>
<hr style=" border: 2px solid;">
<br>
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
  	




});





</script>