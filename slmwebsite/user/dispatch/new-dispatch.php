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
        "Page Title" => "Create new Dispatch | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "dispatch-new",
        "MainMenu"	 => "dispatch_menu",

    ];


    
    

    if(isset($_POST["updateprocess1"]))
    {

    	$laid = $_POST['laid'];
    	$cid = $_POST['cid'];

    	$creationDate = toServerTime($_POST["creation-date"]);


    
    	
        	

    	$prefix = $cid;



    	$result = runQuery("INSERT INTO dispatch (SELECT '$cid',laid,customer,'$creationDate' FROM loading_advice WHERE laid='$laid')");

    	$result2 = runQuery("UPDATE loading_advice SET status ='FULFILLED' WHERE laid='$laid'");


    	if($result && $result2)
    			{
    				
    				?>
    					<form id="redirectform" method="POST" action="dispatch-edit.php">
    						<input type="hidden" name="cid" value="<?php  echo $prefix;?>">
    					</form>
    					<script type="text/javascript">
    						document.getElementById("redirectform").submit();
    					</script>
    				<?php

    			
    			}

    	die();

    	
    	

    	
    

    
    	

    	
    	

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
				<i class="fa fa-truck bg-c-blue"></i>
				<div class="d-inline">
					<h3>New Dispatch</h3>
					<span>Enter details to create a new Dispatch</span>
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

<div class="col-lg-6">

<?php

?>

<div class="card">
<div class="card-header">
	<img src="pallete.gif" class="head-center">
</div>

<div class="card-block">



<ul class="nav nav-tabs md-tabs " role="tablist">
<li class="nav-item">
<a class="nav-link active" data-toggle="tab" href="#creation-tabdiv" role="tab">Dispatch Creation</a>
<div class="slide"></div>
</li>






</ul>

<div class="tab-content card-block">

<div class="tab-pane active" id="creation-tabdiv" role="tabpanel">

<form method="POST" id="newstock">

		
<p style="display:block;text-align:center;color:#212121;">Enter the Date</p>
					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>




					
					<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-md-10" name="laid" >
							<option selected disabled value=""> Choose a Loading Advice</option>

							<?php 

								$result = runQuery("SELECT * FROM loading_advice WHERE status ='UNFULFILLED'");

								if($result->num_rows>0)
								{
									while($row = $result->fetch_assoc())
									{
										echo "<option value=\"".$row["laid"]."\">".$row["laid"]." ( Customer: ".$row["customer"].")</option>";
									}
								}

							?>

						</select>
					</div>


					<div class="form-group" style="display:flex; justify-content: center" >
						<input type="text" class="form-control col-md-10" required name="cid" value="" placeholder="Provide a Consignment ID">
					</div>

					

					<br><br>



						
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

					<input type="hidden" name="updateprocess1" value="">
	

	<div class="form-group row justify-content-md-center">
		
		<div class="col-sm-6 ">
		<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-plus-square"></i>Create Dispatch</button>
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