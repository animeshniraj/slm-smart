<?php
    
	require_once('../../../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'admin_module');

	$external_type = "Customer";


	if(isset($_POST['deleteext']))
	{
		$dumid = $_POST["externalid"];
		runQuery("CALL delete_supplier('$dumid')");
	}

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "external-customer",
        "MainMenu"	 => "external_menu",

    ];


    include("../../../pages/adminhead.php");
    include("../../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-users bg-c-blue"></i>
				<div class="d-inline">
					<h5>All Customers</h5>
					<span>View Customers</span>
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
<h5>Sample Block</h5>
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
	<th rowspan="1" colspan="1" ><?php echo $external_type;?> Id</th>
	<th rowspan="1" colspan="1" >Name</th>

	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM external_conn WHERE type='$external_type'");
	$k=1;
	if($result->num_rows>0)
	{
		while($row=$result->fetch_assoc())
		{
			$currid = $row["externalid"];
			$currName = "";

			$result2 = runQuery("SELECT * FROM external_param WHERE param='Name' AND externalid='$currid'");

			$currName = $result2->fetch_assoc()["value"];
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
			echo "<td>".$currid."</td>";
			echo "<td>".$currName."</td>";
			echo "<td><form action=\"edit.php\" method=\"POST\" style=\"display:inline-block\"><input type=\"hidden\" name=\"externalid\" value=\"".$currid."\"><input type=\"hidden\" name=\"edit\" value=\"\"><a href=\"#\" onclick=\"this.parentNode.submit();\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit ".$external_type."\"><i class=\"fa fa-edit\" style=\"display:inline-block\"></i></a></form><form  method=\"POST\"><input type=\"hidden\" name=\"externalid\" value=\"".$currid."\"><input type=\"hidden\" name=\"deleteext\" value=\"\"><a href=\"#\" onclick=\"delete_ext(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete ".$external_type."\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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

<script type="text/javascript">
	
	document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

	document.getElementById("<?php echo $PAGE["Menu"] ?>-view").classList.add("active");


</script>

<script type="text/javascript">
	function delete_ext(deleteform)
	{
		Swal.fire({
		  icon: 'question',
		  title: 'Delete <?php echo $external_type ?>',
		  html: "Are you sure you want to delete this <?php echo $external_type ?>?",
		  showConfirmButton: true,
		  showCancelButton: true,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'Cancel',
		  
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    	deleteform.submit();
				}
			})
	}
</script>
<?php
    
    include("../../../pages/endbody.php");

?>


