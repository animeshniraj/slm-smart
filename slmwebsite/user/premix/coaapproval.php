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



   if(isset($_POST['searchcoa']))
   	{

   		$processid = $_POST['processid'];

   		$result = runQuery("SELECT * FROM premix_coa_approval WHERE premixid='$processid'");


   		
   		if($result->num_rows==1)
   		{

   			?>

   			<form id="dumform" method="POST" action="coaapproval-edit.php">
				<input type="hidden" name="premixid" value="<?php echo $processid; ?>">
			</form>
			<script type="text/javascript">
				document.getElementById('dumform').submit();
			</script>

   			<?php

   			die();
   		}
   		else
   		{
   			$show_alert = true;
			$alert = showAlert("error","ID does not exists","");
   		}
   		
   	}




    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");


    if($show_alert)
    {
    	echo $alert;
    }


?>




<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>Pending Approval</h5>
					<span>Select premix to approve COA</span>
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


<table class="table table-striped">
	<thead>
		<tr>
			<th>Premix Id</th>
			<th>Prod Code</th>
			<th>Batch Number</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php 

			$result = runQuery("SELECT * FROM premix_prodcode WHERE premixid NOT IN (SELECT premixid FROM premix_coa_approval)");

			while($row = $result->fetch_assoc())
			{


		?>
		<tr>
			<td><?php echo $row['premixid']; ?></td>
			<td><?php echo $row['batchnumber']; ?></td>
			<td><?php echo $row['prodcode']; ?></td>

			<td><form method="POST" action="coaapproval-edit.php">
				<input type="hidden" name="premixid" value="<?php echo $row['premixid']; ?>">
				<button type="submit" class="btn btn-primary">Go to Approval</button>
			</form></td>
		</tr>


		<?php 

		}
	?>
	</tbody>
</table>


	



</div>
</div>




<div class="card">
<div class="card-header">
<h4>Search for Approved COA</h4>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST">
	
	<div class="form-group row">
			<label class="col-sm-2 col-form-label">Premix ID</label>
			<div class="col-sm-10">
			<div class="input-group input-group-button">
				<input required id="processid" name="processid" type="text" class="form-control form-control-uppercase" placeholder="">
				<div class="input-group-append">
				<button class="btn btn-primary" type="submit" name="searchcoa"><i class="feather icon-arrow-up-right"></i>Open</button>
				</div>
			</div>
			
			</div>

		</div>

</form>
	



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