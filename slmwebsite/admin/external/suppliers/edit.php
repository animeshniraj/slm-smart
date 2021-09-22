<?php
    
	require_once('../../../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'admin_module');

	$external_type = "Supplier";


	if(!isset($_POST["externalid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../../pages/error.php");
    	die();
    }

    $externalid = $_POST["externalid"];
	


	if(isset($_POST["editext"]))
	{


		$params = $_POST["Supplier_param"];
		$values = $_POST ["Supplier_val"];
		$ordering = $_POST["Supplier_paramorder"];

		$additives = $_POST["Supplier_additive"];


		for($i=0;$i<count($params);$i++)
		{
			$currp = $params[$i];
			$currv = $values[$i];

			runQuery("UPDATE external_param SET value='$currv' WHERE externalid='$externalid' AND param='$currp'");
		}

		runQuery("DELETE FROM external_param WHERE param='Additives' AND externalid='$externalid'");

		for($i=0;$i<count($additives);$i++)
		{
			$currp = $additives[$i];
			
			runQuery("INSERT INTO external_param VALUES(NULL,'$externalid','Additives','$currp','-1')");
		}
	
	}

	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$externalid' ORDER BY ordering");
	$allparams = [];
	$alladditives = "[";
	while($row = $result2->fetch_assoc())
	{
		

		if($row["param"]=="Name")
		{
			$currName = $row["value"];
		}

		if($row["ordering"]==-1)
		{
			$dumad = $row["value"];
			$alladditives = $alladditives ."\"$dumad\",";
		}
		else
		{
			array_push($allparams,[$row["param"],$row["value"],$row["ordering"]]);
		}
		
	}

	$alladditives = $alladditives ."]";
	


    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "external-supplier",
        "MainMenu"	 => "external_menu",

    ];


    include("../../../pages/adminhead.php");
    include("../../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5><?php echo $externalid." ($currName) " ?></h5>
					<span>Edit details</span>
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
<h5>Details</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST">
	<input type="hidden"name="externalid" value="<?php echo $externalid; ?>">
	<?php

		foreach ($allparams as $value) {
		

	?>
			
			<div class="form-group row">
			<label class="col-sm-2 col-form-label"><?php echo $value[0] ?></label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_<?php echo $value[0] ?>" placeholder="" value="<?php echo $value[1]; ?>">

			<input type="hidden"name="<?php echo $external_type;?>_paramorder[]" value="<?php echo $value[2]; ?>">
			<input type="hidden"name="<?php echo $external_type;?>_param[]" value="<?php echo $value[0]; ?>">
			<span class="messages"></span>
			</div>
			</div>


	<?php

		}
	?>



	<div class="form-group row">
		<label class="col-sm-2 col-form-label">Additives</label>
			<div class="col-sm-10">
			<select required class="js-example-basic-multiple form-control" multiple="multiple"  name="<?php echo $external_type;?>_additive[]" id="addtives_div">
					<?php
						$result = runQuery("SELECT * FROM premix_additives");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["additive"]."\">".$row["additive"]."</option>";
							}
						}

					?>
			</select>
		</div>
		</div>


		<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="editext" class="btn btn-primary pull-right"><i class="fa fa-edit"></i>Edit <?php echo $external_type;?></button>
			<span class="messages"></span>
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

<script type="text/javascript">
	
	document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

	document.getElementById("<?php echo $PAGE["Menu"] ?>-view").classList.add("active");


</script>
<?php
    
    include("../../../pages/endbody.php");

?>

<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();

  	$('#addtives_div').val(<?php echo $alladditives; ?>).trigger('change');

  })
</script>