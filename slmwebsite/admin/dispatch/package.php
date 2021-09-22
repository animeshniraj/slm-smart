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


	if(isset($_POST["deletepackage"]))
	{
		$curr = $_POST["packagename"];

		runQuery("DELETE FROM dispatch_package WHERE packagename='$curr'");

	}

	if(isset($_POST["addnew"]))
	{
		

		$packagename =  $_POST["packagename"];
		$material = $_POST["package_material"];
		$weight = $_POST["package_weight"];
		$l = $_POST["package_l"];
		$b = $_POST["package_b"];
		$h = $_POST["package_h"];
		

		runQuery("INSERT INTO dispatch_package VALUES('$packagename','$material','$weight','$l','$b','$h')");
	}






    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "dispatch-package",
        "MainMenu"	 => "dispatch_menu",

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
					<h5>Package</h5>
					<span>Create and Edit package</span>
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
<h5>Add New Package</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	<form method="POST">

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Name</label>
			<div class="col-md-10">
			<input type="text" required class="form-control" name="packagename" id="packagename" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
			<label class="col-md-2 col-form-label">Material</label>
			<div class="col-md-10">
			<input type="text" required class="form-control" name="package_material" id="package_material" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
			<label class="col-md-2 col-form-label">Gross Weight (kg)</label>
			<div class="col-md-10">
			<input type="number" min ="0" step="0.01" required class="form-control" name="package_weight" id="package_weight" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

			<div class="form-group row">
			<label class="col-md-2 col-form-label">Dimensions(cm)</label>
			<div class="col-md-3">
			<input type="number" required class="form-control" min ="0" step="0.01" name="package_l" id="package_l" placeholder="Length (cm)">
			<span class="messages"></span>
			</div>

			<div class="col-md-3">
			<input type="number" required class="form-control" min ="0" step="0.01" name="package_b" id="package_b" placeholder="Breadth (cm)">
			<span class="messages"></span>
			</div>

			<div class="col-md-3">
			<input type="number" required class="form-control" min ="0" step="0.01" name="package_h" id="package_h" placeholder="Height (cm)">
			<span class="messages"></span>
			</div>


			</div>


			

			






				<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="addnew" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Package</button>
			<span class="messages"></span>
			</div>
			</div>

	</form>




</div>
</div>

</div>
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
<h5>View Package</h5>
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
	<th rowspan="1" colspan="1" >Package Name</th>
	<th rowspan="1" colspan="1" >Material</th>
	<th rowspan="1" colspan="1" >Gross Weight</th>
	<th rowspan="1" colspan="1" >Dimensions</th>


	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM dispatch_package");
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


			echo "<td>".$row["packagename"]."\t</td>";
			echo "<td>".$row["material"]."\t</td>";

			echo "<td>".$row["weight"]."</td>";
			

			echo "<td>Length: ".$row["length"]."cm<br>Breadth: ".$row["length"]."cm<br>Height: ".$row["length"]."cm</td>";
			
			

			
			echo "<td><form  method=\"POST\"><input type=\"hidden\" name=\"packagename\" value=\"".$row["packagename"]."\"><input type=\"hidden\" name=\"deletepackage\" value=\"\"><a href=\"#\" onclick=\"delete_grade(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Package\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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
	function delete_grade(deleteform)
	{
		Swal.fire({
		  icon: 'question',
		  title: 'Delete Package',
		  html: "Are you sure you want to delete this package?",
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


<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();

  })
</script>


