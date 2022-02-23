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
        "Page Title" => "SLM SMART | Add Recipes for Annealing",
        "Home Link"  => "/user/",
        "Menu"		 => "process-annealing-recipe",
        "MainMenu"	 => "process_annealing",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Annealing";



     if(!isset($_POST["recipename"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $recipename = $_POST["recipename"];



    if(isset($_POST['updaterecipe']))
    {

    	$param = serialize($_POST['allparams']);
		$value = serialize($_POST['paramsvalue']);

		runQuery("UPDATE recipe SET param='$param', value='$value' WHERE processname='$processname' AND recipename='$recipename'");

    }



    $result = runQuery("SELECT * FROM recipe WHERE processname='$processname' AND recipename='$recipename'")->fetch_assoc();

    $dumparams = unserialize($result['param']);
    $dumvalues = unserialize($result['value']);

    $currvalue = [];
    for ($i=0; $i <count($dumparams) ; $i++) { 
    	$currvalue[$dumparams[$i]] = $dumvalues[$i];
    }





    require_once("helper.php");
    





	$operationalParams = [];

    

    		$result2 = runQuery("SELECT * FROM processparams WHERE processname='Annealing' AND step='OPERATIONAL' AND param<>'$GRADE_TITLE'");
    		$dumval = "";
    		
    		if($result2->num_rows>0)
    		{
    			while($row = $result2->fetch_assoc())
    			{

    				if(isset($currvalue[$row["param"]]))
    				{
    					$dumval = $currvalue[$row["param"]];
    				}
    				array_push($operationalParams,[$row["param"],$dumval,$row["allowedvalues"],$row["type"]]);
    			}
    			
    		}

    	
    		
    	
    

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
		<div class="col-lg-1">
			<img src="production.png" width="75%">
		</div>
		<div class="col-lg-8">
			<div class="page-header-title">
				<div class="d-inline">
					<h3>Edit Annealing Recipe (Recipe Name: <?php echo $recipename; ?>)</h3>
					<p>Specify all parameters for the recipe</p>
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
<div class="col-md-6">



<div class="card">
	<div class="card-header">

	</div>
	<div class="card-block">

		<form method="POST" >

				
					<input  type="hidden" required class="form-control" name="recipename" value="<?php echo $recipename; ?>">
					


				<?php

					for($i=0;$i<count($operationalParams);$i++)
					{

							if($operationalParams[$i][2])
							{
								optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$operationalParams[$i][2],'required');
							}
							else if($operationalParams[$i][3] == "INTEGER")
							{
								integerInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
							else if($operationalParams[$i][3] == "DECIMAL")
							{
								decimalInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
							else if($operationalParams[$i][3] == "STRING")
							{
								stringInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
							else if($operationalParams[$i][3] == "DATE")
							{
								dateInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
							else if($operationalParams[$i][3] == "TIME")
							{
								timeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
							else if($operationalParams[$i][3] == "DATE TIME")
							{
								datetimeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
							}
					}


				?>


				<div class="form-group row">
				
						<div class="col-sm-12">
						<button type="submit" name="updaterecipe" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Update Recipe</button>
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
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	






  // Creation

  	

  		

  	

});








</script>