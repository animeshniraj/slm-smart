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
        "Page Title" => "Edit External Additive Batch | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-additivesview",
        "MainMenu"	 => "premix_menu",

    ];

    if(!isset($_POST["externalid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $externalid = $_POST["externalid"];
     $result = runQuery("SELECT * FROM additive_external WHERE externalid='$externalid'");
   $result = $result->fetch_assoc();

   $additivename = $result["additive"];

   $entryd = $result["entrydate"];

   $testPermission = true;
   if($myrole =='ADMIN')
	{
		
			$testPermission = true;
		

	}

	$isreshelf = false;

	$result = runQuery("SELECT * FROM additive_internal WHERE internalid='$externalid' AND status='RESHELF'");
   if($result->num_rows==1)
   {
   		$isreshelf = true;
   }

   


   $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }

    if(isset($_POST["updateprocess1"]))
    {
    	$quantity = $_POST["quantity"];

    	runQuery("UPDATE additive_external SET mass='$quantity' WHERE externalid='$externalid'");

    }



     if(isset($_POST["updateprocess4"]))
    {

    	$allParams = $_POST['allparams'];
    	$paramsvalue = $_POST['paramsvalue'];
    	#$qvalue = $_POST['quarantine'];

    		$sqlprefix = $externalid."/%";
    		$prefix = $externalid."/";
    		
    		$result = runQuery("SELECT * FROM additive_external_test WHERE testid LIKE '$sqlprefix' ORDER BY entrytime DESC LIMIT 1");

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

	    
	    	runQuery("INSERT INTO additive_external_test VALUES('$prefix','$externalid','$additivename',CURRENT_TIMESTAMP,'DEFAULT')");
	    	
	    	for($i=0;$i<count($allParams);$i++)
	    	{
	    		
	    		

	    			runQuery("INSERT INTO additive_external_testparams VALUES(NULL,'$prefix','$externalid','$allParams[$i]','$paramsvalue[$i]','UNLOCKED')");
	    		
	    		
	    		
	    		
	    	}





    }

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO additive_external_notes VALUES(NULL,'$externalid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }


      if(isset($_POST["rejecttest"]))
    {

    	$testid = $_POST['testid'];
    	runQuery("DELETE FROM additive_external_testparams WHERE testid = '$testid'");
    	runQuery("DELETE FROM additive_external_test WHERE testid = '$testid'");
    	$currTab = "test-tabdiv";
    	
    }


    $internalid = "";

    if(isset($_POST["approve_stock"]))
    {
    	$internalid = $_POST["internalid"];
    	$approval = $_POST["approval"];

    	$result = runQuery("SELECT * FROM additive_internal WHERE internalid='$internalid'");

    	if($result->num_rows!=0)
    	{
    		$show_alert = true;
    		$alert = showAlert("error","Error","Internal id already exist. Try again.");
    	}

    	elseif($approval=="approve")
    	{	
    		runQuery("UPDATE additive_external SET status='ACCEPTED' WHERE externalid='$externalid'");
    		runQuery("INSERT INTO additive_internal (SELECT '$internalid',externalid,additive,supplier,entrydate,mass,'NOTOVER' FROM additive_external WHERE externalid='$externalid')");
    	}
    	elseif($approval=="reject")
    	{
    		runQuery("UPDATE additive_external SET status='REJECTED' WHERE externalid='$externalid'");
    	}
    	elseif($approval=="allow")
    	{
    		runQuery("UPDATE additive_external SET status='ALLOWED' WHERE externalid='$externalid'");
    	}



    }


    $result = runQuery("SELECT * FROM additive_external WHERE externalid='$externalid'");
   $result = $result->fetch_assoc();

   $mass = $result["mass"];
   $STAT = $result["status"];
	$approvedpermission = true;



	if($result["status"]!="PENDING")
	{
		$approvedpermission = false;
		$testPermission = false;

	}

	if($result["status"]=="ACCEPTED")
	{
		$result = runQuery("SELECT * FROM additive_internal WHERE externalid='$externalid'");
    $result = $result->fetch_assoc();

    $internalid = $result["internalid"];

	}




   $testParams = [];


  	$result  =runQuery("SELECT * FROM additive_test WHERE additive='$additivename'");
  	while($row=$result->fetch_assoc())
   {
   		array_push($testParams,[$row["property"],"","","STRING",$row["min"],$row["max"],">20000"]);
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
						i.setAttribute('name',"externalid");
						i.setAttribute('value',"<?php echo $externalid ?>");

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
					<h5>Edit Stock (<?php echo $additivename.": ".$externalid; ?>)</h5>
					<span>Add additive stock</span>
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


	<?php
	if($isreshelf)
	{
?>
<div class="alert alert-info background-danger">This batch was reshelfed on <?php echo $entryd; ?>.</div>

<?php

}

?>


<ul class="nav nav-tabs md-tabs " role="tablist" id="tablist">
	



<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#creation-tabdiv" role="tab"><i class="icofont icofont-home"></i>Creation</a>
<div class="slide"></div>
</li>



<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="icofont icofont-laboratory"></i>Test Properties</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#approve-tabdiv" role="tab"><i class="icofont icofont-check"></i>Approve/Reject</a>
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
	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
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
if($approvedpermission)
				{


					?>

					<div class="col-sm-12">
				<button type="submit" name="updateprocess1" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
				</div>

<?php } ?>
</form>


</div>







<div class="tab-pane" id="test-tabdiv" role="tabpanel">

<form method="POST">
<?php
if($testPermission)
				{


					?>
	<div class="form-group row">
		<!--	<label class="col-sm-2">Paste Result</label>
			<div class="col-sm-10">
				<div class="input-group input-group-button">
					<input  type="text"  class="form-control" id="test-pastevalue" placeholder="">
					<div class="input-group-append">
					<button class="btn btn-primary" onclick="pastevalues('test')" type="button"><i class="feather icon-check"></i>Apply</button>
					</div>
				</div>
			</div> -->
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
	
	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
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
	
	$result = runQuery("SELECT * FROM additive_external_test WHERE externalid='$externalid'");
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
			$result2 = runQuery("SELECT * FROM additive_external_testparams WHERE testid='$dumtestid'");
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



<div class="tab-pane" id="approve-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

	<?php
if($approvedpermission)
				{


					?>


	 
	 	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
	 	<input type="hidden" name="currtab" value="approve-tabdiv">


	 		<div class="form-group row">
						<label class="col-sm-2">Internal ID</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input required id="approve_iid"  type="text" class="form-control form-control-uppercase" placeholder="" >
								
							</div>
						</div>
					</div>


					<div class="col-sm-6">
				<button type="button" onclick="approve('approve')" name="approve_stock" id="submitBtn" class="btn btn-primary m-b-0 "><i class="feather icon-check"></i>Approve</button>

				<button type="button" name="reject_stock" onclick="approve('reject')" id="submitBtn" class="btn btn-primary m-b-0 "><i class="feather icon-x"></i>Reject</button>
				</div>

<?php } ?>


	<?php
if($myrole !='ADMIN' && !$approvedpermission)
				{


					?>


	 
	 	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
	 	<input type="hidden" name="currtab" value="approve-tabdiv">


	 	<div class="form-group row">
						<label class="col-sm-2">STATUS</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input readonly value="<?php echo $STAT; ?>"  type="text" class="form-control form-control-uppercase" placeholder="" >
								
							</div>
						</div>
					</div>

	 		<div class="form-group row">
						<label class="col-sm-2">Internal Id</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input readonly id="approve_iid"  type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $internalid;?>">
								
							</div>
						</div>
					</div>




<?php } ?>



	<?php
if($myrole ='ADMIN' && !$approvedpermission)
				{


					?>


	 
	 	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
	 	<input type="hidden" name="currtab" value="approve-tabdiv">
	 		<div class="form-group row">
						<label class="col-sm-2">STATUS</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input readonly value="<?php echo $STAT; ?>"  type="text" class="form-control form-control-uppercase" placeholder="">
								
							</div>
						</div>
					</div>

	 		<div class="form-group row">
						<label class="col-sm-2">Internal Id</label>
						<div class="col-sm-10">
							<div class="input-group input-group-button">
							
								<input required id="approve_iid"  type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $internalid;?>">
								
							</div>
						</div>
					</div>

					<?php
						if($STAT =="REJECTED")
						{
					?>

					<div class="col-sm-6">
				<button type="button" onclick="approve('allow')" name="approve_stock" id="submitBtn" class="btn btn-primary m-b-0 "><i class="feather icon-check"></i>ALLOW</button>

				</div>




<?php }} ?>


</div>





<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="externalid" value="<?php echo $externalid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM additive_external_notes WHERE externalid='$externalid' ORDER by time");

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
						i.setAttribute('name',"externalid");
						i.setAttribute('value',"<?php echo $externalid ?>");

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
						i.setAttribute('name',"externalid");
						i.setAttribute('value',"<?php echo $externalid ?>");

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


