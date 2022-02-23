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

	require_once('../process/helper.php');
    $PAGE = [
        "Page Title" => "Edit Premix Batch | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-view",
        "MainMenu"	 => "premix_menu",

    ];

    if(!isset($_POST["premixid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $premixid = $_POST["premixid"];

     $result = runQuery("SELECT * FROM premix_batch WHERE premixid='$premixid'");
   $result = $result->fetch_assoc();

   $gradename = $result["gradename"];

   $testPermission = false;
   if($myrole =='ADMIN' || $myrole =='Lab_Helper' || $myrole =='Lab_Supervisor')
	{
		
			$testPermission = true;
		

	}

   


   $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }



     if(isset($_POST["saveadditive"]))
    {

    	if(!isset($_SESSION['savestore-additive']))
    	{
    		$_SESSION['savestore-additive'] = [];
    	}
    	if(!isset($_SESSION['savestore-additive'][$premixid]))
    	{
    		$_SESSION['savestore-additive'][$premixid] = [];
    	}

    	$ids = $_POST["batchid"];
    	$qtys = $_POST["batchqty"];

    	for($i=0;$i<count($ids);$i++)
    	{
    		$cid= $ids[$i];
    		$cqty = $qtys[$i];

    		if(!isset($_SESSION['savestore-additive'][$premixid][$cid]))
	    	{
	    		$_SESSION['savestore-additive'][$premixid][$cid] = [];
	    	}

	    	$_SESSION['savestore-additive'][$premixid][$cid] = $cqty;
    	}



    }

    $newSteps = [];


     if(isset($_POST["newstep"]))
    {
    	$newstepdata = $_POST["newstep"];
    	$additive = $_POST["param"];


    	for($i=0;$i<count($newstepdata);$i++)
    	{
    		 $newSteps[$additive[$i]] = $newstepdata[$i];
    	}


    	
    	
    }



    if(isset($_POST["approvebatch"]))
    {


    	
    	$approved = $_POST['approvedby'];
    	$finalqty = $_POST['finalqty'];
    	$prodcode = $_POST['prodcode'];
    	$batchnumber = $_POST['batchnumber'];

 
    	runQuery("INSERT INTO premix_prodcode VALUES('$premixid','$batchnumber','$prodcode','$approved','$finalqty')");
    	runQuery("UPDATE premix_batch SET mass='$finalqty' WHERE premixid='$premixid'");
    	




    }



    if(isset($_POST["updateprocess1"]))
    {
    	$quantity = $_POST["quantity"];

    	runQuery("UPDATE premix_batch SET mass='$quantity' WHERE premixid='$premixid'");

    }


     if(isset($_POST["confirmbatch"]))
    {



    	$batchids = $_POST["batchid"];
    	$batchqty = $_POST["batchqty"];
    	$tags = $_POST["tags"];

    	runQuery("DELETE FROM premix_batch_params WHERE premixid='$premixid' AND step='BATCH SELECTION'");

    	for($i=0;$i<count($batchids);$i++)
    	{
    		$batchid = $batchids[$i];
    		$bqt = $batchqty[$i];
    		$tag = $tags[$i];

    		if($tag == "Iron")
    		{


    			runQuery("INSERT INTO premix_batch_params VALUES(NULL,'$premixid','BATCH SELECTION','$batchid','$bqt','$tag')");
    		}
    		else
    		{
    			runQuery("INSERT INTO premix_batch_params VALUES(NULL,'$premixid','BATCH SELECTION','$batchid','$bqt','$tag')");
    		}
    		
    	}


    	
    }


    if(isset($_POST["sequenceparam"]))
    {
    	$sparam = $_POST["sequenceparam"];
    	$sval = $_POST["sequencevalue"];
    	$stag = $_POST["sequencetag"];


    	runQuery("INSERT INTO premix_batch_params VALUES(NULL,'$premixid','FEED SEQUENCE','$sparam','$sval','$stag')");

    }


     if(isset($_POST["updateprocess4"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];
    	$qvalue = $_POST['quarantine'];
    	$testedby = $_POST['testedby'];

    		$sqlprefix = $premixid."/%";
    		$prefix = $premixid."/";
    		
    		$result = runQuery("SELECT * FROM premix_batch_test WHERE testid LIKE '$sqlprefix' ORDER BY entrytime DESC LIMIT 1");

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

	    
	    	runQuery("INSERT INTO premix_batch_test VALUES('$prefix','$premixid','$gradename',CURRENT_TIMESTAMP,'DEFAULT')");
	    	
	    	for($i=0;$i<count($allParams);$i++)
	    	{
	    		
	    		if($qvalue[$i])
	    		{

	    			runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    			runQuery("INSERT INTO additional_process_data VALUES(NULL,'$premixid','$prefix','$allParams[$i]','$testedby[$i]','')");
	    		}
	    		elseif($sym==">")
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);

	    			if(floatval($paramsvalue[$i])>floatval($currv))
	    			{
	    				runQuery("UPDATE premix_batch_testparams SET islocked ='BLOCKED' WHERE premixid='$premixid'");
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    				runQuery("INSERT INTO additional_process_data VALUES(NULL,'$premixid','$prefix','$allParams[$i]','$testedby[$i]','')");

	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    				runQuery("INSERT INTO additional_process_data VALUES(NULL,'$premixid','$prefix','$allParams[$i]','$testedby[$i]','')");
	    			}
	    		}
	    		else
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);
	    			if(floatval($paramsvalue[$i])<floatval($currv))
	    			{
	    				
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    				runQuery("INSERT INTO additional_process_data VALUES(NULL,'$premixid','$prefix','$allParams[$i]','$testedby[$i]','')");
	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    				runQuery("INSERT INTO additional_process_data VALUES(NULL,'$premixid','$prefix','$allParams[$i]','$testedby[$i]','')");
	    			}
	    		}
	    		
	    	}





    }

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO premix_batch_notes VALUES(NULL,'$premixid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }


      if(isset($_POST["rejecttest"]))
    {

    	$testid = $_POST['testid'];
    	runQuery("DELETE FROM premix_batch_testparams WHERE testid = '$testid'");
    	runQuery("DELETE FROM premix_batch_test WHERE testid = '$testid'");
    	$currTab = "test-tabdiv";
    	
    }


   

   $result = runQuery("SELECT * FROM premix_batch WHERE premixid='$premixid'");
   $result = $result->fetch_assoc();

   $mass = $result["mass"];


   $result = runQuery("SELECT * FROM premix_prodcode WHERE premixid='$premixid'");

   $approvedby = "";
   $approved_qty = $mass;
   $approve_prodcode = ""; 

   $approve_batchnumber = ""; 

  	if($result->num_rows==1)
  	{
  		$isapproved= true;
  		$result = $result->fetch_assoc();
  		$approvedby = $result['approvedby'];
  		$approved_qty = $result['finalqty'];
  		$approve_prodcode = $result['prodcode']; 
  		$approve_batchnumber = $result['batchnumber'];

  	}
  	else
  	{
  		$isapproved= false;
  	}


   $testParams = [];

   {
   		//array_push($testParams,["Test Param1","","","DECIMAL",0,2,">20"]);
   		//array_push($testParams,["Test Param2","","","DECIMAL",0,10,">20"]);
   		//array_push($testParams,["Test Param3","","","DECIMAL",0,30,">20"]);

   		$result = runQuery("SELECT * FROM premix_grade_physical WHERE gradename='$gradename'");

   		while($row = $result->fetch_assoc())
   		{
   			array_push($testParams,[$row["parameter"],"","","DECIMAL",$row["min"],$row["max"]," ",$row['units'],"Physical"]);
   		}

   		$result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$gradename'");

   		while($row = $result->fetch_assoc())
   		{
   			array_push($testParams,[$row["additive"],"","","DECIMAL",$row["mintol"],$row["maxtol"],">20","%","Chemical"]);
   		}

   		
   }

   $maxSequence = 0;

   $allSequenceValues = [];

   $result = runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND step='FEED SEQUENCE' ORDER BY tag");

   while($row=$result->fetch_assoc())
   {
   		

   		if($row["param"]=="Entrytime")
   		{
   			array_push($allSequenceValues,[$row["param"],$row["value"],$row["tag"],$row["value"]]);
   		}
   		else
   		{
   			array_push($allSequenceValues,[$row["param"],$row["param"],$row["tag"],$row["value"]]);
   		}
   }


   $allowedBatches = [];
   $result = runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND step='BATCH SELECTION'");
$batchselectiondone = false;
   if($result->num_rows>0)
   {
   		$batchselectiondone = true;


   		while($row=$result->fetch_assoc())
   		{

   			if(isset($allowedBatches[$row["tag"]]))
   			{
   				array_push($allowedBatches[$row["tag"]],$row["param"]);
   			}
   			else
   			{
   				$allowedBatches[$row["tag"]] = [];
   				array_push($allowedBatches[$row["tag"]],$row["param"]);
   			}
   			
   		}
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
						i.setAttribute('name',"premixid");
						i.setAttribute('value',"<?php echo $premixid ?>");

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
					<h3>Currently editing Premix: (<?php echo $premixid; ?>)</h3>
					<span>Edit premix parameters</span>
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
<a class="nav-link" data-toggle="tab" href="#feed-tabdiv" role="tab"><i class="icofont icofont-fire-burn"></i>Feed Sequence</a>
<div class="slide"></div>
</li>

<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i>Test Properties</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#approve-tabdiv" role="tab"><i class="icofont icofont-check"></i>Approve</a>
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
	<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
	<input type="hidden" name="currtab" value="creation-tabdiv">


	<div class="form-group row">
						<label class="col-sm-2">Quantity (kg)</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input required <?php if($isapproved){echo "readonly";} ?>  name="quantity"  type="number" step="0.01" min="0" class="form-control form-control-uppercase" placeholder="" value="<?php echo $mass; ?>">
								
							</div>
						</div>
					</div>
<?php
if(false)
				{


					?>

					<div class="col-sm-12">
				<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

<?php } ?>
</form>


</div>




<div class="tab-pane" id="feed-tabdiv" role="tabpanel">
<form id="saveadditiveqty" method="POST">
		<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
		<input type="hidden" name="currtab" value="feed-tabdiv">
		<input type="hidden" name="saveadditive" value="">
</form>
	<form method="POST">
			<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
			<input type="hidden" name="currtab" value="feed-tabdiv">
			<div class="table-responsive">
			<table class="table table-striped table-bordered" >
				
				<thead>

					<tr>
						<td colspan="100%" style="text-align:right;"><p><button type="submit" form="saveadditiveqty" class="btn btn-primary"><i class="fa fa-save"></i>Save Additives</button></p></td>
					</tr>
					<tr>
						<th>Additives</th>
						<th>Added %</th>
						<th>% for 100%</th>
						<th>Calculated Qty</th>
						<th>Feed Qty</th>
						<th>Batch No.</th>
						<th></th>
					</tr>

				</thead>


				<tbody>
					
					<?php

						$result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$gradename'");

						$total = 0;


						$allWeights = [];
						
						
						while($row=$result->fetch_assoc())
						{

								if($row["over"]==1)
								{
									$total += $row["composition"];
								}
								else
								{
									$total += $row["composition"];
								}
						}

						$total = $total/100;

						$result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$gradename'");
						$k=0;
						while($row=$result->fetch_assoc())
						{
							$k++;

								if(isset($newSteps[$row["additive"]]))
								{
									$row["step"] = $newSteps[$row["additive"]];
								}

								?>

									<tr>
											<td><?php echo $row["additive"]; ?></td>
											<td><?php echo $row["composition"]; ?></td>
											<td><?php echo round($row["composition"]/($total),2); ?></td>

											<td><?php echo round((($row["composition"]/($total))*$mass)/100,2); ?>


											<?php 
														if($row["additive"] != "Iron")
														{


											?>
												
											<br> Min Tolerance: <?php echo $row["mintol"]?> %
											<br> Max Tolerance: <?php echo $row["maxtol"]?> %

											<?php 
														}
												?>


											<br> Round: <div><input type="text" form="update-step"  class="form-control col-sm-3" name="newstep[]" value="<?php echo $row["step"]?>">
											 <button type="submit" form="update-step" class="btn btn-primary col-sm-2"><i class="fa fa-refresh"></i></button></div>

											 <input type="hidden" name="premixid" value="<?php echo $premixid; ?>" form="update-step">
											 <input type="hidden" name="currtab" value="feed-tabdiv" form="update-step">
											 <input type="hidden" name="param[]" value="<?php echo $row["additive"]; ?>" form="update-step">
											</td>
											<td><?php $feedqty =  getFeedQty(round((($row["composition"]/($total))*$mass)/100,2),$row["mintol"],$row["maxtol"],$row["step"],$mass); echo $feedqty; ?></td>

											<?php 
														if($row["additive"] != "Iron" && !$batchselectiondone)
														{


											?>

													<td id="batch_no"><?php echo getFIFOBatch($row["additive"],$feedqty,$row["step"]); ?></td>
											<?php 
														}
														elseif($row["additive"] != "Iron"){

															$d1 = $row["additive"];

															$result2 =runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND STEP='BATCH SELECTION' AND tag='$d1'");
																echo "<td id='batch_no'>";
																while($row2 = $result2->fetch_assoc())
																{
																	
																	echo $row2['param']." (".strval($row2['value'])." kg)<br>";
																	
																} 
																echo "</td>";

														}
														else
														
														{
															$dumid = "";
															$result2 =runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND STEP='BATCH SELECTION' AND tag='Iron'");

															if($result2->num_rows>0)
															{
																$result2 = $result2->fetch_assoc();
																$dumid = $result2["param"];
															} 

											?>

													<td id="batch_no_Iron">
														
															
														<?php 
															if($batchselectiondone)
															{
																$result2 =runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND STEP='BATCH SELECTION' AND tag='Iron'");

																while($row2 = $result2->fetch_assoc())
																{
																	echo $row2['param']." (".strval($row2['value'])." kg)<br>";
																} 
															}
														?>
													


													</td>

												<?php 
												

													}			

											?>

										

											<td>
												
												<?php 
														if($row["additive"] != "Iron")
														{
															echo "";
														}
														elseif($row["additive"] == "Iron" && !$batchselectiondone)
														{



															echo "<button type='button' onclick=\"getbatch('".$gradename."','".$feedqty."')\" class='btn btn-primary'><i class=\"fa fa-plus\"></i></button>";
														}

														$allWeights[$row["additive"]] = $feedqty;
												?>

											</td>

									</tr>

								<?php
						}

						
					?>


				</tbody>


			</table>
		</div>


			<?php 
				if(!$batchselectiondone)
				{



			?>


				<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" disabled name="confirmbatch" id="confirmbatchbtn" class="btn btn-primary pull-right"><i class="fa fa-check"></i>Confirm</button>
			<span class="messages"></span>
			</div>
			</div>


			<?php 

					}
			?>


	</form>



<div class="modal fade" id="searchbatchesmodal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">All Available Batches</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">



<table class="table table-striped table-bordered">
<thead>
<tr>

<th>-</th>
<th>Batch ID</th>
<th>Quantity Available/Total Quantity</th>
<th>Selected Quantity</th>
</tr>
</thead>


<tbody id="searchbatchesmodal-modal-tbody">

	
</tbody>
</table>


</div>
<div class="modal-footer">
<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
<button type="button" disabled onclick="searchbatchesmodal_add()" id="searchbatchesmodal-modal-save" class="btn btn-primary waves-effect waves-light ">Add IDs</button>
</div>
</div>
</div>
</div>





<div class="modal fade" id="readscalemodal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Read Scale</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">


	<script type="text/javascript">
		
		function addtoscalelist()
		{
			var tbody = document.getElementById('readscalemodal-modal-tbody');

		}

	</script>
		
	


<table class="table table-striped table-bordered">
<thead>
<tr>
	<td>
		<select class="form-control">
			<?php 

				$dresult = runQuery("SELECT * FROM devices WHERE type='SCALE'");

				while($drow = $dresult->fetch_assoc())
				{
					?>

						<option value="<?php echo $drow['devicename'] ?>" data-hostname="<?php echo $drow['hostname'] ?>" data-ip="<?php echo $drow['ip'] ?>" data-units="<?php echo $drow['units'] ?>" data-multiplier="<?php echo $drow['multiplier'] ?>" ><?php echo $drow['devicename'] ?> ( <?php echo $drow['units'] ?> )</option>

					<?php
				}

			?>
		</select>
	</td>
	<td>
		<input type="number" id="readscale-qty" value="0">
	</td>

	<td>
		<button class="btn btn-primary" onclick="addtoreadscalelist()" type="button"><i class="feather icon-airplay"></i>Read</button>
	</td>
</tr>
</thead>
</table>



<table class="table table-striped table-bordered">
<thead>


<tr>
<th>-</th>
<th>Entry</th>
<th></th>
</tr>
</thead>

<input type="hidden" id="readscalemodal-item">
<tbody id="readscalemodal-modal-tbody">

	
</tbody>

<tfoot>
	<th></th>
	<th>Total Quantity</th>
	<th id="readscale-totalqty">0</th>
</tfoot>
</table>


</div>
<div class="modal-footer">
<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
<button type="button" onclick="readscalemodal_add()" id="readscalemodal-modal-save" class="btn btn-primary waves-effect waves-light ">Add</button>
</div>
</div>
</div>
</div>
<script type="text/javascript">
	

	function readscalemodal_add()
	{
		var input = document.getElementById('readscalemodal-item').value;

		document.getElementById(input).value = document.getElementById('readscale-totalqty').innerHTML;
		$('#readscalemodal').modal('hide');
	}

	function getscale(scaleinput)
	{

		document.getElementById('readscalemodal-item').value = scaleinput;
		//document.getElementById('readscalemodal-modal-tbody').innerHTML = "";
		$('#readscalemodal').modal('show');


	}

	function compute_total_scale()
	{
		var tbody = document.getElementById('readscalemodal-modal-tbody');

		var total = 0;

		for(var i=0;i<tbody.children.length;i++)
		{
			 total += parseFloat(tbody.children[i].children[1].innerHTML);
		}

		document.getElementById('readscale-totalqty').innerHTML = total;

	}

	function addtoreadscalelist()
	{
		 tbody = document.getElementById('readscalemodal-modal-tbody');

		 var val = document.getElementById('readscale-qty').value;

		 var dumtr = document.createElement('tr')
		 dumtr.innerHTML += "<td></td><td>"+val+"</td>";
		 dumtr.innerHTML += "<td><button class='btn btn-danger' onclick='this.closest(\"tr\").remove();compute_total_scale();'><i class='fa fa-trash'></i>Remove</button</td>"


		 tbody.appendChild(dumtr)

		 compute_total_scale()
	}
</script>
	
					
			

	<form method="POST" id="update-step"></form>

<br>
<br>
<br>
<br>


			<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
			<input type="hidden" name="currtab" value="feed-tabdiv">

			<table class="table table-striped table-bordered" >
				
				<thead>
					<tr>
						<th>Item</th>
						<th>%</th>
						<th>Calculated Feed Weight</th>
						<th>Enter Scale Weight</th>
						<th>Enter Feed Weight</th>
						<th>Sequence</th>
						<th></th>
					</tr>

				</thead>


				<tbody id="feed-tbody">


					
					<?php

					if($batchselectiondone)
					{
						?>
						<form method="POST">
							
						
						<tr>
							<td></td>
							<td></td>
							<td></td>

							<td>Entry Time: <input required type="text" name="sequencevalue" value="" id ="seq-input-0"></td>
							<td><button  type='submit' class='btn btn-primary' id ="seq-btn-0"><i class="fa fa-plus"></i></button></td>
							<input type="hidden" name="sequencetag" value="0">
							<input type="hidden" name="sequenceparam" value="Entrytime">
							<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
							<input type="hidden" name="currtab" value="feed-tabdiv">
						</tr>

						</form>


						<?php
					}

						$result = runQuery("SELECT * FROM premix_grade_feed_sequence WHERE gradename='$gradename' ORDER by ordering");



						$k=1;
						while($row=$result->fetch_assoc())
						{

								?>
								<form method="POST" id ="seq-form-<?php echo $k;?>">
									<tr>
											<td><?php echo $row["additive"]; ?></td>
											<td><?php echo $row["percent"]; ?></td>
											
											<td><?php echo round($allWeights[$row["additive"]]*$row["percent"]/100,2); ?>
												</td>
											<td><input required readonly type="number" min=0 step="0.01" name="sequencesvalue" id ="seq-sval-<?php echo $k;?>" value="0" placeholder="Scale Weight" onchange = "deductcontainer('seq-container-<?php echo $k;?>','seq-sval-<?php echo $k;?>','seq-val-<?php echo $k;?>')">
												<button type="button" class="btn btn-primary" onclick="getscale('seq-sval-<?php echo $k;?>')"><i class="feather icon-airplay"></i></button>
												<br>
												Container Weight:<br><input required type="number" min=0 step="0.01" id ="seq-container-<?php echo $k;?>" value="0.0" placeholder="Container Weight" onchange = "deductcontainer('seq-container-<?php echo $k;?>','seq-sval-<?php echo $k;?>','seq-val-<?php echo $k;?>')">
												</td>

											<td><input required type="number" min=0 step="0.01" name="sequencevalue" id ="seq-val-<?php echo $k;?>" value="<?php echo round($allWeights[$row["additive"]]*$row["percent"]/100,2); ?>" placeholder="Feed Weight" onchange = "deductcontainer('seq-container-<?php echo $k;?>','seq-sval-<?php echo $k;?>','seq-val-<?php echo $k;?>')">
												</td>
												<script type="text/javascript">
													$( document ).ready(function() {
													    deductcontainer('seq-container-<?php echo $k;?>','seq-sval-<?php echo $k;?>','seq-val-<?php echo $k;?>')
													});
												</script>
												
											
											
											<td><input required type="text" name="sequenceparam" value="" id ="seq-input-<?php echo $k;?>" placeholder="Barcode"></td>
											<td><button disabled  type='button' onclick="checkBarcode('<?php echo $row["additive"]; ?>',document.getElementById('seq-input-<?php echo $k;?>'),document.getElementById('seq-form-<?php echo $k;?>'))" class='btn btn-primary' id ="seq-btn-<?php echo $k;?>"><i class="fa fa-plus"></i></button></td>
											<input type="hidden" name="sequencetag" value="<?php echo $k;?>">
									

											<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
											<input type="hidden" name="currtab" value="feed-tabdiv">
									</tr>
								</form>

								<?php
								$k++;
						}

						
					?>


				</tbody>


			</table>


</div>






<script type="text/javascript">

	let allowBatchSubmit1 = false;
	let allowBatchSubmit2 = false;


	function deductcontainer(container,scale,feed)
	{
		container = document.getElementById(container)
		scale = document.getElementById(scale)
		feed = document.getElementById(feed)
		
		feed.value = parseFloat(scale.value) - parseFloat(container.value)

	}


	function checkBarcode(additive,inputObj,formObj)
	{
		console.log(additive,inputObj.value);

		if(inputObj.value != "")
		{
			if(allowedBatch[additive].includes(inputObj.value))
			{
				formObj.submit();
			}
			else
			{
					Swal.fire({
										icon: "error",
										title: "Error",
										html: "Bar Code doesnot match" ,
										showConfirmButton: true,
									  	showCancelButton: false,
									  	confirmButtonText: 'OK',
									  	
									})
			}
		}
		
	}

	function searchbatchesmodal_add()
	{

			var batchesdiv = document.getElementById('batch_no_Iron');
			batchesdiv.innerHTML = [];

			var tbody = document.getElementById('searchbatchesmodal-modal-tbody');

			for(var i=0;i<tbody.children.length-1;i++)
			{
				curr = tbody.children[i];

				if(curr.children[0].children[0].checked)
				{
					if(curr.children[3].children[0].value != "" && parseFloat(curr.children[3].children[0].value))
					{

						var dval =  parseFloat(curr.children[3].children[0].value);
						var did = 	curr.children[1].innerHTML;


						dumdiv = document.createElement('div');
						dumdiv.innerHTML += "<input type='hidden' name='batchid[]' value='"+did+"'>";
						dumdiv.innerHTML += "<input type='hidden' name='batchqty[]' value='"+dval+"'>";
						dumdiv.innerHTML += "<input type='hidden' name='tags[]' value='Iron'>";
						dumdiv.innerHTML += did + " ("+dval+" kg)<br>";
						batchesdiv.appendChild(dumdiv)
							
					}
				}

				
				
			}

				$("#searchbatchesmodal").modal('hide');

				document.getElementById('confirmbatchbtn').disabled=false;
	}


	function updatemodalbatch()
	{
		var tbody = document.getElementById('searchbatchesmodal-modal-tbody');

		var total = 0;
		for(var i=0;i<tbody.children.length-1;i++)
		{
			curr = tbody.children[i];

			if(curr.children[0].children[0].checked)
			{
				if(curr.children[3].children[0].value != "" && parseFloat(curr.children[3].children[0].value))
				{

					if(parseFloat(curr.children[3].children[0].value)>parseFloat(curr.children[3].children[0].max))
					{
						curr.children[3].children[0].value = curr.children[3].children[0].max;
						total +=parseFloat(curr.children[3].children[0].value)
					}
					else
					{
						total +=parseFloat(curr.children[3].children[0].value)
					
					}
				}
			}

			
			
		}

		tbody.children[tbody.children.length-1].children[3].innerHTML = total
		var required = parseFloat(tbody.children[tbody.children.length-1].children[1].innerHTML)
		if(total==required)
			{
				document.getElementById('searchbatchesmodal-modal-save').disabled = false;
			}
			else
			{
				document.getElementById('searchbatchesmodal-modal-save').disabled = true;
			}
	}

	function getbatch(grade,qty)
{



		
			var postData = new FormData();
       
        postData.append("action","getfinalbatch");
         postData.append("grade",grade);




        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           
            var data = JSON.parse(this.responseText);

            
            if(data.response)
            {

            		var tbody = document.getElementById('searchbatchesmodal-modal-tbody');

            		 tbody.innerHTML = "";
            		for(var i=0;i<data.data.length;i++)
            		{
            			var dumtr = document.createElement('tr');

            			dumtr.innerHTML += "<td><input class='form-control' onchange='updatemodalbatch()' type='checkbox' ></td>";
            			dumtr.innerHTML += "<td>"+data.data[i][0]+"</td>";
            			dumtr.innerHTML += "<td>"+data.data[i][1]+"</td>";
            			dumtr.innerHTML += "<td><input class='form-control' onchange='updatemodalbatch()' max='"+data.data[i][1]+"' type='number' min=0 step='0.01' ></td>";

            			tbody.appendChild(dumtr);
            		}


            		var dumtr = document.createElement('tr');

            			dumtr.innerHTML += "<th>Required (kg)</th>";
            			dumtr.innerHTML += "<td>"+qty+"</td>";
            			dumtr.innerHTML += "<th>Selected (kg)</th>";
            			dumtr.innerHTML += "<td>0</td>";

            			tbody.appendChild(dumtr);
            		



            		$("#searchbatchesmodal").modal('show');
               
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
	

function addSequence(additive,tdobj)
{
	$("#seqmodal").modal('show');


			
}


</script>












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
	
	<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
	<input type="hidden" name="currtab" value="test-tabdiv">
<div class="form-group row">
				<table class="table table-striped table-bordered" id="process4table">
		<thead>
		<tr>

		<th>Property</th>
		<th>Min/Max</th>
		<th>Value</th>
		<th>Units</th>
		<th>Tested By</th>


		</tr>
		</thead>
		
		
		<tbody id="test-tablediv">


			<?php
			$isphysical = true;
		
		for($i=0;$i<count($testParams);$i++)
		{
		

		if($i==0)
		{
			echo "<tr><td colspan='5' style='text-align:center;font-weight: bold;'>Physical Properties</td></tr>";
		}
		elseif($isphysical)
		{

			if($testParams[$i][8]=="Chemical")
			{
				$isphysical = false;
				echo "<tr><td colspan='5' style='text-align:center;font-weight: bold;'>Chemical Properties</td></tr>";
			}
		}

?>




<tr>

<td  class="tabledit-view-mode" ><span class="tabledit-span"><?php echo $testParams[$i][0] ?></span></td>
<td class="tabledit-view-mode"><div class="tabledit-span">Min: <?php echo $testParams[$i][4] ?></div>
<div class="tabledit-span">Max: <?php echo $testParams[$i][5] ?></div>

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

<td><?php echo $testParams[$i][7] ?></td>


<td>


<input required type="text" list="labtechlist"  name="testedby[]" placeholder="Tested By" class="form-control">


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


<?php
	
	$result = runQuery("SELECT * FROM premix_batch_test WHERE premixid='$premixid'");
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
			$result2 = runQuery("SELECT * FROM premix_batch_testparams WHERE testid='$dumtestid'");
			$dumParam = "[";
			$dumValue = "[";
			$qstatus = "UNLOCKED";
			if($result2->num_rows>0)
			{
				while($row2 = $result2->fetch_assoc())
				{
						$currParam = $row2["param"];
						$result3 = runQuery("SELECT * FROM additional_process_data WHERE processid='$premixid' AND param1 ='$dumtestid' AND param2 = '$currParam'")->fetch_assoc()['param3'];
						$dumParam = $dumParam . "'" . $row2["param"]. " (Tested By: ".$result3.")" ."',";
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


<div class="tab-pane" id="approve-tabdiv" role="tabpanel">

<form method="POST" id="approvalform">
	<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
	 	<input type="hidden" name="currtab" value="approve-tabdiv">


	 	<table>
	 		<tr>
	 			<th>Approved By</th>
	 			<td><input required <?php if($isapproved){echo "readonly";} ?> type="text" name="approvedby" class="form-control" value="<?php echo $approvedby; ?>" id="approved-by"></td>

	 		</tr>


	 		<tr>
	 			<th>Batch Number</th>
	 			<td><input required <?php if($isapproved){echo "readonly";} ?> type="text" name="batchnumber" class="form-control" id="approved-prodcode"  value="<?php echo $approve_batchnumber; ?>"></td>
	 		</tr>

	 		<tr>
	 			<th>Final Qty</th>
	 			<td><input required <?php if($isapproved){echo "readonly";} ?> type="number" min="0.01" max="<?php echo $mass ?>" step="0.01" name="finalqty" value="<?php echo $approved_qty; ?>" class="form-control" id="approved-finalqty"></td>
	 		</tr>

	 		<tr>
	 			<th>Prod Code</th>
	 			<td><input required <?php if($isapproved){echo "readonly";} ?> type="text" name="prodcode" class="form-control" id="approved-prodcode"  value="<?php echo $approve_prodcode; ?>"></td>
	 		</tr>
	 	</table>

	 	<?php 

	 	if(!$isapproved){
	 	?>
	 	<div class="col-sm-12">
				<button type="submit" name="approvebatch" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-check"></i>Approve</button>
				</div>

				<?php 
			}
	 	?>
</form>

</div>



<script type="text/javascript">
	function confirmapproval()
	{

		if(document.getElementById("approved-by").value=="")
		{
			return false;
		}


		Swal.fire({
		  icon: 'info',
		  title: 'Approve Batch',
		  html: 'Are you sure you want to approve this batch.',
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			  	document.getElementById('approvalform').submit();
			  	return true;
			  }
			  else
			  {
			  	return false;
			  }
			});

	}

</script>


<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM premix_batch_notes WHERE premixid='$premixid' ORDER by time");

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
						i.setAttribute('name',"premixid");
						i.setAttribute('value',"<?php echo $premixid ?>");

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


<script type="text/javascript">
	

	var allSequence = <?php echo json_encode($allSequenceValues); ?>

	var i=0
	for(i=0;i<allSequence.length;i++)
	{
		document.getElementById("seq-btn-"+i).remove()
		document.getElementById("seq-input-"+i).value = allSequence[i][1];

		if(i!=0)
		{
			
			document.getElementById("seq-val-"+i).disabled = true;
			document.getElementById("seq-val-"+i).removeAttribute("onchange");
			document.getElementById("seq-sval-"+i).removeAttribute("onchange");
			document.getElementById("seq-container-"+i).removeAttribute("onchange");
			document.getElementById("seq-val-"+i).value = allSequence[i][3];
			document.getElementById("seq-sval-"+i).value = allSequence[i][3];
			document.getElementById("seq-sval-"+i).disabled = true;
			document.getElementById("seq-container-"+i).disabled = true;
		}
		

		document.getElementById("seq-input-"+i).disabled = true;
	}
	
	document.getElementById("seq-btn-"+i).disabled = false;

	feedtbody = document.getElementById('feed-tbody');

	var allfeeddata = [];

	for(var i =3; i<feedtbody.children.length;i+=2)
	{
		var curr = feedtbody.children[i];
		

		if (typeof allfeeddata[curr.children[0].innerHTML] == 'undefined') {
			 	allfeeddata[curr.children[0].innerHTML]=[]
		    allfeeddata[curr.children[0].innerHTML]["reconcilation"] = 0
		    allfeeddata[curr.children[0].innerHTML]["adjustableobj"] = []
		}

		var actualqty = parseFloat(curr.children[2].innerHTML);
		var feedqty =  parseFloat(curr.children[4].children[0].value);

		allfeeddata[curr.children[0].innerHTML]["reconcilation"] += actualqty-feedqty;

		if(!curr.children[4].children[0].disabled)
		{
			allfeeddata[curr.children[0].innerHTML]["adjustableobj"].push(curr);
		}

	}


	Object.keys(allfeeddata).forEach(function (key) {
   
   var curr = allfeeddata[key];
   if(curr["reconcilation"]==0)
   {
   		return;
   }

   var adjustment = curr["reconcilation"];
   var adjustable = curr["adjustableobj"];

   var currvals = []
   sum = 0;

   for(var i=0;i<adjustable.length;i++)
   {

   		sum += parseFloat(adjustable[i].children[3].children[0].value);
   }




   for(var i=0;i<adjustable.length;i++)
   {

   		var currval =  parseFloat(adjustable[i].children[4].children[0].value);
   		var ratio = currval/sum;

   		var newval = Math.round(ratio*adjustment,2) + currval;

   		adjustable[i].children[3].children[0].value = 0.0;
   		adjustable[i].children[4].children[0].value = 0.0;
   		adjustable[i].children[2].innerHTML = actualqty + "<br> Adjusted = " +  newval

   		adjustment-=Math.round(ratio*adjustment,2);

   		if(i==(adjustable.length-1))
   		{
   			adjustable[i].children[4].children[0].value = 0.0;
   			adjustable[i].children[3].children[0].value = 0.0;
   			adjustable[i].children[2].innerHTML = actualqty + "<br> Adjusted = " +  (newval +adjustment)
   		}

   }

   

});



console.log(allfeeddata)

	var allowedBatch = <?php echo json_encode($allowedBatches); ?>




</script>

<script>
					$(function() {
					  $('#seq-input-0').daterangepicker({
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
<?php


function  getFeedQty($qty,$mintol,$maxtol,$step,$total)
{

	$mintol = $mintol/100;
	$maxtol = $maxtol/100;

	


	$oqty = $qty;


	if($step==1)
	{
		$qty=round($qty);
		
	}
	elseif($step<1)
	{
		$qty = $qty/$step;

		$qty = round($qty);
		$qty = $qty * $step;
	}
	else
	{
		$qty = $qty/$step;

		$qty = round($qty);
		$qty = $qty * $step;
	}

	

	if($oqty<$qty)
	{
		if(($qty)/$total > $maxtol)
		{
			$qty = $total * ($maxtol);
			echo "Cannot Round. Tolerance violation<br>";
		}
	}
	elseif($oqty>$qty)
	{
		if(($qty)/$total < $mintol)
		{
			$qty = $total * ($mintol);
			echo  "Tolerance violation. Rounded off to min allowed<br>";
		}
	}







	return $qty;
}



function getFIFOBatch($additive,$quantity,$step)
{



	if($additive=="Iron")
	{
		return "";
	}
	else
	{

		$allids = [];



		$result2 = runQuery("SELECT * FROM additive_internal WHERE additive in (SELECT additive FROM premix_additives_group_member WHERE groupname='$additive') AND status='NOTOVER' ORDER BY entrydate LIMIT 20");
		while($row2=$result2->fetch_assoc())
		{
			array_push($allids,[$row2["internalid"],$row2["mass"]]);
		}
		

		if(count($allids)==0)
		{

			$result = runQuery("SELECT * FROM additive_internal WHERE additive='$additive' AND status='NOTOVER' ORDER BY entrydate LIMIT 20");
			while($row=$result->fetch_assoc())
			{
				array_push($allids,[$row["internalid"],$row["mass"]]);
			}

		}

		$selectedBatch = [];
		$required = $quantity;
		$flag = true;

		for($i=0;$i<count($allids);$i++)
		{

			$currid = $allids[$i][0];
			$currmass = $allids[$i][1];

			$result = runQuery("SELECT * FROM premix_batch_params WHERE step='BATCH SELECTION' AND param='$currid'");

			while($row=$result->fetch_assoc())
			{
				$currmass -= $row["value"];
			}

			if($currmass>=$step)
			{
				if($currmass>=$required)
				{

					array_push($selectedBatch,[$currid,$required]);
					$required -= $currmass;
					break;
				}
				else
				{
					
					$required -= $currmass;
					array_push($selectedBatch,[$currid,$currmass]);
				}
			}

		}

		if($required>0)
		{
			array_push($selectedBatch,["Error","No Batch Available"]);
			$flag = false;

			
		}


		$aa = "";

		for($i=0;$i<count($selectedBatch);$i++)
		{
			if($selectedBatch[$i][0] !="Error")
			{
				$aa = $aa." ".$selectedBatch[$i][0]." (".$selectedBatch[$i][1]." kg)<br>";
				$aa = $aa . "<input type=\"hidden\" name=\"batchid[]\" value=\"".$selectedBatch[$i][0]."\">";
				$aa = $aa . "<input type=\"hidden\" name=\"batchqty[]\" value=\"".$selectedBatch[$i][1]."\">";

				$aa = $aa . "<input form=\"saveadditiveqty\" type=\"hidden\" name=\"batchid[]\" value=\"".$selectedBatch[$i][0]."\">";
				$aa = $aa . "<input form=\"saveadditiveqty\" type=\"hidden\" name=\"batchqty[]\" value=\"".$selectedBatch[$i][1]."\">";

				$aa = $aa . "<input type=\"hidden\" name=\"tags[]\" value=\"".$additive."\">";

			}
			else
			{
				$aa = $aa." ".$selectedBatch[$i][0]." (".$selectedBatch[$i][1]." )<br>";
			}
			
		}





		return $aa;
		//$result = runQuery("SELECT * FROM additive_internal WHERE additive = ")
	}
}



?>


