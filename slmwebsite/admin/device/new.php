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

	if(isset($_POST["addDevice"]))
	{
		$devicename = $_POST["devicename"];
		$deviceip = $_POST["deviceip"];
		$devicehostname = $_POST["devicehostname"];
		$type = $_POST["type"];

		$result = runQuery("SELECT * FROM devices WHERE devicename='$devicename'");
		
		if($result->num_rows==0)
		{
			if($type == "SCALE")
			{
				$result = runQuery("INSERT devices VALUES('$devicename','$devicehostname','$deviceip','$type','kg',1)");
			}
			else
			{
				$result = runQuery("INSERT devices VALUES('$devicename','$devicehostname','$deviceip','$type','NULL',0)");
			}
			
		}
		else
		{

			$show_alert = true;
			$alert = showAlert("error","Error","Device name already exist. Try again.");

			
			
		}


		
		
	}

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "device-new",
        "MainMenu"	 => "device_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");
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
				<i class="feather icon-airplay bg-c-blue"></i>
				<div class="d-inline">
					<h5>New Devices</h5>
					<span>Add new devices</span>
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
<h5>Device Details</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST">
	
	
			
			<div class="form-group row">
			<label class="col-sm-2 col-form-label">Device Name</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="devicename" id="devicename" placeholder="">
			<span class="messages"></span>
			</div>
			</div>

			<div class="form-group row">
			<label class="col-sm-2 col-form-label">Device Hostname</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="devicehostname" id="devicehostname" placeholder="">
			<span class="messages"></span>
			</div>
			</div>

			<div class="form-group row">
			<label class="col-sm-2 col-form-label">Device IP</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="deviceip" id="deviceip" placeholder="">
			<span class="messages"></span>
			</div>
			</div>



			<div class="form-group row">
			<label class="col-sm-2 col-form-label">Type</label>
			<div class="col-sm-10">
			<select type="text" required class="form-control form-input" name="type" id="type" placeholder="">
				<?php 

					for($i=0;$i<count($DEVICE_TYPES);$i++)
					{
						echo "<option value=\"".$DEVICE_TYPES[$i]."\">".$DEVICE_TYPES[$i]."</option>";
					}
				?>
			</select>
			</div>
			</div>


			<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="addDevice" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Device</button>
			<span class="messages"></span>
			</div>
			</div>

			
	


</form>

<script type="text/javascript">
	
	
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