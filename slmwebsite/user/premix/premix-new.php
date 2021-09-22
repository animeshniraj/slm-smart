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
        "Page Title" => "SLM | Create new Raw Bag",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-createnew",
        "MainMenu"	 => "premix_menu",

    ];


    $processname = "Raw Bag";

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

    	
    	$quantity = $_POST["quantity"];
    	$grade = $_POST["gradename"];
 			$type = $_POST["premixtype"];
    


    	$creationDate = $_POST["creation-date"];
    	
    	$year = substr(explode("-",explode(" ",$creationDate)[0])[0],-2);

    	$month = explode("-",explode(" ",$creationDate)[0])[1];
    	$prefix = "PR".$year." ".$month."/";
    	$sqlprefix = "PR".$year." ".$month."/%";


    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(premixid, '/', -1) AS SIGNED)) max_val FROM premix_batch WHERE premixid LIKE '$sqlprefix'");

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

   

    	$result = runQuery("INSERT INTO premix_batch VALUES('$prefix','$grade','$quantity','$creationDate','$type')");


    	if($result)
    			{
    				
    				?>
    					<form id="redirectform" method="POST" action="premix-edit.php">
    						<input type="hidden" name="premixid" value="<?php  echo $prefix;?>">
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
					<h5>Add Premix</h5>
					<span>Add new premix batch</span>
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

		
<p style="display:block;text-align:center;color:#212121;">Enter the Date</p>
					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="creation-date" id="creation-date" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>

					<div class="form-group" style="display:flex; justify-content: center">
						<input type="text" required name="quantity"  class="form-control col-sm-3" style="display: inline; text-align: center;" placeholder="Quantity (in Kg)">
					</div>

					<div class="form-group" style="display:flex; justify-content: center">
						<select required class="form-control col-sm-3" name="gradename" >
							<option selected disabled value=""> Choose a premix grade</option>

							<?php 

								$result = runQuery("SELECT * FROM premix_grades");

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

					<br><br>

<section>
<div>
  <input required type="radio" id="control_1" name="premixtype" value="simple">
  <label for="control_1">
    <h2>Normal</h2>
    <p></p>
  </label>
</div>

<div>
  <input required disabled type="radio" id="control_2" name="premixtype" value="complex">
  <label for="control_2">
    <h2>TBD</h2>
    <p></p>
  </label>
</div>

</section>


						
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
	

	<div class="form-group row">
		
		<div class="col-sm-12">
		<button type="submit" class="btn btn-primary btn-block"><i class="feather icon-plus"></i>Create New Entry</button>
		</div>
	</div>

</form>


</div>

<script type="text/javascript">
	
	function checkSupplier()
	{
			var externalid = document.getElementById("external-id").value;
			var supplier = document.getElementById("premix_supplier").value;
			var additive = document.getElementById("premix_additive").value;


			var postData = new FormData();
       
        postData.append("action","checkSupplier");
        postData.append("externalid",externalid);
        postData.append("supplier",supplier);
        postData.append("additive",additive);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response =="yes")
            {
                
            	document.getElementById("newstock").submit();
            }
            else
            {
               Swal.fire({
									icon: "error",
									title: "Error",
									html: data.msg ,
									showConfirmButton: true,
								  	showCancelButton: false,
								  	confirmButtonText: 'OK',
								  	
								})
            }
            

        
        
          }
        };
        xmlhttp.open("POST", "/query/premix.php", true);
        xmlhttp.send(postData);
	}

</script>





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