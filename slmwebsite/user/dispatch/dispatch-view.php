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
        "Page Title" => "View all Dispatches | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "dispatch-view",
        "MainMenu"	 => "dispatch_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();



    if(isset($_POST['deleteProcess']))
    {
    	$externalid = $_POST['externalid'];
    	

    	runQuery("UPDATE loading_advice SET status='UNFULFILLED' WHERE laid in ( SELECT laid from dispatch WHERE cid = '$externalid')");



    	runQuery("DELETE FROM dispatch_invoices WHERE cid='$externalid'");
    	runQuery("DELETE FROM dispatch_notes WHERE cid='$externalid'");
    	runQuery("DELETE FROM dispatch WHERE cid='$externalid'");
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

<style>
.btn{margin-left: 5px!important;}</style>



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h3>View all Dispatches</h3>
					<span>Click on Edit button to view or edit the Dispatch</span>
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
	<h5 style="font-weight:bold;">Search by Consignment ID  <i class="fa fa-search"></i></h5>
</div>
<div class="card-block">


<div class="form-group row">
			<label class="col-sm-3 col-form-label"> Consignment ID</label>
			<div class="col-sm-8">
			<div class="input-group input-group-button">
				<input required id="data_externalid" name="externalid" type="text" class="form-control form-control-uppercase" placeholder="">
				<div class="input-group-append">
				<button class="btn btn-primary" type="button" onclick="getexternalid(this)"><i class="feather icon-arrow-up-right"></i> Open</button>
				</div>
			</div>
			
			</div>

		</div>





</div>
</div>
</div>

<div class="col-lg-6">
	<img src="images/dispatch.png">
</div>



<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">


	<table class="table table-bordered table-striped table-xs">
	<thead>
		<tr style="text-align:center;">
		<th>#</th>
		<th>Consignment ID</th>
		<th>Loading Advice</th>
		<th>Customer</th>
		<th>Entry Date</th>
		<th>Edit</th>
		<?php 
			if($deletePermission)
			{
				echo "<th>Remove</th>";
			}
		?>
		</tr>
	</thead>
	<tbody>

		<?php
				$result = runQuery("SELECT * FROM dispatch ORDER BY entrydate DESC");
				if($result->num_rows>0)
				{
					$k=0;
					while($row=$result->fetch_assoc())
					{
						$dumC = $row["customer"];
						$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
						$result2 = $result2->fetch_assoc(); 
		?>
	<tr>
		<th scope="row"><?php echo ++$k; ?></th>
		<td><?php echo $row["cid"]; ?></td>
		<td><?php echo $row["laid"]; ?></td>

		<td><?php echo $result2["value"]."(".$row["customer"].")"; ?></td>

		<td><?php echo Date('d-M-Y',strtotime($row["entrydate"])); ?></td>
		<td><form method="POST" action="dispatch-edit.php"><input type="hidden" name="cid" value="<?php echo $row["cid"]; ?>"><button class="btn btn-info" type="submit"><i class="feather icon-edit-2"></i>Edit</button></form></td>
		<?php


			if($deletePermission)
			{
				echo "<td><button class=\"btn btn-danger\" name=\"deleteProcess\" onclick=\"removeProcess('".$row["cid"]."')\" type=\"button\"><i class=\"feather icon-trash\"></i>Remove</button></td>";
			}
		


		?>

	</tr>
	
	<?php
		}}
	?>

	</tbody>
	</table>


	<script type="text/javascript">
	
	function getexternalid()
	{
			var orderid = document.getElementById("data_externalid").value;



			var postData = new FormData();
       
        postData.append("action","checkorderid");
        postData.append("orderid",orderid);


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
           console.log(this.responseText)
            var data = JSON.parse(this.responseText);

            
            if(data.response =="yes")
            {
                document.getElementById("redirectformid").value = orderid;
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
        xmlhttp.open("POST", "/query/dispatch.php", true);
        xmlhttp.send(postData);
	}

</script>



</div>
</div>



<form method="POST" id="deleteprocessform">
	<input type="hidden" name="externalid" id="deleteprocessid">
	<input type="hidden" name="deleteProcess" >

</form>

<form method="POST" id="redirectform" action="dispatch-edit.php">
	<input type="hidden" name="orderid" id="redirectformid">


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
	const swalWithBootstrapButtons  = Swal.mixin({
	customClass: {
		confirmButton: 'btn btn-success',
		cancelButton: 'btn btn-danger'
	},
	buttonsStyling: false
	})


	swalWithBootstrapButtons.fire({
		  icon: 'warning',
		  title: 'You are deleting a Dispatch',
		  html: 'Are you sure you want to delete it. This action is irreversible. <br> Dispatch ID.:' +externalid,
		  confirmButtonText: '<i class="fa fa-trash"></i> Yes',
		  cancelButtonText: '<i class="fa fa-window-close"></i> No',
		  showCancelButton: true,
		  reverseButtons: true
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("deleteprocessid").value = externalid;
			  		document.getElementById("deleteprocessform").submit();

				}
			})
}





</script>