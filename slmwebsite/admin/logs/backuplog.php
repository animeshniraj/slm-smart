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
        "Menu"		 => "logs-general",
        "MainMenu"	 => "logs_menu",

    ];

    $backupListJSON = file_get_contents($BACKUP_LOCATION ."backups.json");
    $backipList = json_decode($backupListJSON, true);
	krsort($backipList,SORT_REGULAR);

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
	<th rowspan="1" colspan="1"  >Sl No.</th>
	<th rowspan="1" colspan="1" >Backup Name</th>
	<th rowspan="1" colspan="1" >File Name</th>
	<th rowspan="1" colspan="1" >Created Date</th>
	<th rowspan="1" colspan="1" >Uploaded Date (AWS Time)</th>

</tr>
</thead>
<tbody>


<?php
$k=1;
foreach ($backipList as $name => $data) {
 ?>

 <tr>
 	<td><?php echo $k++;?></td>
 	<td><?php echo $data['name'];?></td>
 	<td><?php echo $data['filename'];?></td>
 	<td><?php echo $data['created_date'];?></td>
 	<td><?php echo $data['upload_date'];?></td>
 </tr>
 <?php
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

