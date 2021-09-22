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


	if(isset($_POST["createuser"]))
	{
		$userid = $_POST["userid"];
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$role = $_POST["userrole"];

		$newuser = new user($userid);
		if($newuser->setAll($fname,$lname,$role,true))
		{
			$newpass = $newuser->setDefaultPassword();
			$show_alert = true;
			$alert = showAlert("success","User Added","The Default Password is ".$newpass);
			$session->user->addLog("Created new user (Userid: ".$userid." Role: ".$newuser->getRolename().")");
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Error. Try again.");
		}

	}

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "user-createuser",
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
					<h2>Create New Users</h2>
					<span>Create one user or upload file to create multiple.</span>
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
<h5 class="slm-color">Create A New User</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

	<form id="main" method="post">

		<div class="form-group row">
		<label class="col-md-4 col-form-label">User ID/Username</label>
		<div class="col-md-8">

			<div class="input-group input-group-button">
			<input type="text" name="userid" id="userid" required class="form-control" placeholder="">
			<div class="input-group-append">
			<button class="btn btn-primary" onclick="checkUserid();" type="button"><i class="fa fa-refresh"></i>Validate</button>
			
			</div>

			</div>
			<span id="userid_message" class="messages"></span>
		</div>
		</div>

		<div class="form-group row">
			<label class="col-md-4 col-form-label">First Name</label>
			<div class="col-md-8">
			<input disabled type="text" required class="form-control" name="fname" id="fname" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-md-4 col-form-label">Last Name</label>
			<div class="col-md-8">
			<input disabled type="text" required class="form-control" name="lname" id="lname" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
			<label class="col-md-4 col-form-label">Role</label>
			<div class="col-md-8">
				
				<select required disabled name="userrole" id="userrole" class="form-control form-control-primary fill">
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
			<span class="messages"></span>
			</div>
		</div>

		
		<div class="form-group row">
		<label class="col-md-4"></label>
		<div class="col-md-8">
		<button disabled type="submit" name="createuser" id="submitBtn" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add User</button>
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
	
	function checkUserid()
	{
		userid = document.getElementById('userid').value;
        var postData = new FormData();
       
        postData.append("action","checkUserid");
        postData.append("userid",userid);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           
           
            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                var err = document.getElementById("userid_message");
                err.innerHTML = "User ID/username is available";
                err.classList.remove("form-txt-danger");
                err.classList.remove("form-txt-success");
                err.classList.add("form-txt-success");
                document.getElementById("submitBtn").disabled=false;
                document.getElementById('userid').readOnly = true;
                document.getElementById("fname").disabled=false;
                document.getElementById("lname").disabled=false;
                document.getElementById("userrole").disabled=false;

            }
            else
            {
                var err = document.getElementById("userid_message");

                err.innerHTML = "Userid is not available";
                err.classList.remove("form-txt-danger");
                err.classList.remove("form-txt-success");
                err.classList.add("form-txt-danger");
                document.getElementById("submitBtn").disabled=true;
            }
            

        
        
          }
        };
        xmlhttp.open("POST", "/query/admin.php", true);
        xmlhttp.send(postData);
	}

</script>

<?php
    
    include("../../pages/endbody.php");

?>