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

	$editidPermission = false;

	if($myrole =="ADMIN" OR $myrole =="Production_Supervisor")
	{
		$editidPermission = true;
	}

	require_once('../process/helper.php');
    $PAGE = [
        "Page Title" => "Edit Loading Advice | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "loadingadvice-view",
        "MainMenu"	 => "dispatch_menu",

    ];


    if(!isset($_POST["laid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $laid = $_POST["laid"];

    if(isset($_POST["confirmld"]))
    {




    	$qtys = $_POST['qty'];
    	$batches = $_POST['batch'];
    	$pkgs = $_POST['pkg'];
    	$grades = $_POST['grades'];


    	

    	runQuery("DELETE FROM loadingadvice_batches WHERE laid='$laid'");


    	for ($i=0; $i < count($batches); $i++) { 
    		
    		$cgrade = $grades[$i];
    		$cqty = $qtys[$i];
    		$cpkg = $pkgs[$i];
    		$cbatch = $batches[$i];
    		
    		runQuery("INSERT INTO loadingadvice_batches VALUES(NULL,'$laid','$cgrade','$cbatch','$cqty','$cpkg')");
    		
    	}
    }





   if(isset($_POST["updatebasic"]))
   {

   		$dumcompany = $_POST['company'];
   		$dumtransport = $_POST['transport'];
   		$dumdate = $_POST['deliverydate'];



   		runQuery("UPDATE loading_advice SET company='$dumcompany', entrydate='$dumdate', transport='$dumtransport' WHERE laid='$laid'");
   }


  	if(isset($_POST["editid"]))
  	{
  		
  		$newid = $_POST["laidName"];

  		$result = runQuery("SELECT * FROM loading_advice WHERE laid='$newid'");
    	if($result->num_rows==0)
    	{
    		runQuery("INSERT INTO loading_advice (SELECT '$newid',poid,customer,company,transport,entrydate,status FROM loading_advice WHERE laid='$laid')");


    		
    		runQuery("INSERT INTO loadingadvice_notes (SELECT NULL,'$newid',sender,note,time FROM loadingadvice_notes WHERE laid='$laid' ORDER by time)");
    		runQuery("INSERT INTO loadingadvice_params (SELECT NULL,'$newid',step,param,value,tag FROM loadingadvice_params WHERE laid='$laid')");

    		runQuery("INSERT INTO loadingadvice_params (SELECT NULL,'$newid',step,param,value,tag FROM loadingadvice_params WHERE laid='$laid')");
    		
    		
    		runQuery("DELETE FROM loadingadvice_batches WHERE laid='$laid'");
    		runQuery("DELETE FROM loadingadvice_notes WHERE laid='$laid'");
    		runQuery("DELETE FROM loadingadvice_params WHERE laid='$laid'");
    		runQuery("DELETE FROM loading_advice WHERE laid='$laid'");
    		$laid = $newid;
    	}
    	else
    	{
    		$show_alert = true;
			$alert = showAlert("error","ID already exists","");
    	}
  		
  		
  	}


   $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }



    if(isset($_POST["addorder"]))
    {
    	if(!isset($_POST["order_batchid"]))
    	{
    			runQuery("DELETE FROM loadingadvice_params WHERE laid='$laid' AND step='BATCH'");

    	}
    	else
    	{


    		$batchids= $_POST["order_batchid"];

	    	$qty = $_POST['order_batchqty'];



	    	runQuery("DELETE FROM loadingadvice_params WHERE laid='$laid' AND step='BATCH'");


	    	for($i=0;$i<count($batchids);$i++)
	    	{
	    		$cid =  $batchids[$i];
	    		$cqty =  $qty[$i];

	    		runQuery("INSERT INTO loadingadvice_params VALUES(NULL,'$laid','BATCH','$cid','$cqty','batchid')");
	    		
	    	}

    	}
    	
    	
    }
    

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO loadingadvice_notes VALUES(NULL,'$laid','$myuserid','$note',CURRENT_TIMESTAMP)");

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
						i.setAttribute('name',"laid");
						i.setAttribute('value',"<?php echo $laid ?>");

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
					<h5>Edit Order (<?php echo $laid; ?>)</h5>
					<span>Edit Loading Advice parameters</span>
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
<a class="nav-link" data-toggle="tab" href="#batches-tabdiv" role="tab"><i class="fa fa-shopping-bag"></i>Add Batches</a>
<div class="slide"></div>
</li>






<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#notes-tabdiv" role="tab"><i class="icofont icofont-edit"></i>Notes</a>
<div class="slide"></div>
</li>




</ul>

<div class="tab-content card-block">

<div class="tab-pane" id="creation-tabdiv" role="tabpanel">
<?php
	
	$result = runQuery("SELECT * FROM loading_advice WHERE laid='$laid'");

	$result = $result->fetch_assoc();

	$delivery_date = $result['entrydate'];
	$dumC = $result["customer"];
	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
	$result2 = $result2->fetch_assoc(); 
	$customerid = $dumC;

	$tranport_type = $result['transport'];
	$currCompany = $result['company'];

	$result3 = runQuery("SELECT * FROM loadingadvice_params WHERE laid='$laid' AND param='Tentative Date'");
	$result3 = $result3->fetch_assoc(); 

?>

	<?php

				if($editidPermission)
						{

							
							?>

							<form method="POST">

					<div class="form-group" style="display:flex; justify-content: center;">
						<input type="hidden" name="laid" value="<?php echo $laid; ?>">
						<input type="hidden" name="currtab" value="creation-tabdiv">

						<div class="col-sm-6">
							<div class="input-group input-group-button">

								
								<input name="laidName"  required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo $laid; ?>"><div> </div>


								<input  name="deliverydate"  required type="text" class="form-control form-control-uppercase" placeholder="" style="display: none; margin: 10px;" value="<?php echo $delivery_date; ?>"><div> </div>
								
								
							</div>
						</div>
					</div>


					<div class="form-group row">
		
						<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-block col-sm-2 pull-right" name="editid"><i class="feather icon-edit"></i>Edit</button>
						</div>
					</div>


				</form>
					<?php 
						}
					?>


	
	Customer Name: <?php echo $result2["value"] ?> <br>
	Customer Id: <?php echo $dumC ?> 		<br>
	Tentative Dispatch Date: <?php echo $result3["value"] ?>


	<?php 

		$CUTOMER_NAME = $result2["value"];

	?>

	<br>
	<br>

	<hr>

	<br>
	<br>
	<form method="POST">
		
		<div class="form-group row" >
			<label class="col-md-3"> Company</label>
			<select required class="form-control col-sm-3" name="company" id = "curr_company">
				<option value="SLM Metal">SLM Metal</option>
				<option value="SLM Technology">SLM Technology</option>


			</select>
		</div>

		<div class="form-group row" >
			<label for="transport" class="col-md-3"> Mode of Transport</label>
			<select required class="form-control col-sm-3" name="transport" id ="curr_transport">
				<option value="Truck">Truck</option>
				<option value="Cargo">Cargo</option>
				<option value="Courier">Courier</option>


			</select>
		</div>
		<div class="form-group row" >
			<label for="deliverydate" class="col-md-3"> Delivery Date</label>
			<input name="deliverydate" id="deliverydate"  required type="text" class="form-control form-control-uppercase col-md-3" placeholder=" Delivery Date" style="margin: 10px;" value="<?php echo $delivery_date; ?>">
		</div>


		<?php

				if($editidPermission)
						{

							
							?>

							<input type="hidden" name="laid" value="<?php echo $laid; ?>">
							<input type="hidden" name="currtab" value="creation-tabdiv">
		<div class="form-group row">
						
						<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-block col-sm-2 pull-right" name="updatebasic"><i class="feather icon-edit"></i>Update Details</button>
						</div>
		</div>

		<?php

			}
		?>

	</form>
	<script>
					$(function() {
					  $('input[name="deliverydate"]').daterangepicker({
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
					

	</script>

	<script type="text/javascript">
		
		document.getElementById('curr_company').value = '<?php echo $currCompany; ?>';
		document.getElementById('curr_transport').value = '<?php echo $tranport_type; ?>';

	</script>


</div>





<div class="tab-pane" id="batches-tabdiv" role="tabpanel" >


<form method="POST">

<div class="form-group" style="display:flex; justify-content: center;">
	<input type="hidden" name="laid" value="<?php echo $laid; ?>">
	<input type="hidden" name="currtab" value="batches-tabdiv">


	<table class="table table-striped">
		<thead>
			<tr>
				<th>Sl. No</th>
				<th>Grade</th>
				<th>Batch</th>
				<th>Quantity</th>
				<th>Packaging</th>
				<th></th>
			</tr>
		</thead>

		<tbody id="batch-tbody">
			
			<?php 

				$result = runQuery("SELECT * FROM loadingadvice_batches WHERE laid='$laid'");
				$k=1;
				while($row= $result->fetch_assoc())
				{

					
			?>

				<tr id="batchrow-<?php echo $k;?>">
					<td><?php echo $k;?></td>
					<td><input  required type="text" name="grades[]" class="form-control" value="<?php echo $row["grade"];?>"></td></td>
					<td>
						<select id="batch-select-<?php echo $k;?>" required class="form-control" name="batch[]">
							<option disabled value="">Choose a batch</option>
							<?php

							$dgrade = $row["grade"];
							$result2 = runQuery("SELECT premixid as processid FROM premix_batch WHERE gradename='$dgrade' ");

							if($result2->num_rows==0)
							{
								$result2 = runQuery("SELECT * FROM processentry WHERE processname='BATCH' AND (islocked='LOCKED' OR islocked='BATCHED') AND processid in (SELECT processid FROM processentryparams WHERE param='$GRADE_TITLE' AND value='$dgrade')");
							}

							


							while($row2=$result2->fetch_assoc())
							{
								?>

									<option value="<?php echo $row2["processid"] ?>"><?php echo $row2["processid"] ?></option>

								<?php
							}

							?>

						</select>
					</td>
					<td><input required type="number" min="0" step="0.01" name="qty[]" class="form-control" value="<?php echo $row["quantity"];?>"></td>
					<td>
						<select id="pkg-select-<?php echo $k;?>" required class="form-control" name="pkg[]">
							<?php

							$result2 = runQuery("SELECT * FROM dispatch_package");

							while($row2=$result2->fetch_assoc())
							{
								?>


								<option value="<?php echo $row2["packagename"] ?>"><?php echo $row2["packagename"] ?></option>

								<?php
							}

						?>


						</select>
					</td>

					<td>
						<button onclick="duplicaterow(document.getElementById('batchrow-<?php echo $k;?>'),this.closest('tbody'))" class="btn btn-primary" type="button"><i class="fa fa-copy"></i>Duplicate</button>

						<button onclick="this.closest('tr').remove()" class="btn btn-danger" type="button"><i class="fa fa-trash"></i>Delete</button>

						<button type="button"  class="btn btn-primary" onclick="opentag(document.getElementById('batch-select-<?php echo $k;?>'))"><i class="fa fa-tag"></i>Dispatch Tag</button>
					</td>

						<script type="text/javascript">

							$( document ).ready(function() {
							    document.getElementById('pkg-select-<?php echo $k;?>').value='<?php echo $row["package"];?>';
								document.getElementById('batch-select-<?php echo $k;?>').value='<?php echo $row["batch"];?>';
							});

							function opentag(batch)
							{
								window.open('dispatch-print.php?id='+batch.value+'&cid=<?php echo $laid;?>', '_blank').focus();
							}


							function duplicaterow(trrow,tbody)
							{
								

								var tr = document.createElement('tr');

								tr.innerHTML = trrow.innerHTML;
								tr.children[2].children[0].id = tr.children[2].children[0].id + '-d';
								tr.children[2].children[0].value = null;
								
								tbody.appendChild(tr);
							}
							
						</script>

						
				</tr>


			<?php

				$k++;

				}

			?>


		</tbody>
	</table>

	

</div>
<div class="form-group row">
			
			<div class="col-sm-12">	
			<button type="submit"  name="confirmld" class="btn btn-primary pull-right"><i class="fa fa-check"></i>Confirm</button>
			<button type="button" onclick="printbatch()"  class="btn btn-primary pull-left"><i class="fa fa-print"></i>Print</button>
			<span class="messages"></span>
			</div>
	</div>

</form>




</div>



<script type="text/javascript">
	
	function printbatch()
	{

		alldata = [];
		

		
		var form  = document.createElement('form');
  		form.setAttribute('method','POST');
  		form.setAttribute('action','/user/dispatch/loadingadvice-print.php');
  		form.setAttribute('target','_blank');

  		var i = document.createElement("input"); //input element, text
		i.setAttribute('type',"hidden");
		i.setAttribute('name',"laid");
		i.setAttribute('value',"<?php echo $laid ?>");

		form.appendChild(i);


		var i = document.createElement("input"); //input element, text
		i.setAttribute('type',"hidden");
		i.setAttribute('name',"delivery_date");
		i.setAttribute('value',"<?php echo $delivery_date ?>");

		form.appendChild(i);


		var i = document.createElement("input"); //input element, text
		i.setAttribute('type',"hidden");
		i.setAttribute('name',"customer");
		i.setAttribute('value',"<?php echo $CUTOMER_NAME; ?>");

		form.appendChild(i);


		<?php


			$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Address'");
			$result2 = $result2->fetch_assoc(); 
			$c_address = $result2['value'];

		?>
			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"customer_address[]");
			i.setAttribute('value',"<?php echo $c_address ; ?>");

			form.appendChild(i);

		<?php
			$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='City'");
			$result2 = $result2->fetch_assoc(); 
			$c_city = $result2['value'];

		?>
			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"customer_address[]");
			i.setAttribute('value',"<?php echo $c_city ; ?>");

			form.appendChild(i);

		<?php


			$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='State'");
			$result2 = $result2->fetch_assoc(); 
			$c_state = $result2['value'];

		?>
			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"customer_address[]");
			i.setAttribute('value',"<?php echo $c_state ; ?>");

			form.appendChild(i);

		<?php


			$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Pincode'");
			$result2 = $result2->fetch_assoc(); 
			$c_pincode = $result2['value'];


		?>

			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"customer_address[]");
			i.setAttribute('value',"<?php echo $c_pincode ; ?>");

			form.appendChild(i);
		

		


		tbody = document.getElementById('batch-tbody');

		for(var j =0; j <tbody.children.length;j++)
		{
			var curr = tbody.children[j];

			


			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"datagrade[]");
			i.setAttribute('value',curr.children[1].children[0].value);

			form.appendChild(i);


			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"databatch[]");
			i.setAttribute('value',curr.children[2].children[0].value);

			form.appendChild(i);


			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"dataqty[]");
			i.setAttribute('value',curr.children[3].children[0].value);

			form.appendChild(i);


			var i = document.createElement("input"); //input element, text
			i.setAttribute('type',"hidden");
			i.setAttribute('name',"datapkg[]");
			i.setAttribute('value',curr.children[4].children[0].value);

			form.appendChild(i);

			
		}



		document.body.appendChild(form);
		form.submit();
		document.body.removeChild(form);
	}

</script>





<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="laid" value="<?php echo $laid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM loadingadvice_notes WHERE laid='$laid' ORDER by time");

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
						i.setAttribute('name',"laid");
						i.setAttribute('value',"<?php echo $laid ?>");

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
						i.setAttribute('name',"laid");
						i.setAttribute('value',"<?php echo $laid ?>");

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




