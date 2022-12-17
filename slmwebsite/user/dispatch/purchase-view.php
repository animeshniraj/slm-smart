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


	if(isset($_POST['modify_order']))
	{
		$modify_type = $_POST['modify_order'];
		$p_id = $_POST['poid'];

		if($modify_type=="open_order")
		{
			$result = runQuery("SELECT * FROM loading_advice WHERE poid = '$p_id'");
			if($result->num_rows==0)
			{
				$new_status = "UNFULFILLED";
			}
			else
			{
				$new_status = "LOADING ADVICE";
			}

			$result = runQuery("UPDATE purchase_order SET status='$new_status' WHERE orderid = '$p_id'");

		}
		elseif($modify_type=="close_order")
		{
			$result = runQuery("UPDATE purchase_order SET status='FULFILLED' WHERE orderid = '$p_id'");
		}



	}



	$data = [];
	$json_data = [];

	$open_flag = false;
	if(isset($_POST['getdata']))
	{

		$isopen = "open" == $_POST['filter'] ;
		$open_flag = $isopen;
		$data = [];
		$json_data = [];
		if($isopen)
		{
			$result = runQuery("SELECT * FROM purchase_order WHERE status<>'FULFILLED'");

			while($row = $result->fetch_assoc())
			{
				$poid = $row['orderid'];
				$po_date = Date('d-M-Y',strtotime($row['entrydate']));
				$customer_id = $row['customer'];
				$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$customer_id' AND param='Name'");
				$customer_name = $result2->fetch_assoc()["value"];

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

					$dum_json_data = [
						"poid" => $poid,
						"customer_name" => $customer_name,
						"po_date"	=> $po_date,
						"grade"		=> $grade,
						"po_qty"	=> $po_qty,
						"dis_qty"	=> $dis_qty,
						"pending_qty" => $pending_qty,
						"package"	=> $package,
						"option"	=> "Delete"
					];

					array_push($json_data,$dum_json_data);

					array_push($data,[$poid,$customer_name,$po_date,$grade,$po_qty,$dis_qty,$pending_qty,$package]);
					



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
				$customer_id = $row['customer'];
				$result2 = runQuery("SELECT * FROM external_param WHERE externalid='$customer_id' AND param='Name'");
				$customer_name = $result2->fetch_assoc()["value"];

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

					$dum_json_data = [
						"poid" => $poid,
						"customer_name" => $customer_name,
						"po_date"	=> $po_date,
						"grade"		=> $grade,
						"po_qty"	=> $po_qty,
						"dis_qty"	=> $dis_qty,
						"pending_qty" => $pending_qty,
						"package"	=> $package,
						"option"	=> "Delete"
					];

					array_push($json_data,$dum_json_data);

					array_push($data,[$poid,$customer_name,$po_date,$grade,$po_qty,$dis_qty,$pending_qty,$package]);
					



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

<style>
.btn{margin-left: 5px!important;}</style>

<link rel="stylesheet" type="text/css" href="/pages/css/aggrid/ag-grid.css">
<link rel="stylesheet" type="text/css" href="/pages/css/aggrid/ag-theme-alpine.css">
<script type="text/javascript" src="/pages/js/aggrid/ag-grid-community.min.js" ></script>




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


			<div class="form-group row" style="display:flex; justify-content: center;">
		
					<div class="col-sm-3">
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

<div class="table-responsive">
	<div id="result-table"></div>

</div>


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

	
	const swalWithBootstrapButtons  = Swal.mixin({
	customClass: {
		confirmButton: 'btn btn-success',
		cancelButton: 'btn btn-danger'
	},
	buttonsStyling: false
	})



	swalWithBootstrapButtons.fire({
		  icon: 'warning',
		  title: 'Delete Purchase Order',
		  html: 'Are you sure you want to delete it. <br> Purchase Order No.: '+externalid + '.<br>Note: All Data related to this purchase order will be deleted.',
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

<?php 
if (count($json_data)>0)
{
?>

setTable()

<?php 
}
?>

function setTable()
{
	rowData = <?php echo json_encode($json_data); ?>;
	
	const columnDefs = [
		{ field: "poid", headerName: "PO No.", floatingFilter: true, pinned: "left" },
		{ field: "customer_name", headerName: "Customer Name",filter:"agTextColumnFilter" , floatingFilter: true  },
		{ field: "po_date", headerName: "PO Date",filter:"agDateColumnFilter" , floatingFilter: true  },
		{ field: "grade", headerName: "Grade",filter:"agTextColumnFilter" , floatingFilter: true  },
		{ field: "po_qty", headerName: "PO Qty",filter:"agNumberColumnFilter" , floatingFilter: true  },
		{ field: "dis_qty", headerName: "Dispatch Qty",filter:"agNumberColumnFilter" , floatingFilter: true  },
		{ field: "pending_qty", headerName: "Pending Qty",filter:"agNumberColumnFilter" , floatingFilter: true  },
		{ field: "package", headerName: "Package",filter:"agTextColumnFilter" , floatingFilter: true  },

		<?php
		if($open_flag)
		{
		?>

		{ field: "close", headerName: "Close Order",
				cellRenderer: function(params) {
				let keyData = params.data.option;
				let newLink = 
				`<a href='#'><i class='fa fa-lock'></i> Close Order</a>`;
				return newLink;
			}
		},

		<?php
		}
		else{
		?>
		{ field: "open", headerName: "Open Order",
				cellRenderer: function(params) {
				let keyData = params.data.option;
				let newLink = 
				`<a href='#'><i class='fa fa-unlock'></i> Open Order</a>`;
				return newLink;
			}
		},

		<?php
		}
		?>
		{ field: "delete", headerName: "Delete",
				cellRenderer: function(params) {
				let keyData = params.data.option;
				let newLink = 
				`<a href='#'><i class='fa fa-times'></i> Delete</a>`;
				return newLink;
			}
		},
	];

	gridOptions = {
		columnDefs: columnDefs,
		rowData	: rowData,
		rowData: rowData,
		pagination: true,
		paginationPageSize: 100,
		defaultColDef: {
			sortable: true
		},
		onCellClicked: (event) => send_post(event.value,event),

	}


		const resultdiv = document.querySelector('#result-table');
		gridDiv = document.createElement('div');
		gridDiv.id = 'agGridElement';
		//gridDiv.classList.add('ag-theme-alphine')
		gridDiv.classList.add('ag-theme-alpine')
		gridDiv.style.height = "500px";

		resultdiv.innerHTML = "";
		resultdiv.appendChild(gridDiv);





		new agGrid.Grid(gridDiv, gridOptions);


}

function close_order(externalid)
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
		  title: 'Close Purchase Order',
		  html: 'Are you sure you want to close the purchase order. <br> Purchase Order No.: '+externalid ,
		  confirmButtonText: '<i class="fa fa-lock"></i> Yes',
		  cancelButtonText: '<i class="fa fa-window-close"></i> No',
		  showCancelButton: true,
		  reverseButtons: true

		}).then((result) => {
			  if (result.isConfirmed) {
			    		
				var form  = document.createElement("form")
					form.method = "POST"
					
					var input = document.createElement("input")
					input.type = "hidden"
					input.name = "modify_order"
					input.value = "close_order"
					form.appendChild(input)

					var input = document.createElement("input")
					input.type = "hidden"
					input.name = "poid"
					input.value = externalid
					form.appendChild(input)

					document.body.appendChild(form)
					form.submit()

				}
			})
}

function open_order(externalid)
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
		  title: 'Open Purchase Order',
		  html: 'Are you sure you want to open the purchase order. <br> Purchase Order No.: '+externalid ,
		  confirmButtonText: '<i class="fa fa-unlock"></i> Yes',
		  cancelButtonText: '<i class="fa fa-window-close"></i> No',
		  showCancelButton: true,
		  reverseButtons: true

		}).then((result) => {
			  if (result.isConfirmed) {
			    		
					var form  = document.createElement("form")
					form.method = "POST"
					
					var input = document.createElement("input")
					input.type = "hidden"
					input.name = "modify_order"
					input.value = "open_order"
					form.appendChild(input)

					var input = document.createElement("input")
					input.type = "hidden"
					input.name = "poid"
					input.value = externalid
					form.appendChild(input)

					document.body.appendChild(form)
					form.submit()

				}
			})
}




function send_post(poid,event)
{


	if (event.column.colId == "delete") {
		removeProcess(event.data.poid)
		return;
	}

	if (event.column.colId == "close") {
		close_order(event.data.poid)
		return;
	}

	if (event.column.colId == "open") {
		open_order(event.data.poid)
		return;
	}
	


	if (event.column.colId != "poid") {
		return;
	}

	var form = document.createElement("form")
	form.target = "__blank"
	form.method = "POST"
	form.action = "purchase-edit.php"

	var input1 = document.createElement("input")
	input1.type = "hidden"
	input1.name = "orderid"
	input1.value = poid

	form.appendChild(input1);

	document.body.appendChild(form)
	form.submit()
	form.remove()
}



</script>

