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
        "Page Title" => "Edit Purchase Order | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "purchase-view",
        "MainMenu"	 => "dispatch_menu",

    ];

    if(!isset($_POST["orderid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $orderid = $_POST["orderid"];



   if(isset($_POST['closepo']))
   {

   		runQuery("UPDATE purchaseorder_tentative SET status='CLOSED' WHERE orderid='$orderid' AND status='UNFULFILLED'");
   		runQuery("UPDATE purchase_order SET status='FULFILLED' WHERE orderid='$orderid'");

   }

   if(isset($_POST["addTentative"]))
  	{





  		$tAllgrades = $_POST['tentative-grade'];
  		$tDates = $_POST['tentative-date'];
  		$tQty = $_POST['tentative-qty'];
  		$tpkg = $_POST['tentative-pkg'];


  		

  		runQuery("DELETE FROM purchaseorder_tentative WHERE orderid='$orderid'");
  		foreach ($tAllgrades as $currid => $currGrade) {

  				
  				
  				for($i=0;$i<count($tDates[$currid]);$i++)
  				{
  					$cDate = $tDates[$currid][$i];
  					$cQty  = $tQty[$currid][$i];
  					$cpkg = $tpkg[$currid][$i];

  					runQuery("INSERT INTO purchaseorder_tentative VALUES(NULL,'$orderid','$currGrade','$cDate','$cQty','$cpkg','UNFULFILLED')");

  				}
  		}

  		

  		

  	}



  	if(isset($_POST["editid"]))
  	{
  		
  		$newid = $_POST["orderidName"];

  		$result = runQuery("SELECT * FROM purchase_order WHERE orderid='$newid'");
    	if($result->num_rows==0)
    	{
    		runQuery("INSERT INTO purchaseorder_notes (SELECT NULL,'$newid',sender,note,time FROM purchaseorder_notes WHERE orderid='$orderid' ORDER by time)");
    		runQuery("INSERT INTO purchaseorder_params (SELECT NULL,'$newid',step,param,value,tag FROM purchaseorder_params WHERE orderid='$orderid')");

    		runQuery("INSERT INTO purchaseorder_tentative (SELECT NULL,'$newid',grade,date,quantity,status FROM purchaseorder_params WHERE orderid='$orderid')");
    		runQuery("INSERT INTO purchase_order (SELECT '$newid',customer,entrydate,status FROM purchase_order WHERE orderid='$orderid')");

    		runQuery("DELETE FROM purchaseorder_notes WHERE orderid='$orderid'");
    		runQuery("DELETE FROM purchaseorder_params WHERE orderid='$orderid'");
    		runQuery("DELETE FROM purchase_order WHERE orderid='$orderid'");
    		$orderid = $newid;
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
    			runQuery("DELETE FROM purchaseorder_params WHERE orderid='$orderid' AND step='BATCH'");
    			runQuery("DELETE FROM purchaseorder_params WHERE orderid='$orderid' AND step='DATA'");

    	}
    	else
    	{


    		$batchids= $_POST["order_batchid"];
    		//$package = $_POST['order_batchpkg'];
	    	$qty = $_POST['order_batchqty'];

	    	runQuery("DELETE FROM purchaseorder_params WHERE orderid='$orderid' AND step='BATCH'");
	    	runQuery("DELETE FROM purchaseorder_params WHERE orderid='$orderid' AND step='DATA'");

	    	for($i=0;$i<count($batchids);$i++)
	    	{
	    		$cid =  $batchids[$i];
	    		//$cpkg =  $package[$i];
	    		$cqty =  $qty[$i];

	    		runQuery("INSERT INTO purchaseorder_params VALUES(NULL,'$orderid','BATCH','$cid','$cqty','batchid')");
	    		//runQuery("INSERT INTO purchaseorder_params VALUES(NULL,'$orderid','DATA','$cid','$cpkg','package')");
	    		
	    	}

	    	$allgrades = [];
	    	$result = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$orderid' AND tag='batchid'");
	  		while($row=$result->fetch_assoc())
	  		{
	  			array_push($allgrades,$row['param']);
	  		}

	  		$added = [];
	    	$result = runQuery("SELECT * FROM purchaseorder_tentative WHERE orderid='$orderid'");
	  		while($row=$result->fetch_assoc())
	  		{
	  			array_push($added,$row['grade']);
	  		}

	  		$array3 = array_diff($added, $allgrades);

	  		foreach ($array3 as $value) {
	  			runQuery("DELETE FROM purchaseorder_tentative WHERE orderid='$orderid' AND grade='$value' ");
	  		}

    	}

    }
    

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO purchaseorder_notes VALUES(NULL,'$orderid','$myuserid','$note',CURRENT_TIMESTAMP)");

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
		titleicon.classList.remove("fa-file-text-o");
		titleicon.classList.add("fa-refresh");

	}
	function titleicontonormal()
	{
		var titleicon = document.getElementById('titleicon');
		titleicon.classList.remove("fa-refresh");
		titleicon.classList.add("fa-file-text-o");
		

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
						i.setAttribute('name',"orderid");
						i.setAttribute('value',"<?php echo $orderid ?>");

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
				<i id="titleicon" onmouseenter="titleicontoRefresh()" onmouseleave="titleicontonormal()" onclick="reloadCurrPage()" style="cursor: pointer;"  class="fa fa-file-text-o bg-c-blue"></i>
				
				<div class="d-inline">
					<h3>Edit Order (<?php echo $orderid; ?>)</h3>
					<span>Enter purchase order details</span>
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
<a class="nav-link" data-toggle="tab" href="#batches-tabdiv" role="tab"><i class="fa fa-shopping-bag"></i> Add Grades</a>
<div class="slide"></div>
</li>


<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#tentative-tabdiv" role="tab"><i class="fa fa-clock-o"></i> Tentative Dispatch</a>
<div class="slide"></div>
</li>



<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#notes-tabdiv" role="tab"><i class="icofont icofont-edit"></i> Notes</a>
<div class="slide"></div>
</li>




</ul>

<div class="tab-content card-block">

<div class="tab-pane" id="creation-tabdiv" role="tabpanel">


	<?php

				if($editidPermission)
						{

							
							?>

							<form method="POST">

					<h5 style="text-align:center;font-weight:boldl">Purchase Order No.</h5>		
					<div class="form-group" style="display:flex; justify-content: center;">
						<input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
						<input type="hidden" name="currtab" value="creation-tabdiv">
						<div class="col-sm-3">
							<div class="input-group input-group-button">

								
								<input name="orderidName"  required type="text" class="form-control form-control-uppercase" placeholder="" style="margin: 10px;" value="<?php echo $orderid; ?>"><div> </div>
								
								
							</div>
						</div>
					</div>


					<div class="form-group row">
		
						<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-block col-sm-2 pull-right" name="editid"><i class="feather icon-edit"></i>Update P.O.</button>
						</div>
					</div>


				</form>
					<?php 
						}
					?>

					<form method="POST" id="closepoform">
						<input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
						<input type="hidden" name="currtab" value="creation-tabdiv">
						<input type="hidden" name="closepo" value="">
						

						<div class="form-group row">
			
							<div class="col-sm-12">
							<button type="button" onclick="closepoalert()" class="btn btn-primary btn-block col-sm-2 pull-right"><i class="fa fa-times"></i>Close PO.</button>
							</div>
						</div>
					</form>


					<script type="text/javascript">
						function closepoalert()
						{
							Swal.fire({
							  icon: 'info',
							  title: 'Close PO?',
							  html: 'Are you sure you want to close this PO. All pending dispatches will be closed. This process is not reversible.',
							  confirmButtonText: 'Yes',
							  cancelButtonText: 'No',
							  showCancelButton: true,
							  
							}).then((result) => {

								document.getElementById('closepoform').submit();

							});
						}
					</script>

<?php
	
	$result = runQuery("SELECT * FROM purchase_order WHERE orderid='$orderid'");

	$result = $result->fetch_assoc();

	$dumC = $result["customer"];
	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
	$result2 = $result2->fetch_assoc(); 
	$customerid = $dumC;

	$currStatus =  $result["status"]

?>
	
	<div class="row" style="border:2px solid #800000;border-radius:10px;width:50%;">
		<div class="col-md-4">
			<img src="customers.png" style="width:75px;padding:10px;">
		</div>
		<div class="col-md-8 mt-2">
			<h4>Customer: <?php echo $result2["value"] ?><br>Customer ID: <?php echo $dumC ?></h4>
		</div>
	</div>

</div>





<div class="tab-pane" id="batches-tabdiv" role="tabpanel" >



	<div class="form-group row">


	
			<div class="col-sm-3">
				
					<select class="form-control" id="select-batch">
						<option disabled selected value=""> Choose a grade</option>
						<optgroup label="Premix Grades">
						<?php

							$result = runQuery("SELECT * FROM premix_grades WHERE gradename in (SELECT value FROM external_param WHERE externalid='$customerid' AND param='Grades')");



							while($row=$result->fetch_assoc())
							{
								?>


								<option value="<?php echo $row["gradename"] ?>"><?php echo $row["gradename"] ?></option>

								<?php
							}

						?>

						<optgroup label="Iron Powder Grades">

						<?php

							$result = runQuery("SELECT * FROM processgrades WHERE gradename in (SELECT value FROM external_param WHERE externalid='$customerid' AND param='Grades')");



							while($row=$result->fetch_assoc())
							{
								?>


								<option value="<?php echo $row["gradename"] ?>"><?php echo $row["gradename"] ?></option>

								<?php
							}

						?>



					</select>
			</div>


		<!--	<div class="col-sm-3">
				
					<select class="form-control" id="select-package">
						<option disabled selected=""> Choose a package</option>


						<?php

							$result = runQuery("SELECT * FROM dispatch_package");

							while($row=$result->fetch_assoc())
							{
								?>


								<option value="<?php echo $row["packagename"] ?>"><?php echo $row["packagename"] ?></option>

								<?php
							}

						?>


					</select>
			</div> -->




			<div class="col-sm-2">
				
					<input  type="number" min="0.01" step="0.01"  class="form-control" id="batch-qty" placeholder="Quantity(kg)">
			</div>

			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="addtolist()"><i class="fa fa-plus"></i>Add</button>
					
			</div>
	</div>


<script type="text/javascript">


	function addtolist()
	{
		var batchSelect = document.getElementById('select-batch')
		var batchid = batchSelect.value;
		var batch_avail = parseFloat(batchSelect.options[batchSelect.selectedIndex].getAttribute('data-available'))
		var used = parseFloat(document.getElementById('batch-qty').value)
		//var package = document.getElementById('select-package').value;
		
		if(false)
		{
			console.log(11);
			Swal.fire({
									icon: "error",
									title: "Error",
									html: "Selected quantity is more than available" ,
									showConfirmButton: true,
								  	showCancelButton: false,
								  	confirmButtonText: 'OK',
								  	
								})
		}
		else
		{
				var tr =  document.createElement('tr');
				var count = parseInt(document.getElementById('products-tbody').children.length) +1;
				tr.innerHTML = "<td>"+count+"</td><td><input type=\"hidden\" name=\"order_batchid[]\" value=\""+batchid+"\">"+batchid+"</td><td><input type=\"hidden\" name=\"order_batchqty[]\" value=\""+used+"\">"+used+"</td><td><button type=\"button\" class=\"btn btn-danger btn-sm rmv\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Remove Grades\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i></button></td>"
				document.getElementById('products-tbody').appendChild(tr);
				batchSelect.options[batchSelect.selectedIndex].remove();
		}

	}
	
	function loadBatch(type)
	{
			  var postData = new FormData();
       
        postData.append("action","loadbatch");
        postData.append("type",type);


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response)
            {
                
            	var batchSelect = document.getElementById('select-batch')
            	batchSelect.innerHTML = "";
            	

            	var opt = document.createElement('option');
					    opt.value = "";
					    opt.innerHTML = "Choose a batch";
					    opt.disabled = true;
					    opt.selected = true;
					    batchSelect.appendChild(opt);

					   for(var i=0;i<data.data.length;i++)
					   {
					   		opt = document.createElement('option');
						    opt.value = data.data[i][0];
						    opt.innerHTML = data.data[i][0] + "(Available:"+data.data[i][1]+" kg)";
						    opt.setAttribute("data-available", data.data[i][1]);
		
						    batchSelect.appendChild(opt);
					   }

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
        xmlhttp.open("POST", "/query/dispatch.php", true);
        xmlhttp.send(postData);
	}



</script>


<form method="POST">

<input type="hidden" name="orderid" value="<?php echo $orderid ?>">
<input type="hidden" name="currtab" value="batches-tabdiv">

<table class="table table-striped table-bordered table-xs" id="process4table">
		<thead>
		<tr>
			<th>Sl. No</th>
			<th>Item</th>
			<th>Quantity</th>
			<th>Remove</th>
		</tr>

	</thead>

	<tbody id="products-tbody">
			

		<?php

			$result = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$orderid' AND step='BATCH'");
			$k=1;
			$allgrades = [];
			while($row=$result->fetch_assoc())
			{
				$package ="";
				$currid = $row["param"];
				
				$result2 = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$orderid' AND param='$currid' AND step='DATA' AND tag='package'");
				$result2 = $result2->fetch_assoc();
				//$package = $result2["value"];


				array_push($allgrades,[$row["param"],$row["value"],$package])
			
				?>

				<tr>
					<td><?php echo $k; ?></td>
					<td><input type="hidden" name="order_batchid[]" value="<?php echo $row["param"]; ?>"><?php echo $row["param"]; ?></td>
					<td><input type="hidden" name="order_batchqty[]" value="<?php echo $row["value"]; ?>"><?php echo $row["value"]; ?></td>
					<!--<td><input type="hidden" name="order_batchpkg[]" value="<?php echo $package; ?>"><?php echo $package; ?></td>-->
					<td><button type="button" class="btn btn-danger" onclick="this.closest('tr').remove();"><i class="fa fa-trash"></i></button></td>

				</tr>


				<?php

				$k++;
			}


		?>

	</tbody>

</table>

<?php
	
	if($currStatus=="UNFULFILLED") {
?>

	<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit"  name="addorder" id="addorderbtn" class="btn btn-primary pull-right"><i class="fa fa-save"></i>Save Order</button>
			<span class="messages"></span>
			</div>
	</div>
<?php
	
	}
?>


</form>

</div>



<div class="tab-pane" id="tentative-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST" id="tentative-form">

<input type="hidden" name="orderid" value="<?php echo $orderid ?>">
<input type="hidden" name="currtab" value="tentative-tabdiv">


<?php

$result  = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$orderid' AND step='BATCH'");

if($result->num_rows>0)
{
	$k=0;
	foreach ($allgrades as $currgrade) {
		

	?>

	<big>For <?php echo $currgrade[0]?></big>

	<div class="form-group row">


	
			<div class="col-sm-3">

					<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" required name="curr-date" id="tentative-date-<?php echo $k; ?>" class="form-control" style="display: inline; text-align: center;" placeholder="Date">
						
					</div>
				

			</div>


			<div class="col-sm-3">
				
					<select class="form-control" id="tentative-pkg-<?php echo $k; ?>">
						<option disabled selected value=""> Choose a package</option>


						<?php

							$result = runQuery("SELECT * FROM dispatch_package");

							while($row=$result->fetch_assoc())
							{
								?>


								<option value="<?php echo $row["packagename"] ?>"><?php echo $row["packagename"] ?></option>

								<?php
							}

						?>


					</select>
			</div>





			<div class="col-sm-2">
				
					<input  type="number" min="0.01" step="0.01"  class="form-control" id="tentative-qty-<?php echo $k; ?>" placeholder="Quantity(kg)">
			</div>

			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="addtotentative(document.getElementById('tentative-tbody-<?php echo $k; ?>'),document.getElementById('tentative-date-<?php echo $k; ?>').value,document.getElementById('tentative-qty-<?php echo $k; ?>').value,document.getElementById('tentative-pkg-<?php echo $k; ?>').value,'<?php echo $currgrade[0] ?>')"><i class="fa fa-plus"></i>Add</button>
					
			</div>
	</div>

	<table class="table table-striped table-bordered table-xs" id="tentative-grade-<?php echo $k; ?>">

		<tr>
			<th>Tentative Date</th>
			<th>Quantity (in Kg)</th>
			<th></th>
		</tr>


		<tbody id="tentative-tbody-<?php echo $k; ?>">
			

			<tr id="tentative-total-<?php echo $k; ?>">
				<td>Total Quantity</td>
				<td>0 kg</td>
				<td></td>
			</tr>
		</tbody>




	</table>
	<?php 

			$dumgrade = $currgrade[0];
			$result2 = runQuery("SELECT * FROM purchaseorder_tentative WHERE orderid='$orderid' AND grade='$dumgrade'") ;
			echo "<script>";
			
			echo "\$( document ).ready(function() {";
			while($row2 = $result2->fetch_assoc())
			{
				//echo "s";
				
				echo "addtotentative(document.getElementById('tentative-tbody-".$k."'),'".$row2['date']."',".$row2['quantity'].",'".$row2['package']."','".$currgrade[0]."');\n";
			}
			echo "}); \n</script>";
		?>
	<br><br>
	<hr>
	<br><br>

	<?php

	$k++;

	}
	?>



	<?php
}
else
{
	echo "Update the previous tab with grade and quantity details first.";
}



?>
<?php
	
	if($currStatus!="FULFILLED") {
?>
	<div class="form-group row">
			<input type="hidden" name="addTentative" value="">
			<div class="col-sm-12">
			<button  type="button" name="addTentative" id="addTentative" class="btn btn-primary pull-right"><i class="fa fa-save"></i>Save Order</button>
			<span class="messages"></span>
			</div>
	</div>
<?php
	
	}
?>
</form>


<script type="text/javascript">
	
	function addtotentative(tbody,tdate,tqty,tpkg,cgrade)
	{

		
		currTotalTD = tbody.children[tbody.children.length-1].innerHTML;
		currTotalId = tbody.children[tbody.children.length-1].id

		
		tbody.children[tbody.children.length-1].remove();
		var tr =  document.createElement('tr');
		

		tr.innerHTML = "<td>"+tdate+"(Package: "+tpkg+")</td><td>"+tqty+"</td><td><button class='btn btn-danger' type='button' onclick=\"this.closest('tr').remove();check_all_tentative();\"><i class=\"fa fa-trash\"></i>Remove</button><input type='hidden' name=\"tentative-date['"+currTotalId+"'][]\" value='"+tdate+"'><input type='hidden' name=\"tentative-qty['"+currTotalId+"'][]\" value='"+tqty+"'><input type='hidden' name=\"tentative-pkg['"+currTotalId+"'][]\" value='"+tpkg+"'><input type='hidden' name=\"tentative-grade['"+currTotalId+"']\" value='"+cgrade+"'></td>"
		tbody.appendChild(tr);



		var tr =  document.createElement('tr');
		tr.id = currTotalId
		tr.innerHTML = currTotalTD;
		tbody.appendChild(tr);
		check_all_tentative()
	}



	function check_all_tentative()
	{
		total_grades = <?php echo count($allgrades);?>
		

		allGradesQty = [<?php foreach ($allgrades as $currgrade) {
			echo $currgrade[1].",";
		} ?>]

		flag = true;

		k = 0;

		for (k=0;k<total_grades;k++)
		{
			currTbody = document.getElementById('tentative-tbody-'+String(k))
			currQty = allGradesQty[k];
			

			total = 0;
			for(var j=0;j<currTbody.children.length-1;j++)
			{
				
				total += parseFloat(currTbody.children[j].children[1].innerHTML)
			}

			
			cflag = (total==currQty);
			flag = cflag && flag
			document.getElementById('tentative-total-'+String(k)).children[1].innerHTML = total + " kg";

			if(cflag)
			{	
				document.getElementById('tentative-total-'+String(k)).classList.remove('bg-danger')
				document.getElementById('tentative-total-'+String(k)).classList.add('bg-success')
			}
			else{
				document.getElementById('tentative-total-'+String(k)).classList.remove('bg-success')
				document.getElementById('tentative-total-'+String(k)).classList.add('bg-danger')
			}
		}

		if(flag)
		{

			if(document.body.contains(document.getElementById('addTentative')))
			{
					document.getElementById('addTentative').onclick = function (){
						document.getElementById('tentative-form').submit();
					};
			}
			
			
			
		}
		else
		{

			if(document.body.contains(document.getElementById('addTentative')))
			{
					document.getElementById('addTentative').onclick = function (){
				
						Swal.fire({
						  icon: 'error',
						  title: 'Error',
						  text: 'The tentative quantities does not add up to total.',
						  
						})
					};
			}
			
			
		}
	}


</script>


					<script>
					$(function() {
					  $('input[name="curr-date"]').daterangepicker({
					    singleDatePicker: true,
					    timePicker: false,
					    timePicker24Hour: false,
					    showDropdowns: true,
					    locale: 
					    {    
					    	format: 'YYYY-MM-DD',
					    },
					  	
					    minYear: 1901,
					    maxYear: parseInt(moment().format('YYYY'),10)
					  }, function(start, end, label) {
					    
					  });


					});
					$('#creation-date').val('<?php echo DATE('Y-m-d H:i',strtotime("now")) ?>');

					</script>
</div>


<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM purchaseorder_notes WHERE orderid='$orderid' ORDER by time");

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
						i.setAttribute('name',"orderid");
						i.setAttribute('value',"<?php echo $orderid ?>");

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
						i.setAttribute('name',"orderid");
						i.setAttribute('value',"<?php echo $orderid ?>");

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




