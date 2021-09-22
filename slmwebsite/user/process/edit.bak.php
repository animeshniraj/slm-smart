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

	require_once('helper.php');
	$myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

    $PAGE = [
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "process-melting-view",
        "MainMenu"	 => "process_melting",

    ];


    $processname = "Melting";

    if(!isset($_POST["processid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $processid = $_POST["processid"];


   
    

     if(isset($_POST["updateprocess1"]))
    {
    	
    	$newprocessid = $_POST["processidName"][0]."-".$_POST["processidName"][1]."-".$_POST["processidName"][2];
    	$result = runQuery("SELECT * FROM processentry WHERE processid='$newprocessid'");
    	if($result->num_rows==0)
    	{
    		runQuery("INSERT INTO processentry (SELECT '$newprocessid',processname,currentstep,entrytime,islocked FROM processentry WHERE processid='$processid')");
	    	runQuery("UPDATE processentryparams SET processid='$newprocessid' WHERE processid='$processid'");
	    	runQuery("DELETE FROM processentry WHERE processid='$processid'");
	    	$processid = $newprocessid;
    	}
    	else
    	{
    		$show_alert = true;
				$alert = showAlert("error","ID already exists","");
    	}


    	


    }

    $currStep = runQuery("SELECT currentstep FROM processentry WHERE processid='$processid'");
    $currStep = $currStep->fetch_assoc()["currentstep"];

    if(isset($_POST["updateprocess2"]))
    {
    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];

    	

    	for($i=0;$i<count($allParams);$i++)
    	{
    		runQuery("DELETE FROM processentryparams WHERE processid='$processid' AND step='GENERIC' AND param='$allParams[$i]'");
    		runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','GENERIC','$allParams[$i]','$paramsvalue[$i]')");
    	}

    	if($currStep=="CREATION")
    	{
    		runQuery("UPDATE processentry SET currentstep='GENERIC' WHERE processid='$processid'");
    	}
    	
    	
    	
    	


    }

    if(isset($_POST["updateprocess3"]))
    {
    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];

    	

    	for($i=0;$i<count($allParams);$i++)
    	{
    		runQuery("DELETE FROM processentryparams WHERE processid='$processid' AND step='OPERATIONAL' AND param='$allParams[$i]'");
    		runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','OPERATIONAL','$allParams[$i]','$paramsvalue[$i]')");
    	}

    	if($currStep=="GENERIC")
    	{
    		runQuery("UPDATE processentry SET currentstep='OPERATIONAL' WHERE processid='$processid'");
    	}
    	
    	

    }


    if(isset($_POST["updateprocess4"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];

    		$sqlprefix = $processid."/T%";
    		$prefix = $processid."/T";
    		
    		$result = runQuery("SELECT * FROM processtest WHERE testid LIKE '$sqlprefix' ORDER BY entrytime DESC LIMIT 1");

	    	if($result->num_rows==0)
	    	{	
	    		$count = 1;
	    	}
	    	else
	    	{
	    		$lastID = $result->fetch_assoc()["testid"];
		    	$lastID = substr($lastID, 0, strpos($lastID, $prefix)).substr($lastID, strpos($lastID, $prefix)+strlen($prefix));
		    	$count = intval($lastID)+1;
	    	}
	    	$prefix = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);


	    
	    	runQuery("INSERT INTO processtest VALUES('$prefix','$processid','$processname',CURRENT_TIMESTAMP,'DEFAULT')");
	    	
	    	for($i=0;$i<count($allParams);$i++)
	    	{
	    		
	    		runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]')");
	    		
	    	}

    	if($currStep=="OPERATIONAL")
    	{
    		runQuery("UPDATE processentry SET currentstep='TEST' WHERE processid='$processid'");
    	}



    }


    if(isset($_POST["updateprocess5"]))
    {

    	

    }


    if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO processnotes VALUES(NULL,'$processid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }


    if(isset($_POST["accepttest"]))
    {

    	$testid = $_POST['testid'];
    	runQuery("UPDATE processtest SET status='ACCEPT' WHERE testid = '$testid'");
    	
    }

      if(isset($_POST["rejecttest"]))
    {

    	$testid = $_POST['testid'];
    	runQuery("UPDATE processtest SET status='REJECT' WHERE testid = '$testid'");
    	
    }


    $result = runQuery("SELECT currentstep,islocked FROM processentry WHERE processid='$processid'");
     $result = $result->fetch_assoc();
    $currStep = $result["currentstep"];

    if($result["islocked"]=="UNLOCKED")
    {
    	$editidPermission = true;
    }
    else
    {
    	$editidPermission = false;
    }

    $stepPermission = false;




    $genericParams = [];
    $genericPermission = false;

    $result = runQuery("SELECT * FROM processparams WHERE processname='$processname' AND step='GENERIC' ORDER BY ordering");

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		$dumParam = $row["param"];
    		$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='GENERIC' AND param='$dumParam'");
    		$dumval = "";
    		
    		if($result2->num_rows>0)
    		{
    			$dumval = $result2->fetch_assoc()["value"];
    			
    		}
    		array_push($genericParams,[$dumParam,$dumval,$row["allowedvalues"],$row["type"],$row["islocked"]]);
    	}


    }

    if($currStep=="CREATION" || $currStep=="GENERIC" || $currStep=="OPERATIONAL" || $currStep=="TEST")
    {
    	$stepPermission = true;
    }

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='GENERIC' AND role ='$myrole'");

	if($result->num_rows>0)
	{
		$dumPermission = $result->fetch_assoc()["permission"];
		if($dumPermission=="ALLOW" && $stepPermission)
		{
			$genericPermission = true;
		}

	}






	$operationalParams = [];
    $operationalPermission = false;

    $result = runQuery("SELECT * FROM processparams WHERE processname='$processname' AND step='OPERATIONAL' ORDER BY ordering");

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		$dumParam = $row["param"];
    		$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='OPERATIONAL' AND param='$dumParam'");
    		$dumval = "";
    		
    		if($result2->num_rows>0)
    		{
    			$dumval = $result2->fetch_assoc()["value"];
    			
    		}
    		array_push($operationalParams,[$dumParam,$dumval,$row["allowedvalues"],$row["type"]]);
    	}
    }

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='OPERATIONAL' AND role ='$myrole'");
    if( $currStep=="GENERIC" || $currStep=="OPERATIONAL" || $currStep=="TEST")
    {
    	$stepPermission = true;
    }
    else
    {
    	$stepPermission = false;
    }
	if($result->num_rows>0)
	{
		$dumPermission = $result->fetch_assoc()["permission"];
		if($dumPermission=="ALLOW" && $stepPermission)
		{
			$operationalPermission = true;
		}

	}


	$testParams = [];
    $testPermission = false;

    $result = runQuery("SELECT * FROM gradeproperties WHERE processname='$processname' ORDER BY ordering");

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		$dumParam = $row["properties"];


    		array_push($testParams,[$dumParam,""]);
    	}
    }



    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='TEST' AND role ='$myrole'");
    if( $currStep=="OPERATIONAL" || $currStep=="TEST")
    {
    	$stepPermission = true;
    }
    else
    {
    	$stepPermission = false;
    }
	if($result->num_rows>0)
	{
		$dumPermission = $result->fetch_assoc()["permission"];
		if($dumPermission=="ALLOW" && $stepPermission)
		{
			$testPermission = true;
		}

	}

	if($currStep=="TEST")
    {
    	$stepPermission = true;
    }
    else
    {
    	$stepPermission = false;
    }







 

    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");


    if($show_alert)
    {
    	echo $alert;
    }

    unset($_POST);


?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		inobj.value = val;
	}


</script>

<style type="text/css">
	
#creation-tabdiv section {
  display: flex;
  flex-flow: row wrap;
}

#creation-tabdiv section > div {
  flex: 1;
  padding: 0.5rem;
}

#creation-tabdiv input[type=radio] {
  display: none;
}
#creation-tabdiv input[type=radio]:not(:disabled) ~ label {
  cursor: pointer;
}
#creation-tabdiv input[type=radio]:disabled ~ label {
  color: #bcc2bf;
  border-color: #bcc2bf;
  box-shadow: none;
  cursor: not-allowed;
}

#creation-tabdiv label {
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

#creation-tabdiv input[type=radio]:checked + label {
  background: #4099FF;
  color: white;
  box-shadow: 0px 0px 20px rgba(64, 153, 255, 0.75);
}
#creation-tabdiv input[type=radio]:checked + label::after {
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



#creation-tabdiv p {
  font-weight: 900;
}

@media only screen and (max-width: 700px) {
  #creation-tabdiv section {
    flex-direction: column;
  }
}


input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}2


</style>


<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5><?php echo $processid; ?></h5>
					<span>Edit Melting process parameters</span>
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



<ul class="nav nav-tabs md-tabs " role="tablist">
<li class="nav-item">
<a class="nav-link active" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i>Creation</a>
<div class="slide"></div>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#generic-tabdiv" role="tab"><i class="icofont icofont-ui-file "></i>Generic</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#operational-tabdiv" role="tab"><i class="icofont icofont-ui-folder"></i>Operational Parameter</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i>Test Properties</a>
<div class="slide"></div>
</li>




<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#stock-tabdiv" role="tab"><i class="icofont icofont-page"></i>Stock/Inventory</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#notes-tabdiv" role="tab"><i class="icofont icofont-edit"></i>Notes</a>
<div class="slide"></div>
</li>





</ul>

<div class="tab-content card-block">

<div class="tab-pane active" id="creation-tabdiv" role="tabpanel">

<form method="POST">
				<?php

				if($editidPermission)
						{
							?>

					<div class="form-group" style="display:flex; justify-content: center;">
						<input type="hidden" name="processid" value="<?php echo $processid; ?>">

						<div class="col-sm-3">
							<div class="input-group input-group-button">

								
								<input name="processidName[]" readonly required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo explode("-", $processid)[0]; ?>"><div> </div>
								<input name="processidName[]" readonly required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;"value="<?php echo explode("-", $processid)[1]; ?>"><div> </div>
								<input name="processidName[]" required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo explode("-", $processid)[2]; ?>">
							</div>
						</div>
					</div>
					<?php 
						}
					?>
	<section>

		<?php 
				$result = runQuery("SELECT furnaces.prefix FROM processentryparams LEFT JOIN furnaces ON value=furnacename WHERE processid = '$processid' AND param='Furnace'");

				$currFid = $result->fetch_assoc()["prefix"];

				$result = runQuery("SELECT * FROM furnaces");
				if($result->num_rows>0)
				{

					while($row=$result->fetch_assoc())
					{

						


		?>

	<div>
	  <input required type="radio" id="control_<?php echo $row["prefix"]?>"  value="<?php echo $row["prefix"]?>" disabled >
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
	<?php

	if($editidPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

				<?php
			}



	?>

	<script type="text/javascript">
			
			
				document.getElementById("control_<?php echo $currFid?>").checked = true;
		
			

		</script>



</form>


</div>

<div class="tab-pane" id="generic-tabdiv" role="tabpanel">

<form method="POST">

	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	<?php 
		
		for($i=0;$i<count($genericParams);$i++)
		{

				if($genericParams[$i][0]==$MASS_TITLE&&$genericPermission)
				{
					?>

					<div class="form-group row">
						<label class="col-sm-2"><?php echo $genericParams[$i][0]; ?></label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
								<input type="hidden" name="allparams[]" value="<?php echo $MASS_TITLE; ?>">
								<input required  type="number" step="0.01" min="0" name="paramsvalue[]" class="form-control form-control-uppercase" placeholder="" value="<?php echo $genericParams[$i][1]; ?>">
								<div class="input-group-append">
								<button class="btn btn-primary" type="button"><i class="feather icon-airplay"></i>Read</button>
								</div>
							</div>
						</div>
					</div>

					<?php
				}
				else if(!$genericPermission && $genericParams[$i][4]=="LOCKED")
				{

					if($genericParams[$i][2])
					{
						optionInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],$genericParams[$i][2],'readonly required');
					}
					else if($genericParams[$i][3] == "INTEGER")
					{
						integerInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}
					else if($genericParams[$i][3] == "DECIMAL")
					{
						decimalInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}
					else if($genericParams[$i][3] == "STRING")
					{
						stringInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}
					else if($genericParams[$i][3] == "DATE")
					{
						dateInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}
					else if($genericParams[$i][3] == "TIME")
					{
						timeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}
					else if($genericParams[$i][3] == "DATE TIME")
					{
						datetimeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly required');
					}

				}
				else if($genericPermission && $genericParams[$i][4]=="LOCKED")
				{

					if($genericParams[$i][2])
					{
						optionInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],$genericParams[$i][2],'required');
					}
					else if($genericParams[$i][3] == "INTEGER")
					{
						integerInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}
					else if($genericParams[$i][3] == "DECIMAL")
					{
						decimalInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}
					else if($genericParams[$i][3] == "STRING")
					{
						stringInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}
					else if($genericParams[$i][3] == "DATE")
					{
						dateInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}
					else if($genericParams[$i][3] == "TIME")
					{
						timeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}
					else if($genericParams[$i][3] == "DATE TIME")
					{
						datetimeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'required');
					}

				}
				else if(!$genericPermission)
				{

					if($genericParams[$i][2])
					{
						optionInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],$genericParams[$i][2],'readonly');
					}
					else if($genericParams[$i][3] == "INTEGER")
					{
						integerInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}
					else if($genericParams[$i][3] == "DECIMAL")
					{
						decimalInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}
					else if($genericParams[$i][3] == "STRING")
					{
						stringInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}
					else if($genericParams[$i][3] == "DATE")
					{
						dateInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}
					else if($genericParams[$i][3] == "TIME")
					{
						timeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}
					else if($genericParams[$i][3] == "DATE TIME")
					{
						datetimeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],'readonly');
					}

				}
				else
				{
					if($genericParams[$i][2])
					{
						optionInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],$genericParams[$i][2],false);
					}
					else if($genericParams[$i][3] == "INTEGER")
					{
						integerInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}
					else if($genericParams[$i][3] == "DECIMAL")
					{
						decimalInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}
					else if($genericParams[$i][3] == "STRING")
					{
						stringInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}
					else if($genericParams[$i][3] == "DATE")
					{
						dateInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}
					else if($genericParams[$i][3] == "TIME")
					{
						timeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}
					else if($genericParams[$i][3] == "DATE TIME")
					{
						datetimeInput($genericParams[$i][0],"generic-".$genericParams[$i][0],$genericParams[$i][1],false);
					}


					
				}
				
		}


	?>
	
	


	<div class="form-group row">
		<?php

		

			
			if($genericPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="submit" name="updateprocess2" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

				<?php
			}

			

			

		?>
		
	</div>

</form>

</div>

<div class="tab-pane" id="operational-tabdiv" role="tabpanel">

<form method="POST">
	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	
	<?php 
		
		for($i=0;$i<count($operationalParams);$i++)
		{
				
				if(!$operationalPermission)
				{

					if($operationalParams[$i][3] == "INTEGER")
					{
						integerInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}
					else if($operationalParams[$i][3] == "DECIMAL")
					{
						decimalInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}
					else if($operationalParams[$i][3] == "STRING")
					{
						stringInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}
					else if($operationalParams[$i][3] == "DATE")
					{
						dateInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}
					else if($operationalParams[$i][3] == "TIME")
					{
						timeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}
					else if($operationalParams[$i][3] == "DATE TIME")
					{
						datetimeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'readonly');
					}

				}
				else
				{
					if($operationalParams[$i][3] == "INTEGER")
					{
						integerInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}
					else if($operationalParams[$i][3] == "DECIMAL")
					{
						decimalInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}
					else if($operationalParams[$i][3] == "STRING")
					{
						stringInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}
					else if($operationalParams[$i][3] == "DATE")
					{
						dateInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}
					else if($operationalParams[$i][3] == "TIME")
					{
						timeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}
					else if($operationalParams[$i][3] == "DATE TIME")
					{
						datetimeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],false);
					}


					
				}
				
		}


	?>
	
	


	<div class="form-group row">
		<?php

		

			
			if($operationalPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="submit" name="updateprocess3" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

				<?php
			}

			

			

		?>
		
	</div>

</form>


</div>


<div class="tab-pane" id="test-tabdiv" role="tabpanel">

<form method="POST">
<?php
if($testPermission)
				{


					?>
	<div class="form-group row">
			<label class="col-sm-2">Paste Result</label>
			<div class="col-sm-10">
				<div class="input-group input-group-button">
					<input  type="text"  class="form-control" id="test-pastevalue" placeholder="">
					<div class="input-group-append">
					<button class="btn btn-primary" onclick="pastevalues('test')" type="button"><i class="feather icon-check"></i>Apply</button>
					</div>
				</div>
			</div>
		</div>

		<?php
}

					?>
	
	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
<div id="test-propdiv">
	

	<?php 
		
		for($i=0;$i<count($testParams);$i++)
		{
				
				if($testPermission)
				{

						decimalTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],false);
					
					

				}
				
				
		}


	?>
	
	
</div>

	<div class="form-group row">
		<?php

		

			
			if($testPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="submit" name="updateprocess4" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Add test Result</button>
				</div>

				<?php
			}

			

			

		?>
		
	</div>

</form>


<br><br><br>


<?php
	
	$result = runQuery("SELECT * FROM processtest WHERE processid='$processid'");
	$k=1;
	if($result->num_rows>0)
	{

		?>
<h5>All Tests</h5>
<table class="table">
	<th rowspan="1" colspan="1"  style="width: 84.578125px;">Sl No.</th>
	<th rowspan="1" colspan="1" >Test Id</th>
	<th rowspan="1" colspan="1" >Entry Time</th>
	<th rowspan="1" colspan="1" >Options</th>
	<th rowspan="1" colspan="1" ></th>


	<?php 

		while($row=$result->fetch_assoc())
		{

			$dumtestid = $row["testid"];
			$result2 = runQuery("SELECT * FROM processtestparams WHERE testid='$dumtestid'");
			$dumParam = "[";
			$dumValue = "[";
			if($result2->num_rows>0)
			{
				while($row2 = $result2->fetch_assoc())
				{
						$dumParam = $dumParam . "'" . $row2["param"]."',";
						$dumValue = $dumValue . "'" . $row2["value"]."',";
				}
			}

			$dumParam = $dumParam. "]";
			$dumValue = $dumValue. "]";
			


			if($k%2==0)
			{
				$type = "even";
			}
			else
			{
				$type = "odd";
			}

			if($row["status"]=="REJECT")
			{
				echo "<tr role=\"row\" class=\"".$type."\" style=\"color: red\">";
			}
			else if($row["status"]=="ACCEPT")
			{
				echo "<tr role=\"row\" class=\"".$type."\" style=\"color: green\">";
			}
			else
			{
				echo "<tr role=\"row\" class=\"".$type."\" >";
			}

			

			echo "<td>".$k++."</td>";
			echo "<td>".$row["testid"]."</td>";
			echo "<td>".$row["entrytime"]."</td>";
			if($row["status"]=="DEFAULT")
			{
				echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\" onclick=\"viewTest('".$row["testid"]."',".$dumParam.",".$dumValue.")\"><i class=\"fa fa-eye\"></i>View</button><button type=\"button\" class=\"btn btn-success m-b-0\" style=\"margin-left:30px;\" onclick=\"acceptTest('".$row["testid"]."')\"><i class=\"fa fa-check\"></i>Accept</button><button type=\"button\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"rejectTest('".$row["testid"]."')\"><i class=\"fa fa-times\"></i>Reject</button></div></td><td>";
			}
			else if($row["status"]=="ACCEPT")
			{
				echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\" onclick=\"viewTest('".$row["testid"]."',".$dumParam.",".$dumValue.")\"><i class=\"fa fa-eye\"></i>View</button></div></td><td>ACCEPTED</td>";
			}
			else if($row["status"]=="REJECT")
			{
				echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\" onclick=\"viewTest('".$row["testid"]."',".$dumParam.",".$dumValue.")\"><i class=\"fa fa-eye\"></i>View</button></div></td><td>REJECTED</td>";
			}
			

			
			echo "</tr>";

			


		}

	?>
</table>

<?php 
	}

?>

</div>





<div class="tab-pane" id="stock-tabdiv" role="tabpanel">

<form method="POST">

	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	
	<div class="form-group row">
		
		<div class="col-sm-12">
		<button type="submit" name="updateprocess6" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>
	</div>

</form>

</div>


<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM processnotes WHERE processid='$processid' ORDER by time");

                		if($result->num_rows>0)
                		{
                			while($row = $result->fetch_assoc())
                			{

                					if($row["sender"]==$myuserid)
                					{
                						echo "<blockquote class=\"blockquote blockquote-reverse\"><p class=\"m-b-0\">".$row["note"]."</p><footer class=\"blockquote-footer\">You, <i>".$row["time"]."</i></footer></blockquote>";
                					}
                					else
                					{
                						echo "<blockquote class=\"blockquote\"><p class=\"m-b-0\">".$row["note"]."</p><footer class=\"blockquote-footer\">".getFullName($row["sender"]).", <i>".$row["time"]."</i></footer></blockquote>";
                					}
                			}
                		}

                ?>
               
            </div>
            
            <div class="input-group input-group-button">
            <textarea required rows="1" cols="500" class="form-control" placeholder="" name="note" ></textarea>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" name="addNotes"><i class="fa fa-plus"></i>Add Note</button>
            </div>
            </div>
            

    </div>

</form>

</div>





</div></div>
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


function pastevalues(step)
{

		divobj = document.getElementById(step+"-propdiv");
		if(step=="test")
		{
				var val = document.getElementById("test-pastevalue").value;
				val = val.split("\t")

				for(var i=0;i<divobj.children.length;i++)
				{
					divobj.children[i].children[1].children[1].value = val[i];
				}
		}
}



function acceptTest(testid)
{
	Swal.fire({
		  icon: 'success',
		  title: 'Accept test',
		  html: 'Are you sure you want to accept Test -  '+testid,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		
			  		var form  = document.createElement('form');
			  		form.setAttribute('method','POST');

			  		var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"processid");
						i.setAttribute('value',"<?php echo $processid ?>");

						form.appendChild(i);


						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"testid");
						i.setAttribute('value',testid);

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"accepttest");
						i.setAttribute('value',"");

						form.appendChild(i);

						document.body.appendChild(form);
						form.submit();
			  		

				}
			})
}


function rejectTest(testid)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Reject test',
		  html: 'Are you sure you want to reject Test -  '+testid,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		var form  = document.createElement('form');
			  		form.setAttribute('method','POST');

			  		var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"processid");
						i.setAttribute('value',"<?php echo $processid ?>");

						form.appendChild(i);


						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"testid");
						i.setAttribute('value',testid);

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"rejecttest");
						i.setAttribute('value',"");

						form.appendChild(i);

						document.body.appendChild(form);
						form.submit();

				}
			})
}


function viewTest(testid,params,values)
{
	
	var rows = "";
	for(var i =0;i<params.length;i++)
	{
		rows = rows + "<tr><td>"+params[i]+"</td><td>"+values[i]+"<td></tr>";
	}

	
	Swal.fire({
		  icon: 'info',
		  title: testid,
		  html: '<table class="table"><th>Property</th><th>Value</th>'+rows+'</table>',
		  confirmButtonText: 'Ok',
		  cancelButtonText: 'No',
		  showCancelButton: false,
		  
		})
}


$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	

  		

  	

});




    
    var itemContainer = $("#notesDiv");
    itemContainer.slimScroll({
        height: '500px',
        start: 'bottom',
        alwaysVisible: true
    });







</script>