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
        "Page Title" => "Edit Raw Blend | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "process-rawblend-view",
        "MainMenu"	 => "process_rawblend",

    ];


    $processname = "Raw Blend";
    $QUANTITY = 0;

    if(!isset($_POST["processid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $processid = $_POST["processid"];

    $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }
   
    

     if(isset($_POST["updateprocess1"]))
    {
    	
    	$newprocessid = $_POST["processidName"][0].$_POST["processidName"][1].$_POST["processidName"][2];


    	$result = runQuery("SELECT * FROM processentry WHERE processid='$newprocessid'");
    	if($result->num_rows==0)
    	{


    		runQuery("INSERT INTO processentry (SELECT '$newprocessid',processname,currentstep,entrytime,islocked FROM processentry WHERE processid='$processid')");
    		$result2 = runQuery("SELECT * FROM processtest WHERE processid = '$processid'");
    		if($result2->num_rows>0)
    		{
    			while($row2=$result2->fetch_assoc())
    			{
    				$testid = $row2["testid"];
    				$newtestid = str_replace($processid,$newprocessid,$testid);



    					runQuery("INSERT INTO processtest (SELECT '$newtestid','$newprocessid',processname,entrytime,status FROM processtest WHERE testid='$testid')");
    					runQuery("UPDATE processtestparams SET processid='$newprocessid', testid='$newtestid' WHERE testid='$testid'");
    			}
    		}

    		runQuery("DELETE FROM processtest WHERE processid='$processid'");


    		runQuery("UPDATE processnotes SET processid='$newprocessid' WHERE processid='$processid'");
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

    $currStep = runQuery("SELECT currentstep,entrytime FROM processentry WHERE processid='$processid'");
    $currStep = $currStep->fetch_assoc();

    $entrytime = Date('d-M-Y H:i',strtotime($currStep["entrytime"]));
    $currStep = $currStep["currentstep"];

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


    if(isset($_POST['updateBlendMaster']))
    {
    	$allbmgrades = $_POST["bmgrades"];
    	
    	runQuery("DELETE FROM blendmastergrade WHERE processid='$processid'");
    	for($i=0;$i<count($allbmgrades);$i++)
    	{
    		$dumgrade = $allbmgrades[$i];
    		runQuery("INSERT INTO blendmastergrade VALUES(NULL,'$processid','$dumgrade')");
    	}
    }


    if(isset($_POST["updateprocess4"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];
    	$qvalue = $_POST['quarantine'];

    		$sqlprefix = $processid."/%";
    		$prefix = $processid."/";
    		
    		$result = runQuery("SELECT * FROM processtest WHERE testid LIKE '$sqlprefix' ORDER BY entrytime DESC LIMIT 1");

	    	if($result->num_rows==0)
	    	{	
	    		$alpha = "A";
	    	}
	    	else
	    	{
	    		$lastID = $result->fetch_assoc()["testid"];
		    	$lastID = substr($lastID, 0, strpos($lastID, $prefix)).substr($lastID, strpos($lastID, $prefix)+strlen($prefix));
		    	$alpha = ++$lastID;
	    	}
	    	$prefix = $prefix . $alpha;

	    
	    	runQuery("INSERT INTO processtest VALUES('$prefix','$processid','$processname',CURRENT_TIMESTAMP,'DEFAULT')");
	    	
	    	for($i=0;$i<count($allParams);$i++)
	    	{
	    		
	    		if($qvalue[$i])
	    		{

	    			runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    		}
	    		elseif($sym==">")
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);

	    			if(floatval($paramsvalue[$i])>floatval($currv))
	    			{
	    				runQuery("UPDATE processentry SET islocked ='BLOCKED' WHERE processid='$processid'");
	    				runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    			}
	    		}
	    		else
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);
	    			if(floatval($paramsvalue[$i])<floatval($currv))
	    			{
	    				runQuery("UPDATE processentry SET islocked ='BLOCKED' WHERE processid='$processid'");
	    				runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    			}
	    		}
	    		
	    	}

    	if($currStep=="OPERATIONAL")
    	{
    		runQuery("UPDATE processentry SET currentstep='TEST' WHERE processid='$processid'");
    	}



    }


    if(isset($_POST["updateprocess5"]))
    {

    	
    	runQuery("DELETE FROM processentryparams WHERE processid='$processid' AND step='PARENT'");

    	
    	if(isset($_POST["parentname"]))
    	{
    		$total1 = 0;
    		for($i=0;$i<count($_POST["parentname"]);$i++)
	    	{
	    		$dumname = $_POST["parentname"][$i];
	    		$dumval = floatval($_POST["parentvalues"][$i]);
	    		$total1 +=floatval($_POST["parentvalues"][$i]);
	 

	    		runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','PARENT','$dumname','$dumval')");
	    		runQuery("UPDATE processentry SET islocked='LOCKED' WHERE processid='$dumname'");

	    	}

	    	runQuery("DELETE FROM processentryparams WHERE processid='$processid' AND param='$MASS_TITLE'");
	    	runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','GENERIC','$MASS_TITLE','$total1')");
    	}
 
    	
    
    }


    if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO processnotes VALUES(NULL,'$processid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }


    

      if(isset($_POST["rejecttest"]))
    {

    	$testid = $_POST['testid'];
    	runQuery("DELETE FROM processtestparams WHERE testid = '$testid'");
    	runQuery("DELETE FROM processtest WHERE testid = '$testid'");
    	$currTab = "test-tabdiv";
    	
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


    $creationPermission = false;

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='CREATION' AND role ='$myrole'");

		if($result->num_rows>0)
		{
			$dumPermission = $result->fetch_assoc()["permission"];
			if($dumPermission=="ALLOW" )
			{
				$creationPermission = true;
			}

		}




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


	$blendmasterpermission = false;

	$result = runQuery("SELECT * from blendmastergrade WHERE processid='$processid'");

	if($result->num_rows>=1)
	{
		$blendmasterpermission = true;
	}

	$testParams = [];
    $testPermission = false;
    $currGradeName = "**";
    $result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='OPERATIONAL' AND param='$GRADE_TITLE'");
		if($result2->num_rows>0)
		{
			$result2 = $result2->fetch_assoc();
			$currGradeName = $result2["value"];
		}


		
    

    $result = runQuery("SELECT * FROM gradeproperties WHERE processname='$processname' AND gradename='$currGradeName' ORDER BY ordering");

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		$dumParam = $row["properties"];



				$result2 = runQuery("SELECT * FROM processgradesproperties WHERE processname='$processname' AND gradeparam='$dumParam'");


    		$result2 = $result2->fetch_assoc();



    		array_push($testParams,[$dumParam,"","",$result2["type"],$row["min"],$row["max"],$row["quarantine"]]);
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



		$parentParams = [];
    $parentPermission = false;

    $dum = getAllParents($processid);
    $parentParams = $dum["Parents"];
    $parent_total = $dum["Total"];

   
   $blendmasterData = getAllBlendmasterGrades($processid,$processname);



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
}


</style>


<script type="text/javascript">
	
	function titleicontoRefresh()
	{
		var titleicon = document.getElementById('titleicon');
		titleicon.classList.remove("fa-shopping-bag");
		titleicon.classList.add("fa-refresh");

	}
	function titleicontonormal()
	{
		var titleicon = document.getElementById('titleicon');
		titleicon.classList.remove("fa-refresh");
		titleicon.classList.add("fa-shopping-bag");
		

	}

	function reloadCurrPage()
	{
		var tabs = document.getElementById("tablist");

  	for(var i=0;i<tabs.children.length;i++)
  	{
  		
  		
  		if(tabs.children[i].children[0].classList.contains("active"))
  		{
  				var currTab = tabs.children[i].children[0].getAttribute("href");
					currTab = currTab.substring(1);


						var form  = document.createElement('form');
			  		form.setAttribute('method','POST');

			  		var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"processid");
						i.setAttribute('value',"<?php echo $processid ?>");

						form.appendChild(i);


						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"currtab");
						i.setAttribute('value',currTab);

						form.appendChild(i);

						document.body.appendChild(form);
						form.submit();

					
  		}
  	}
	}
</script>


<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i id="titleicon" onmouseenter="titleicontoRefresh()" onmouseleave="titleicontonormal()" onclick="reloadCurrPage()" style="cursor: pointer;"  class="fa fa-shopping-bag bg-c-blue"></i>
				
				<div class="d-inline">
					<h5>Editing: <?php echo $processid; ?> - Created on (<?php echo $entrytime; ?>)</h5>
					<span>Edit Raw Blend parameters</span>
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


<ul class="nav nav-tabs md-tabs " role="tablist" id="tablist">
	



<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i>Creation</a>
<div class="slide"></div>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#generic-tabdiv" role="tab"><i class="icofont icofont-ui-file "></i>Generic</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#operational-tabdiv" role="tab"><i class="icofont icofont-speed-meter"></i>Operational Parameter</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#blendmaster-tabdiv" role="tab"><i class="icofont icofont-link"></i>Blend Master</a>
<div class="slide"></div>
</li>



<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i>Test Properties</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#parent-tabdiv" role="tab"><i class="icofont icofont-link"></i>Forward Tracking</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#notes-tabdiv" role="tab"><i class="icofont icofont-edit"></i>Notes</a>
<div class="slide"></div>
</li>




</ul>

<div class="tab-content card-block">

<div class="tab-pane" id="creation-tabdiv" role="tabpanel">

<form method="POST">
				<?php

				if($editidPermission&& $creationPermission)
						{
							?>

					<div class="form-group" style="display:flex; justify-content: center;">
						<input type="hidden" name="processid" value="<?php echo $processid; ?>">
						<input type="hidden" name="currtab" value="creation-tabdiv">

						<div class="col-sm-6">
							<div class="input-group input-group-button">

								
								<input name="processidName[]" readonly required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo substr($processid, 0,2) ?>"><div></div>
								<input name="processidName[]" type="hidden" value=" "><div></div>
								
								<input name="processidName[]" required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo substr($processid, 2) ?>">


							</div>
						</div>
					</div>
					<?php 
						}
					?>
	
<br><br>
	<?php

	if($editidPermission&& $creationPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="button" class="btn btn-primary m-b-0 pull-left" onclick="window.open('/user/print/rawblend-tag.php?processid=<?php echo $processid; ?>&grade=<?php echo $currGradeName; ?>&quantity=<?php echo getTotalQuantity($processid) ?>')"><i class="icofont icofont-barcode"></i>Generate Label</button>
				<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

				<?php
			}



	?>





</form>


</div>

<div class="tab-pane" id="generic-tabdiv" role="tabpanel">

<form method="POST">

	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	<input type="hidden" name="currtab" value="generic-tabdiv">
	<?php 
		
		for($i=0;$i<count($genericParams);$i++)
		{

				if($genericParams[$i][0]==$MASS_TITLE&&$genericPermission)
				{
					if($genericParams[$i][1])
					{
						$QUANTITY += $genericParams[$i][1];
					}
					?>

					<div class="form-group row">
						<label class="col-sm-2"><?php echo $genericParams[$i][0]; ?></label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input readonly  type="number" step="0.01" min="0"  class="form-control form-control-uppercase" placeholder="" value="<?php echo $genericParams[$i][1]; ?>">
								<div class="input-group-append">
								
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
	<input type="hidden" name="currtab" value="operational-tabdiv">
	
	<?php 
		
		for($i=0;$i<count($operationalParams);$i++)
		{
				
				if($operationalParams[$i][0]==$GRADE_TITLE)
				{

					$result = runQuery("SELECT * FROM processgrades WHERE processname='$processname'");
					$dum  = "";
					
					$dumValue = "";
					if($result->num_rows>0)
					{
						
						while($row=$result->fetch_assoc())
						{
								$dum = $dum . $row["gradename"] . ",";
								
						}

						$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='OPERATIONAL' AND param='$GRADE_TITLE'");
						if($result2->num_rows>0)
						{
							$result2 = $result2->fetch_assoc();
							$dumValue = $result2["value"];
						}

					}

					if($dumValue)
					{
						stringInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$dumValue,'readonly');
					}
					else if(!$dumValue&&$operationalPermission)
					{
						optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$dum,false);
					}
					else if(!$dumValue&&!$operationalPermission)
					{
						optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$dum,'readonly');
					}



					

				}
				
				else if(!$operationalPermission)
				{

					if($operationalParams[$i][2])
					{
						optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$operationalParams[$i][2],'readonly');
					}
					else if($operationalParams[$i][3] == "INTEGER")
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
					if($operationalParams[$i][2])
					{
						optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$operationalParams[$i][2],false);
					}
					else if($operationalParams[$i][3] == "INTEGER")
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



<?php


if($operationalPermission)
			{
				?>
				


				<br><br>

				
				<h4>Select Grades for Blend Master</h4>
				<form method="POST">
					<input type="hidden" name="currtab" value="operational-tabdiv">
					<?php 

							$result = runQuery("SELECT * FROM processgrades WHERE processname='Raw Bag'");
							$k=0;
							while($row = $result->fetch_assoc())
							{

							$dumgrade = $row["gradename"];
							$checked = "";
							$dumR = runQuery("SELECT * FROM blendmastergrade WHERE processid='$processid' AND gradename='$dumgrade'");
							if($dumR->num_rows>=1)
							{
								$checked = "checked";
							}

					?>

					<div class="checkbox-color checkbox-primary">
						<input id="bmgrades-<?php echo $k;?>" type="checkbox" <?php echo $checked; ?> name="bmgrades[]" value="<?php echo $row["gradename"] ?>">
						<label for="bmgrades-<?php echo $k;?>">
						<?php echo $row["gradename"] ?>
						</label>
					</div>


				

				<?php
				$k++;
					}
				?>

				<input type="hidden" name="processid" value="<?php echo $processid; ?>">
				<div class="col-sm-12">
				<button type="submit" name="updateBlendMaster" id="updateBlendMaster-submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Blend Master</button>
				</div>
				</form>

				<?php
			}

			?>






</div>

<style>

.tab-pane.bm2{padding:0!important;}
	.table.bms{font-size:10px!important;}
.table td, .table th {padding:0.45rem 0.25rem;}
.table th.bm{background-color:#990000;color:#fff;text-wrap:normal;word-wrap:break-word;width:150px;}
</style>



<div class="tab-pane bm2" id="blendmaster-tabdiv" role="tabpanel">
<?php if($blendmasterpermission){?>
	<table class="table table-striped table-bordered bms">
	
	

	<?php
		$blenmasterparams = $blendmasterData[1];
			$blendmasterData = $blendmasterData[0];
		for($i=1;$i<count($blendmasterData[0]);$i++)
		{
			echo "<th class='bm'>".$blendmasterData[0][$i]."</th>";
		}

	?>


	<th rowspan="1" class="bm">Blend Qty</th>
	<form method="POST">

		<input type="hidden" name="processid" value="<?php echo $processid; ?>">
		<input type="hidden" name="currtab" value="blendmaster-tabdiv">

<tbody id="blendmasterparenttbody">
	
	<?php 

			for($i=1;$i<count($blendmasterData);$i++)
			{
				echo "<tr>";

				?>
				<td>
				<div class="checkbox-color checkbox-primary">
						<input onchange="evalAverage(this)"  type="checkbox" <?php echo $blendmasterData[$i][0]; ?> id="bmparent-<?php echo $i; ?>"  value="<?php echo $blendmasterData[$i][2]; ?>" name = "parentname[]">
						
						<label for = "bmparent-<?php echo $i; ?>">
							<?php echo $blendmasterData[$i][2]; ?>
						</label>
					</div>
				</td>
				
				<?php

				for($j=3;$j<count($blendmasterData[$i]);$j++)
				{
	?>

		<?php if($j == count($blendmasterData[$i])-1){ ?>	
			<td id = "bmparent-<?php echo $i."-balqty"; ?>"><?php echo $blendmasterData[$i][$j]; ?></td>
		<?php }else{ ?>
		<td id = "bmparent-<?php echo $i."-"."$j"; ?>"><?php echo $blendmasterData[$i][$j]; ?></td>

		<?php } ?>

	<?php
		
			}
			$max = $blendmasterData[$i][1]+$blendmasterData[$i][count($blendmasterData[$i])-1];
			if($blendmasterData[$i][0]=="checked")
			{
							echo "<td ><input id=\"bmparent-".$i."-qty\" type=\"number\" name = \"parentvalues[]\" max = ".$max."  value=\"".$blendmasterData[$i][1]."\"></td>";

			}
			else
			{
							echo "<td ><input id=\"bmparent-".$i."-qty\" type=\"number\" name = \"parentvalues[]\" max = ".$max."  disabled value=\"".$blendmasterData[$i][1]."\"></td>";

			}
			echo "</tr>";
		}
	?>

	<tr>
		<th colspan="3">Weighted Average</th>
		
	</tr>

</tbody>

</table>


<div class="col-sm-12">
		<button type="submit"  name="updateprocess5" id="process5-submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
		</div>

</form>
<br><br>

<script type="text/javascript">
	findAvg();
	function evalAverage(inObj)
	{

		if(inObj.checked)
		{

			document.getElementById(inObj.id+"-qty").disabled = false;
			document.getElementById(inObj.id+"-qty").value = document.getElementById(inObj.id+"-balqty").innerHTML;
			findAvg()
			
		}
		else
		{

			document.getElementById(inObj.id+"-qty").disabled = true;
			document.getElementById(inObj.id+"-qty").value = 0;
			findAvg()

			
		}
			

			
	}

	function findAvg()
	{
		var tbody = document.getElementById('blendmasterparenttbody');

			var avgdiv = tbody.children[tbody.children.length-1];
			var divstore = avgdiv; 
			
			var sum = new Array(tbody.children[0].children.length-5).fill(0);
			var total = new Array(tbody.children[0].children.length-5).fill(0);
			
			var totalSelected = 0;
			for(var i=0;i<tbody.children.length-1;i++)
			{
				currrow = tbody.children[i];
				totalSelected += parseFloat(currrow.children[currrow.children.length-1].children[0].value);
				for(var j = 3;j<currrow.children.length-2;j++)
				{
						if(currrow.children[j].innerHTML)
						{
							sum[j-3] += parseFloat(currrow.children[j].innerHTML) * parseFloat(currrow.children[currrow.children.length-1].children[0].value);
							total[j-3] += parseFloat(currrow.children[currrow.children.length-1].children[0].value);
						}
							
				}
				
			}

			avgdiv.innerHTML = "<th colspan=\"3\">Weighted Average</th>";

			for(var j = 3;j<tbody.children[0].children.length-2;j++)
			{
				if(sum[j-3]/total[j-3])
				{
					avgdiv.innerHTML += "<th>"+ Math.round((sum[j-3]/total[j-3])*100)/100 +"</th>";
				}
				else
				{
					avgdiv.innerHTML += "<th>0</th>";
				}
				
			}

			
			avgdiv.innerHTML += "<th></th>";
			avgdiv.innerHTML += "<th>"+ totalSelected +"</th>";

	}


</script>
<br><br>

<table class="table">
	<th rowspan="1" colspan="1" >Grade - <?php echo $currGradeName ?></th>
	

	<?php 

			for($i=0;$i<count($testParams);$i++)
			{
				?>

					<th rowspan="1" colspan="1"><?php echo $testParams[$i][0] ?></th>

				<?php
			}

	?>


<tbody>
	
	<tr>
		<td>Specs</td>
		<?php 

			for($i=0;$i<count($testParams);$i++)
			{
				?>

					<td><?php echo $testParams[$i][4]."-".$testParams[$i][5] ?></td>

				<?php
			}

	?>
	</tr>

</tbody>


</table>


<?php }?>
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

		<script type="text/javascript">
			function pastevalues(step)
{

		divobj = document.getElementById(step+"-tablediv");
		console.log(divobj);
		if(step=="test")
		{
				var val = document.getElementById("test-pastevalue").value;

				val = val.split("\t")
				
				for(var i=0;i<divobj.children.length;i++)
				{
					
					divobj.children[i].children[2].children[0].children[0].children[2].value = val[i];
				}
		}
}
		</script>

		<?php
}

					?>
	
	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	<input type="hidden" name="currtab" value="test-tabdiv">
<div class="form-group row">
				<table class="table table-striped table-bordered" id="process4table">
		<thead>
		<tr>

		<th>Property</th>
		<th>Min/Max</th>
		<th>Value</th>


		</tr>
		</thead>
		
		
		<tbody id="test-tablediv">


			<?php
		
		for($i=0;$i<count($testParams);$i++)
		{
		

?>




<tr>

<td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $testParams[$i][0] ?></span></td>
<td class="tabledit-view-mode"><div class="tabledit-span">Min: <?php echo $testParams[$i][4] ?></div>
<div class="tabledit-span">Max: <?php echo $testParams[$i][5] ?></div>
<div style="display: none;" class="tabledit-span">Quarantine: <?php echo $testParams[$i][6] ?></div>
</td>


<td>

	<?php
					if($testParams[$i][3] == "INTEGER")
					{
						integerTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required',$testParams[$i][4],$testParams[$i][5],$testParams[$i][6]);
					}
					else if($testParams[$i][3] == "DECIMAL")
					{
						decimalTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required',$testParams[$i][4],$testParams[$i][5],$testParams[$i][6]);
					}
					else if($testParams[$i][3] == "STRING")
					{
						stringTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required');
					}
					/*
					else if($testParams[$i][3] == "DATE")
					{
						dateTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required');
					}
					
					else if($testParams[$i][3] == "TIME")
					{
						timeTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required');
					}
					else if($testParams[$i][3] == "DATE TIME")
					{
						datetimeTestInput($testParams[$i][0],"test-".$testParams[$i][0],$testParams[$i][1],'required');
					}
					*/

?>

</td>


	

</tr>

<?php


	}
?>

	</tbody>


</table>

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
			$qstatus = "UNLOCKED";
			if($result2->num_rows>0)
			{
				while($row2 = $result2->fetch_assoc())
				{
						$dumParam = $dumParam . "'" . $row2["param"]."',";
						$dumValue = $dumValue . "'" . $row2["value"]."',";

						if($row2["status"]=="BLOCKED")
						{
							$qstatus = "BLOCKED";
						}
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

			if($qstatus=="UNLOCKED")
			{
				echo "<tr role=\"row\" class=\"".$type."\" >";
			}
			else
			{
				echo "<tr style=\"color:red;\" role=\"row\" class=\"".$type."\" >";
			}
			
				
			

			

			echo "<td>".$k++."</td>";
			echo "<td>".$row["testid"]."</td>";
			echo "<td>".$row["entrytime"]."</td>";
			
				echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\" onclick=\"viewTest('".$row["testid"]."',".$dumParam.",".$dumValue.")\"><i class=\"fa fa-eye\"></i>View</button><button type=\"button\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"rejectTest('".$row["testid"]."')\"><i class=\"fa fa-trash\"></i>Delete</button></div></td><td>";
			
			
			

			
			echo "</tr>";

			


		}

	?>
</table>

<?php 
	}

?>

</div>



<div class="tab-pane" id="parent-tabdiv" role="tabpanel">






<script type="text/javascript">
	

</script>





<hr>
<h5>Forward Tracking</h5>

<br>


<table class="table table-striped table-bordered col-lg-6">
<thead>
<tr>

<th>Annealing ID</th>
<th>Used Quantity</th>

</tr>
</thead>
<tbody>

<?php
	
	$result = runQuery("SELECT * FROM processentryparams WHERE step='PARENT' AND param = '$processid'");

	$totalforward = 0;
	if($result->num_rows>0)
	{
		while($row = $result->fetch_assoc())
		{


		$totalforward += floatval($row["value"]);


?>
		

		<tr>
		<td><?php echo $row["processid"]?></td>
		<td><?php echo $row["value"]?> kg</td>
		</tr>

		

<?php
		}

?>


		

<?php


}
else
{
	?>
		<tr>
				<td></td>
				<td></td>
		</tr>
	<?php
}
?>

<tr style="border-top: 2px black solid;">
			<th>Total Used</th>
			<th><?php echo $totalforward;?> kg</th>
		</tr>

		<tr>
			<th>Total Quantity</th>
			<th><?php echo $QUANTITY?> kg</th>
		</tr>

		<tr>
			<th>Total Left</th>
			<th><?php echo $QUANTITY - $totalforward;?> kg</th>
		</tr>

</tbody>

</table>




</div>


















<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
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
		  title: 'Delete test',
		  html: 'Are you sure you want to delete Test -  '+testid,
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
  	

  	var currTab = "<?php echo $currTab; ?>";
  	
  	document.getElementById(currTab).classList.add('active');

  	var tabs = document.getElementById("tablist");

  	for(var i=0;i<tabs.children.length;i++)
  	{
  		var tabid = ("#"+currTab);
  		
  		if(tabs.children[i].children[0].getAttribute("href")== tabid)
  		{
  			tabs.children[i].children[0].classList.add("active");
  		}
  	}
  	

});




    
    var itemContainer = $("#notesDiv");
    itemContainer.slimScroll({
        height: '500px',
        start: 'bottom',
        alwaysVisible: true
    });



</script>


