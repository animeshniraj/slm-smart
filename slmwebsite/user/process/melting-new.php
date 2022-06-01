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
        "Page Title" => "Create a new Heat ID | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "process-melting-new",
        "MainMenu"	 => "process_melting",

    ];


    $processname = "Melting";

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

    	$furnaceid = $_POST["furnaceid"];
    	$furnacename = $_POST["furnacename"];

    	$creationDate = $_POST["creation-date"];
    	$heatofftime = $_POST["heatofftime"];
    	$heatno = $_POST["heatno"];
    	$fheatno = $_POST["fheatno"];
    	
    	$year = substr(explode("-",explode(" ",$creationDate)[0])[0],-2);
    	$prefix = $year.'-I'.$furnaceid.'-';
    	$sqlprefix = $year.'-I'.$furnaceid.'-%';

    	

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

    	if($result)
    	{
    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Furnace','$furnacename')");
    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','CREATION','Date','$creationDate')");
    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','GENERIC','Heat On Time','$creationDate')");

    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','GENERIC','Heat Off Time','$heatofftime')");

    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','GENERIC','Heat No.','$heatno')");
    			$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','GENERIC','Furnace Heat No.','$fheatno')");

    			$result = runQuery("INSERT INTO processentryparams (SELECT NULL,'$prefix','STOCK',materialname,0 FROM rawmaterials)");
    			if($result)
    			{

    				addprocesslog('PROCESS',$prefix,$session->user->getUserid(),'New Melting Process ('.$prefix.') created');
    				
    				?>
    					<form id="redirectform" method="POST" action="melting-edit.php">
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
	
	function changeSelect(val)
	{
		document.getElementById("furnacename").value = val;
		
	}

</script>

<style type="text/css">

.icofont-i {
    font-family: icofont!important;
    speak: none;
    font-style: normal;
    font-weight: bold;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-size: 22px;
}
	
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
  padding: 0.75rem;
  text-align: center;
  box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.5);
  position: relative;
  transition:ease-in-out .5s;
}

label:hover{
	background-color:#990000;
	color:#FFE6D9;
	transition:ease-in-out .5s;
	-webkit-box-shadow: 10px 10px 23px -7px rgba(153,0,0,1);
	-moz-box-shadow: 10px 10px 23px -7px rgba(153,0,0,1);
	box-shadow: 10px 10px 23px -7px rgba(153,0,0,1);
	border: 2px solid #00BDF1;
}


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


input[name="heatofftime"] {
    height: 40px;
    width: 250px;
    display: block;
    background: white;
    border: 2px solid #4099FF;
    border-radius: 5px;
    padding: 1rem;
    margin-bottom: 1rem;
    text-align: center;
    box-shadow: 0px 3px 10px -2px rgb(161 170 166 / 50%);
    position: relative;
}

input[type=radio]:checked + label {
  background: #4099FF;
  color: white;
  box-shadow: 0px 0px 20px rgba(64, 153, 255, 0.75);
}
input[type=radio]:checked + label::after {
  color: #990000;
  font-family: FontAwesome;
  border: 2px solid #990000;
  content:"\f2c5";
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
					<h3>Melting Batch creation</h3>
					<span>Enter the Melting details to create a new heat</span>
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
<a class="nav-link active" data-toggle="tab" href="#creation-tabdiv" role="tab" style="font-size:18px;"><i class="icofont-i icofont-fire-burn"></i> Create a new Heat (Last Created Id: <?php echo get_last_id($processname);?>)</a>
<div class="slide"></div>
</li>






</ul>

<div class="tab-content card-block">

<div class="tab-pane active" id="creation-tabdiv" role="tabpanel">

<form method="POST">
				<div class="row justify-content-center">

				<div class="col-md-3">
					<p style="display:block;text-align:center;color:#212121;" data-toggle="tooltip" data-placement="bottom" title="Choose the date and time">Enter the Heat On Time</p>
					
					<div class="form-group" style="display:flex;justify-content:center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>
				</div>

				<div class="col-md-3">
					<p style="display:block;color:#212121;text-align:center;" data-toggle="tooltip" data-placement="bottom" title="Choose the date and time">Enter the Heat Off Time</p>
					<div class="form-group" style="display:flex;justify-content:center;">
						
						<input type="text" required name="heatofftime" id="heatofftime" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>
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

					$(function() {
					  $('input[name="heatofftime"]').daterangepicker({
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
					$('#heatofftime').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>

				</div>

				<div class="row justify-content-center">
					<div class="col-md-3">
						<div class="form-group" style="display:flex; justify-content: center;" data-toggle="tooltip" data-placement="bottom" title="Enter Day Heat Number">
							
							<input type="text" required name="heatno" id="heatno" class="form-control" style="display: inline; text-align: center;" placeholder="Day Heat Number">
							
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" style="display:flex; justify-content: center;" data-toggle="tooltip" data-placement="bottom" title="Enter Furnace Heat Number">
							
							<input type="text" required name="fheatno" id="fheatno" class="form-control" style="display: inline; text-align: center;" placeholder="Furnace Heat Number">
							
						</div>
					</div>
				</div>


<img src="/pages/png/furnace.png" class="furnace-img img-center">
<p style="display:block;text-align:center;font-size:18px;cursor:auto;" data-toggle="tooltip" data-placement="bottom" title="Furnace based on quantity">Select the used furnace</p>

<section>
<br>
	<?php 

			$result = runQuery("SELECT * FROM furnaces WHERE processname='$processname'");
			if($result->num_rows>0)
			{

				while($row=$result->fetch_assoc())
				{



	?>

<div>
  <input onclick="changeSelect('<?php echo $row["furnacename"]?>')" required type="radio" id="control_<?php echo $row["prefix"]?>" name="furnaceid" value="<?php echo $row["prefix"]?>">
  <label for="control_<?php echo $row["prefix"]?>">
    <h2><?php echo $row["furnacename"]?></h2>
    <p><?php echo $row["specification"]?></p>
  </label>
</div>


	<?php 
		}}
	?>

</section>

	
<br><br>
	<div class="form-group row">
		<input type="hidden" id="furnacename" name="furnacename" value="">
		<div class="col-sm-12">
		<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Create New Heat</button>
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