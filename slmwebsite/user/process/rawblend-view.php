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

	

    $PAGE = [
        "Page Title" => "SLM | View all Raw Blends",
        "Home Link"  => "/user/",
        "Menu"		 => "process-rawblend-view",
        "MainMenu"	 => "process_rawblend",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Raw Blend";

    if(isset($_POST['deleteProcess']))
    {
    	$processid = $_POST['processid'];
    	runQuery("call delete_process('$processid')");

    	addprocesslog('PROCESS',$processid,$session->user->getUserid(),'Raw Blend Process ('.$processid.') deleted');
    }


    
    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='DELETION' AND role ='$myrole'");

    $deletePermission = false;
    
	if($result->num_rows>0)
	{
		$dumPermission = $result->fetch_assoc()["permission"];
		if($dumPermission=="ALLOW")
		{
			$deletePermission = true;
		}

	}
 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		inobj.value = val;
	}


</script>



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-shopping-bag bg-c-blue"></i>
				<div class="d-inline">
					<h5>View all Raw Blends</h5>
					<span>Select Raw Blend to view or edit parameters</span>
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

<form id="searchbyid" method="POST" action="rawblend-edit.php">
<div class="form-group row">
			<label class="col-sm-2 col-form-label">Raw Blend ID</label>
			<div class="col-sm-10">
			<div class="input-group input-group-button">
				<input required id="processid" name="processid" type="text" class="form-control form-control-uppercase" placeholder="">
				<div class="input-group-append">
				<button class="btn btn-primary" type="button" onclick="getHeatid(this)"><i class="feather icon-arrow-up-right"></i>Open</button>
				</div>
			</div>
			
			</div>

		</div>

</form>



</div>
</div>


<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">


	<table class="table">
	<thead>
		<tr>
		<th>#</th>
		<th>Raw Blend ID</th>
		<th>Entry Time</th>
		<th></th>
		<?php 
			if($deletePermission)
			{
				echo "<th></th>";
			}
		?>
		</tr>
	</thead>
	<tbody>

		<?php
				$result = runQuery("SELECT * FROM processentry WHERE processentry.processname = '$processname' ORDER BY entrytime DESC LIMIT 10");
				if($result->num_rows>0)
				{
					$k=0;
					while($row=$result->fetch_assoc())
					{
						$dumid = $row["processid"];
						$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$dumid' AND param='Pre-Processed'");
						if($result2->fetch_assoc()["value"]=="Atomized")
						{
							$urlre = "rawblend-edit.php";
						}
						else
						{
							$urlre = "rawblend-edit-pre.php";
						}
		?>
	<tr>	
		<th scope="row"><?php echo ++$k; ?></th>
		<td><?php echo $row["processid"]; ?></td>
		
		<td><?php echo Date('Y-M-d H:i',strtotime($row["entrytime"])); ?></td>

		<td><form method="POST" action="<?php echo $urlre; ?>"><input type="hidden" name="processid" value="<?php echo $row["processid"]; ?>"><button class="btn btn-primary" type="submit"><i class="feather icon-edit-2"></i>Edit</button></form></td>
		<?php


			if($deletePermission)
			{
				echo "<td><button class=\"btn btn-danger\" name=\"deleteProcess\" onclick=\"removeProcess('".$row["processid"]."')\" type=\"button\"><i class=\"feather icon-trash\"></i>Remove</button></td>";
			}
		


		?>

	</tr>
	
	<?php
		}}
	?>

	</tbody>
	</table>



</div>
</div>



<form method="POST" id="deleteprocessform">
	<input type="hidden" name="processid" id="deleteprocessid">
	<input type="hidden" name="deleteProcess" >

</form>




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





$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	






  // Creation

  	

  		

  	

});


function getHeatid(inObj)
{

	var processid = document.getElementById("processid").value;
	var postData = new FormData();
       
        postData.append("action","getprocessid");
        postData.append("processid",processid);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response)
            {
                
            	document.getElementById("searchbyid").submit();
            }
            else
            {
               Swal.fire({
					icon: "error",
					title: "Raw Blend ID not Found",
					showConfirmButton: true,
				  	showCancelButton: false,
				  	confirmButtonText: 'OK',
				  	
				})
            }
            

        
        
          }
        };
        xmlhttp.open("POST", "/query/process.php", true);
        xmlhttp.send(postData);
}


function removeProcess(processid)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Process',
		  html: 'Are you sure you want to delete process '+processid,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("deleteprocessid").value = processid;
			  		document.getElementById("deleteprocessform").submit();

				}
			})
}





</script>