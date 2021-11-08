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

	

    $PAGE = [
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "process-melting-stock",
        "MainMenu"	 => "process_melting",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();



    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





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
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>Trace</h5>
					<span>Forward/Backward Trace</span>
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

<div class="card-header-right">
</div>
</div>
<div class="card-block">



			<div class="form-group row">
				<label class="col-sm-2 col-form-label">Process Name</label>
				<div class="col-sm-10">
				<input type="text" required class="form-control" id="processidinput" placeholder="">
				<span class="messages"></span>
				</div>
			</div>


			<div class="form-group row">
			
			

				<div class="col-sm-6">
			<button type="button" class="btn btn-primary pull-left" onclick="openbackward()"><i class="fa fa-backward"></i>Backward Trace</button>
			<span class="messages"></span>
			</div>

			<div class="col-sm-6">
			<button type="button" class="btn btn-primary pull-right" onclick="openforward()"><i class="fa fa-forward"></i>Forward Trace</button>
			<span class="messages"></span>
			</div>

			</div>

<script type="text/javascript">
	function openforward()
	{
		var processid = document.getElementById('processidinput').value;
		

		url = "./forwardtrace.php?id="+processid;
		window.open(url, '_blank').focus();
	}

	function openbackward()
	{
		var processid = document.getElementById('processidinput').value;
		

		url = "./backwardtrace.php?id="+processid;
		window.open(url, '_blank').focus();
	}
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

<script type="text/javascript">





$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	






  // Creation

  	

  		

  	

});








</script>