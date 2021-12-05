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
        "Page Title" => "Edit Dispatch Order | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "dispatch-view",
        "MainMenu"	 => "dispatch_menu",

    ];


    if(!isset($_POST["cid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $cid = $_POST["cid"];

   



   if(isset($_POST['confirminvoice']))
   {

 
   		$qtys = $_POST['invoice-qty'];
   		$invoices = $_POST['invoice'];

   		runQuery("DELETE  FROM dispatch_invoices WHERE cid='$cid'");
   		foreach ($invoices as $batchname => $invoice) {
   			
   			for($i=0;$i<count($invoice);$i++)
   			{
   				$cinv = $invoice[$i];
   				$cqty = $qtys[$batchname][$i];
   				
   				runQuery("INSERT INTO dispatch_invoices VALUES(NULL,'$cid','$batchname','$cinv','$cqty')");

   			}
   		}


   }

   


   $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }



   

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO dispatch_notes VALUES(NULL,'$cid','$myuserid','$note',CURRENT_TIMESTAMP)");

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
						i.setAttribute('name',"cid");
						i.setAttribute('value',"<?php echo $cid ?>");

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
					<h5>Edit Order (<?php echo $cid; ?>)</h5>
					<span>Edit dispatch parameters</span>
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
<a class="nav-link" data-toggle="tab" href="#invoices-tabdiv" role="tab"><i class="fa fa-shopping-bag"></i>Add Invoices</a>
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
	
	$result = runQuery("SELECT * FROM dispatch WHERE cid='$cid'");

	$result = $result->fetch_assoc();

	$dumC = $result["customer"];
	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
	$result2 = $result2->fetch_assoc(); 
	$customerid = $dumC;


	

?>
	
	Customer Name: <?php echo $result2["value"] ?> <br>
	Customer Id: <?php echo $dumC ?> 		<br>


	<br>
	<br>

	<hr>

	<br>
	<br>
	




</div>





<div class="tab-pane" id="invoices-tabdiv" role="tabpanel" >


<form method="POST" id="invoice-form">
<input type="hidden" name="cid" value="<?php echo $cid; ?>">
	<input type="hidden" name="currtab" value="invoices-tabdiv">

	<?php

		$result = runQuery("SELECT * FROM loadingadvice_batches WHERE laid in (SELECT laid from dispatch WHERE cid='$cid')");

		$k=1;

		while($row=$result->fetch_assoc())
		{

			$coaflag = false;

	?>



	

	<big><?php echo $row["batch"] . " ( ".$row['quantity']." kg ) " ?></big>
	<br>
	<br>

	<input type="hidden" id="invoice-tqty-<?php echo $k;?>" value="<?php echo $row['quantity']; ?>">

	<div class="form-group row">


	
			<div class="col-sm-3">
				<input  type="text"   class="form-control" id="invoice-id-<?php echo $k;?>" placeholder="Invoice Id">
					
			</div>


			<div class="col-sm-2">
				
					<input  type="number" min="0.01" step="0.01"  class="form-control" id="invoice-qty-<?php echo $k;?>" placeholder="Quantity(kg)">
			</div>

			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="addtolist(document.getElementById('invoice-id-<?php echo $k;?>').value,document.getElementById('invoice-qty-<?php echo $k;?>').value,document.getElementById('invoice-total-<?php echo $k;?>'),<?php echo $k; ?>,'<?php echo $row['batch']; ?>')"><i class="fa fa-plus"></i>Add</button>
					
			</div>
	</div>



	
	<div class="form-group" style="display:flex; justify-content: center;">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Invoice</th>
				<th>Quantity</th>
				<th></th>
			</tr>
		</thead>

		<tbody id="invoice-tbody-<?php echo $k;?>">

			<tr id="invoice-total-<?php echo $k;?>">
				<td>Total Quantity</td>
				<td>0 Kg</td>
				<td></td>

			</tr>
		</tbody>
			
			
	</table>

	

</div>
<br>
	<br>
	
	




	<?php 
	$dbatch = $row['batch'];
		$result3 = runQuery("SELECT * FROM dispatch_invoices WHERE cid='$cid' AND batch='$dbatch'");
		while($row3=$result3->fetch_assoc())
		{

			$coaflag = true;
	?>

	<script type="text/javascript">

		$( document ).ready(function() {
		    addtolist('<?php echo $row3['invoice'];?>','<?php echo $row3['qty'];?>',document.getElementById('invoice-total-<?php echo $k;?>'),<?php echo $k; ?>,'<?php echo $row['batch']; ?>');
		});
		
	</script>



	
<?php

	}
?>

	<?php 

		if($coaflag)
		{



	?>

	<div class="form-group row">

			<div class="col-sm-12">
			<button type="button"  class="btn btn-primary pull-right" onclick="window.open('generatecoa.php?id=<?php echo $row["batch"];?>&cid=<?php echo $cid;?>', '_blank').focus();"><i class="fa fa-page"></i>Generate COA</button>
			<span class="messages"></span>
			</div>
	</div>


	<hr>
	<br>
	<br>


	<?php 
		}

	?>


<?php


	$k++;
}
?>


<script type="text/javascript">
	function addtolist(invoice,qty,total,k,batchname)
	{
		dumTotal = total.cloneNode('true');
		total.remove();

		invoiceTbody = document.getElementById('invoice-tbody-'+String(k));


		var tr =  document.createElement('tr');

		tr.innerHTML = "<td>"+invoice+"</td>" + "<td>"+qty+"</td>" + "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();confirmInvoice();\"><i class=\"fa fa-trash\"></i>Remove</button></td><input type='hidden' name=\"invoice-qty["+batchname+"][]\" value='"+qty+"'><input type='hidden' name=\"invoice["+batchname+"][]\" value='"+invoice+"'>" ; 

		invoiceTbody.appendChild(tr);

		invoiceTbody.appendChild(dumTotal);
		
		confirmInvoice()


	}


	function confirmInvoice()
	{
		totalbatches = <?php echo $k-1; ?>

		flag = true;

		for (var i = 1; i <= totalbatches; i++) {
			currTbody = document.getElementById('invoice-tbody-'+String(i));

			var total = 0;
			var required = parseFloat(document.getElementById('invoice-tqty-'+String(i)).value);
			

			for(var j=0;j<currTbody.children.length-1;j++)
			{

				total += parseFloat(currTbody.children[j].children[1].innerHTML)
			}

			cflag = (total==required);
			flag = flag && cflag;
			document.getElementById('invoice-total-'+String(i)).children[1].innerHTML = String(total) + " kg";
			
			if(cflag)
			{
				document.getElementById('invoice-total-'+String(i)).classList.remove('bg-danger')
				document.getElementById('invoice-total-'+String(i)).classList.add('bg-success')
			}
			else
			{
				document.getElementById('invoice-total-'+String(i)).classList.remove('bg-success')
				document.getElementById('invoice-total-'+String(i)).classList.add('bg-danger')
			}
		}

		if(flag)
		{
			
			document.getElementById('confirminvoice').onclick = function (){
				document.getElementById('invoice-form').submit();
			};
		}
		else
		{
			document.getElementById('confirminvoice').onclick = function (){
				
				Swal.fire({
				  icon: 'error',
				  title: 'Error',
				  text: 'The invoice quantities does not add up to total.',
				  
				})
			};
		}
	}

</script>

<div class="form-group row">
			<input type="hidden" name="confirminvoice" value="">
			<div class="col-sm-12">
			<button type="button"  id="confirminvoice"  name="confirminvoice" class="btn btn-primary pull-right"><i class="fa fa-check"></i>Confirm</button>
			<span class="messages"></span>
			</div>
	</div>

</form>




</div>









<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="cid" value="<?php echo $cid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM dispatch_notes WHERE cid='$cid' ORDER by time");

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
						i.setAttribute('name',"cid");
						i.setAttribute('value',"<?php echo $cid ?>");

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
						i.setAttribute('name',"cid");
						i.setAttribute('value',"<?php echo $cid ?>");

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




