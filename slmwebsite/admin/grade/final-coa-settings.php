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
        "Menu"		 => "processgrade-finalblend",
        "MainMenu"	 => "processgrade_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5>COA Settings</h5>
					<span>Edit COA Grades settings</span>
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
<h5>All Grade</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	
	<div class="dt-responsive table-responsive">
<div id="user-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

	<div class="row">
		<div class="col-xs-12 col-sm-12">

<table id="user-table" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="user-table_info">
<thead>
 <tr role="row">
	<th rowspan="1" colspan="1" >Sl No.</th>
	<th rowspan="1" colspan="1" >Grade Name</th>
	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");
	$k=1;
	if($result->num_rows>0)
	{
		while($row=$result->fetch_assoc())
		{
			
			echo "<tr role=\"row\" >";

			echo "<td>".$k++."</td>";

			echo "<td>".$row["gradename"]."\t</td>";
			
			
			echo "<td><form action=\"final-coa-settings-edit.php\" method=\"POST\" style=\"display:inline-block\"><input type=\"hidden\" name=\"gradename\" value=\"".$row["gradename"]."\"><input type=\"hidden\" name=\"editgrade\" value=\"\"><a href=\"#\" onclick=\"this.parentNode.submit();\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit Grade\"><i class=\"fa fa-edit\" style=\"display:inline-block\"></i></a></form></td>";
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
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();


  	document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

		document.getElementById("finalblend-coa").classList.add("active");

  })
</script>


