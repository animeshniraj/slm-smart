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
   		$notes = $_POST['notes'];
   		$prodcode = $_POST['prodcode'];

   		$dumcid = "SAMPLE/".$cid;
 		
 		runQuery("DELETE FROM sdispatch_batches WHERE cid='$cid'");
 		runQuery("DELETE FROM coa_notes WHERE cid='$dumcid'");

 		for($i=0;$i<count($processids);$i++)
 		{
 			$cprocess = $processes[$i];
 			$cpid = $processids[$i];
 			$cqty = $qtys[$i];
 			$cgrade = $grades[$i];
 			$cnote = $notes[$i];
 			$cpcode = $prodcode[$i];

 			runQuery("INSERT INTO sdispatch_batches VALUES(NULL,'$cid','$cpid','$cprocess','$cgrade','$cqty','$cpcode')");

 			
 			

 			runQuery("INSERT INTO coa_notes VALUES(NULL,'$dumcid','$cpid','$cnote')");
 		}

 		

   }


   if(isset($_POST['confirmtest']))
   {


   	$dumTestval = $_POST['testval'];

   	
   	runQuery("DELETE FROM sdispatch_test WHERE cid='$cid'");

   	foreach ($dumTestval as $dumprocess => $dumprocesstest) {
   		foreach ($dumprocesstest as $dprop => $dval) {
   			

   			runQuery("INSERT INTO sdispatch_test VALUES(NULL,'$cid','$dumprocess','$dprop','$dval')");
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

    			$used = getChildPremixQuantity($row['premixid']);
    			array_push($allSamples[$process],[$row['premixid'],$row['gradename']]);
    		}
    	}
    	elseif($process=="Melting")
    	{
    		$result = runQuery("SELECT * FROM processentry WHERE processname='$process'");
    		while($row=$result->fetch_assoc())
    		{
    			$total = getTotalQuantity($row['processid']);
    			
    			$used = getChildProcessQuantity($row['processid']);

    			if(($total-$used)>0)
    			{
    				array_push($allSamples[$process],[$row['processid'],'Default Grade']);
    			}
    			
    		}
    	}
    	elseif($process=="Batch")
    	{
    		$result = runQuery("SELECT * FROM processentry WHERE processname='$process'");
    		while($row=$result->fetch_assoc())
    		{
    			$cpid = $row['processid'];
    			$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$cpid' AND param='$GRADE_TITLE'");
    			
    			if($result2 = $result2->fetch_assoc())
    			{
    				
    				$remaining = getfinalbatchqty($row['processid']);

    				if($remaining>0)
    				{
    					array_push($allSamples[$process],[$row['processid'],$result2['value']]);
    				}
    			
    			}


    			
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

    				$total = getTotalQuantity($row['processid']);
    			
    				$used = getChildProcessQuantity($row['processid']);

    				if(($total-$used)>0)
    				{
    					array_push($allSamples[$process],[$row['processid'],$result2['value']]);
    				}
    				
    			}


    			
    		}
    	}
    }





   $allTest = [];


  $result = runQuery("SELECT * FROM sdispatch_batches WHERE cid='$cid'");

  while($row=$result->fetch_assoc())
  {
  	$allTest[$row['processid']] = [];
  	$allTest[$row['processid']]['grade'] = $row['grade'];
  	$allTest[$row['processid']]['test'] = [];

  	$cgrade = $row['grade'];
  	$cprocess = $row['processid'];

  	$result2 = runQuery("SELECT * FROM gradeproperties WHERE gradename='$cgrade' AND processname='Final Blend' ORDER BY ordering");

  	while($row2=$result2->fetch_assoc())
  	{
  		$allTest[$row['processid']]['test'][$row2['properties']]=[];
  		$allTest[$row['processid']]['test'][$row2['properties']]['min'] = $row2['min'];
  		$allTest[$row['processid']]['test'][$row2['properties']]['max'] = $row2['max'];
  		$allTest[$row['processid']]['test'][$row2['properties']]['value'] = "";

  		$cprop = $row2['properties'];
  		$result3 = runQuery("SELECT * FROM sdispatch_test WHERE processid='$cprocess' AND cid='$cid' AND property='$cprop'");

  		if($result3->num_rows==1)
  		{
  			$allTest[$row['processid']]['test'][$row2['properties']]['value'] = $result3->fetch_assoc()['value'];
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
<a class="nav-link" data-toggle="tab" href="#test-tabdiv" role="tab"><i class="fa fa-flask "></i>Test</a>
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
	$company = $result["company"];
	$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
	$result2 = $result2->fetch_assoc(); 
	$customerid = $dumC;


	

?>
	
	Customer Name: <?php echo $result2["value"] ?> <br>
	Customer Id: <?php echo $dumC ?> 		<br>
	Company: <?php echo $company ?> 		<br>


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
				<th>Prodcode</th>
				<th>Notes</th>
				<th></th>
			</tr>
		</thead>

		<tbody id="sample-tbody">

			
		</tbody>
			
			
	</table>

	

</div>


<script type="text/javascript">


	function loadOptions(selected="")
	{
		out = "";


		<?php 

			$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");

			$dumGrades = [];

			while($row=$result->fetch_assoc())
			{
				array_push($dumGrades,$row['gradename']);
			}

		?>


		allOptions = <?php echo json_encode($dumGrades) ?>;
		

		 var opt = document.createElement('option');
		    opt.value = "";
		    opt.innerHTML = "Choose grade";
		    opt.disabled = true;
		   
		    out += opt.outerHTML;


		for (var i = 0; i<allOptions.length; i++){
		    var opt = document.createElement('option');
		    opt.value = allOptions[i];
		    opt.innerHTML = allOptions[i];

		    if(selected==allOptions[i])
		    {
		    	opt.setAttribute('selected', 'selected');
		    	

		    }
		    out += opt.outerHTML;
		}

		
		return out;
	}
	
	function addtolist()
	{
		var select =  document.getElementById('sample-select');
		var id = select.value
		var qty = document.getElementById('sample-qty').value

		if(id=="")
		{
			Swal.fire({
				icon: 'error',
				title: 'Choose a sample',
				confirmButtonText: 'Yes',
				cancelButtonText: 'No',
				showCancelButton: false,
			})
			return;
		}

		if(qty=="")
		{
			Swal.fire({
				icon: 'error',
				title: 'Enter a quantity',
				confirmButtonText: 'Yes',
				cancelButtonText: 'No',
				showCancelButton: false,
			})
			return;
		}

		var process = select.options[select.selectedIndex].getAttribute('data-process')
		var grade = select.options[select.selectedIndex].getAttribute('data-grade')
		

		tbody = document.getElementById('sample-tbody');

		var tr =  document.createElement('tr');

		tr.innerHTML = "<td>"+id+"</td>" + "<td>"+process+"</td>" + "<td><select class=\"form-control\" required name=\"grade[]\">"+loadOptions()+"</select></td>" + "<td>"+qty+"</td><td><input type=\"text\" class=\"form-control\" name=\"prodcode[]\" value=''></td><td><input type=\"text\" class=\"form-control\" name=\"notes[]\"></td>" + "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button><button type=\"button\" class=\"btn btn-primary\" onclick=\"\"><i class=\"fa fa-print\"></i>COA</button></td><input type='hidden' name=\"qty[]\" value='"+qty+"'><input type='hidden' name=\"process[]\" value='"+process+"'><input type='hidden' name=\"processid[]\" value='"+id+"'></td>";

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


	function addtolistmanual(id,qty,process,grade,notes,prodcode)
	{
		
		tbody = document.getElementById('sample-tbody');

		var tr =  document.createElement('tr');

		tr.innerHTML = "<td>"+id+"</td>" + "<td>"+process+"</td>" + "<td><select class=\"form-control\" required name=\"grade[]\">"+loadOptions(grade)+"</select></td>" + "<td>"+qty+"</td><td><input type=\"text\" class=\"form-control\" name=\"prodcode[]\" value=\""+prodcode+"\"></td><td><input type=\"text\" class=\"form-control\" name=\"notes[]\" value=\""+notes+"\"></td>" + "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button> <button type=\"button\" class=\"btn btn-danger pull-right\" onclick=\"window.open('/user/dispatch/generatesamplecoa.php?id="+id+"&cid=<?php echo $cid; ?>', '_blank')\"><i class=\"fa fa-print\"></i>COA</button></td><input type='hidden' name=\"qty[]\" value='"+qty+"'><input type='hidden' name=\"process[]\" value='"+process+"'><input type='hidden' name=\"processid[]\" value='"+id+"'></td>";



		tbody.appendChild(tr);

		

	}

</script>

<?php
	
	$result2 = runQuery("SELECT * FROM sdispatch_batches WHERE cid='$cid'");
	while($row=$result2->fetch_assoc())
	{

		$dumid = "SAMPLE/".$cid;
		$dumpid = $row['processid'];
		$result3 = runQuery("SELECT * FROM coa_notes WHERE cid='$dumid' AND batch='$dumpid'");

		$dnotes = "";

		if($result3->num_rows==1)
		{
			$dnotes = $result3->fetch_assoc()['note'];
		}
?>
<script type="text/javascript">
	addtolistmanual('<?php echo $row['processid'] ?>','<?php echo $row['quantity'] ?>','<?php echo $row['process'] ?>','<?php echo $row['grade'] ?>','<?php echo $dnotes; ?>','<?php echo $row['prodcode']?>')

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





<div class="tab-pane" id="test-tabdiv" role="tabpanel" >

<form method="POST">
	<input type="hidden" name="cid" value="<?php echo $cid; ?>">
	<input type="hidden" name="currtab" value="test-tabdiv">

<?php 
	
	foreach ($allTest as $dprocessid => $dvals) {
		
	
?>


	<table id="" class="table table-bordered">
		<thead>
			<tr>
				<th colspan="2"><big>ID: <?php echo $dprocessid; ?></big></th>

				<th colspan="3"><big>Grade: <?php echo $dvals['grade']; ?></big></th>
			</tr>

			<tr>
				<th>Sl.No</th>
				<th>Property</th>
				<th>Min</th>
				<th>Max</th>
				<th>Value</th>
			</tr>
		</thead>

		<tbody>

			<?php 
				$k=1;
				foreach ($dvals['test'] as $dtest => $dtestval) {
				

			?>
			<tr>
				<td><?php echo $k; ?></td>
				<td><?php echo $dtest; ?></td>
				<td><?php echo $dtestval['min']; ?></td>
				<td><?php echo $dtestval['max']; ?></td>
				<td>
					
					<input class="form-control" type="number" step="0.001" name="testval[<?php echo $dprocessid; ?>][<?php echo $dtest; ?>]" value="<?php echo $dtestval['value']; ?>">
				</td>
			</tr>

			<?php 
				$k++;
				}
			?>
		</tbody>

	</table>

	<br>
	
	<hr>
	<br>

<?php 
}
?>
<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit"  name="confirmtest" class="btn btn-primary pull-right"><i class="fa fa-check"></i>Confirm</button>
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




