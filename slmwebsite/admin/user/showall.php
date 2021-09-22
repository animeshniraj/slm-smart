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


	if(isset($_POST["deleteuser"]))
	{
		$dumuserid = $_POST["userid"];
		

		
		if(runQuery("call delete_user('$dumuserid')"))
		{
			$show_alert = true;
			$alert = showAlert("success","User Deleted","");
			$session->user->addLog("Deleted User (Userid: ".$dumuserid.")");
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Error","Error. Try again.");
		}

	}

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "user-showall",
        "MainMenu"	 => "user_menu",	

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

    if($show_alert)
    {
    	echo $alert;
    }

?>






<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-user bg-c-blue"></i>
				<div class="d-inline">
					<h2>All Users</h2>
					<span>View All Users</span>
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
<h5 class="slm-color">All Users</h5>
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
	<th rowspan="1" colspan="1" >User Id</th>
	<th rowspan="1" colspan="1" >First Name</th>
	
	<th rowspan="1" colspan="1" >Last Name</th>
	<th rowspan="1" colspan="1">Role</th>

	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM userdetails");
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
			echo "<td>".$row["userid"]."</td>";
			echo "<td>".$row["fname"]."</td>";
			echo "<td>".$row["lname"]."</td>";
			echo "<td>".$row["rolename"]."</td>";
			echo "<td><form action=\"edituser.php\" method=\"POST\" style=\"display:inline-block\"><input type=\"hidden\" name=\"userid\" value=\"".$row["userid"]."\"><input type=\"hidden\" name=\"edituser\" value=\"\"><a href=\"#\" onclick=\"this.parentNode.submit();\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit User\"><i class=\"fa fa-edit\" style=\"display:inline-block\"></i></a></form><form  method=\"POST\"><input type=\"hidden\" name=\"userid\" value=\"".$row["userid"]."\"><input type=\"hidden\" name=\"deleteuser\" value=\"\"><a href=\"#\" onclick=\"delete_user(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete User\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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

<script type="text/javascript" src="/pages/js/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
$('#user-table').DataTable({
"order": [[ 0, "asc" ]],
"columnDefs": [
    { "orderable": false, "targets": 5 }
  ]
});
$('.dataTables_length').addClass('bs-select');
});
</script>
<script type="text/javascript">
	function delete_user(deleteform)
	{
		Swal.fire({
		  icon: 'question',
		  title: 'Delete User',
		  html: "Are you sure you want to delete this user?",
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
    
    include("../../pages/endbody.php");

?>

