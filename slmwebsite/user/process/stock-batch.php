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
        "Page Title" => "View all stock | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "stock-process",
        "MainMenu"	 => "stock_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();

	$today_date =Date("d-m-Y 00:00",strtotime("now"));
    $some_months_ago =Date("d-m-Y 00:00",strtotime("-3 months"));


 


    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

<script type="text/javascript" src="/pages/js/jquery.min.js" ></script>
<script type="text/javascript" src="/pages/js/stock-process.js" ></script>
<script type="text/javascript" src="/pages/js/stock-process-table.js" ></script>
<script type="text/javascript" src="/pages/js/aggrid/ag-grid-enterprise.min.js" ></script>
<link rel="stylesheet" type="text/css" href="/pages/css/stock-process.css">


<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i id="titleicon" onmouseenter="titleicontoRefresh()" onmouseleave="titleicontonormal()" onclick="reloadCurrPage()" style="cursor: pointer;" class="fa fa-signal bg-c-blue"></i>
				<div class="d-inline">
					<h3>Stock Report</h3>
					<span>Choose Process and Grade first</span>
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
		<h5>Stock</h5>
		<i class="fa fa-search"></i>
	</div>
<div class="card-block">

<div class="table-responsive">
<table class="table">
	<tr">
		<th style="text-align:right;">Process</th>
		<th>
			<select class="form-control" id="processname" onchange="load_grades();">
				<option value="Melting">Melting</option>
				<option value="Raw Bag">Raw Bag</option>
				<option value="Raw Blend">Raw Blend</option>
				<option value="Annealing">Annealing</option>
				<option value="Semi Finished">Semi Finished</option>
				<option value="Final Blend">Final Blend</option>
			</select>
		</th>
		<th style="text-align:right; ">Start Date</th>
		<th>
			<input required name="starttime" id='starttime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $some_months_ago; ?>">
		</th>
		<th style="text-align:right;">End Date</th>
		<th style="">
			<input required name="stoptime" id='stoptime' type="text" class="form-control form-control-uppercase" placeholder="" value="<?php echo $today_date; ?>">
		</th>
	</tr>

	<tr>
		<th style="text-align:right;">Grade</th>
		<th style="" colspan="4">
			<select onchange="load_properties()" style="max-width: 200px;" class="js-example-basic-multiple form-control" multiple="multiple"  id="gradename">
				
			</select>
		</th>
		<th style="" colspan="1">
			<button class="btn btn-primary" onclick="select_all_grade()">Select All</button>
			<p style="text-align:right;font-size:12px;color:#ccc;display:inline;">*To select all grades</a>
		</th>

		
	</tr>
	<tr style="">
		<th colspan="1">Properties
		</td>
		<th colspan="4">
			<div id="properties-selected-div">
				
			</div>

		</td>

		<th colspan="1">
			<div id="properties-selected-div">
				<input type="checkbox" id="show_only_balance"> Balance Report
			</div>

		</td>
	</tr>
</table>
</div>

<div class="row">
<div class="col-lg-12">
	
		<button class="btn btn-primary" onclick="showPropertyModal()"><i class="fa fa-edit"> Edit Property</i></button>

		<button class="btn btn-primary" onclick="showAdditionalFilterModal()"><i class="fa fa-edit"> Edit Additional Filters</i></button>

		<button class="btn btn-primary pull-right" onclick="load_data()"><i class="fa fa-search"> Load Data</i></button>

</div>
</div>


</div>
</div>




<div class="card">
	<div class="card-header">
		<h5>Results</h5>
		<i class="fa fa-signal"></i>

		<button id="download_csv_btn" class="btn btn-primary pull-right" style="display:none" onclick="data_model.gridOptions.api.exportDataAsCsv()"><i class="fa fa-download"> Download as CSV</i></button>
		
		<div class="pull-right col-sm-1" style="display:none" id="loading_arrow">

			<div class="loading_arrow">
				  <span>↓</span>
				  <span style="--delay: 0.1s">↓</span>
				  <span style="--delay: 0.2s">↓</span>
				  <span style="--delay: 0.3s">↓</span>
				  <span style="--delay: 0.4s">↓</span>
				  <span style="--delay: 0.5s">↓</span>
			</div>
		</div>

	</div>
<div class="card-block" id="resultdiv" style="min-height: 200px;">

	

<div>




</div>


</div>
</div>







</div>
</div>
</div>

</div>
</div>
</div>
</div>






<div class="modal fade" id="properties-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">All Available Properties</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">



<table class="table table-striped table-bordered">
<thead>
<tr>

<th>Property</th>
<th><input type="checkbox" onchange="select_all_property(this)"> Select All</th>

</tr>
</thead>


<tbody id="properties-modal-tbody">

	
</tbody>
</table>


</div>
<div class="modal-footer">
<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>

</div>
</div>
</div>
</div>





<div class="modal fade" id="additional-filter-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Additional Filters</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">



<table class="table table-striped table-bordered">
<thead>
<tr>

<th>Filter</th>
<th>Options</th>

</tr>
</thead>


<tbody id="additional-filter-modal-tbody">

	
</tbody>
</table>


</div>
<div class="modal-footer">
<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>

</div>
</div>
</div>
</div>

<script type="text/javascript">
	let selected_properties = {}
	let additional_filters = [];
	let additional_properties = [];
	let basic_properties = [];
	let search_query_payload = []
	let selected_filters = {};
	let uid_map = [];
	let data_model = null;





</script>


<?php
    
    include("../../pages/endbody.php");

?>

