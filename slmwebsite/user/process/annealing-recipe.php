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

	isAuthenticated($session,'user_module');

	

    $PAGE = [
        "Page Title" => "Add Recipes for Annealing | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "process-annealing-recipe",
        "MainMenu"	 => "process_annealing",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Annealing";

    if(isset($_POST['deleteProcess']))
    {
    	$processid = $_POST['processid'];
    	runQuery("call delete_process('$processid')");
    }

    require_once("helper.php");
    
    $result = runQuery("SELECT * FROM processpermission WHERE processname='$processname' AND step='DELETION' AND role ='$myrole'");

    $deletePermission = false;
    
	if($result->num_rows>0)
	{
		$dumPermission = $result->fetch_assoc()["permission"];
		if($dumPermission=="ALLOW")
		{
			$deletePermission = true;
		}

	}



	if(isset($_POST["newrecipe"]))
	{
		
		$name = $_POST['recipe'];
		$param = serialize($_POST['allparams']);
		$value = serialize($_POST['paramsvalue']);



		runQuery("INSERT INTO recipe VALUES(NULL,'$processname','$name','$param','$value')");

	}

	if(isset($_POST["deleterecipe"]))
	{
		
		$name = $_POST['recipename'];
		



		runQuery("DELETE FROM recipe WHERE processname='$processname' AND recipename='$name'");

	}



	$operationalParams = [];

    

    		$result2 = runQuery("SELECT * FROM processparams WHERE processname='Annealing' AND step='OPERATIONAL' AND param<>'$GRADE_TITLE'");
    		$dumval = "";
    		
    		if($result2->num_rows>0)
    		{
    			while($row = $result2->fetch_assoc())
    			{
    				array_push($operationalParams,[$row["param"],$dumval,$row["allowedvalues"],$row["type"]]);
    			}
    			
    		}

    	
    		
    	
    

    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





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
				<i class="fa fa-fire bg-c-blue"></i>
				<div class="d-inline">
					<h5>Add Annealing Recipes</h5>
					<span>Specify all parameters for a recipe</span>
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

<div class="card-header-right">
</div>
</div>
<div class="card-block">

<form method="POST" >

		<div class="form-group row">
			<label class="col-md-2 col-form-label">Recipe Name</label>
			<div class="col-md-10">
			<input  type="text" required class="form-control" name="recipe" id="recipe" placeholder="">
			
			</div>
		</div>

		<?php

			for($i=0;$i<count($operationalParams);$i++)
			{

					if($operationalParams[$i][2])
					{
						optionInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],$operationalParams[$i][2],'required');
					}
					else if($operationalParams[$i][3] == "INTEGER")
					{
						integerInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
					else if($operationalParams[$i][3] == "DECIMAL")
					{
						decimalInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
					else if($operationalParams[$i][3] == "STRING")
					{
						stringInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
					else if($operationalParams[$i][3] == "DATE")
					{
						dateInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
					else if($operationalParams[$i][3] == "TIME")
					{
						timeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
					else if($operationalParams[$i][3] == "DATE TIME")
					{
						datetimeInput($operationalParams[$i][0],"operational-".$operationalParams[$i][0],$operationalParams[$i][1],'required');
					}
			}


		?>


		<div class="form-group row">
		
				<div class="col-sm-12">
				<button type="submit" name="newrecipe" id="submitBtn" class="btn btn-primary m-b-0 pull-right"><i class="feather icon-plus"></i>Create Recipe</button>
				</div>

				
		
	</div>


</form>



</div>
</div>


<div class="card">
<div class="card-header">

<div class="card-header-right">
</div>
</div>
<div class="card-block">


	<table class="table">
	<thead>
		<tr>
		<th>#</th>
		<th>Recipe</th>
		<th>Values</th>
		<th>Options</th>
		
		</tr>
	</thead>

	<tbody>
		<?php


			$result = runQuery("SELECT * FROM recipe WHERE processname='$processname'");
			$k=0;
			while($row=$result->fetch_assoc())
			{
				?>

				<tr>
					<td><?php echo ++$k; ?></td>
					<td><?php echo $row["recipename"]; ?></td>

					<td>
						
						<?php

							$param = unserialize($row["param"]);
							$value = unserialize($row["value"]);


							for($i=0;$i<count($param);$i++)
							{
								?>

									<div><?php echo $param[$i]; ?> : <?php echo $value[$i]; ?></div>

								<?php
							}

						?>



					</td>

					<td><form method="POST">
						
						<input type="hidden" name="recipename" value="<?php echo $row["recipename"]; ?>">
						<button type="submit" name="deleterecipe" id="submitBtn" class="btn btn-primary m-b-0 pull-left"><i class="feather icon-trash"></i>Delete Recipe</button>
					</form></td>
				</tr>

				<?php
			}


		?>
	</tbody>
	
	</table>



</div>
</div>



<form method="POST" id="deleteform">
	<input type="hidden" name="processid" id="deleteid">
	<input type="hidden" name="deleteProcess" >

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
  	






  // Creation

  	

  		

  	

});








</script>