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

	require_once('helper_batch.php');
	$myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

    $PAGE = [
        "Page Title" => "Edit Batch | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "process-batch-view",
        "MainMenu"	 => "process_batch",

    ];


    $processname = "Batch";

    $oldprocess = 'Final Blend';
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
   	

   	runQuery("UPDATE processentry SET currentstep='TEST' WHERE processid='$processid'");
    
    

     if(isset($_POST["updateprocess1"]))
    {
    	
    	$newprocessid = $_POST["processidName"];


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
	    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch '.$processid.' ID changed to '.$newprocessid);
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
    	
    	
    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch ('.$processid.') Generic properties updated');
    	


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
    	
    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch ('.$processid.') Operational properties updated');

    }





    if(isset($_POST["updateprocess4"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];
    	$qvalue = $_POST['quarantine'];
    	$testedby = $_POST['testedby'];
    	$approvedby = $_POST['approved'];

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
	    		
	    		if($allParams[$i] == 'Tested By')
	    		{
	    			runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    		}
	    		else
	    		{

	    			runQuery("INSERT INTO processtestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");

	    			runQuery("INSERT INTO additional_process_data VALUES(NULL,'$processid','$prefix','$allParams[$i]','$testedby[$i]','$approvedby[$i]')");
	    		}
	    		
	    		
	    		
	    	}

    	if($currStep=="OPERATIONAL")
    	{
    		runQuery("UPDATE processentry SET currentstep='TEST' WHERE processid='$processid'");
    	}
    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch ('.$processid.') new Test added');


    }



    if(isset($_POST["updateinternaltestform"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];
    	
    	$testedby = $_POST['testedby'];
    	

    		$sqlprefix = $processid."/%";
    		$prefix = $processid."/";
    		
    		$result = runQuery("SELECT * FROM processinternaltest WHERE testid LIKE '$sqlprefix' ORDER BY entrytime DESC LIMIT 1");

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

	    
	    	runQuery("INSERT INTO processinternaltest VALUES('$prefix','$processid','$processname',CURRENT_TIMESTAMP,'DEFAULT')");
	    	
	    	for($i=0;$i<count($allParams);$i++)
	    	{
	    		
	    		

	    			runQuery("INSERT INTO processinternaltestparams VALUES(NULL,'$prefix','$processid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED','$testedby[$i]')");

	    	
	    		
	    		
	    		
	    	}

    	
    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch ('.$processid.') new internal Test added');


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
 
    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Bag ('.$processid.') parent IDs updated');
    
    }

    if(isset($_POST["approveprocess"]))
    {

    	$approved = $_POST['approved-by'];
    	$finalqty = $_POST['finalqty'];
    	$prodcode = $_POST['prodcode'];



    	runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','CREATION','approved-by','$approved')");
    	runQuery("DELETE FROM processentryparams WHERE processid='$processid' AND param='$MASS_TITLE'");
	    runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','GENERIC','$MASS_TITLE','$finalqty')");
	    runQuery("INSERT INTO processentryparams VALUES(NULL,'$processid','CREATION','prodcode','$prodcode')");
    	runQuery("UPDATE processentry SET islocked='BATCHED' WHERE processid='$processid'");

    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Batch ('.$processid.') approved by '.$approved);



    }


    if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO processnotes VALUES(NULL,'$processid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }


    

      if(isset($_POST["rejecttest"]))
    {

    	
    	
    	$testid = $_POST['testid'];

		
		
    	if($_POST['internal']=="true")
    	{

    		runQuery("DELETE FROM processinternaltestparams WHERE testid = '$testid'");
	    	runQuery("DELETE FROM processinternaltest WHERE testid = '$testid'");
	    	$currTab = "testinternal-tabdiv";
    	}
    	else
    	{

    		runQuery("DELETE FROM processtestparams WHERE testid = '$testid'");
	    	runQuery("DELETE FROM processtest WHERE testid = '$testid'");
	    	$currTab = "test-tabdiv";
    	}
		

    	
    	
    }


    $result = runQuery("SELECT currentstep,islocked FROM processentry WHERE processid='$processid'");
    $result = $result->fetch_assoc();
    $currStep = $result["currentstep"];

    if($result["islocked"]=="UNLOCKED")
    {
    	$editidPermission = true;
    	$approvedblend = false;
    }
    else
    {
    	$editidPermission = false;
    	$approvedblend = false;
    }

    if($result["islocked"]=="BATCHED" || $result["islocked"]=="LOCKED")
    {
    	$approvedblend = true;
    }

    $stepPermission = false;


    $creationPermission = false;

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$oldprocess' AND step='CREATION' AND role ='$myrole'");

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

    $result = runQuery("SELECT * FROM processparams WHERE processname='$oldprocess' AND step='GENERIC' ORDER BY ordering");

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

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$oldprocess' AND step='GENERIC' AND role ='$myrole'");

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

    $result = runQuery("SELECT * FROM processparams WHERE processname='$oldprocess' AND step='OPERATIONAL' ORDER BY ordering");

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

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$oldprocess' AND step='OPERATIONAL' AND role ='$myrole'");
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
    $currGradeName = "**";
    $result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='OPERATIONAL' AND param='$GRADE_TITLE'");
		if($result2->num_rows>0)
		{
			$result2 = $result2->fetch_assoc();
			$currGradeName = $result2["value"];
		}


		
    

    $result = runQuery("SELECT * FROM gradeproperties WHERE processname='$oldprocess' AND gradename='$currGradeName' ORDER BY ordering");

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		$dumParam = $row["properties"];



    		if(substr($dumParam,0,5)=="Sieve")
    		{

    			$print = "Printed";
    			$cum = "Cumulative";



	    		array_push($testParams,[$dumParam,"","","DECIMAL",$row["min"],$row["max"],"-","5",$print.", ".$cum]);

    		}
    		else
    		{

    			$result2 = runQuery("SELECT * FROM processgradesproperties WHERE processname='$oldprocess' AND gradeparam='$dumParam'");


	    		$result2 = $result2->fetch_assoc();



	    		array_push($testParams,[$dumParam,"","",$result2["type"],$row["min"],$row["max"],$row["quarantine"],$result2['mpif'],$result2['class']]);

    		}



				
    	}
    }

    

    $result = runQuery("SELECT * FROM processpermission WHERE processname='$oldprocess' AND step='TEST' AND role ='$myrole'");
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

    #$dum = getAllParents($processid);
    #$parentParams = $dum["Parents"];
    #$parent_total = $dum["Total"];

   





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
					<h3 style="margin-bottom:0;">Currently updating: <?php echo $processid; ?></h3>
					<p class="created">(Created on: <?php echo $entrytime; ?>)</p>
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
<a class="nav-link" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i> Creation</a>
<div class="slide"></div>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#generic-tabdiv" role="tab"><i class="icofont icofont-ui-file "></i> Generic</a>
<div class="slide"></div>
</li>


<?php
/*
?>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#operational-tabdiv" role="tab"><i class="icofont icofont-speed-meter"></i> Operational Parameter</a>
<div class="slide"></div>
</li>


<?php

*/
?>






<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i> Test Properties</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#testinternal-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i> Other Tests </a>
<div class="slide"></div>
</li>

<?php
/*
?>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#parent-tabdiv" role="tab"><i class="icofont icofont-link"></i>Forward Tracking</a>
<div class="slide"></div>
</li>

<?php

*/
?>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#approve-tabdiv" role="tab"><i class="icofont icofont-check"></i>Approve</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#notes-tabdiv" role="tab"><i class="icofont icofont-edit"></i> Notes</a>
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

								
								<input name="processidName"  required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo $processid ?>"><div></div>
								
								

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
				<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

				<?php
			}



	?>


<div class="col-sm-12">
	<button type="button" class="btn btn-primary m-b-0 pull-left" onclick="window.open('/user/print/batch-tag.php?processid=<?php echo $processid; ?>&grade=<?php echo $currGradeName; ?>&quantity=<?php echo getTotalQuantity($processid) ?>')"><i class="icofont icofont-barcode"></i>Generate Label</button>

	<button type="button" class="btn btn-primary m-b-0 ml-1 pull-left" onclick="window.open('/user/report/basic-batch.php?id=<?php echo $processid; ?>')"><i class="icofont icofont-page"></i>Generate Report</button>
</div>




</form>


</div>

<div class="tab-pane" id="generic-tabdiv" role="tabpanel">


<?php 
	
	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='PARENT'");
	
	$finalblendid = "";
	$finalblendqty ="";
	if($result->num_rows==1)
	{
		$result  = $result->fetch_assoc();

		$finalblendid = $result['param'];
		$finalblendqty = $result['value'];
	}



	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='$GRADE_TITLE'");
	$result  = $result->fetch_assoc();

	$currgrade = $result['value'];

	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='$MASS_TITLE'");
	$qty = "Not Assigned";
	if($result->num_rows>0)
	{
		$result=$result->fetch_assoc();
		$qty = $result['value'];
	}


	$cprodcode = "Not Assigned";
	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='prodcode'");
	if($result->num_rows>0)
	{
		$result=$result->fetch_assoc();
		$cprodcode = $result['value'];
	}




	$result = runQuery("SELECT * FROM processentry WHERE processid='$finalblendid'");
	$finalblenddate = "";
	if($result->num_rows==1)
	{
		$result  = $result->fetch_assoc();

		$finalblenddate = $result['entrytime'];
	}

	
	

?>




<table class="table table-striped">
	<tr>
		<td>Parent ID </td>
		<td> <?php echo $finalblendid; ?> ( Qty: <?php echo $finalblendqty; ?>)</td>
	</tr>

	<tr>
		<td>Parent Creation Date </td>
		<td> <?php echo $finalblenddate; ?></td>
	</tr>

	<tr>
		<td>Quantity Left </td>
		<td> <?php echo $qty; ?></td>
	</tr>


	<tr>
		<td>Grade </td>
		<td> <?php echo $currgrade; ?></td>
	</tr>

	<tr>
		<td>Prod Code </td>
		<td> <?php echo $cprodcode; ?></td>
	</tr>
</table>

</div>

<div class="tab-pane" id="operational-tabdiv" role="tabpanel">


</div>





<div class="tab-pane" id="test-tabdiv" role="tabpanel">



<?php
	
	$result = runQuery("SELECT * FROM processtest WHERE processid='$processid'");
	$k=1;
	if($result->num_rows>0)
	{

		?>
<h4>All Tests</h4>
<table class="table table-responsive table-xs table-striped" style="border:solid 1px grey">
<thead class="thead-dark" style="text-align:center;">
	<tr>
		<th rowspan="1" colspan="1"  style="width: 84.578125px;">Sl No.</th>
		<th rowspan="1" colspan="1" >Test Id</th>
		<th rowspan="1" colspan="1" >Entry Time</th>



		<th rowspan="1" colspan="1" >Options</th>
		<th rowspan="1" colspan="1" ></th>
	<tr>
</thead>
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
						$currParam = $row2["param"];
						$result3 = runQuery("SELECT * FROM additional_process_data WHERE processid='$processid' AND param1 ='$dumtestid' AND param2 = '$currParam'");
						$result3 = $result3->fetch_assoc();
						$dapproved = $result3['param4'];
						$result3 = $result3['param3'];
						echo "<script>console.log('".$dapproved."')</script>";
						if($dapproved)
						{
							$dumParam = $dumParam . "'" . $row2["param"] . " (Tested By: ".$result3.")(Approved By: ".$dapproved.")" ."',";
						}
						else
						{
							$dumParam = $dumParam . "'" . $row2["param"] . " (Tested By: ".$result3.")" ."',";
						}
						
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




<form method="POST">
<?php
if($testPermission)
				{


					?>
	<div class="form-group row">
			<!--<label class="col-sm-2">Paste Result</label>
			<div class="col-sm-10">
				<div class="input-group input-group-button">
					<input  type="text"  class="form-control" id="test-pastevalue" placeholder="">
					<div class="input-group-append">
					<button class="btn btn-primary" onclick="pastevalues('test')" type="button"><i class="feather icon-check"></i>Apply</button>
					</div>
				</div>
			</div>-->
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

<script type="text/javascript">
	function checkminmax(valuein,approvedby,min,max)
	{
		if(!valuein.value)
		{
			return;
		}

		if(min=="BAL" || max=="BAL")
		{
			approvedby.value = "";
			return;
		}

		currval = valuein.value;
		

		flag = true;
		if(min || min==0)
		{
			
			if(parseFloat(currval)<parseFloat(min))
			{
				flag = false;
				
			}
		}

		if( max || max==0)
		{
			
			if(parseFloat(currval)>parseFloat(max))
			{
				flag = false;

			}
		}

		if(!flag)
		{
			Swal.fire({
			icon: 'error',
		  title: 'Out of Bounds',
		  input: 'text',
		  inputLabel: 'Enter who approved.',
		  showCancelButton: false,
		  allowEscapeKey: false,
       allowOutsideClick: false,

		  inputValidator: (value) => {
		    if (!value) {
		      return 'Please enter who approved'
		    }
		  }
		}).then((result) => {
				approvedby.value = result.value;
				console.log(result.value);
		})

			

	}
}
</script>

<h4>Add Test Results</h4>

<div class="form-group row">
	<table class="table table-striped table-responsive table-xs" id="process4table">
		<thead>
		<tr>

		<th>Property</th>
		<th>Min/Max</th>
		<th>MPIF Number</th>
		<th>Value</th>
		<th>Tested By</th>

		</tr>
		</thead>
		
		
		<tbody id="test-tablediv">



			<?php
		
		for($i=0;$i<count($testParams);$i++)
		{
		
			$round = 0.01;

			if($testParams[$i][8]=="Chemical")
			{
				$round = 0.001;
			}
?>




<tr>

<td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $testParams[$i][0] ?></span></td>
<td class="tabledit-view-mode"><div class="tabledit-span">Min: <?php echo $testParams[$i][4]; ?></div>
<div class="tabledit-span">Max: <?php echo $testParams[$i][5] ?></div>
<div style="display: none;" class="tabledit-span">Quarantine: <?php echo $testParams[$i][6] ?></div>
</td>

<td><?php echo $testParams[$i][7];?></td>



<td>

	<?php
					if($testParams[$i][3] == "INTEGER")
					{
						?>

						<div class="form-group row">
						<div class="col-sm-12">

							<input type="hidden" name="allparams[]" value="<?php echo $testParams[$i][0] ?>">
							<input type="hidden" name="quarantine[]" value="<?php echo $testParams[$i][6] ?>">
							<input type="hidden" id= "testapprovedby-<?php echo $i;?>" name="approved[]" value="">
							<input type="number" onfocusout="checkminmax(this,document.getElementById('testapprovedby-<?php echo $i;?>'),'<?php echo $testParams[$i][4]?>','<?php echo $testParams[$i][5]?>')"  step="1"  class="form-control" name="paramsvalue[]" value="">
						</div>
						</div>

						<?php
					}
					else if($testParams[$i][3] == "DECIMAL")
					{
						?>

						<div class="form-group row">
						<div class="col-sm-12">

							<input type="hidden" name="allparams[]" value="<?php echo $testParams[$i][0] ?>">
							<input type="hidden" name="quarantine[]" value="<?php echo $testParams[$i][6] ?>">
							<input type="hidden" id= "testapprovedby-<?php echo $i;?>" name="approved[]" value="">
							<input type="number" onfocusout="checkminmax(this,document.getElementById('testapprovedby-<?php echo $i;?>'),'<?php echo $testParams[$i][4]?>','<?php echo $testParams[$i][5]?>')"  <?php if($testParams[$i][8]=="Chemical"){echo "step=0.001";}else{echo "step=0.01";} ?>  class="form-control" name="paramsvalue[]" value="">
						</div>
						</div>

						<?php
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




<td>


<input  type="text" list="labtechlist"  name="testedby[]" placeholder="Tested By" class="form-control">


			<datalist id="labtechlist">
				<option value="Lab1">Lab1</option>
				<option value="Lab2">Lab2</option>
				<option value="Lab3">Lab3</option>
			</datalist>
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



</div>




<div class="tab-pane" id="testinternal-tabdiv" role="tabpanel">

<form method="POST">
<?php
if($testPermission)
				{


					?>




		<?php
}

					?>
	
	<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	<input type="hidden" name="currtab" value="testinternal-tabdiv">

	<script type="text/javascript">
		function addinttestprop()
		{
			var tbody = document.getElementById('testinternal-tablediv');

			var tr = document.createElement('tr');

			tr.innerHTML = "<td><input class='form-control' required type ='text' name='allparams[]'></td>";
			tr.innerHTML += "<td><input class='form-control' required type ='text' name='paramsvalue[]'></td>";

			tr.innerHTML += "<td><input class='form-control' required type ='text' list='labtechlist' name='testedby[]'></td>";
			tr.innerHTML += "<td><button class='btn btn-primary' onclick='this.closest(\"tr\").remove()'><i class ='fa fa-trash'></i>Remove</button></td>";

			document.getElementById('updateinternaltest').disabled = false;
			tbody.appendChild(tr);
		}
	</script>

<div class="row">
				<button type="button" onclick="addinttestprop()"  class="btn btn-primary pull-right"><i class="feather icon-plus"></i>Add Test Property</button>
</div>
<br>

<div class="form-group row">
				<table class="table table-striped table-bordered" id="testinternaltable">
		<thead>
		<tr>

		<th>Property</th>
		<th>Value</th>
		<th>Tested By</th>
		<th></th>
		</tr>
		</thead>
		
		
		<tbody id="testinternal-tablediv">



			

	</tbody>


</table>

<?php

		

			
			if($testPermission)
			{
				?>
				<div class="col-sm-12">
				<button type="submit" disabled name="updateinternaltestform" id="updateinternaltest" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Add Test Result</button>
				</div>

				<?php
			}

			

			

		?>
	</div>



</form>


<br><br><br>


<?php
	
	$result = runQuery("SELECT * FROM processinternaltest WHERE processid='$processid'");
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
			$result2 = runQuery("SELECT * FROM processinternaltestparams WHERE testid='$dumtestid'");
			$dumParam = "[";
			$dumValue = "[";
			$qstatus = "UNLOCKED";
			if($result2->num_rows>0)
			{
				while($row2 = $result2->fetch_assoc())
				{
						$currParam = $row2["param"];
						
						
						
							$dumParam = $dumParam . "'" . $row2["param"] . " (Tested By: ".$row2["testedby"].")" ."',";
						
						
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
			
				echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\" onclick=\"viewTest('".$row["testid"]."',".$dumParam.",".$dumValue.")\"><i class=\"fa fa-eye\"></i>View</button><button type=\"button\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"rejectTest('".$row["testid"]."',true)\"><i class=\"fa fa-trash\"></i>Delete</button></div></td><td>";
			
			
			

			
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













<div class="tab-pane" id="approve-tabdiv" role="tabpanel">


	<?php 

	$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='PARENT'");


			$result  = $result->fetch_assoc();
			if($result)
			{
				$qty = $result["value"];
			}
			else{
				$qty = "";
			}
			

		if(!$approvedblend)
		{
	?>

<form method="POST" id="approveform">
		<input type="hidden" name="processid" value="<?php echo $processid; ?>">
	 	<input type="hidden" name="currtab" value="approve-tabdiv">

	 	<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="approved-by"class="form-control" style="display: inline; text-align: center;" placeholder="Approved By">

						<input type="text" required name="prodcode"class="form-control" style="display: inline; text-align: center;" placeholder="Prod Code">

						<input type="number" name="finalqty" required min="0.01" step="0.01" max='<?php echo $qty ; ?>' class="form-control" style="display: inline; text-align: center;" placeholder="Final Qty (Kg)" >
						
		</div>


		
	 	<div class="col-sm-12">
		<button type="submit"  name="approveprocess" id="process5-submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-check"></i>Approve</button>
		</div>

</form>

<?php
	}
	else
	{


		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='$MASS_TITLE'");
			$result  = $result->fetch_assoc();
			$finalqty = $result["value"];


		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='approved-by' AND step='CREATION'");

		$approvedby = "";
		if($result->num_rows>0)
		{
			$approvedby = "Approved By: ".$result->fetch_assoc()['value'];
		}
		

			
	
?>

<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" disabled class="form-control" style="display: inline; text-align: center;" placeholder="Approved By" value="<?php echo $approvedby ?>">



						
		</div>


	
<br>
<br>

<hr>
<br>
<br>



		<?php 

}
		?>


</div>





<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

			<div class="input-group input-group-button">
            <textarea required rows="1" cols="500" class="form-control" placeholder="" name="note" ></textarea>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" name="addNotes"><i class="fa fa-commenting" aria-hidden="true"></i> Add Note</button>
            </div>
            </div>


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

<style type="text/css">
	.swal-wide{
    width:850px !important;
}
</style>

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


function rejectTest(testid,internal=false)
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


						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"internal");
						i.setAttribute('value',internal);

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
		  customClass: 'swal-wide',
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


