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
	$myuserid = $session->user->getUserid();
    $PAGE = [
        "Page Title" => "SLM | Messages",
        "Home Link"  => "/user/",
        "Menu"		 => "message-compose",
        "MainMenu"	 => "message_menu",

    ];


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-edit bg-c-blue"></i>
				<div class="d-inline">
					<h5>Compose New Message</h5>
					<span>Send a new message to other users.</span>
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
<h5>New Message</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<form method="POST" action="send.php">
	
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">To</label>
			<div class="col-sm-10">
			<select class="js-example-basic-multiple col-sm-10" multiple="multiple" name="recipients[]" id="recipients">
					<?php
						$result = runQuery("SELECT * FROM userdetails WHERE userid<>'$myuserid' ORDER BY fname");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["userid"]."\">".$row["fname"]." ". $row["lname"]."</option>";
							}
						}

					?>
			</select>
			
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Subject</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="subject" id="subject" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Message</label>
			<div class="col-sm-10">
			<textarea rows="10" cols="5" class="form-control" placeholder="" name="message" id="message" ></textarea>
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="sendMessage" id="sendMessage" class="btn btn-primary m-b-0"><i class="fa fa-send"></i>Send Message</button>
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
  $(".js-example-basic-multiple").select2();
});
</script>