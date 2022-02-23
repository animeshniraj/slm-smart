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
        "Page Title" => "View all Purchase Order | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "purchase-view",
        "MainMenu"	 => "dispatch_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


	$data = [];
	if(isset($_POST['getdata']))
	{

		$isopen = "open" == $_POST['filter'] ;
		
		$data = [];
		if($isopen)
		{
			$result = runQuery("SELECT * FROM purchase_order WHERE status<>'FULFILLED'");

			while($row = $result->fetch_assoc())
			{
				$poid = $row['orderid'];
				$po_date = Date('d-M-Y',strtotime($row['entrydate']));

				$result2 = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$poid' AND step='BATCH'");

				while($row2 = $result2->fetch_assoc())
				{
					$grade = $row2['param'];
					$po_qty = $row2['value'];
					$dis_qty = 0;
					$package = "-";


					$result3 = runQuery("SELECT SUM(quantity) as total FROM loadingadvice_batches WHERE laid in (SELECT laid FROM loading_advice WHERE poid ='$poid' AND status='FULFILLED') AND grade='$grade'")->fetch_assoc();

					if($result3['total'])
					{
						$dis_qty = $result3['total'];
					}

					$pending_qty = $po_qty - $dis_qty;

					array_push($data,[$poid,$po_date,$grade,$po_qty,$dis_qty,$pending_qty,$package]);
					



				}




			}
		}
		else
		{
			$daterange = $_POST['date'];
			$startdate = explode(" - ",$daterange)[0];
			$enddate = explode(" - ",$daterange)[1];


			$result = runQuery("SELECT * FROM purchase_order WHERE status='FULFILLED' AND entrydate>='$startdate' AND entrydate<='$enddate'");

			while($row = $result->fetch_assoc())
			{
				$poid = $row['orderid'];
				$po_date = $row['entrydate'];

				$result2 = runQuery("SELECT * FROM purchaseorder_params WHERE orderid='$poid' AND step='BATCH'");

				while($row2 = $result2->fetch_assoc())
				{
					$grade = $row2['param'];
					$po_qty = $row2['value'];
					$dis_qty = 0;
					$package = "-";


					$result3 = runQuery("SELECT SUM(quantity) as total FROM loadingadvice_batches WHERE laid in (SELECT laid FROM loading_advice WHERE poid ='$poid' AND status='FULFILLED') AND grade='$grade'")->fetch_assoc();

					if($result3['total'])
					{
						$dis_qty = $result3['total'];
					}

					$pending_qty = $po_qty - $dis_qty;

					array_push($data,[$poid,$po_date,$grade,$po_qty,$dis_qty,$pending_qty,$package]);
					



				}




			}
		}

	}

    if(isset($_POST['deleteProcess']))
    {
    	$externalid = $_POST['externalid'];
    	
    	runQuery("DELETE FROM purchaseorder_notes WHERE orderid='$externalid'");
    	runQuery("DELETE FROM purchaseorder_params WHERE orderid='$externalid'");
    	runQuery("DELETE FROM purchaseorder_tentative WHERE orderid='$externalid'");
    	runQuery("DELETE FROM purchase_order WHERE orderid='$externalid'");
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
					<h5>View all Purchase Order</h5>
					<span>Select order to edit</span>
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

	<form method="POST">
			<div class="form-group" style="display:flex; justify-content: center">
						<select required onchange="editoption(this);" class="form-control col-sm-3" name="filter" >
							<option selected disabled value=""> Choose an option</option>
							<option value="open">Open</option>
							<option value="closed">Fulfilled</option>
						</select>
			</div>


			<div class="form-group" style="display:flex; justify-content: center;">
						
						<input type="text" disabled required name="date" id="datein" class="form-control col-sm-4" style="display: inline; text-align: center;" placeholder="Date">

						
						
			</div>



					<script>

						function editoption(selectobj)
						{
							currval = selectobj.value;

							if(currval=="open")
							{
								document.getElementById('datein').disabled = true;
							}
							else
							{
								document.getElementById('datein').disabled = false;
							}
						}



					$(function() {
					  $('input[name="date"]').daterangepicker({
					    singleDatePicker: false,
					    timePicker: false,
					    
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
					

					</script>


			<div class="form-group row">
		
					<div class="col-sm-12">
					<button type="submit"  name ='getdata' id='getdatabtn' class="btn btn-primary btn-block"><i class="feather icon-plus"></i>Get Data</button>
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
		<th>PO No</th>
		<th>PO Date</th>
		<th>Grade</th>
		<th>PO Qty</th>
		<th>Dispatch Qty</th>
		<th>Pending Qty</th>
		<th>Package</th>
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

			foreach ($data as  $value) {
				


		?>

		<tr>
			<td><?php echo $value[0];?></td>
			<td><?php echo $value[1];?></td>
			<td><?php echo $value[2];?></td>
			<td><?php echo $value[3];?></td>
			<td><?php echo $value[4];?></td>
			<td><?php echo $value[5];?></td>
			<td><?php echo $value[6];?></td>
			<td><form method="POST" action="purchase-edit.php"><input type="hidden" name="orderid" value="<?php echo $value[0]; ?>"><button class="btn btn-primary" type="submit"><i class="feather icon-edit-2"></i>Edit</button></form></td>
			<?php


			if($deletePermission)
			{
				echo "<td><button class=\"btn btn-danger\" name=\"deleteProcess\" onclick=\"removeProcess('".$value[0]."')\" type=\"button\"><i class=\"feather icon-trash\"></i>Remove</button></td>";
			}
		


			?>
		</tr>

		<?php 



			}


		?>



	


	</tbody>
	</table>






</div>
</div>



<form method="POST" id="deleteprocessform">
	<input type="hidden" name="externalid" id="deleteprocessid">
	<input type="hidden" name="deleteProcess" >

</form>

<form method="POST" id="redirectform" action="purchase-edit.php">
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
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Purchase Order',
		  html: 'Are you sure you want to delete it. <br> Purchase Order No.: '+externalid + '.<br>Note: All Data related to this purchase order will be deleted.',
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