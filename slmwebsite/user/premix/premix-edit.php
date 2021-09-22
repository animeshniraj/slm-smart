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
    		runQuery("INSERT INTO premix_batch_params VALUES(NULL,'$premixid','BATCH SELECTION','$batchid','$bqt','$tag')");
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
	    		}
	    		elseif($sym==">")
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);

	    			if(floatval($paramsvalue[$i])>floatval($currv))
	    			{
	    				runQuery("UPDATE premix_batch_testparams SET islocked ='BLOCKED' WHERE premixid='$premixid'");
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    			}
	    		}
	    		else
	    		{
	    			$sym = $qvalue[$i][0];
	    			$currv = str_replace($sym,"",$qvalue[$i]);
	    			if(floatval($paramsvalue[$i])<floatval($currv))
	    			{
	    				
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','BLOCKED')");
	    			}
	    			else
	    			{
	    				runQuery("INSERT INTO premix_batch_testparams VALUES(NULL,'$prefix','$premixid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
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
  


   $testParams = [];

   {
   		array_push($testParams,["Test Param1","","","DECIMAL",0,2,">20"]);
   		array_push($testParams,["Test Param2","","","DECIMAL",0,10,">20"]);
   		array_push($testParams,["Test Param3","","","DECIMAL",0,30,">20"]);
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
					<h5>Edit Premix (<?php echo $premixid; ?>)</h5>
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
							
								<input required name="quantity"  type="number" step="0.01" min="0" class="form-control form-control-uppercase" placeholder="" value="<?php echo $mass; ?>">
								
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

	<form method="POST">
			<input type="hidden" name="premixid" value="<?php echo $premixid; ?>">
			<input type="hidden" name="currtab" value="feed-tabdiv">

			<table class="table table-striped table-bordered" >
				
				<thead>
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

								?>

									<tr>
											<td><?php echo $row["additive"]; ?></td>
											<td><?php echo $row["composition"]; ?></td>
											<td><?php echo round($row["composition"]/($total),2); ?></td>

											<td><?php echo round((($row["composition"]/($total))*$mass)/100,2); ?>
												
											<br> Min Tolerance: <?php echo $row["mintol"]?> %
											<br> Max Tolerance: <?php echo $row["maxtol"]?> %
											<br> Step: <?php echo $row["step"]?> per 100kg


											</td>
											<td><?php $feedqty =  getFeedQty(round((($row["composition"]/($total))*$mass)/100,2),$row["mintol"],$row["maxtol"],$row["step"]); echo $feedqty; ?></td>

											<?php 
														if($row["additive"] != "Iron")
														{


											?>

													<td id="batch_no"><?php echo getFIFOBatch($row["additive"],$feedqty,$row["step"]); ?></td>
											<?php 
														}else
														{
															$dumid = "";
															$result2 =runQuery("SELECT * FROM premix_batch_params WHERE premixid='$premixid' AND STEP='BATCH SELECTION' AND tag='Iron'");

															if($result2->num_rows>0)
															{
																$result2 = $result2->fetch_assoc();
																$dumid = $result2["param"];
															} 

											?>

													<td id="batch_no">
														
															<input type="text" name="batchid[]" id="iron_batchid" value="<?php echo $dumid; ?>" placeholder="Final Blend Id" >

															<input type="hidden" name="batchqty[]" value="<?php echo $feedqty; ?>">

															<input type="hidden" name="tags[]" value="Iron">


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
															echo "<button type='button' onclick=\"getbatch('".$feedqty."')\" class='btn btn-primary'><i class=\"fa fa-plus\"></i></button>";
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
						<th>Actual Feed Weight</th>
						<th>Enter Feed Weight</th>
						<th>Sequence</th>
						<th></th>
					</tr>

				</thead>


				<tbody>


					
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
											<td><input required type="number" min=0 step="0.01" name="sequencevalue" id ="seq-val-<?php echo $k;?>" value="<?php echo round($allWeights[$row["additive"]]*$row["percent"]/100,2); ?>" placeholder="Feed Weight">
												</td>

												
											
											
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

	function getbatch(qty)
{

	var ironid = document.getElementById("iron_batchid").value;
	 document.getElementById("confirmbatchbtn").disabled = false;
   document.getElementById("iron_batchid").readonly = true;

   return;

	// remove return later

	if(ironid!="")
	{
		
			var postData = new FormData();
       
        postData.append("action","checkFinalBlend");
        postData.append("id",ironid);
        postData.append("quantity",qty);


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response)
            {
                document.getElementById("confirmbatchbtn").disabled = false;
                document.getElementById("iron_batchid").readonly = true;
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
<div class="tabledit-span">Quarantine: <?php echo $testParams[$i][6] ?></div>
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


function approve(approval)
{
	var iid = document.getElementById("approve_iid").value;

	if(!iid && approval!='reject')
	{
		return;
	}

	Swal.fire({
		  icon: 'info',
		  title: 'Finalize',
		  html: 'Are you sure you want to submit?',
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
						i.setAttribute('name',"internalid");
						i.setAttribute('value',iid);

						form.appendChild(i);



						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"approval");
						i.setAttribute('value',approval);

						form.appendChild(i);

							var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"currtab");
						i.setAttribute('value',"approve-tabdiv");

						form.appendChild(i);

						var i = document.createElement("input"); //input element, text
						i.setAttribute('type',"hidden");
						i.setAttribute('name',"approve_stock");
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
			document.getElementById("seq-val-"+i).value = allSequence[i][3];
			document.getElementById("seq-val-"+i).disabled = true;
		}
		

		document.getElementById("seq-input-"+i).disabled = true;
	}
	console.log(allSequence.length);
	document.getElementById("seq-btn-"+i).disabled = false;


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


function  getFeedQty($qty,$mintol,$maxtol,$step)
{

	$mintol = $mintol/100;
	$maxtol = $maxtol/100;

	$step = $step * ($qty/100);

	if($step>=1)
		$step  = round($step);
	else
		$step  = round($step,2);

	if($step==0)
	{
		$step =0.1;
	}

	

	$oqty = $qty;
	$qty = $qty/$step;

	$qty = round($qty);
	$qty = $qty * $step;

	if($oqty>$qty)
	{
		if(($oqty-$qty)/$qty > $maxtol)
		{
			$qty = $oqty * (1+$maxtol);
		}
	}

	elseif($oqty<$qty)
	{
		if(($qty-$oqty)/$qty > $mintol)
		{
			$qty = $oqty * (1-$mintol);
		}
	}

	return $qty;
}



function getFIFOBatch($additive,$quantity,$step)
{

	$step = $step * ($quantity/$step);
	if($step>=1)
		$step  = round($step);
	else
		$step  = round($step,2);

	if($step==0)
	{
		$step =0.1;
	}

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


