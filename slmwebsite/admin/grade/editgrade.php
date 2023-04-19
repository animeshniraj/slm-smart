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
    	
    	runQuery("INSERT INTO processgrades VALUES(NULL,'$processname','$gradename','NO',CURRENT_TIMESTAMP)");
    	addprocesslog('GRADE',$gradename,$session->user->getUserid(),'New '.$processname .' Grade created.');
    }

    if(isset($_POST["copygradename"]))
    {
    	$gradename = $_POST['copygradename'];

	    $prefix = explode('#',$gradename)[0];
	    
	    $sqlprefix = $prefix."%";
    	

    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(gradename, '#', -1) AS SIGNED)) max_val FROM processgrades WHERE gradename LIKE '$sqlprefix' AND processname='$processname'");

    	if($result->num_rows==0)
    	{	
    		$count = 1;
    	}
    	else
    	{
    		$lastID = $result->fetch_assoc()["max_val"];
	    	
	    	$count = intval($lastID)+1;
    	}


    	$prefix = $prefix ."#".strval($count);

    	

    	runQuery("INSERT INTO processgrades ( SELECT NULL,'$processname','$prefix',cumulative,CURRENT_TIMESTAMP FROM processgrades WHERE gradename='$gradename' AND processname='$processname')");

    	runQuery("INSERT INTO gradeproperties (SELECT NULL,processname,'$prefix',properties,min,max,quarantine,ordering FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename')");

    	addprocesslog('GRADE',$gradename,$session->user->getUserid(),$processname.' Grade '.$gradename.' copied.');

    	
    }

    if(isset($_POST["deletegradename"]))
    {
    	$gradename = $_POST['deletegradename'];
    	runQuery("DELETE FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename'");
    	runQuery("DELETE FROM processgrades WHERE processname='$processname' AND gradename='$gradename'");

    	addprocesslog('GRADE',$gradename,$session->user->getUserid(),$processname.' Grade ' . $gradename .' deleted.');
    }


  
    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

    if($show_alert)
    {
    	echo $alert;
    }

?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		
		inobj.value = val;
	}


</script>
<style>
.btn i {
    margin-right: 0;
}

</style>

<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h2><?php echo $processname; ?> Grades</h2>
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



<div class="col-md-6">
<div class="card">
	<div class="card-header">
		<h5 class="slm-color">All Saved <?php echo $processname; ?> Grades</h5>
		<div class="card-header-right">

		</div>
	</div>
	<div class="card-block">

		<div class="dt-responsive table-responsive text-center">
			<div id="grade-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

				<div class="row">
					<div class="col-md-12 col-sm-12">
						<?php
							
							$result = runQuery("SELECT * FROM processgrades WHERE processname='$processname'");
							$k=1;
							if($result->num_rows>0)
							{

								?>
							<table id="grade-table" class="table table-striped table-bordered nowrap dataTable" role="grid">
								<thead>
								<tr role="row">
									<th rowspan="1" colspan="1"  style="width:15%">Sl No.</th>
									<th rowspan="1" colspan="1"  style="width:30%">Grade Name</th>
									<th rowspan="1" colspan="1"  style="width:55%">Options</th>
									
									

								</tr>
								</thead>
								<tbody>


										<?php
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

											echo "<td style=\"padding-top:20px;\">".$k++.".</td>";
											echo "<td style=\"padding-top:20px;\">".$row["gradename"]."</td>";

											if($processname=="Melting")
											{
												echo "<td><div><button type=\"button\"  data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit Grade\" class=\"btn btn-primary\" aria-hidden=\"true\" onclick=\"editGrade('".$row["gradename"]."')\"><i class=\"fa fa-edit\"></i></button></div></td>";
											}
											else
											if ($processname!="Final Blend")
											{
												echo "<td><div><button type=\"button\"  data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit Grade\" class=\"btn btn-primary\" aria-hidden=\"true\" onclick=\"editGrade('".$row["gradename"]."')\"><i class=\"fa fa-edit\"></i></button><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Copy Grade\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"copygrade('".$row["gradename"]."')\"><i class=\"fa fa-copy\"></i></button><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Grade\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"deleteGrade('".$row["gradename"]."')\"><i class=\"fa fa-trash\"></i></button> </div></td>";
											}

											if($processname=="Final Blend")
											{
												echo "<td><div><button type=\"button\"  data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit Grade\" class=\"btn btn-primary\" aria-hidden=\"true\" onclick=\"editGrade('".$row["gradename"]."')\"><i class=\"fa fa-edit\"></i></button><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Copy Grade\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"copygrade('".$row["gradename"]."')\"><i class=\"fa fa-copy\"></i></button><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print Grade\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"window.open('print-final-grade.php?grade=".$row["gradename"]."')\"><i class=\"fa fa-print\"></i></button><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete Grade\" class=\"btn btn-danger m-b-0\" style=\"margin-left:30px;\" onclick=\"deleteGrade('".$row["gradename"]."')\"><i class=\"fa fa-trash\"></i></button> </div></td>";

											}

											
											echo "</tr>";

											if($processname=="Melting")
											{
												break;
											}


										}

										?>
								</tbody>

							</table>

						<?php
							}

						?>
					</div>
				</div>

			</div>
		</div>


		<form method="POST" id="deletegrade">
			<input type="hidden" name="deletegradename" id="deletegradename">
		</form>

		<form method="POST" id="copygrade">
			<input type="hidden" name="copygradename" id="copygradename">
		</form>


		<form method="POST" id="editgrade" action="<?php echo str_replace(" ", "",strtolower($processname)) ?>-grade.php">
			<input type="hidden" name="editgradename" id="editgradename">
		</form>



	</div>
</div>
</div>

<?php

if($processname!="Melting")
{


?>


<div class="col-md-4">
		<div class="card" style="margin-top:4rem;">
			<div class="card-header">
			<img src="grade.png" style="margin-top:-5rem; width:100px;">

				<div class="card-header-right">
				<h5 class="slm-color">Add A New Grade</h5>

				</div>
			</div>
			
			<div class="card-block">

				<form method="POST">

						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Grade Name</label>
							<div class="col-sm-9">
							<input type="text" required class="form-control" name="gradename" id="gradename" placeholder="">
							<span class="messages"></span>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-3"></label>
							<div class="col-sm-9">
							<button type="submit" name="createnewgrade" id="createnewgradeBtn" class="btn btn-primary m-b-0 pull-right"><i class="fa fa-plus"></i> Add Grade Now</button>
							</div>
						</div>

				</form>
			</div>
		</div>

	<?php
	}

	?>
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

function copygrade(gradename)
{
	Swal.fire({
		  icon: 'info',
		  title: 'Copy Grade',
		  html: 'Are you sure you want to copy '+gradename,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("copygradename").value = gradename;
			  		document.getElementById("copygrade").submit();

				}
			})
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

function editGrade(gradename)
{	    
		document.getElementById("editgradename").value = gradename;
		document.getElementById("editgrade").submit();		
}


document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

document.getElementById("<?php echo $PAGE["Menu"] ?>-edit").classList.add("active");


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