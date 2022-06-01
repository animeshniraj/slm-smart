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
        "Page Title" => "SLM | Create new Loading Advice",
        "Home Link"  => "/user/",
        "Menu"		 => "loadingadvice-new",
        "MainMenu"	 => "dispatch_menu",

    ];


    
    

    if(isset($_POST["updateprocess1"]))
    {

    	
    
    	$ponumber = $_POST["ponumber"];
 			$transport = $_POST['transport'];
 			$company = $_POST['company'];

 			if($company=='SLM Metal')
 			{
 				$type = "M"; 
 			}
 			else
 			{
 				$type = "T"; 
 			}
    


    	$creationDate = $_POST["creation-date"];
    	

    	$year = substr(explode("-",explode(" ",$creationDate)[0])[0],-2);

    	$month = explode("-",explode(" ",$creationDate)[0])[1];
    	

    	$prefix = $year."/LD".$type."/";
    	$sqlprefix =$year."/LD".$type."/%";


    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(laid, '/', -1) AS SIGNED)) max_val FROM loading_advice WHERE laid LIKE '$sqlprefix'");



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



    	$result = runQuery("INSERT INTO loading_advice ( SELECT '$prefix',orderid,customer,'$company','$transport','$creationDate','UNFULFILLED' FROM purchase_order WHERE orderid='$ponumber')");

    	$result2 = runQuery("UPDATE purchase_order SET status = 'LOADING ADVICE' WHERE orderid ='$ponumber'");

    	$result = runQuery("SELECT date FROM `purchaseorder_tentative` where date>= CURDATE() AND status ='UNFULFILLED' ORDER BY date LIMIT 1");

    	$currDate = $result->fetch_assoc()['date'];
    	
    	$result = runQuery("INSERT INTO loadingadvice_params VALUES(NULL,'$prefix','CREATION','Tentative Date','$currDate','date')");

    	$result = runQuery("SELECT * FROM `purchaseorder_tentative` where date ='$currDate' AND orderid='$ponumber'");

    	while($row=$result->fetch_assoc())
    	{
    		$dPO = $row["orderid"];
    		$dId = $row["id"];
    		$dGrade = $row["grade"];
    		$dqty = $row["quantity"];
				$dpackage = $row["package"];



    		runQuery("INSERT INTO loadingadvice_batches VALUES(NULL,'$prefix','$dGrade','','$dqty','$dpackage')");
    		runQuery("UPDATE purchaseorder_tentative SET status = 'LOADING ADVICE' WHERE id ='$dId'");
    	}


    	if($result)
    			{
    				
    				?>
    					<form id="redirectform" method="POST" action="loadingadvice-edit.php">
    						<input type="hidden" name="laid" value="<?php  echo $prefix;?>">
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
				<i class="fa fa-shopping-bag bg-c-blue"></i>
				<div class="d-inline">
					<h5>New Order</h5>
					<span>Start a New order</span>
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

<form method="POST" id="newstock">

		
<p style="display:block;text-align:center;color:#212121;">Enter the Delivery Date</p>
					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>




					
					<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-sm-3" name="ponumber" >
							<option selected disabled value=""> Choose a purchase order</option>

							<?php 

								$result = runQuery("SELECT * FROM purchase_order WHERE status <>'FULFILLED'");

								if($result->num_rows>0)
								{
									while($row = $result->fetch_assoc())
									{
										echo "<option value=\"".$row["orderid"]."\">".$row["orderid"]." ( Customer: ".$row["customer"].")</option>";
									}
								}

							?>

						</select>
					</div>


					<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-sm-3" name="company" >
							<option selected disabled value=""> Choose company</option>
							<option value="SLM Metal">SLM Metal</option>
							<option value="SLM Technology">SLM Technology</option>


						</select>
					</div>

					<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-sm-3" name="transport" >
							<option selected disabled value=""> Choose mode of transport</option>
							<option value="Truck">Truck</option>
							<option value="Cargo">Cargo</option>
							<option value="Courier">Courier</option>


						</select>
					</div>

					<br><br>



						
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

					<input type="hidden" name="updateprocess1" value="">
	

	<div class="form-group row justify-content-center">
		
		<div class="col-sm-6">
		<button type="submit" class="btn btn-primary btn-block"><i class="feather icon-plus"></i>Create New Loading Advice</button>
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