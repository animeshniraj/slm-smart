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


	if(isset($_POST["addnew"]))
	{
		


		$params = $_POST["Supplier_param"];
		$values = $_POST ["Supplier_val"];
		$ordering = $_POST["Supplier_paramorder"];

		$additives = $_POST["Supplier_additive"];



		$prefix = "S-";
    	$sqlprefix = "S-%";


    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(externalid, '-', -1) AS SIGNED)) max_val FROM external_conn WHERE externalid LIKE '$sqlprefix'");

    	if($result->num_rows==0)
    	{	
    		$count = 1;
    	}
    	else
    	{
    		$lastID = $result->fetch_assoc()["max_val"];
	    	
	    	$count = intval($lastID)+1;
    	}

    	
		$prefix = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);

		

		runQuery("INSERT INTO external_conn VALUES('$prefix','$external_type')");


		for($i=0;$i<count($params);$i++)
		{
			$currp = $params[$i];
			$currv = $values[$i];
			$curro = $ordering[$i];
			runQuery("INSERT INTO external_param VALUES(NULL,'$prefix','$currp','$currv','$curro')");
		}


		for($i=0;$i<count($additives);$i++)
		{
			$currp = $additives[$i];
			
			runQuery("INSERT INTO external_param VALUES(NULL,'$prefix','Additives','$currp','-1')");
		}

		if($result)
    	{
    			
    			
    				?>
    					<form id="redirectform" method="POST" action="edit.php">
    						<input type="hidden" name="externalid" value="<?php  echo $prefix;?>">
    					</form>
    					<script type="text/javascript">
    						document.getElementById("redirectform").submit();
    					</script>
    				<?php

    			
    			
    	}
	}








	

    $PAGE = [
        "Page Title" => "Add new supplier | SMART SLM",
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
				<i class="feather icon-user-plus bg-c-blue"></i>
				<div class="d-inline">
					<h5>Adding a new <?php echo $external_type; ?></h5>
					<span>Add information for a new <?php echo $external_type; ?></span>
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
<div class="col-lg-8">


<div class="card">
<div class="card-header">
<h5>Provide information for a New Supplier</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST">
	
	
			
			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Supplier Name</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_name" placeholder="">

			<input type="hidden"name="<?php echo $external_type;?>_paramorder[]" value="0">
			<input type="hidden"name="<?php echo $external_type;?>_param[]" value="Name">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Supplier Address</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden"name="<?php echo $external_type;?>_paramorder[]" value="1">
			<input type="hidden"name="<?php echo $external_type;?>_param[]" value="Address">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Email</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden"name="<?php echo $external_type;?>_paramorder[]" value="1">
			<input type="hidden"name="<?php echo $external_type;?>_param[]" value="Email">
			<span class="messages"></span>
			</div>
			</div>



	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Additives</label>
			<div class="col-sm-9">
			<select required class="js-example-basic-multiple form-control" multiple="multiple"  name="<?php echo $external_type;?>_additive[]" >
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
			<button type="submit" name="addnew" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add <?php echo $external_type;?></button>
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

	document.getElementById("<?php echo $PAGE["Menu"] ?>-new").classList.add("active");




</script>
<?php
    
    include("../../../pages/endbody.php");

?>

<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();

  })
</script>