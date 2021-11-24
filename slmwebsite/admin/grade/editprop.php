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
        "Page Title" => "SLM | Grade Properties",
        "Home Link"  => "/admin/",
        "Menu"		 => "processgrade-".str_replace(" ", "",strtolower($processname)),
        "MainMenu"	 => "processgrade_menu",

    ];



    if(isset($_POST["updategrade"]))
    {

    	
	    	$propname = $_POST["grade-propname"];
	    	$type = $_POST["grade-proptype"];
	    	$mpif = $_POST["grade-mpif"];
	    	$class = $_POST["grade-class"];
	    	

	    	runQuery("DELETE FROM processgradesproperties WHERE processname='$processname'");

	    	for($i=0;$i<count($propname);$i++)
	    	{
	    		
	    		runQuery("INSERT INTO processgradesproperties VALUES(NULL,'$processname','$propname[$i]','$type[$i]','$mpif[$i]','$class[$i]')");
	    	}
	    

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
					<h2><?php echo $processname; ?> Grade Properties</h2>
					<span>Edit <?php echo $processname; ?> process grade properties</span>
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
<div class="col-md-12">



<div class="card">
<div class="card-header">
<h5 class="slm-color">Grade Properties</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<form method="POST">

	
	<div id = "grade-propdiv">

		<?php 
			$result = runQuery("SELECT * FROM processgradesproperties WHERE processname='$processname'");

			if($result->num_rows>0)
			{

				while($row = $result->fetch_assoc())
				{


		?>
		<div class="form-group row">
			
			<div class="col-md-3">
			<input required type="text" required class="form-control" name="grade-propname[]"  placeholder="Name" value="<?php echo $row["gradeparam"]; ?>">
			<span class="messages"></span>
			</div>

			
			<div class="col-md-3">
				<select required class="form-control" name="grade-proptype[]" id = "grade-proptype-<?php echo str_replace(" ","_",$row["gradeparam"]); ?>" onready="changeSelect(this,'<?php echo $row["type"]; ?>')" >

					
						<option value="INTEGER">INTEGER</option>
						<option value="DECIMAL">DECIMAL</option>
						<option value="STRING">STRING</option>
						<option value="DATE">DATE</option>
						<option value="TIME">TIME</option>
						<option value="DATE TIME">DATE TIME</option>
						<option value="VALUE">VALUE</option>

						<script type="text/javascript">
							
							changeSelect(document.getElementById("grade-proptype-<?php echo str_replace(" ","_",$row["gradeparam"]); ?>"),'<?php echo $row["type"]; ?>')

						</script>

						
				</select>

				
			</div>
			<?php 
				if($processname=='Final Blend')
				{

			?>
			<div class="col-sm-2">
				<input  type="text" class="form-control" name="grade-mpif[]" placeholder="MPIF" value="<?php echo $row["mpif"]; ?>">
				<span class="messages"></span>
			</div>
			<div class="col-sm-2">
				<input  type="text" class="form-control" name="grade-class[]" placeholder="Property Class" value="<?php echo $row["class"]; ?>">
				<span class="messages"></span>
			</div>
			<div class="col-sm-2">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0"><i class="fa fa-trash"></i>Remove</button>
			</div>

			<?php 
				}
				else
				{


			?>
			
			<div class="col-sm-2">
				<input style="display:none"  type="text" class="form-control" name="grade-mpif[]" placeholder="MPIF" value="<?php echo $row["mpif"]; ?>">
				<span class="messages"></span>
			</div>
			<div class="col-sm-3">
				<input  style="display:none" type="text" class="form-control" name="grade-class[]" placeholder="Property Class" value="<?php echo $row["class"]; ?>">
				<span class="messages"></span>
			</div>

			<div class="col-sm-2">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0"><i class="fa fa-trash"></i>Remove</button>
			</div>

			<?php 
				}

					
			?>

			

			
		</div>

		<?php

			}
			}

		?>

	</div>


	<?php 
				if($processname=='Finished')
				{

			?>

	<div class="form-group row">
		
		<div class="col-sm-10">
		<button type="button" onclick="addpropFn('grade',false)" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add Grade Properties</button>
		</div>
	</div>

	<?php 
	}
	else
	{


	?>

	<div class="form-group row">
		
		<div class="col-sm-10">
		<button type="button" onclick="addpropFn('grade')" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add Properties</button>
		</div>
	</div>

	<?php 
		}
	?>


	<br><br>


	<div class="form-group row">
		<label class="col-sm-2"></label>
		<div class="col-sm-10">
		<button type="submit" name="updategrade" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Process</button>
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
</div>


<?php
    
    include("../../pages/endbody.php");

?>

<script type="text/javascript">





$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	


});


function addpropFn(step,noshow=true)
{
	propdiv = document.getElementById(step+'-propdiv');

	newDiv = document.createElement("div");

	if(noshow)
	{
		newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-md-4\"><input required type=\"text\" required class=\"form-control\" name=\""+ step +"-propname[]\"  placeholder=\"Property Name\"><span class=\"messages\"></span></div><div class=\"col-md-3\"><select required class=\"form-control\" name=\""+ step +"-proptype[]\" ><option value=\"INTEGER\">INTEGER</option><option value=\"DECIMAL\">DECIMAL</option><option value=\"STRING\">STRING</option><option value=\"DATE\">DATE</option><option value=\"TIME\">TIME</option><option value=\"DATE TIME\">DATE TIME</option></select></div><div class=\"col-sm-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div><div class=\"col-sm-2\"><input style=\"display:none\" type=\"text\" class=\"form-control\" name=\""+ step +"-mpif[]\" placeholder=\"MPIF\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><input type=\"text\" style=\"display:none\" class=\"form-control\" name=\""+ step +"-class[]\" placeholder=\"Property Class\"><span class=\"messages\"></span></div></div>";
	}
	else
	{
		newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-md-4\"><input required type=\"text\" required class=\"form-control\" name=\""+ step +"-propname[]\"  placeholder=\"Property Name\"><span class=\"messages\"></span></div><div class=\"col-md-3\"><select required class=\"form-control\" name=\""+ step +"-proptype[]\" ><option value=\"INTEGER\">INTEGER</option><option value=\"DECIMAL\">DECIMAL</option><option value=\"STRING\">STRING</option><option value=\"DATE\">DATE</option><option value=\"TIME\">TIME</option><option value=\"DATE TIME\">DATE TIME</option></select></div><div class=\"col-sm-2\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-mpif[]\" placeholder=\"MPIF\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><input type=\"text\" class=\"form-control\" name=\""+ step +"-class[]\" placeholder=\"Property Class\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div></div>";
	}
	
	propdiv.appendChild(newDiv);
}


document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

document.getElementById("<?php echo $PAGE["Menu"] ?>-prop").classList.add("active");


</script>