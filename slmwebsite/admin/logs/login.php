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

	isAuthenticated($session,'admin_module');

	

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "logs-login",
        "MainMenu"	 => "logs_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="fa fa-book bg-c-blue"></i>
				<div class="d-inline">
					<h5>Logs</h5>
					<span>View All Logs</span>
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
<h5>Download Logs</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">
	<form method="POST" target="_blank" action="/admin/logs/downloadloginlog.php">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Date Range</label>
			<div class="col-sm-4">
			<input type="text" required class="form-control" name="daterange" id="daterange" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

	
		<div class="form-group row">
			<label class="col-sm-2"></label>
			<div class="col-sm-10">
			<button type="submit" id="downloadBtn" class="btn btn-primary m-b-0"><i class="fa fa-download"></i>Download Log</button>
			</div>
		</div>
	</form>

</div>
</div>




<div class="card">
<div class="card-header">
<h5>Logs</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<div class="dt-responsive table-responsive">
<div id="log-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

	<div class="row">
		<div class="col-xs-12 col-sm-12">

<table id="log-table" class="table table-striped table-bordered nowrap dataTable" role="grid">
<thead>
 <tr role="row">
	<th rowspan="1" colspan="1"  style="width: 84.578125px;">Sl No.</th>
	<th rowspan="1" colspan="1"  style="width: 356.875px;">Login Time</th>
	<th rowspan="1" colspan="1"  style="width: 176.703125px;">User</th>


</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM loginlog ORDER BY currtime DESC LIMIT 200");
	$k=1;
	if($result->num_rows>0)
	{
		while($row=$result->fetch_assoc())
		{
			if($k%2==0)
			{
				$type = "even";
			}
			else
			{
				$type = "odd";
			}
			echo "<tr role=\"row\" class=\"".$type."\">";

			echo "<td>".$k++."</td>";
			echo "<td>".Date('d-M-Y H:i',strtotime($row["currtime"]))."</td>";

			if(getFullName($row["userid"]))
			{
				echo "<td>".getFullName($row["userid"])."</td>";
			}
			else
			{
				echo "<td></td>";

			}
			
			
			echo "</tr>";



		}
	}

?>





</tbody>

</table></div></div></div>
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









<?php
    
    include("../../pages/endbody.php");

?>
<script type="text/javascript">
	$(document).ready(function () {
$('#log-table').DataTable({
"order": [[ 0, "asc" ]],
"columnDefs": [
    { "orderable": false, "targets": 4 }
  ]
});
$('.dataTables_length').addClass('bs-select');


	

});
</script>


<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    
  }, function(start, end, label) {
    
  });
});
</script>

