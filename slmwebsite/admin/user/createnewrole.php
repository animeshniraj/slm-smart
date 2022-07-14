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


	if(isset($_POST["createrole"]))
	{
		$roleid = $_POST["roleid"];
		$rolename = $_POST["rolename"];
		

		$result = runQuery("INSERT  INTO roles VALUES('$roleid','$rolename')");
		if(!$result)
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Error. Try again.");
		}
		else
		{
			
			runQuery("INSERT INTO defaultpermissions VALUES(NULL,'$roleid','admin_module','DENY')");
			runQuery("INSERT INTO defaultpermissions VALUES(NULL,'$roleid','user_module','ALLOW')");
			header("Location: showallroles.php");		
		}

	}

    $PAGE = [
        "Page Title" => "SLM | Create User Profile",
        "Home Link"  => "/admin/",
        "Menu"		 => "user-createrole",
        "MainMenu"	 => "user_menu",	

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
			<i class="fa fa-user-plus bg-c-blue"></i>
				<div class="d-inline">
					<h2>Create New Role</h2>
					<span>Create a custom role</span>
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


<div class="card col-md-6">
<div class="card-header">
<h5 class="slm-color">Create A New Role</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

	<form id="main" method="post">

		

		<div class="form-group row">
			<label class="col-md-4 col-form-label">Role ID</label>
			<div class="col-md-8">
			<input onkeypress="return event.charCode != 32"  type="text" required class="form-control" name="roleid" id="roleid" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-md-4 col-form-label">Role Name</label>
			<div class="col-md-8">
			<input  type="text" required class="form-control" name="rolename" id="rolename" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		

		
		<div class="form-group row">
		<label class="col-md-4"></label>
		<div class="col-md-8">
		<button  type="submit" name="createrole" id="submitBtn" class="btn btn-primary m-b-0"><i class="fa fa-user-plus"></i> Add Role</button>
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