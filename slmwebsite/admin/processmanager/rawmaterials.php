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
        "Page Title" => "SLM | Manage Raw Materials",
        "Home Link"  => "/admin/",
        "Menu"		 => "processmanager-rawmaterials",
        "MainMenu"	 => "processmanager_menu",

    ];



    if(isset($_POST["updatematerials"]))
    {

    	$propnames = $_POST['propname'];

    	runQuery("DELETE FROM rawmaterials");
    	for($i=0;$i<count($propnames);$i++)
    	{
    		runQuery("INSERT INTO rawmaterials VALUES('$propnames[$i]')");
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
					<h2>Raw Materials</h2>
					<span>Add and remove Raw Materials for Melting</span>
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
<div class="col-md-5">



<div class="card">
<div class="card-header">
<h5 class="slm-color">Manage Raw Materials</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<form method="POST">

	
	<div id = "propdiv">

		<?php 
			$result = runQuery("SELECT * FROM rawmaterials ORDER BY materialname");

			if($result->num_rows>0)
			{

				while($row = $result->fetch_assoc())
				{


		?>
		<div class="form-group row">
			
			<div class="col-md-6">
			<input required type="text" required class="form-control" name="propname[]"  placeholder="Name" value="<?php echo $row["materialname"]; ?>">
			<span class="messages"></span>
			</div>

			
			

			

			<div class="col-md-3">
				<button type="button" onclick="this.closest('.form-group').remove()" class="btn btn-danger m-b-0" data-toggle="tooltip" data-placement="top" title="Remove Material"><i class="fa fa-trash" style="margin:0;"></i></button>
			</div>

			
		</div>

		<?php

			}
			}

		?>

	</div>

	<div class="form-group row">
		
		<div class="col-sm-10">
		<button type="button" onclick="addpropFn()" class="btn btn-primary m-b-0"><i class="fa fa-plus"></i>Add New Raw Material</button>
		</div>
	</div>


	<br><br>


	<div class="form-group row">
		<label class="col-md-2"></label>
		<div class="col-md-10">
		<button type="submit" name="updatematerials" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-edit"></i>Update Materials</button>
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


function addpropFn()
{
	propdiv = document.getElementById('propdiv');

	newDiv = document.createElement("div");
	newDiv.innerHTML = "<div class=\"form-group row\"><div class=\"col-md-6\"><input required type=\"text\" required class=\"form-control\" name=\"propname[]\"  placeholder=\"Property Name\"><span class=\"messages\"></span></div><div class=\"col-md-2\"><button type=\"button\" onclick=\"this.closest('.form-group').remove()\" class=\"btn btn-danger m-b-0\"><i class=\"fa fa-trash\"></i>Remove</button></div></div>";
	propdiv.appendChild(newDiv);
}





</script>