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

	

    $PAGE = [
        "Page Title" => "SLM | Semi Finished Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "processmanager-semifinished",
        "MainMenu"	 => "processmanager_menu",

    ];


    $processname = "Semi Finished";

    $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }


    if(isset($_POST["updateprocess1"]))
    {

    	$permissions = $_POST["creationpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='CREATION'");
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','CREATION','$perm','ALLOW')");
    	}

    	


    	


    }


    if(isset($_POST["updateprocess2"]))
    {

    	$permissions = $_POST["genericpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='GENERIC'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','GENERIC','$perm','ALLOW')");
 
    	}

    	if(isset($_POST["process2-propname"]))
    	{
	    	$propname = $_POST["process2-propname"];
	    	$propvalue = $_POST["process2-propvalue"];
	    	$proptype = $_POST["process2-proptype"];
	    	$proporder = $_POST["process2-proporder"];
	    	$proplock = $_POST["process2-locked"];

	    	runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='GENERIC'");

	    	for($i=0;$i<count($propname);$i++)
	    	{

	    		if($proplock[$i]=="true")
	    		{
	    			runQuery("INSERT INTO processparams VALUES(NULL,'$processname','GENERIC','$propname[$i]','$propvalue[$i]','$proptype[$i]','$proporder[$i]','LOCKED')");
	    		}
	    		else
	    		{
	    			runQuery("INSERT INTO processparams VALUES(NULL,'$processname','GENERIC','$propname[$i]','$propvalue[$i]','$proptype[$i]','$proporder[$i]','UNLOCKED')");
	    		}
	    		
	    		
	    	}
	    }
	    else
	    {
	    	runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='GENERIC'");
	    }

    	


    }

    if(isset($_POST["updateprocess3"]))
    {

    	$permissions = $_POST["operationalpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='OPERATIONAL'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','OPERATIONAL','$perm','ALLOW')");
 
    	}

    	if(isset($_POST["process3-propname"]))
    	{
    		$propname = $_POST["process3-propname"];
	    	$propvalue = $_POST["process3-propvalue"];
	    	$proptype = $_POST["process3-proptype"];
	    	$proporder = $_POST["process3-proporder"];
	    	$proplock = $_POST["process3-locked"];

	    	runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='OPERATIONAL'");

	    	for($i=0;$i<count($propname);$i++)
	    	{
	    		if($proplock[$i]=="true")
	    		{
	    			runQuery("INSERT INTO processparams VALUES(NULL,'$processname','OPERATIONAL','$propname[$i]','$propvalue[$i]','$proptype[$i]','$proporder[$i]','LOCKED')");
	    		}
	    		else
	    		{
	    			runQuery("INSERT INTO processparams VALUES(NULL,'$processname','OPERATIONAL','$propname[$i]','$propvalue[$i]','$proptype[$i]','$proporder[$i]','UNLOCKED')");
	    		}
	    		
	    	}
    	}
    	else
    	{
    		runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='OPERATIONAL'");
    	}	
    	

    }


    if(isset($_POST["updateprocess4"]))
    {

    	$permissions = $_POST["testpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='TEST'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','TEST','$perm','ALLOW')");
 
    	}

    	if(isset($_POST["process4-propname"]))
    	{
    		$propname = $_POST["process4-propname"];
	    	$propvalue = $_POST["process4-propvalue"];
	    	$proptype = $_POST["process4-proptype"];
	    	$proporder = $_POST["process4-proporder"];

	    	runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='TEST'");

	    	for($i=0;$i<count($propname);$i++)
	    	{
	    		
	    		runQuery("INSERT INTO processparams VALUES(NULL,'$processname','TEST','$propname[$i]','$propvalue[$i]','$proptype[$i]','$proporder[$i]','UNLOCKED')");
	    	}
    	}
    	else
    	{
    		runQuery("DELETE FROM processparams WHERE processname='$processname' AND step='TEST'");
    	}	

    }


    if(isset($_POST["updateprocess5"]))
    {

    	$permissions = $_POST["parentpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='PARENT'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','PARENT','$perm','ALLOW')");
 
    	}

    }


    if(isset($_POST["updateprocess6"]))
    {

    	$permissions = $_POST["stockpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='STOCK'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','STOCK','$perm','ALLOW')");
 
    	}

    }

    if(isset($_POST["updateprocess7"]))
    {

    	$permissions = $_POST["deletionpermission"];
    	array_push($permissions,"ADMIN");
    	runQuery("DELETE FROM processpermission WHERE processname='$processname' AND step='DELETION'");
    	
    	foreach ($permissions as $perm) {
    		
    		runQuery("INSERT INTO processpermission VALUES(NULL,'$processname','DELETION','$perm','ALLOW')");
 
    	}

    }


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");





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
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h2>Semi Finished</h2>
					<span>Edit Semi Finished parameters</span>
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
<div class="col-md-12">



<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">



<ul class="nav nav-tabs md-tabs " role="tablist" id="tablist">
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i> Creation</a>
<div class="slide"></div>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#generic-tabdiv" role="tab"><i class="icofont icofont-ui-file "></i> Generic</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#operational-tabdiv" role="tab"><i class="icofont icofont-speed-meter"></i> Operational Parameter</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i> Test Properties</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#parentprocess-tabdiv" role="tab"><i class="icofont icofont-link"></i> Previous Process</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#stock-tabdiv" role="tab"><i class="icofont icofont-page"></i> Stock/Inventory</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#delete-tabdiv" role="tab"><i class="icofont icofont-trash"></i> Deletion</a>
<div class="slide"></div>
</li>



</ul>

<div class="tab-content card-block">

<div class="tab-pane" id="creation-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="creation-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="creationpermission" name="creationpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>

		</div>
		

		<br><br>
		

			

	


	<br><br>

	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>


</div>

<div class="tab-pane" id="generic-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="generic-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="genericpermission" name="genericpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>


	<div id = "process2-propdiv">



		<?php 
			$result = runQuery("SELECT * FROM processparams WHERE processname='$processname' AND step='GENERIC' ORDER by ordering");

			if($result->num_rows>0)
			{

				while($row = $result->fetch_assoc())
				{

					
					$readonly = "";



					if($row["islocked"]=="LOCKED")
					{
						$readonly = "readonly";
					}


				
		?>
		<div class="form-group row">
			<?php
				if($row["param"]==$MASS_TITLE)
				{
					?>
						<input type="hidden" name="process2-propname[]" value="<?php echo $MASS_TITLE; ?>">
						<input type="hidden" name="process2-propvalue[]" value="">
						<input type="hidden" name="process2-proptype[]" value="DECIMAL">
						<input type="hidden" name="process2-proporder[]" value="0">
						<input type="hidden" name="process2-locked[]" value="true">

					<?php
				}

				else
				{


			?>
			<div class="col-md-2">
			<input  <?php echo $readonly; ?>  required type="text" required class="form-control" name="process2-propname[]"  placeholder="Property Name" value="<?php echo $row["param"]; ?>">
			<span class="messages"></span>
			</div>

			

			<div class="col-md-2">
			<input  <?php echo $readonly; ?>  type="text" class="form-control" name="process2-propvalue[]" placeholder="Allowed Values" value="<?php echo $row["allowedvalues"]; ?>">
			<span class="messages"></span>
			</div>
			
			<div class="col-md-2">
				<select  <?php echo $readonly; ?>   required class="form-control" name="process2-proptype[]" id = "process2-proptype-<?php echo $row["param"]; ?>" onready="changeSelect(this,'<?php echo $row["type"]; ?>')" >

					
						<option value="INTEGER">INTEGER</option>
						<option value="DECIMAL">DECIMAL</option>
						<option value="STRING">STRING</option>
						<option value="DATE">DATE</option>
						<option value="TIME">TIME</option>
						<option value="DATE TIME">DATE TIME</option>

						<script type="text/javascript">
							
							changeSelect(document.getElementById("process2-proptype-<?php echo $row["param"]; ?>"),'<?php echo $row["type"]; ?>')

						</script>

						
				</select>

				
			</div>

			<?php
					if($row["islocked"]=="UNLOCKED")
					{


			?>
				<input type="hidden" name="process2-locked[]" value="false">

				<div class="col-md-1">
				<input required type="text" class="form-control" name="process2-proporder[]" placeholder="Ordering" value="<?php echo $row["ordering"]; ?>">
				<span class="messages"></span>
			</div>
			
			<div class="col-md-2">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0"><i class="fa fa-trash"></i>Remove</button>
			</div>

			<?php
					}
					else
					{


			?>

				<input type="hidden" name="process2-locked[]" value="true">
				<div class="col-md-1">
				<input required type="text" class="form-control" name="process2-proporder[]" placeholder="Ordering" value="<?php echo $row["ordering"]; ?>">
				<span class="messages"></span>
			</div>
			
			


			<?php
				}
			?>

			

			<?php
				}
					
			?>
			
		</div>

		<?php

			}
			}

		?>

	</div>

	<div class="form-group row">
		
		<div class="col-md-10">
		<button type="button" onclick="addpropFn('process2')" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add Properties</button>
		</div>
	</div>


	<br><br>


	<div class="form-group row">
		<label class="col-md-2"></label>
		<div class="col-md-10">
		<button type="submit" name="updateprocess2" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>

<div class="tab-pane" id="operational-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="operational-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="operationalpermission" name="operationalpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>


		<div id = "process3-propdiv">

		<?php 
			$result = runQuery("SELECT * FROM processparams WHERE processname='$processname' AND step='OPERATIONAL' ORDER by ordering");

			if($result->num_rows>0)
			{

				while($row = $result->fetch_assoc())
				{


					$readonly = "";

					if($row["islocked"]=="LOCKED")
					{
						$readonly = "readonly";
					}

		?>

		<div class="form-group row">
			<?php
				if($row["param"]==$GRADE_TITLE)
				{
					?>
						<input type="hidden" name="process3-propname[]" value="<?php echo $GRADE_TITLE; ?>">
						<input type="hidden" name="process3-propvalue[]" value="">
						<input type="hidden" name="process3-proptype[]" value="STRING">
						<input type="hidden" name="process3-proporder[]" value="0">
						<input type="hidden" name="process3-locked[]" value="true">
						

					<?php
				}

				else
				{


			?>
			<div class="col-md-2">
			<input <?php echo $readonly; ?>  required type="text" required class="form-control" name="process3-propname[]"  placeholder="Property Name" value="<?php echo $row["param"]; ?>">
			<span class="messages"></span>
			</div>

			<div class="col-md-2">
			<input <?php echo $readonly; ?>  type="text" class="form-control" name="process3-propvalue[]" placeholder="Allowed Values" value="<?php echo $row["allowedvalues"]; ?>">
			<span class="messages"></span>
			</div>
			<div class="col-md-2">
				<select <?php echo $readonly; ?>  required class="form-control" name="process3-proptype[]" id = "process3-proptype-<?php echo $row["param"]; ?>" onready="changeSelect(this,'<?php echo $row["type"]; ?>')" >
						<option value="INTEGER">INTEGER</option>
						<option value="DECIMAL">DECIMAL</option>
						<option value="STRING">STRING</option>
						<option value="DATE">DATE</option>
						<option value="TIME">TIME</option>
						<option value="DATE TIME">DATE TIME</option>

						<script type="text/javascript">
							
							changeSelect(document.getElementById("process3-proptype-<?php echo $row["param"]; ?>"),'<?php echo $row["type"]; ?>')

						</script>

						
				</select>

				
			</div>


			<?php
				if($row["islocked"]=="UNLOCKED")
				{


			?>

			<input type="hidden" name="process3-locked[]" value="false">
			<div class="col-md-1">
				<input required type="text" class="form-control" name="process3-proporder[]" placeholder="Ordering" value="<?php echo $row["ordering"]; ?>">
				<span class="messages"></span>
			</div>

			<div class="col-md-2">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0"><i class="fa fa-trash"></i>Remove</button>
			</div>

			<?php
				}
				else
				{


			?>

			<input type="hidden" name="process3-locked[]" value="true">
			<div class="col-md-1">
				<input required type="text" class="form-control" name="process3-proporder[]" placeholder="Ordering" value="<?php echo $row["ordering"]; ?>">
				<span class="messages"></span>
			</div>

			

			<?php
				}
			?>
			
			<?php 
					}
			?>
			
		</div>

		<?php

			}
			}

		?>

	</div>
	<div class="form-group row">
		
		<div class="col-md-10">
		<button type="button" onclick="addpropFn('process3')" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add Properties</button>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-md-2"></label>
		<div class="col-md-10">
		<button type="submit" name="updateprocess3" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>


</div>


<div class="tab-pane" id="test-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="test-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="testpermission" name="testpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>


		



	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateprocess4" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>


<div class="tab-pane" id="parentprocess-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="parentprocess-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="parentpermission" name="parentpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateprocess5" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>


<div class="tab-pane" id="stock-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="stock-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">User Permissions</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="stockpermission" name="stockpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateprocess6" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>


<div class="tab-pane" id="delete-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="currtab" value="delete-tabdiv">
	<div class="form-group row">
			<label class="col-md-3 col-form-label">Permission</label>
			<div class="col-md-6">
			<select required class="js-example-basic-multiple col-sm-10" multiple="multiple" id="deletionpermission" name="deletionpermission[]" >
					<?php
						$result = runQuery("SELECT * FROM roles WHERE rolename<>'ADMIN'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">".$row["rolename"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateprocess7" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>





</div></div>









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


<?php

	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='CREATION'");

	$creationPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$creationPermission = $creationPermission."\"".$row["role"]."\",";
		}
	}
	$creationPermission = $creationPermission."]";


	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='GENERIC'");

	$genericPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$genericPermission = $genericPermission."\"".$row["role"]."\",";
		}
	}
	$genericPermission = $genericPermission."]";


	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='OPERATIONAL'");

	$operationalPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$operationalPermission = $operationalPermission."\"".$row["role"]."\",";
		}
	}
	$operationalPermission = $operationalPermission."]";





	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='TEST'");

	$testPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$testPermission = $testPermission."\"".$row["role"]."\",";
		}
	}
	$testPermission = $testPermission."]";



	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='PARENT'");

	$parentPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$parentPermission = $parentPermission."\"".$row["role"]."\",";
		}
	}
	$parentPermission = $parentPermission."]";



	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='STOCK'");

	$stockPermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$stockPermission = $stockPermission."\"".$row["role"]."\",";
		}
	}
	$stockPermission = $stockPermission."]";


	$result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND permission='ALLOW' AND step='DELETION'");

	$deletePermission = "[";
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{
			$deletePermission = $deletePermission."\"".$row["role"]."\",";
		}
	}
	$deletePermission = $deletePermission."]";

?>


$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	

  	$('#creationpermission').val(<?php echo $creationPermission; ?>).trigger('change');
  	$('#genericpermission').val(<?php echo $genericPermission; ?>).trigger('change');
  	$('#operationalpermission').val(<?php echo $operationalPermission; ?>).trigger('change');
  	$('#testpermission').val(<?php echo $testPermission; ?>).trigger('change');
  	$('#parentpermission').val(<?php echo $parentPermission; ?>).trigger('change');
  	$('#stockpermission').val(<?php echo $stockPermission; ?>).trigger('change');
  	$('#deletionpermission').val(<?php echo $deletePermission; ?>).trigger('change');


  	var currTab = "<?php echo $currTab; ?>";
  	
  	document.getElementById(currTab).classList.add('active');

  	var tabs = document.getElementById("tablist");
  	console.log(tabid);
  	for(var i=0;i<tabs.children.length;i++)
  	{
  		var tabid = ("#"+currTab);
  		
  		if(tabs.children[i].children[0].getAttribute("href")== tabid)
  		{
  			tabs.children[i].children[0].classList.add("active");
  		}
  	}


  // Creation

  	

  		

  	

});





function addpropFn(step)
{
	
	if(step=="furnace")
	{
		propdiv = document.getElementById(step+'-propdiv');

		newDiv = document.createElement("div");
		newDiv.innerHTML = "<div class=\"form-group row\">\n<div class=\"col-sm-4\">\n<input required type=\"text\" required class=\"form-control\" name=\"furnace-name[]\"  placeholder=\"Furnace Name\" value=\"\">\n<span class=\"messages\"></span>\n</div>\n<div class=\"col-sm-2\">\n<input required type=\"text\" class=\"form-control\" name=\"furnace-prefix[]\" placeholder=\"Prefix\" value=\"\">\n<span class=\"messages\"></span>\n</div>\n<div class=\"col-sm-4\">\n<input type=\"text\" class=\"form-control\" name=\"furnace-specification[]\" placeholder=\"Ordering\" value=\"\">\n<span class=\"messages\"></span>\n</div>\n<div class=\"col-sm-2\">\n<button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button>\n</div>\n</div>";
	}
	else
	{
		propdiv = document.getElementById(step+'-propdiv');

		newDiv = document.createElement("div");
		newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-sm-3\"><input required type=\"text\" required class=\"form-control\" name=\""+ step +"-propname[]\"  placeholder=\"Property Name\"><span class=\"messages\"></span></div><div class=\"col-sm-4\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-propvalue[]\" placeholder=\"Allowed Values\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><select required class=\"form-control\" name=\""+ step +"-proptype[]\" ><option value=\"INTEGER\">INTEGER</option><option value=\"DECIMAL\">DECIMAL</option><option value=\"STRING\">STRING</option><option value=\"DATE\">DATE</option><option value=\"TIME\">TIME</option><option value=\"DATE TIME\">DATE TIME</option></select></div><div class=\"col-sm-1\"><input required type=\"text\" class=\"form-control\" name=\""+ step +"-proporder[]\" placeholder=\"Ordering\"><span class=\"messages\"></span></div><input type=\"hidden\" name=\""+step+"-locked[]\" value=\"false\"><div class=\"col-sm-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div></div>";
	}
	
	propdiv.appendChild(newDiv);
}



</script>