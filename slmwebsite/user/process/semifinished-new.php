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
	$myrole = $session->user->getRoleid();

    $PAGE = [
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "process-semifinished-new",
        "MainMenu"	 => "process_semifinished",

    ];


    $processname = "Semi Finished";

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='CREATION' AND role ='$myrole'");

		if($result->num_rows>0)
		{
			$dumPermission = $result->fetch_assoc()["permission"];
			if($dumPermission!="ALLOW")
			{
				$ERR_TITLE = "Error";
	    	$ERR_MSG = "You are not authorized to view this page.";
	    	include("../../pages/error.php");
	    	die();
			}

		}
		else
		{
			$ERR_TITLE = "Error";
	    	$ERR_MSG = "You are not authorized to view this page.";
	    	include("../../pages/error.php");
	    	die();
		}

    

    if(isset($_POST["updateprocess1"]))
    {

    	
    	


    	$pre = $_POST["preprocessed"];

    	if($pre == "Pre-Processed")
    	{
    		$dumpre = "S";
    	}
    	else
    	{
    		$dumpre = "A";
    	}

    	$creationDate = $_POST["creation-date"];
    	$year = substr(explode("-",explode(" ",$creationDate)[0])[0],-2);
    	$month = explode("-",explode(" ",$creationDate)[0])[1];
    	$prefix = $dumpre.$year." ".$month."/";
    	$sqlprefix = $dumpre.$year." ".$month."/%";

    


    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(processid, '/', -1) AS SIGNED)) max_val FROM processentry WHERE processid LIKE '$sqlprefix'");

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

    	
    	
    	$result = runQuery("INSERT INTO processentry VALUES('$prefix','$processname','CREATION','$creationDate','UNLOCKED')");

    	if($result)
    	{
    		

    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Date','$creationDate')");
    		

    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Pre-Processed','$pre')");

    			
    			if($result)
    			{
    				
    				?>
    					<form id="redirectform" method="POST" action="semifinished-edit.php">
    						<input type="hidden" name="processid" value="<?php  echo $prefix;?>">
    					</form>
    					<script type="text/javascript">
    						document.getElementById("redirectform").submit();
    					</script>
    				<?php

    			
    			}
    	}

    	

    }

   


 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

<script type="text/javascript">
	
	

</script>

<style type="text/css">
	
section {
  display: flex;
  flex-flow: row wrap;
}

section > div {
  flex: 1;
  padding: 0.5rem;
}

input[type=radio] {
  display: none;
}
input[type=radio]:not(:disabled) ~ label {
  cursor: pointer;
}
input[type=radio]:disabled ~ label {
  color: #bcc2bf;
  border-color: #bcc2bf;
  box-shadow: none;
  cursor: not-allowed;
}

label{
  height: 100%;
  display: block;
  background: white;
  border: 2px solid #4099FF;
  border-radius: 10px;
  padding: 1rem;
  margin-bottom: 1rem;
  text-align: center;
  box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.5)!important;
  position: relative;
}

label:hover{box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.85);}


input[name="creation-date"] {
  height: 40px;
  width: 250px;
  display: block;
  background: white;
  border: 2px solid #4099FF;
  border-radius: 5px;
  padding: 1rem;
  margin-bottom: 1rem;
  text-align: center;
  box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.5);
  position: relative;
}

input[type=radio]:checked + label {
  background: #4099FF;
  color: white;
  box-shadow: 0px 0px 20px rgba(64, 153, 255, 0.75);
}
input[type=radio]:checked + label::after {
  color: #3d3f43;
  font-family: FontAwesome;
  border: 2px solid #4099FF;
  content: "";
  font-size: 24px;
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  height: 50px;
  width: 50px;
  line-height: 50px;
  text-align: center;
  border-radius: 50%;
  background: white;
  box-shadow: 0px 2px 5px -2px rgba(0, 0, 0, 0.25);
}



p {
  font-weight: 900;
}

@media only screen and (max-width: 700px) {
  section {
    flex-direction: column;
  }
}


</style>


<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>Semi Finished Process</h5>
					<span>Edit Semi Finished process parameters</span>
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

<?php

?>

<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">



<ul class="nav nav-tabs md-tabs " role="tablist">
<li class="nav-item">
<a class="nav-link active" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i>Creation</a>
<div class="slide"></div>
</li>






</ul>

<div class="tab-content card-block">

<div class="tab-pane active" id="creation-tabdiv" role="tabpanel">

<form method="POST">
					<p style="display:block;text-align:center;color:#212121;">Enter the Time</p>
					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>

					<script>
					$(function() {
					  $('input[name="creation-date"]').daterangepicker({
					    singleDatePicker: true,
					    timePicker: true,
					    timePicker24Hour: true,
					    showDropdowns: true,
					    locale: 
					    {    
					    	format: 'YYYY-MM-DD HH:mm',
					    },
					  	
					    minYear: 1901,
					    maxYear: parseInt(moment().format('YYYY'),10)
					  }, function(start, end, label) {
					    
					  });


					});
					$('#creation-date').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>




	
<br><br>

<section>
<div>
  <input required type="radio" id="control_1" name="preprocessed" value="Pre-Processed">
  <label for="control_1">
    <h2>Sponged</h2>
    <p></p>
  </label>
</div>

<div>
  <input required type="radio" id="control_2" name="preprocessed" value="New">
  <label for="control_2">
    <h2>Atomized</h2>
    <p></p>
  </label>
</div>

</section>

	<div class="form-group row">
		<input type="hidden" id="furnacename" name="furnacename" value="">
		<div class="col-sm-12">
		<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Create New Entry</button>
		</div>
	</div>

</form>


</div>







</div></div>
</div>


<?php



?>





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
  	

  	$('#creationpermission').val(<?php echo $creationPermission; ?>).trigger('change');
  	


});










</script>