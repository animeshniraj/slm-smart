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
        "Page Title" => "Edit Sample Dispatch | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "sdispatch-view",
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

   



   if(isset($_POST['confirmbatches']))
   {



   		$processids = $_POST['processid'];
   		$processes = $_POST['process'];

   		$grades = $_POST['grade'];
   		$qtys = $_POST['qty'];
 		
 		runQuery("DELETE FROM sdispatch_batches WHERE cid='$cid'");

 		for($i=0;$i<count($processids);$i++)
 		{
 			$cprocess = $processes[$i];
 			$cpid = $processids[$i];
 			$cqty = $qtys[$i];
 			$cgrade = $grades[$i];

 			runQuery("INSERT INTO sdispatch_batches VALUES(NULL,'$cid','$cpid','$cprocess','$cgrade','$cqty')");
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

    	runQuery("INSERT INTO sdispatch_notes VALUES(NULL,'$cid','$myuserid','$note',CURRENT_TIMESTAMP)");

    }



    $allSamples = [];

    $processes = ['Melting','Raw Bag','Raw Blend','Annealing','Semi Finished','Batch','Premix'];


    foreach ($processes as $process) {
    	$allSamples[$process] = [];

    	if($process=="Premix")
    	{
    		$result = runQuery("SELECT * FROM premix_batch");
    		while($row=$result->fetch_assoc())
    		{
    			array_push($allSamples[$process],[$row['premixid'],$row['gradename']]);
    		}
    	}
    	elseif($process=="Melting")
    	{
    		$result = runQuery("SELECT * FROM processentry WHERE processname='$process'");
    		while($row=$result->fetch_assoc())
    		{
    			

    			array_push($allSamples[$process],[$row['processid'],'Default Grade']);
    		}
    	}
    	else
    	{
    		$result = runQuery("SELECT * FROM processentry WHERE processname='$process'");
    		while($row=$result->fetch_assoc())
    		{
    			$cpid = $row['processid'];
    			$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$cpid' AND param='$GRADE_TITLE'");
    			
    			if($result2 = $result2->fetch_assoc())
    			{
    				array_push($allSamples[$process],[$row['processid'],$result2['value']]);
    			}


    			
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
<a class="nav-link" data-toggle="tab" href="#batches-tabdiv" role="tab"><i class="fa fa-shopping-bag"></i>Add Samples</a>
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
	
	$result = runQuery("SELECT * FROM sample_dispatch WHERE cid='$cid'");

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





<div class="tab-pane" id="batches-tabdiv" role="tabpanel" >


<form method="POST">
<input type="hidden" name="cid" value="<?php echo $cid; ?>">
<input type="hidden" name="currtab" value="batches-tabdiv">


	<div class="form-group row">


	
			<div class="col-sm-3">
				
				<select id="sample-select" class="form-control">
					
					<option selected disabled value="">Choose a sample</option>

					<?php 

						foreach ($allSamples as $process => $sample) {
							echo "<optgroup label='".$process."'>";
							foreach ($sample as $csample) {

						

					?>
					<option data-grade="<?php echo $csample[1] ?>" data-process="<?php echo $process ?>" value="<?php echo $csample[0] ?>"><?php echo $csample[0]. "( ". $csample[1]." )" ?></option>


					<?php 
				}
					}

					?>

				</select>
					
			</div>


			<div class="col-sm-2">
				
					<input  type="number" min="0.01" step="0.01"  class="form-control" id="sample-qty" placeholder="Quantity(kg)">
			</div>

			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="addtolist()"><i class="fa fa-plus"></i>Add</button>
					
			</div>
	</div>

	<div class="form-group" style="display:flex; justify-content: center;">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Process Id</th>
				<th>Process</th>
				<th>Grade</th>
				<th>Quantity</th>
				<th></th>
			</tr>
		</thead>

		<tbody id="sample-tbody">

			
		</tbody>
			
			
	</table>

	

</div>


<script type="text/javascript">
	
	function addtolist()
	{
		var select =  document.getElementById('sample-select');
		var id = select.value
		var qty = document.getElementById('sample-qty').value

		var process = select.options[select.selectedIndex].getAttribute('data-process')
		var grade = select.options[select.selectedIndex].getAttribute('data-grade')
		

		tbody = document.getElementById('sample-tbody');

		var tr =  document.createElement('tr');

		tr.innerHTML = "<td>"+id+"</td>" + "<td>"+process+"</td>" + "<td>"+grade+"</td>" + "<td>"+qty+"</td>" + "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button></td><input type='hidden' name=\"qty[]\" value='"+qty+"'><input type='hidden' name=\"process[]\" value='"+process+"'><input type='hidden' name=\"processid[]\" value='"+id+"'><input type='hidden' name=\"grade[]\" value='"+grade+"'></td>";

		tbody.appendChild(tr);

		if(tbody.children.length>0)
		{
			document.getElementById('confirmbatches').disabled=false;
		}
		else
		{
			document.getElementById('confirmbatches').disabled=true;
		}

	}


	function addtolistmanual(id,qty,process,grade)
	{
		
		tbody = document.getElementById('sample-tbody');

		var tr =  document.createElement('tr');

		tr.innerHTML = "<td>"+id+"</td>" + "<td>"+process+"</td>" + "<td>"+grade+"</td>" + "<td>"+qty+"</td>" + "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button></td><input type='hidden' name=\"qty[]\" value='"+qty+"'><input type='hidden' name=\"process[]\" value='"+process+"'><input type='hidden' name=\"processid[]\" value='"+id+"'><input type='hidden' name=\"grade[]\" value='"+grade+"'></td>";

		tbody.appendChild(tr);

		

	}

</script>

<?php
	
	$result2 = runQuery("SELECT * FROM sdispatch_batches WHERE cid='$cid'");
	while($row=$result2->fetch_assoc())
	{

?>
<script type="text/javascript">
	addtolistmanual('<?php echo $row['processid'] ?>','<?php echo $row['quantity'] ?>','<?php echo $row['process'] ?>','<?php echo $row['grade'] ?>')

	$( document ).ready(function() {
	    document.getElementById('confirmbatches').disabled=false;
	});
</script>

<?php
	
	}

?>

<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" disabled  id="confirmbatches"  name="confirmbatches" class="btn btn-primary pull-right"><i class="fa fa-check"></i>Confirm</button>
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

                		$result = runQuery("SELECT * FROM sdispatch_notes WHERE cid='$cid' ORDER by time");

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




