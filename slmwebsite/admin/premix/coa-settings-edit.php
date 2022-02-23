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

	isAuthenticated($session,'admin_module');

	if(!isset($_POST["gradename"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $gradename = $_POST["gradename"];



    if (isset($_POST["changecomposition"])) {


    	for ($i=0; $i < count($_POST['property']) ; $i++) { 
    		
    		$dprop =  $_POST['property'][$i];
    		$daltname =  $_POST['altname'][$i];
    		$dprint =  $_POST['print'][$i];
    		$ordering = $_POST['ordering'][$i];

    		runQuery("UPDATE premix_coa_grade_settings SET showname='$daltname', print='$dprint', ordering='$ordering' WHERE gradename='$gradename' AND property='$dprop'");

    	}

    }


    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "premix-coa",
        "MainMenu"	 => "premix_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5>COA Settings (<?php echo $gradename; ?>)</h5>
					<span>Edit COA Grades settings</span>
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
<h5>Compositions</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<div class="table-responsive">
<form method="POST">
	<input type="hidden" name="gradename" value="<?php echo $gradename; ?>">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Property</th>
				<th>Alternate Name</th>
				<th>Print?</th>
				<th>Ordering</th>
			</tr>
		</thead>

		<tbody>
			
			<?php 

				$result = runQuery("SELECT * FROM premix_coa_grade_settings WHERE gradename='$gradename' AND type='Chemical' ORDER BY ordering ");

				while($row = $result->fetch_assoc())
				{

			?>

			<tr>
				<td><?php echo $row['property'] ?></td>
				<input type="hidden" name="property[]" value="<?php echo $row['property'] ?>">
				<td><input required class="form-control" type="text" name="altname[]" value="<?php echo $row['showname'] ?>"></td>
				<td><select name="print[]"  class="form-control">

						<option value="1" <?php if($row['print']==1) {echo "selected";} ?>>Yes</option>
						<option value="0" <?php if($row['print']==0) {echo "selected";} ?>>No</option>
						
						
					</select>

				</td>
				<td><input required class="form-control" type="number" min="0"  name="ordering[]" value="<?php echo $row['ordering'] ?>"></td>
			</tr>


			<?php 
				}

			?>

		</tbody>
		
	</table>

	<div class="col-sm-12">
			<button type="submit"  name ="changecomposition" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update Value</button>
			<span class="messages"></span>
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




<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

<div class="page-body">
<div class="row">
<div class="col-lg-12">


<div class="card">
<div class="card-header">
<h5>Physical Properties</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<div class="table-responsive">
<form method="POST">
	<input type="hidden" name="gradename" value="<?php echo $gradename; ?>">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Property</th>
				<th>Alternate Name</th>
				<th>Print?</th>
				<th>Ordering</th>
			</tr>
		</thead>

		<tbody>
			
			<?php 

				$result = runQuery("SELECT * FROM premix_coa_grade_settings WHERE gradename='$gradename' AND type='Physical' ORDER BY ordering ");

				while($row = $result->fetch_assoc())
				{

			?>

			<tr>
				<td><?php echo $row['property'] ?></td>
				<input  type="hidden" name="property[]" value="<?php echo $row['property'] ?>">
				<td><input required class="form-control" type="text" name="altname[]" value="<?php echo $row['showname'] ?>"></td>
				<td><select name="print[]"  class="form-control">

						<option value="1" <?php if($row['print']==1) {echo "selected";} ?>>Yes</option>
						<option value="0" <?php if($row['print']==0) {echo "selected";} ?>>No</option>
						
						
					</select>

				</td>
				<td><input required class="form-control" type="number" min="0" name="ordering[]" value="<?php echo $row['ordering'] ?>"></td>
			</tr>


			<?php 
				}

			?>

		</tbody>
		
	</table>

	<div class="col-sm-12">
			<button type="submit"  name ="changecomposition" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update Value</button>
			<span class="messages"></span>
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




</div>



<?php
    
    include("../../pages/endbody.php");

?>


<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();

  })
</script>


