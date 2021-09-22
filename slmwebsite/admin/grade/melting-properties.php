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

	
	$processname = $_GET["process"];
    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "processgrade-".str_replace(" ", "",strtolower($processname)),
        "MainMenu"	 => "processgrade_menu",

    ];


    if(isset($_POST["createnewgrade"]))
    {
    	$gradename = $_POST['gradename'];

    	runQuery("INSERT INTO processgrades VALUES(NULL,'$processname','$gradename')");
    }

    if(isset($_POST["deletegradename"]))
    {
    	$gradename = $_POST['deletegradename'];
    	runQuery("DELETE FROM processgrades WHERE processname='$processname' AND gradename='$gradename'");
    }


  
    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");


?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		
		inobj.value = val;
	}


</script>


<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5><?php echo $processname; ?> Grades</h5>
					<span>Edit <?php echo $processname; ?> process grades</span>
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
			<label class="col-sm-2 col-form-label">Grade Name</label>
			<div class="col-sm-10">
			<input type="text" required class="form-control" name="gradename" id="gradename" placeholder="">
			<span class="messages"></span>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2"></label>
			<div class="col-sm-10">
			<button type="submit" name="createnewgrade" id="createnewgradeBtn" class="btn btn-primary m-b-0 pull-right"><i class="fa fa-plus"></i>Add New Grade</button>
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
<div id="grade-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

	<div class="row">
		<div class="col-xs-12 col-sm-12">

<table id="grade-table" class="table table-striped table-bordered nowrap dataTable" role="grid">
<thead>
 <tr role="row">
	<th rowspan="1" colspan="1"  style="width: 84.578125px;">Sl No.</th>
	<th rowspan="1" colspan="1"  style="width: 356.875px;">Grade Name</th>
	<th rowspan="1" colspan="1"  style="width: 176.703125px;">Options</th>
	
	

</tr>
</thead>
<tbody>



<?php
	
	$result = runQuery("SELECT * FROM processgrades WHERE processname='$processname'");
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
			echo "<td>".$row["gradename"]."</td>";
			echo "<td><div><button type=\"button\"  class=\"btn btn-primary m-b-0\"><i class=\"fa fa-edit\"></i>Edit</button><button type=\"button\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"deleteGrade('".$row["gradename"]."')\"><i class=\"fa fa-trash\"></i>Remove</button></div></td>";

			
			echo "</tr>";



		}
	}

?>





</tbody>

</table></div></div></div>
</div>


<form method="POST" id="deletegrade">
	<input type="hidden" name="deletegradename" id="deletegradename">
</form>



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
  	


});


function addpropFn(step)
{
	propdiv = document.getElementById(step+'-propdiv');

	newDiv = document.createElement("div");
	newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-sm-3\"><input required type=\"text\" required class=\"form-control\" name=\""+ step +"-propname[]\"  placeholder=\"Property Name\"><span class=\"messages\"></span></div><div class=\"col-sm-1\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-min[]\" placeholder=\"Min\"><span class=\"messages\"></span></div><div class=\"col-sm-1\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-max[]\" placeholder=\"Max\"><span class=\"messages\"></span></div><div class=\"col-sm-1\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-mpif[]\" placeholder=\"MPIF\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-class[]\" placeholder=\"Property Class\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div></div>";
	propdiv.appendChild(newDiv);
}

function deleteGrade(gradename)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Grade',
		  html: 'Are you sure you want to delete '+gradename,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("deletegradename").value = gradename;
			  		document.getElementById("deletegrade").submit();

				}
			})
}


document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

document.getElementById("<?php echo $PAGE["Menu"] ?>-prop").classList.add("active");


</script>


<script type="text/javascript">
	$(document).ready(function () {
		$('#grade-table').DataTable({
		"order": [[ 0, "asc" ]],
		"columnDefs": [
		    { "orderable": false, "targets": 4 }
		  ]
		});
		$('.dataTables_length').addClass('bs-select');


	

});
</script>