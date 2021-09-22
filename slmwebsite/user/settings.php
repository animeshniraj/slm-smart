<?php
    
	require_once('../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'user_module');


	$myuserid = $session->user->getUserid();

	if(isset($_POST["updateUser"]))
	{
		
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		



		$newuser = new user($myuserid);
		$newuser->pullInfo();
		$newuser->setFname($fname);
		$newuser->setLname($lname);
		
		if($newuser->updateDB())
		{
			
			$show_alert = true;
			$alert = showAlert("success","User Details Updated","");
			
			$session->user->addLog("User Details Updated (Userid: ".$myuserid.")");
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Error. Try again.");
		}

	}

	if(isset($_POST["changePassword"]))
	{
		$currentpassword = $_POST["currentpassword"];
		$newpassword = $_POST["password"];

		$dumuser = new user($session->user->getUserid());

		if($dumuser->pullByAuth($currentpassword))
		{
			if($dumuser->setPassword($_POST["password"]))
			{
				$show_alert = true;
				$alert = showAlert("success","","Password has been changed");
				$session->user->addLog("Password changed (Userid: ".$session->user->getUserid().")");
			}
			else
			{
				$show_alert = true;
				$alert = showAlert("error","Error","Error. Try again.");
			}
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Current password is invalid");
		}

		
	}


	$result = runQuery("SELECT * FROM userdetails WHERE userid = '$myuserid'");
    

    $userdetails = $result->fetch_assoc();

    $PAGE = [
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "settings_menu",
        "MainMenu"	 => "",

    ];


    include("../pages/userhead.php");
    include("../pages/usermenu.php");

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
				<i class="feather icon-settings bg-c-blue"></i>
				<div class="d-inline">
					<h5>Setting</h5>
					<span>User Settings</span>
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
<h5>User Details</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

	<form id="main" method="post">

		<div class="form-group row">
			<label class="col-sm-2 col-form-label">User Id</label>
			<div class="col-sm-10">
			<input type="text" readonly required class="form-control" name="userid" id="userid" placeholder="" value="<?php echo $userdetails["userid"]?>">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label">First Name</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="fname" id="fname" placeholder="" value="<?php echo $userdetails["fname"]?>">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Last Name</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="lname" id="lname" placeholder="" value="<?php echo $userdetails["lname"]?>">
			<span class="messages"></span>
			</div>
		</div>


		
		<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updateUser" id="submitBtn1" class="btn btn-primary m-b-0"><i class="fa fa-check"></i>Update</button>
		</div>
		</div>

	</form>


</div>
</div>


<div class="card">
<div class="card-header">
<h3>Change Password</h3>
<div class="card-header-right">

</div>
</div>


<div class="card-block">
	<form method="POST">
		<div class="col-sm-3">
			<div class="form-group row">
				<input required type="password" placeholder="Current Password" name="currentpassword" class="form-control">
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group row">
				<input required type="password" placeholder="New Password" name="password" id="password" oninput="checkPassword()" class="form-control">
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group row">
				<input required type="password" placeholder="Confirm Password" name="cpassword" id="cpassword" oninput="checkPassword()" class="form-control">
			</div>
		</div>
		<div class="col-sm-3">
			<p id="pmatcherr" class="text-center text-danger" style="display: none;">Passwords do not match</p>
		</div>
		<div class="col-sm-3">
			
			<div class="form-group row">
				<button type="submit" disabled id="submitBtn" name="changePassword" class="btn btn-primary waves-effect waves-light"><i class="fa fa-check"></i>Change Password</button>
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
	function checkPassword()
    {
        if(document.getElementById("password").value=="" || document.getElementById("cpassword").value=="")
        {
            return false;
        }
        if(document.getElementById("password").value==document.getElementById("cpassword").value)
        {
            document.getElementById("pmatcherr").style.display = 'none';
            document.getElementById("submitBtn").disabled = false;
        }
        else
        {
            document.getElementById("pmatcherr").style.display = 'block';
            document.getElementById("submitBtn").disabled = true;
        }
    }
</script>

<?php
    
    include("../pages/endbody.php");

?>