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

	
	$processname = "Raw Bag";

	$gradename = $_POST['editgradename'];

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "processgrade-".str_replace(" ", "",strtolower($processname)),
        "MainMenu"	 => "processgrade_menu",

    ];


    if(isset($_POST["addnewprop"]))
    {
    	$propname = $_POST["propname"];

    	if(isset($_POST['min']))
    	{
    		$min = $_POST['min'];
    	}
    	

    	if(isset($_POST['max']))
    	{
    		$max = $_POST['max'];
    	}
    	

    	if(isset($_POST['quarantine']))
    	{
    		$quarantine = $_POST['quarantine'];
    	}
    

    	
    	
    	

    	$result = runQuery("SELECT count(*) as val FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename'");
    	$row=$result->fetch_assoc();

    	$count = $row["val"] +1;

    	runQuery("INSERT INTO gradeproperties VALUES(NULL,'$processname','$gradename','$propname','$min','$max','$quarantine','$count')");

    	

    }

    

    if(isset($_POST["deletepropname"]))
    {
    	$propname = $_POST["deletepropname"];
    	$result = runQuery("DELETE FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename' AND properties='$propname'");
    	

    }


    if(isset($_POST["updateprop"]))
    {
    	$propnames = $_POST["propnames"];
    	$min = $_POST["prop-min"];
    	$max = $_POST["prop-max"];
    	$quarantine = $_POST["prop-quarant"];
    	runQuery("DELETE FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename'");
    	
    	for($i=0;$i<count($propnames);$i++)
    	{
    		$count = $i+1;
    		runQuery("INSERT INTO gradeproperties VALUES(NULL,'$processname','$gradename','$propnames[$i]','$min[$i]','$max[$i]','$quarantine[$i]','$count')");
    	}

    }



    $result = runQuery("SELECT * FROM processgradesproperties WHERE processname='$processname'");

    $allProps = [];

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		array_push($allProps,$row["gradeparam"]);
    	}
    }



$result = runQuery("SELECT * FROM gradeproperties WHERE processname='$processname' AND gradename='$gradename'");

    $currProps = [];

    if($result->num_rows>0)
    {
    	while($row=$result->fetch_assoc())
    	{
    		array_push($currProps,$row["properties"]);
    	}
    }



    $notSelected  = [];
    $dumSelected = array_diff($allProps,$currProps);

    foreach ($dumSelected as $key => $value) {
    	array_push($notSelected,$value);
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
					<h5>Grades - <?php echo $gradename; ?></h5>
					<span>Edit grade <?php echo $gradename; ?> grades</span>
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
		<h5>Add New Property</h5>
		<div class="card-header-right">

		</div>
	</div>
	<div class="card-block">

		<form method="POST">

				<input type="hidden" name="editgradename" value="<?php echo $gradename; ?>">

				<div class="form-group row">
					<label class="col-sm-6" style="margin-top:0.75rem;">Choose Grade</label>
					<div class="col-sm-6">
					<select required class="form-control" name="propname">
						
						<?php
							for($i=0;$i<count($notSelected);$i++)
							{
								echo "<option value=\"".$notSelected[$i]."\">".$notSelected[$i]."</option>";
							}

						?>

					</select>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="min" placeholder="Min" value="">
					</div>
					<div class="col-sm-6">
						<input type="text"  class="form-control" name="max" placeholder="Max" value="">
					</div>

					<div class="col-sm-6">
						<input type="text"  class="form-control" name="quarantine" placeholder="Quarantine" value="">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2"></label>
					<div class="col-sm-10">
					<button type="submit" name="addnewprop" id="addnewprop" class="btn btn-primary m-b-0 pull-right"><i class="fa fa-plus"></i>Add Property</button>
					</div>
				</div>

		</form>
	</div>
</div>
</div>


<div class="col-md-6">
<div class="card">
	<div class="card-header">
		<h5>Edit Properties</h5>
		<div class="card-header-right">

		</div>
	</div>
	<div class="card-block">

		<form method="POST">

				<input type="hidden" name="editgradename" value="<?php echo $gradename; ?>">

				<div class="form-group row">
					<label class="col-sm-2"></label>
					<div class="col-sm-10">
					<button type="submit" name="updateprop" id="updateprop" class="btn btn-primary m-b-0 pull-right"><i class="fa fa-refresh"></i>Update Properties</button>
					</div>
				</div>

				<div class="col-md-12" id="draggableMultiple">

					<?php 

						$result = runQuery("SELECT * FROM gradeproperties WHERE gradeproperties.processname='$processname' AND gradeproperties.gradename='$gradename' ORDER BY ordering");

						if($result->num_rows>0)
						{
							while($row1 = $result->fetch_assoc())
							{
								$dumprop = $row1['properties'];
								$result2 = runQuery("SELECT * FROM processgradesproperties WHERE processname='$processname' AND gradeparam='$dumprop'");

								if($row = $result2->fetch_assoc())
								{


					?>	

					<div class="sortable-moves card-sub">

					<h5 class="card-title"><?php echo $row["gradeparam"];?></h5>
					<div style="cursor:move;" class="col-sm-12">
						


						<div class="form-group row">
							
							<div class="col-md-3 col-sm-4">
							<label class="min">Min</lable>
								<input type="text" class="form-control" name="prop-min[]" placeholder="Min" value="<?php echo $row1["min"];?>" style="margin-top:0!important;">
							</div>
							<div class="col-md-3 col-sm-4">
							<label class="min">Max</lable>
								<input type="text" class="form-control" name="prop-max[]" placeholder="Max" value="<?php echo $row1["max"];?>" style="margin-top:0!important;">
							</div>
							<div class="col-md-3 col-sm-4">
							<label class="min">Quarantine</lable>
								<input type="text" class="form-control" name="prop-quarant[]" placeholder="Quarantine" value="<?php echo $row1["quarantine"];?>" style="margin-top:0!important;">
							</div>
							
						</div>

						
						<button type="button" class="btn btn-danger pull-right" onclick="deleteProp('<?php echo $row["gradeparam"];?>');">Remove</button>
						<hr class="solid" style="margin-top: 70px;">
						<input type="hidden" name="propnames[]" value="<?php echo $row["gradeparam"];?>">
					</div>
					</div>


					
					<?php


						}
						else
						{


						
						}
						}}

					?>


				</div>

		</form>



	</div>
</div>


<form method="POST" id="deletepropform">
	<input type="hidden" name="deletepropname" id="deletepropname">
	<input type="hidden" name="editgradename" value="<?php echo $gradename; ?>">
</form>



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


function deleteProp(propname)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Property',
		  html: 'Are you sure you want to delete '+propname,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		document.getElementById("deletepropname").value = propname;
			  		document.getElementById("deletepropform").submit();

				}
			})
}




document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

document.getElementById("<?php echo $PAGE["Menu"] ?>-edit").classList.add("active");


</script>


