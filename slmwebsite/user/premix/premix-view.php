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
        "Page Title" => "View all Premix Batches | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "premix-view",
        "MainMenu"	 => "premix_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

	 $LIMIT = 20;

	if(isset($_GET['limit']))
	{
		$LIMIT =$_GET['limit'];
	}

    if(isset($_POST['deleteProcess']))
    {
    	$externalid = $_POST['externalid'];

    	runQuery("DELETE FROM premix_prodcode WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_coa_approval WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_batch_notes WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_batch_testparams WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_batch_test WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_batch_params WHERE premixid='$externalid'");
    	runQuery("DELETE FROM premix_batch WHERE premixid='$externalid'");
    }


    
    

    $deletePermission = false;
    
	if($myrole =='ADMIN')
	{
		
			$deletePermission = true;
		

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
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h3>Recent Premixes</h3>
					<span>Click on Edit button to view or edit individual Premix Blend</span>
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
<div class="col-lg-6">



<div class="card">
	<div class="card-header">
		<h5>Open by Premix ID</h5>
		<i class="fa fa-search"></i>
	</div>

	<div class="card-block">


<div class="form-group row">
			<label class="col-sm-2 col-form-label">Premix ID</label>
			<div class="col-sm-10">
			<div class="input-group input-group-button">
				<input required id="data_externalid" name="externalid" type="text" class="form-control form-control-uppercase" placeholder="">
				<div class="input-group-append">
				<button class="btn btn-success" type="button" onclick="getexternalid(this)"><i class="feather icon-arrow-up-right"></i>Open</button>
				</div>
			</div>
			
			</div>

		</div>





</div>
</div>

</div>

<div class="col-lg-6">
	<img src="images/premix.png">
</div>


<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">


<table class="table table-striped table-bordered table-xs">
	<thead style="text-align:center;font-size:13px;">
		<tr>
		<th>#</th>
		<th>Blend ID</th>
		<th>Entry Time</th>
		<th>Edit<br>Premix</th>
		<?php 
			if($deletePermission)
			{
				echo "<th>Remove<br>Premix</th>";
			}
		?>
		</tr>
	</thead>
	<tbody>

		<?php
				$result = runQuery("SELECT * FROM premix_batch ORDER BY entrydate DESC LIMIT $LIMIT");
				if($result->num_rows>0)
				{
					$k=0;
					while($row=$result->fetch_assoc())
					{

						$dumFlag = true;
						$dumid = $row["premixid"];

						$result2 = runQuery("SELECT * FROM loadingadvice_batches WHERE batch='$dumid'");

						if($result2->num_rows!=0)
						{
							$dumFlag = false;
						}

						$result2 = runQuery("SELECT * FROM dispatch_invoices WHERE batch='$dumid'");

						if($result2->num_rows!=0)
						{
							$dumFlag = false;
						}
		?>
	<tr>
		<th scope="row"><?php echo ++$k; ?></th>
		<td><?php echo $row["premixid"]; ?></td>

		<td><?php echo Date('d-M-Y - h:i A',strtotime($row["entrydate"])); ?></td>
		<td><form method="POST" action="premix-edit.php"><input type="hidden" name="premixid" value="<?php echo $row["premixid"]; ?>"><button class="btn btn-info" type="submit"><i class="feather icon-edit-2"></i>Edit</button></form></td>
		<?php


			if($deletePermission && $dumFlag)
			{
				echo "<td><button class=\"btn btn-danger\" name=\"deleteProcess\" onclick=\"removeProcess('".$row["premixid"]."')\" type=\"button\"><i class=\"feather icon-trash\"></i>Remove</button></td>";
			}
		


		?>

	</tr>
	
	<?php
		}}
	?>

	</tbody>
	</table>

	<form method="GET">
	<div class="row">
		<div class="col-sm-3">
		<input type="number" class="form-control" step="1" min="1" name="limit" placeholder="Show last results" value="<?php echo $LIMIT ?>" >
		</div>
		<div class="col-sm-3">
			<button class="btn btn-primary">Show Last</button>
		</div>

	</div>
</form>


	<script type="text/javascript">


		
	$(document).ready( function () {
    $('.table').DataTable(

    {
    	 "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false
            },

            
        ]
    }


    	);
} );
	
	function getexternalid()
	{
			var premixid = document.getElementById("data_externalid").value;



			var postData = new FormData();
       
        postData.append("action","checkPremixId");
        postData.append("premixid",premixid);


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response =="yes")
            {
                document.getElementById("redirectformid").value = premixid;
            	document.getElementById("redirectform").submit();
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

</script>



</div>
</div>



<form method="POST" id="deleteprocessform">
	<input type="hidden" name="externalid" id="deleteprocessid">
	<input type="hidden" name="deleteProcess" >

</form>

<form method="POST" id="redirectform" action="premix-edit.php">
	<input type="hidden" name="premixid" id="redirectformid">


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





function removeProcess(externalid)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Premix',
		  html: 'Are you sure you want to delete stock '+externalid,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("deleteprocessid").value = externalid;
			  		document.getElementById("deleteprocessform").submit();

				}
			})
}





</script>