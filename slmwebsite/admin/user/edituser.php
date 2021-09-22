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

	if(!isset($_POST["userid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

	$userid = $_POST["userid"];

    $result = runQuery("SELECT * FROM userdetails WHERE userid = '$userid'");
    if($result->num_rows==0)
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $userdetails = $result->fetch_assoc();

	if(isset($_POST["updateUser"]))
	{
		
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$role = $_POST["userrole"];



		$newuser = new user($userid);
		$newuser->setAll($fname,$lname,$role);
		runQuery("DELETE FROM ACL WHERE userid='$userid'");
		if($newuser->updateDB())
		{
			$newuser->setPermission();
			$show_alert = true;
			$alert = showAlert("success","User Details Updated","");
			
			$session->user->addLog("User Details Updated (Userid: ".$userid." Role: ".$role.")");
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Error. Try again.");
		}

	}

	if(isset($_POST["togglePermission"]))
	{
		
		if($_POST["permission"]=="ALLOW")
		{
			$dumModule = $_POST["module"];
			runQuery("UPDATE ACL SET permission='DENY' WHERE userid='$userid' AND module='$dumModule'");
			$session->user->addLog("User Permission Updated (Userid: ".$userid." Module: ".$dumModule." - Changed from Allow to Deny)");
		}
		else
		{
			$dumModule = $_POST["module"];
			runQuery("UPDATE ACL SET permission='ALLOW' WHERE userid='$userid' AND module='$dumModule'");
			$session->user->addLog("User Permission Updated (Userid: ".$userid." Module: ".$dumModule." - Changed from Deny to Allow)");
		}

	}


	if(isset($_POST["resetPassword"]))
	{
		
		$dumuser = new user($userid);
		$newpass = $dumuser->setDefaultPassword();

		$alert = showAlert("success","Password Resetted","The new password is ".$newpass);

		$show_alert = true;
		$session->user->addLog("User Password Resetted (Userid: ".$userid.")");
	}

	$result = runQuery("SELECT * FROM userdetails WHERE userid = '$userid'");
    

    $userdetails = $result->fetch_assoc();

    $PAGE = [
        "Page Title" => "SLM | Edit Users",
        "Home Link"  => "/admin/",
        "Menu"		 => "user-showall",
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
				<i class="feather icon-user bg-c-blue"></i>
				<div class="d-inline">
					<h2>Edit</h2>
					<span>Edit User details and Permissions</span>
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

<div class="col-md-6">
<div class="card">
<div class="card-header">
<h5>User Details</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

	<form id="main" method="post">

		<div class="form-group row">
			<label class="col-md-4 col-form-label">User Id</label>
			<div class="col-md-8">
			<input type="text" readonly required class="form-control" name="userid" id="userid" placeholder="" value="<?php echo $userdetails["userid"]?>">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-md-4 col-form-label">First Name</label>
			<div class="col-md-8">
			<input type="text" required class="form-control" name="fname" id="fname" placeholder="" value="<?php echo $userdetails["fname"]?>">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-md-4 col-form-label">Last Name</label>
			<div class="col-md-8">
			<input type="text" required class="form-control" name="lname" id="lname" placeholder="" value="<?php echo $userdetails["lname"]?>">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
			<label class="col-md-4 col-form-label">Role</label>
			<div class="col-md-8">
				
				<select required name="userrole" id="userrole" class="form-control form-control-primary fill">
				<?php
						$result = runQuery("SELECT * FROM roles");
						if($result->num_rows>0)
						{
							while($row=$result->fetch_assoc())
							{
								echo "<option value=\"".$row["roleid"]."\">" .$row["rolename"]. "</option>";
							}
						}

				?>
				</select>

				<script type="text/javascript">
					document.getElementById("userrole").value = "<?php echo $userdetails["role"]; ?>";

				</script>
			<span class="messages"></span>
			</div>
		</div>

		
		<div class="form-group row">
		<label class="col-md-4"></label>
		<div class="col-md-8">
		<button type="submit" name="updateUser" id="submitBtn" class="btn btn-primary m-b-0"><i class="fa fa-check"></i>Update User</button>
		</div>
		</div>

	</form>


</div>
</div>
</div>


<div class="col-md-6">
	<div class="card">
		<div class="card-header">
		<h5>Permissions</h5>
			<div class="card-header-right">

			</div>
		</div>
		<div class="card-block">


			<table>
				

				<?php

					$result = runQuery("SELECT * FROM ACL LEFT JOIN module ON module.module=ACL.module WHERE userid='$userid'");
					if($result->num_rows>0)
					{
						while($row = $result->fetch_assoc())
						{
							echo "<tr><form method=\"POST\">";
							echo "<td style=\"padding:15px 30px;\">".$row["modulename"]."</td>";
							if($row["permission"]=="ALLOW")
							{
								echo "<td><button style=\"width: 120px;\" class=\"btn btn-primary\"><i class=\"feather icon-check\"></i>ALLOW</button></td>";
							}
							else
							{
								echo "<td><button style=\"width: 120px;\" class=\"btn btn-danger\"><i class=\"feather icon-slash\"></i>DENY</button></td>";
							}
							
							echo "<input type=\"hidden\" name=\"module\" value=\"".$row["module"]."\"><input type=\"hidden\" name=\"permission\" value=\"".$row["permission"]."\"><input type=\"hidden\" name=\"userid\" value=\"".$userid."\"><input type=\"hidden\" name=\"togglePermission\" value=\"\"></form></tr>";
						}
					}

				?>

			</table>


		<?php
			
			

		?>


		</div>
	</div>

	<div class="card">
		<div class="card-header">
		<h5>Reset Your Password</h5>
			<div class="card-header-right">

			</div>
		</div>
		<div class="card-block">
			<form id="resetPasswordForm" method="POST">
				<input type="hidden" name="userid" value="<?php echo $userid; ?>">
				<input type="hidden" name="resetPassword" value="">
			</form>
			<div class="form-group row">
				
				<div class="col-sm-12">
					<button type="button" name="resetPassword" id="resetPassword" class="btn btn-inverse m-b-0" onclick="resetPassword()" ><i class="fa fa-refresh"></i>Reset Password</button>
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
</div>

<script type="text/javascript" src="/pages/js/jquery.min.js"></script>
<script type="text/javascript">
	
	function resetPassword()
	{
		Swal.fire({
			icon: "question",
			title: "Are you sure?",
			html: "Do you want to reset the password?",
			showConfirmButton: true,
		  	showCancelButton: true,
		  	confirmButtonText: 'Yes',
		  	cancelButtonText: 'Cancel',
		}).then((result) => {
			  if (result.isConfirmed) {
			    	document.getElementById("resetPasswordForm").submit();
				}
			})
	}

</script>

<?php
    
    include("../../pages/endbody.php");

?>

