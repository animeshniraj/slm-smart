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


	if(isset($_POST["deleteadditive"]))
	{
		$curr = $_POST["additive"];
		runQuery("DELETE FROM premix_additives WHERE additive='$curr'");

	}


	if(isset($_POST["deletegroup"]))
	{
		$curr = $_POST["groupname"];
		runQuery("DELETE FROM premix_additives_group_member WHERE groupname='$curr'");
		runQuery("DELETE FROM premix_additives_groups WHERE groupname='$curr'");
	}

	if(isset($_POST["addnew"]))
	{
		

		$shelflife = $_POST['shelflife'];

		$additive =  $_POST["additive"];

		runQuery("INSERT INTO premix_additives VALUES('$additive','$shelflife')");
	}

	if(isset($_POST["addnewgroup"]))
	{

		$additives = $_POST["additives"];
		$groupname = $_POST["groupname"];

		runQuery("INSERT INTO premix_additives_groups VALUES('$groupname')");

		for($i=0;$i<count($additives);$i++)
		{
			$currAdditive = $additives[$i];
			runQuery("INSERT INTO premix_additives_group_member VALUES(NULL,'$groupname','$currAdditive')");
		}



	}






    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "premix-additives",
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
				<i class="feather icon-users bg-c-blue"></i>
				<div class="d-inline">
					<h5>Additives</h5>
					<span>Create and Edit Additives</span>
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
<h5>Add New Additive</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	<form method="POST">

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Additive Name</label>
			<div class="col-md-10">
			<input type="text" required class="form-control" name="additive" id="additive" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
		<label class="col-sm-2 col-form-label">Shelf Life(Days)</label>
			<div class="col-sm-10">
			<input type="number" required class="form-control" name="shelflife" id="shelflife" placeholder="">

		</div>
		
	</div>



				<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="addnew" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Additive</button>
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
<h5>Add New Group</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	<form method="POST">

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Group Name</label>
			<div class="col-md-10">
			<input type="text" required class="form-control" name="groupname" id="groupname" placeholder="">
			<span class="messages"></span>
			</div>
		</div>


		<div class="form-group row">
		<label class="col-sm-2 col-form-label">Additives</label>
			<div class="col-sm-10">
			<select required name="additives[]"  class="js-example-basic-multiple " multiple="multiple">
				<?php 

					$result = runQuery("SELECT * FROM premix_additives");
					if($result->num_rows>0)
					{
						while($row=$result->fetch_assoc())
						{
							echo "<option value='".$row["additive"]."'>".$row["additive"]."</option>";
						}

					}

				?>
			</select>

		</div>
		
		</div>



				<div class="form-group row">
			
			<div class="col-sm-12">
			<button type="submit" name="addnewgroup" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Group</button>
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
<h5>View Additives</h5>
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
	<th rowspan="1" colspan="1" >Additive</th>
	<th rowspan="1" colspan="1" >Shelf Life (Days)</th>

	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM premix_additives");
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

			
			echo "<td>".$row["additive"]."\t</td>";

			echo "<td>".$row["shelflife"]."\t</td>";
			
			

			
			echo "<td><form  method=\"POST\"><input type=\"hidden\" name=\"additive\" value=\"".$row["additive"]."\"><input type=\"hidden\" name=\"deleteadditive\" value=\"\"><a href=\"#\" onclick=\"delete_additive(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Additive\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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






<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

<div class="page-body">
<div class="row">
<div class="col-lg-12">


<div class="card">
<div class="card-header">
<h5>View Group</h5>
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
	<th rowspan="1" colspan="1" >Group Name</th>
	<th rowspan="1" colspan="1" >Additives</th>

	<th rowspan="1" colspan="1">Options</th>
</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM premix_additives_groups");
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

			
			echo "<td>".$row["groupname"]."\t</td>";

			echo "<td>";

			$currGroup = $row["groupname"];

			$result2 = runQuery("SELECT * FROM premix_additives_group_member WHERE groupname='$currGroup'");
			while($row2=$result2->fetch_assoc())
			{
				echo $row2["additive"]."<br>";
			}

			echo "</td>";
			
			

			
			echo "<td><form  method=\"POST\"><input type=\"hidden\" name=\"groupname\" value=\"".$currGroup."\"><input type=\"hidden\" name=\"deletegroup\" value=\"\"><a href=\"#\" onclick=\"delete_group(this.parentNode);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Group\"><i class=\"fa fa-trash\" style=\"display:inline-block\"></i></a></form></td>";
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
	function delete_additive(deleteform)
	{
		Swal.fire({
		  icon: 'question',
		  title: 'Delete Additive',
		  html: "Are you sure you want to delete this additive?",
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

	function delete_group(deleteform)
	{
		Swal.fire({
		  icon: 'question',
		  title: 'Delete Group',
		  html: "Are you sure you want to delete this group?",
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


