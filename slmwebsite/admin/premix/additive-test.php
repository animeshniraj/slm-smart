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

	
	if(!isset($_POST["additive"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $additive = $_POST["additive"];


    if(isset($_POST["property"]))
    {
    	

    	runQuery("DELETE FROM additive_test WHERE additive='$additive'");

    	for ($i=0; $i < count($_POST["property"]); $i++) { 
    		$curr_p = $_POST["property"][$i];
    		$curr_min = $_POST["min"][$i];
    		$curr_max = $_POST["max"][$i];

    		runQuery("INSERT INTO additive_test VALUES(NULL,'$additive','$curr_p','$curr_min','$curr_max')");
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
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5>Additives Test</h5>
					<span>View Additive Requiements</span>
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
<h5>Additive Test</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

	
<form method="POST">
	<input type="hidden" name="additive" value="<?php echo $additive;?>">
<table class="table table-striped"> 
	<thead>
		<th>Property</th>
		<th>Min</th>
		<th>Max</th>
		<th>Option</th>
	</thead>

	<tbody id="test-body">
		
	</tbody>

	<tfoot>
		<tr>
			<th><button type="button" class="btn btn-primary" onclick="add_new()"><i class="fa fa-plus"></i>Add New</button></th>
			<th><button type="submit" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update</button></th>
		</tr>
	</tfoot>
</table>
<form>



<script type="text/javascript">

	function add_new(val = "",min="-",max="-")
	{
		
		tbody = document.getElementById("test-body");
		var tr = document.createElement("tr");

		tr.innerHTML += "<td><input value=\""+val+"\" required name=\"property[]\" class=\"form-control\"></td>"
		tr.innerHTML += "<td><input value=\""+min+"\" required   name=\"min[]\" class=\"form-control\"></td>"
		tr.innerHTML += "<td><input value=\""+max+"\" required name=\"max[]\" class=\"form-control\"></td>"
		tr.innerHTML += "<td><button onclick=\"this.closest('tr').remove();\" class=\"btn btn-primary\" onclick=\"add_new()\"><i class=\"fa fa-trash\"></i>Remove</button></td>"

		tbody.appendChild(tr);
	}


	<?php 
		$result = runQuery("SELECT * FROM additive_test WHERE additive='$additive'");

		while($row = $result->fetch_assoc())
		{
			echo "add_new(\"".$row["property"]."\",\"".$row["min"]."\",\"".$row["max"]."\");\n"; 
		}
	?>
	
</script>






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

