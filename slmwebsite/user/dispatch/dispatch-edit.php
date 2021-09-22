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
        "Menu"		 => "dispatch-view",
        "MainMenu"	 => "dispatch_menu",

    ];

    if(!isset($_POST["invoiceid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $invoiceid = $_POST["invoiceid"];


   $currTab = "creation-tabdiv";

    if(isset($_POST["currtab"]))
    {
    	$currTab =$_POST["currtab"];
    }



    if(isset($_POST["addorder"]))
    {
    	$batchids= $_POST["order_batchid"];
    	$package = $_POST['order_batchpkg'];
    	$qty = $_POST['order_batchqty'];

    	runQuery("DELETE FROM dispatch_params WHERE invoiceid='$invoiceid' AND step='BATCH'");
    	runQuery("DELETE FROM dispatch_params WHERE invoiceid='$invoiceid' AND step='DATA'");

    	for($i=0;$i<count($batchids);$i++)
    	{
    		$cid =  $batchids[$i];
    		$cpkg =  $package[$i];
    		$cqty =  $qty[$i];

    		runQuery("INSERT INTO dispatch_params VALUES(NULL,'$invoiceid','BATCH','$cid','$cqty','batchid')");
    		runQuery("INSERT INTO dispatch_params VALUES(NULL,'$invoiceid','DATA','$cid','$cpkg','package')");
    	}
    	
    }
    

   if(isset($_POST["addNotes"]))
    {

    	$note = $_POST["note"];

    	runQuery("INSERT INTO dispatch_notes VALUES(NULL,'$invoiceid','$myuserid','$note',CURRENT_TIMESTAMP)");

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
						i.setAttribute('name',"invoiceid");
						i.setAttribute('value',"<?php echo $invoiceid ?>");

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
					<h5>Edit Order (<?php echo $invoiceid; ?>)</h5>
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

	$result = runQuery("SELECT * FROM dispatch_order WHERE invoiceid='$invoiceid'");

	$result = $result->fetch_assoc();

	$dumC = $result["customer"];
	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
	$result2 = $result2->fetch_assoc(); 

?>
	
	Customer Name: <?php echo $result2["value"] ?> <br>
	Customer Id: <?php echo $dumC ?>


</div>





<div class="tab-pane" id="batches-tabdiv" role="tabpanel" >



	<div class="form-group row">
			<div class="col-sm-2">
				
					<select class="form-control" id="select-type" onchange="loadBatch(this.value)">
						<option disabled selected=""> Choose a type</option>
						<option value="premix">Premix</option>
						<option value="final">Final Blend</option>
					</select>
					
			</div>

	
			<div class="col-sm-3">
				
					<select class="form-control" id="select-batch">
						<option disabled selected=""> Choose a batch</option>
					</select>
			</div>



			<div class="col-sm-3">
				
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
			</div>



			<div class="col-sm-2">
				
					<input  type="number" step="0.01"  class="form-control" id="batch-qty" placeholder="Quantity(kg)">
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
		var package = document.getElementById('select-package').value;
		
		if(used>batch_avail)
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
				tr.innerHTML = "<td>"+count+"</td><td><input type=\"hidden\" name=\"order_batchid[]\" value=\""+batchid+"\">"+batchid+"</td><td><input type=\"hidden\" name=\"order_batchqty[]\" value=\""+used+"\">"+used+"</td><td><input type=\"hidden\" name=\"order_batchpkg[]\" value=\""+package+"\">"+package+"</td><td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button></td>"
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

<input type="hidden" name="invoiceid" value="<?php echo $invoiceid ?>">
<input type="hidden" name="currtab" value="batches-tabdiv">

<table class="table table-striped table-bordered" id="process4table">
		<thead>
		<tr>
			<th>Sl. No</th>
			<th>Item</th>
			<th>Quantity</th>
			<th>Package</th>
			<th></th>
		</tr>

	</thead>

	<tbody id="products-tbody">
			

		<?php

			$result = runQuery("SELECT * FROM dispatch_params WHERE invoiceid='$invoiceid' AND step='BATCH'");
			$k=1;
			while($row=$result->fetch_assoc())
			{
				$package ="";
				$currid = $row["param"];
				$result2 = runQuery("SELECT * FROM dispatch_params WHERE invoiceid='$invoiceid' AND param='$currid' AND step='DATA' AND tag='package'");
				$result2 = $result2->fetch_assoc();
				$package = $result2["value"];

			
				?>

				<tr>
					<td><?php echo $k; ?></td>
					<td><input type="hidden" name="order_batchid[]" value="<?php echo $row["param"]; ?>"><?php echo $row["param"]; ?></td>
					<td><input type="hidden" name="order_batchqty[]" value="<?php echo $row["value"]; ?>"><?php echo $row["value"]; ?></td>
					<td><input type="hidden" name="order_batchpkg[]" value="<?php echo $package; ?>"><?php echo $package; ?></td>
					<td><button type="button" class="btn btn-danger" onclick="this.closest('tr').remove();"><i class="fa fa-trash"></i>Remove</button></td>

				</tr>


				<?php

				$k++;
			}


		?>

	</tbody>

</table>


	<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit"  name="addorder" id="addorderbtn" class="btn btn-primary pull-right"><i class="fa fa-save"></i>Save Order</button>
			<span class="messages"></span>
			</div>
			</div>



</form>

</div>









<div class="tab-pane" id="notes-tabdiv" role="tabpanel" style="position: relative; min-height: 600px;">

<form method="POST">

	 <div style="position: absolute; bottom: 0px; margin: 10px;">
	 	<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
	 	<input type="hidden" name="currtab" value="notes-tabdiv">
            <div id="notesDiv">
                <?php

                		$result = runQuery("SELECT * FROM dispatch_notes WHERE invoiceid='$invoiceid' ORDER by time");

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
						i.setAttribute('name',"invoiceid");
						i.setAttribute('value',"<?php echo $invoiceid ?>");

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
						i.setAttribute('name',"invoiceid");
						i.setAttribute('value',"<?php echo $invoiceid ?>");

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




