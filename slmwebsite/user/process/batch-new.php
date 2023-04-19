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
        "Page Title" => "SLM | Create a new Batch",
        "Home Link"  => "/user/",
        "Menu"		 => "process-batch-new",
        "MainMenu"	 => "process_batch",

    ];


    $processname = "Batch";

   
    

    if(isset($_POST["updateprocess1"]))
    {

    	
    	
    	$creationDate = toServerTime($_POST["creation-date"]);

    	$parentid = $_POST['finalblendId'];
    	
    	$prefix = $_POST['processid'];

    	$overridegrade =  $_POST['overridegrade'];








    	$result = runQuery("SELECT * FROM processentry WHERE processid = '$prefix'");

    	if($result->num_rows==0)
    	{	
    		
    	
    	
	    	
	    	$result = runQuery("INSERT INTO processentry VALUES('$prefix','$processname','CREATION','$creationDate','UNLOCKED')");

	    	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$parentid' AND param='$MASS_TITLE'");
	    	$qty = $result->fetch_assoc()["value"];


	    	if($overridegrade == "NO")
	    	{
	    		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$parentid' AND param='$GRADE_TITLE'");
	    		$grade = $result->fetch_assoc()["value"];
	    	}
	    	else
	    	{
	    		$grade = $overridegrade;
	    	}

	    	

	    	$result = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','PARENT','$parentid','$qty')");
	    	$result2 = runQuery("INSERT INTO processentryparams VALUES(NULL,'$prefix','OPERATIONAL','$GRADE_TITLE','$grade')");

	    	$result3 = runQuery("UPDATE processentry SET islocked='BATCHED' WHERE processid='$parentid'");

	    	
	    	if($result && $result2 && $result3)
	    	{
	    			
	    			addprocesslog('PROCESS',$prefix,$session->user->getUserid(),'New Final Batch ('.$prefix.') created');
	    				?>
	    					<form id="redirectform" method="POST" action="batch-edit.php">
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
	   		$show_alert = true;
				$alert = showAlert("error","ID already exists","");
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
					<h3>Create a New Batch</h3>
					<span>Enter the Batch details</span>
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
<a class="nav-link active" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i> Batch Creation</a>
<div class="slide"></div>
</li>






</ul>

<div class="tab-content card-block">

<div class="tab-pane active" id="creation-tabdiv" role="tabpanel">

<form method="POST">


<p style="display:block;text-align:center;color:#212121;">Last Created Id: <?php echo get_last_id($processname) ?></p>		
<p style="display:block;text-align:center;color:#212121;">Enter the Batch Creation Date</p>

<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">

					</div>


					<p style="display:block;text-align:center;color:#212121;font-size:13px;font-weight:normal;">Sample Batch No.: T21IP05123(Technology), M21IP05123(Metal) </p>

					<div class="form-group" style="display:flex; justify-content: center;" >
							<div class="input-group input-group-button col-sm-3">
								<input type="text" required name="processid" id="processid" class="form-control" style="display: inline; text-align: center;text-transform:uppercase;" placeholder="Batch Number" pattern="[T,M][0-9]{2}IP[0-9]{5}">						
							</div>
						</div>

						<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-sm-3" name="finalblendId" >
							<option selected disabled value=""> Choose a Final Blend</option>

							<?php 

								$result = runQuery("SELECT * FROM processentry WHERE processname='Final Blend' AND islocked <>'BLOCKED' AND islocked <>'FAILED' AND islocked <>'REANNEALED'  AND islocked <>'UNLOCKED' AND islocked <> 'BATCHED' ");

								if($result->num_rows>0)
								{
									while($row = $result->fetch_assoc())
									{

										$finalid = $row['processid'];

										$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$finalid' AND param ='$GRADE_TITLE'");

										$dumqty = getTotalQuantity($finalid) - getChildProcessQuantity($finalid);

										if($dumqty<=0)
										{
											continue;
										}

										$grade = $result2->fetch_assoc()['value'];

										echo "<option value=\"".$row["processid"]."\">".$row["processid"]." </option>";

										echo "<script> console.log('".$row["processid"]."')</script>";
									}
								}

							?>

						</select>
					</div>

					<div class="form-group" style="display:flex; justify-content: center">

						<select required class="form-control col-sm-3" name="overridegrade" >
							<option selected  value="NO"> Use Default Grade</option>

							<?php 

								$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");

								if($result->num_rows>0)
								{
									while($row = $result->fetch_assoc())
									{

										

										echo "<option value=\"".$row["gradename"]."\">".$row["gradename"]."</option>";
									}
								}

							?>

						</select>
					</div>

					<script>
					$(function() {
					  $('input[name="creation-date"]').daterangepicker({
					    singleDatePicker: true,
					    timePicker: true,
					    showDropdowns: true,
					    locale: 
					    {    
					    	format: 'YYYY-MM-DD hh:mm A',
					    },
					  	
					    minYear: 1901,
					    maxYear: parseInt(moment().format('YYYY'),10)
					  }, function(start, end, label) {
					    
					  });


					});
					$('#creation-date').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>


	

	<div class="form-group row">
		
		<div class="col-sm-12">
		<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary btn-block"><i class="feather icon-plus"></i>Create New Batch</button>
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