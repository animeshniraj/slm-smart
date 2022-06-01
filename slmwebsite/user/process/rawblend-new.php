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
        "Page Title" => "SLM | Create New Raw Blend",
        "Home Link"  => "/user/",
        "Menu"		 => "process-rawblend-new",
        "MainMenu"	 => "process_rawblend",

    ];


    $processname = "Raw Blend";

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

    		

    	if($_POST['type']=="newblend")
    	{

    		$creationDate = $_POST["creation-date"];
    	
	    	$year = substr(explode("-",explode(" ",$creationDate)[0])[0],-2);

	    	$month = explode("-",explode(" ",$creationDate)[0])[1];
	    	$prefix = $year.'/'.$month.'-BA-';
	    	$sqlprefix = $year.'/'.$month.'-BA-%';;

	    	$blendid = $_POST['blendid'];

	    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(processid, '-', -1) AS SIGNED)) max_val FROM processentry WHERE processid LIKE '$sqlprefix'");

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

	    	$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Pre-Processed','Atomized')");
    		$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Date','$creationDate')");

    		$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','GENERIC','Blend Number','$blendid')");

    		addprocesslog('PROCESS',$prefix,$session->user->getUserid(),'New Atomized Raw Blend Process ('.$prefix.') created');

		    	if($result)
		    	{
		    			
		    			
		    				?>
		    					<form id="redirectform" method="POST" action="rawblend-edit.php">
		    						<input type="hidden" name="processid" value="<?php  echo $prefix;?>">
		    					</form>
		    					<script type="text/javascript">
		    						document.getElementById("redirectform").submit();
		    					</script>
		    				<?php

		    			
		    			
		    	}

    	}
    	else
    	{
    		
    		
    		$creationDate = $_POST["creation-date"];

    		$processid = $_POST["processid"];
    		$sqlprefix = $processid[0].'/'.$processid[1]."-".$processid[2]."-".$processid[3];

    		$blendid = $_POST['blendid'];

    		$result = runQuery("SELECT * FROM processentry WHERE processid ='$sqlprefix'");

    		if($result->num_rows==0)
    		{

    			$result = runQuery("INSERT INTO processentry VALUES('$sqlprefix','$processname','CREATION','$creationDate','UNLOCKED')");

	    	$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$sqlprefix','CREATION','Pre-Processed','Sponge')");
    		$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$sqlprefix','CREATION','Date','$creationDate')");
    		$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$sqlprefix','GENERIC','Blend Number','$blendid')");
    			if($result)
		    	{
		    			addprocesslog('PROCESS',$sqlprefix,$session->user->getUserid(),'New Sponge Raw Bag Process ('.$sqlprefix.') created');
		    			
		    				?>
		    					<form id="redirectform" method="POST" action="rawblend-edit-pre.php">
		    						<input type="hidden" name="processid" value="<?php  echo $sqlprefix;?>">
		    					</form>
		    					<script type="text/javascript">
		    						document.getElementById("redirectform").submit();
		    					</script>
		    				<?php

		    			
		    			
		    	}
    		}
    		else
    		{

    			$show_alert = true;
  				$alert = showAlert("error","ID already exists","");
    			
    		}



    	}
    	
    
    	
    

    	

    }

   


 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");

if($show_alert)
    {
    	echo $alert;
    }




?>



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

label {
  height: 100%;
  display: block;
  background: white;
  border: 2px solid #4099FF;
  border-radius: 20px;
  padding: 1rem;
  margin-bottom: 1rem;
  text-align: center;
  box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.5);
  position: relative;
}

input[name="creation-date"] {
  height: 40px;
  width: 250px;
  display: block;
  background: white;
  border: 2px solid #4099FF;
  border-radius: 20px;
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
				<i class="fa fa-shopping-bag bg-c-blue"></i>
				<div class="d-inline">
					<h5>Creating New Raw Blend</h5>
					<span>Enter Raw Blend parameters</span>
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

<p style="display:block;text-align:center;color:#212121;">Last Created Id: <?php echo get_last_id($processname) ?></p>		
<p style="display:block;text-align:center;color:#212121;">Enter the Raw Blend Input Date and Time</p>
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


					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="blendid" id="blendid" class="form-control col-sm-3" style="display: inline; text-align: center;" placeholder="Blend Number">
						
					</div>

<section>
<br>

<div>
  <input onclick="toggleDisp('pre')"  required type="radio" id="preprocessed" name="type" value="preprocessed">
  <label for="preprocessed">
    <h2>Sponge</h2>
    <p></p>
  </label>
</div>

<div>
  <input onclick="toggleDisp('new')"  required type="radio" id="newblend" name="type" value="newblend">
  <label for="newblend">
    <h2>Atomized</h2>
    <p></p>
  </label>
</div>

</section>
<br><br>


<script type="text/javascript">
	
	function toggleDisp(val)
	{
		div1 = document.getElementById("pre-div");
		if(val=='pre')
		{
			document.getElementById('preprocessed-entry').style.display = "block";

			for(var i=0;i<div1.children.length;i++)
			{
				div1.children[i].required = true;
			}


		}
		else
		{
			document.getElementById('preprocessed-entry').style.display = "none";
			for(var i=0;i<div1.children.length;i++)
			{
				div1.children[i].required = false;
			}
		}


	}

</script>

<div id="preprocessed-entry" style="display:none">
<p style="display:block;text-align:center;color:#212121;">Enter the Raw Blend Output Date</p>
<div class="form-group" style="display:flex; justify-content: center;">

			<div class="col-sm-4">
							<div id="pre-div" class="input-group input-group-button">

								
								
								
								<input name="processid[]" minlength="2" maxlength="2" required type="text" class="form-control form-control-uppercase" placeholder="YY" style="margin: 10px;" value="">
								<input name="processid[]" minlength="2" maxlength="2" required type="text" class="form-control form-control-uppercase" placeholder="MM" style="margin: 10px;" value="">
								<input name="processid[]" readonly required type="text" class="form-control col-sm-2 form-control-uppercase" placeholder="" style="margin: 10px;" value="BS"><div></div>
								<input name="processid[]" minlength="3" maxlength="3" required type="text" class="form-control form-control-uppercase" placeholder="XXX" style="margin: 10px;" value="">

							</div>
						</div>

	</div>
</div>
	

	<div class="form-group row">
		
		<div class="col-sm-12">
		<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary btn-block"><i class="feather icon-plus"></i>Create New Entry</button>
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
  	

  	
  	


});










</script>