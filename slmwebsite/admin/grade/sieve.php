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
        "Page Title" => "SLM | Sieve Properties",
        "Home Link"  => "/admin/",
        "Menu"		 => "processgrade-sieve",
        "MainMenu"	 => "processgrade_menu",

    ];



    if(isset($_POST["updategrade"]))
    {

    	
	    	$propname = $_POST["sieve-propname"];
	    	$mesh = $_POST["sieve-mesh"];
	    	$micron = $_POST["sieve-micron"];
	    	

	    	runQuery("DELETE FROM sieve ");

	    	for($i=0;$i<count($propname);$i++)
	    	{
	    		
	    		runQuery("INSERT INTO sieve VALUES('$propname[$i]','$mesh[$i]','$micron[$i]')");
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
					<h2>Sieve Properties</h2>
					<span>Edit Sieve properties</span>
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
<h5 class="slm-color">Sieve Properties</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<form method="POST">

	
	<div id = "sieve-propdiv">

		<?php 
			$result = runQuery("SELECT * FROM sieve");

			if($result->num_rows>0)
			{

				while($row = $result->fetch_assoc())
				{


		?>
		<div class="form-group row">
			
			<div class="col-md-3">
			<input required type="text" required class="form-control" name="sieve-propname[]"  placeholder="Name" value="<?php echo $row["name"]; ?>">
			<span class="messages"></span>
			</div>

			
			
			<div class="col-sm-2">
				<input   type="text" required class="form-control" name="sieve-mesh[]" placeholder="Mesh" value="<?php echo $row["mesh"]; ?>">
				<span class="messages"></span>
			</div>
			<div class="col-sm-3">
				<input   type="text" required class="form-control" name="sieve-micron[]" placeholder="Micron" value="<?php echo $row["micron"]; ?>">
				<span class="messages"></span>
			</div>

			<div class="col-sm-2">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0"><i class="fa fa-trash"></i>Remove</button>
			</div>

		</div>

			<?php 
				}

					
			?>

			



		<?php

			
			}

		?>

	</div>




	<div class="form-group row">
		
		<div class="col-sm-10">
		<button type="button" onclick="addpropFn('sieve')" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add Properties</button>
		</div>
	</div>


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


	
		newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-md-4\"><input required type=\"text\" required class=\"form-control\" name=\""+ step +"-propname[]\"  placeholder=\"Sieve Name\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><input required type=\"text\" class=\"form-control\" name=\""+ step +"-mesh[]\" placeholder=\"Mesh\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><input required type=\"text\" class=\"form-control\" name=\""+ step +"-micron[]\" placeholder=\"Micron\"><span class=\"messages\"></span></div><div class=\"col-sm-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div></div>";
	
	
	propdiv.appendChild(newDiv);
}


document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

document.getElementById("<?php echo $PAGE["Menu"] ?>-prop").classList.add("active");


</script>