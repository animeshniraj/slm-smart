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


	if(isset($_POST["deletegrade"]))
	{
		$curr = $_POST["gradename"];
		runQuery("DELETE FROM premix_grade_physical WHERE gradename='$curr'");
		runQuery("DELETE FROM premix_grade_feed_sequence WHERE gradename='$curr'");
		runQuery("DELETE FROM premix_grade_compositions WHERE gradename='$curr'");
		runQuery("DELETE FROM premix_grades WHERE gradename='$curr'");

	}

	if(isset($_POST["addnew"]))
	{
		

		$gradename =  $_POST["gradename"];
		$finalgrade = serialize($_POST["finalblendgrade"]);

		runQuery("INSERT INTO premix_grades VALUES('$gradename','$finalgrade')");
	}






    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "premix-grades",
        "MainMenu"	 => "premix_menu",

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
					<h5>Grades</h5>
					<span>Create and Edit Grades</span>
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
<h5>Add New Grade</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	<form method="POST">

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Premix Grade Name</label>
			<div class="col-md-10">
			<input type="text" required class="form-control" name="gradename" id="gradename" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Final Blend Grade</label>
			<div class="col-md-10">
				<select required class="js-example-basic-multiple " multiple="multiple" name="finalblendgrade[]">
					<?php 

						$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");

						while($row=$result->fetch_assoc())
						{
							echo "<option value='".$row["gradename"]."'>".$row["gradename"]."</option>";
						}

					?>

				</select>
			</div>

		</div>






				<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="addnew" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Grade</button>
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
<h5>View Grade</h5>
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
	<th rowspan="1" colspan="1" >Final Blend Grade</th>


	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM premix_grades");
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


			echo "<td>".$row["gradename"]."\t</td>";
			echo "<td>";
			$curr1 = unserialize($row["finishedgrade"]);

			for($i=0;$i<count($curr1);$i++)
			{
				echo $curr1[$i]."<br>";
			}

			echo "</td>";
			
			

			
			echo "<td><form action=\"grades-edit.php\" method=\"POST\" style=\"display:inline-block\"><input type=\"hidden\" name=\"gradename\" value=\"".$row["gradename"]."\"><input type=\"hidden\" name=\"editgrade\" value=\"\"><a href=\"#\" onclick=\"this.parentNode.submit();\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit Grade\"><i class=\"fa fa-edit\" style=\"display:inline-block\"></i></a></form><form  method=\"POST\"><input type=\"hidden\" name=\"gradename\" value=\"".$row["gradename"]."\"><input type=\"hidden\" name=\"deletegrade\" value=\"\"><a href=\"#\" onclick=\"delete_grade(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Grade\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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
		  title: 'Delete Grade',
		  html: "Are you sure you want to delete this grade?",
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


