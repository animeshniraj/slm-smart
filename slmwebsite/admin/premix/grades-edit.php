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


	
	if(!isset($_POST["gradename"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

    $gradename = $_POST["gradename"];



    if(isset($_POST["addphysicalprop"]))
    {
     	
     	

     	$params = $_POST['param'];
     	$mpifs = $_POST['mpif'];
     	$mins = $_POST['min'];
     	$maxs = $_POST['max'];
     	$units = $_POST['unit'];

     	runQuery("DELETE FROM premix_grade_physical WHERE gradename='$gradename'");
     	runQuery("DELETE FROM premix_coa_grade_settings WHERE gradename='$gradename' AND type='Physical'");
     	for ($i=0; $i < count($params) ; $i++) { 
     		
     		$cparam = $params[$i];
     		$cmpif = $mpifs[$i];
     		$cmin = $mins[$i];
     		$cmax = $maxs[$i];
     		$cunit = $units[$i];

     		runQuery("INSERT INTO premix_grade_physical VALUES(NULL,'$gradename','$cparam','$cmpif','$cmin','$cmax','$cunit')");
     		runQuery("INSERT INTO premix_coa_grade_settings VALUES(NULL,'$gradename','$cparam','$cparam',1,'$i','Physical')");


     	}

     	
    }


    if(isset($_POST["addcomposition"]))
    {
  

    	$list = $_POST["additive_list"];
    	$values = $_POST["additive_val"];
    	$splits = $_POST["feedsplits"];
    	$mintols = $_POST["mintols"];
    	$maxtols = $_POST["maxtols"];

    	$steps = $_POST["steps"];

    	$overlist = [];
    	if(isset($_POST["over"]))
    	{
    		$overlist = $_POST["over"];
    	}

    	runQuery("DELETE FROM premix_grade_feed_sequence WHERE gradename='$gradename'");
    	runQuery("DELETE FROM premix_grade_compositions WHERE gradename='$gradename'");
    	runQuery("DELETE FROM premix_coa_grade_settings WHERE gradename='$gradename' AND type='Chemical'");

    	$k=0;
    	for($i=0;$i<count($list);$i++)
    	{
    		$currL = $list[$i];
    		$currV = $values[$i];
    		$currS = $splits[$i];
    		$mintol = $mintols[$i];
    		$maxtol = $maxtols[$i];
    		
    		$step = $steps[$i];

    		$perSplit = round(100/$currS,2);
    		$total = 0.0;

    		for($j=0;$j<$currS;$j++)
    		{
    			$total +=$perSplit;

    			$currsplit = $perSplit;

    			if($j==$currS-1)
    			{
					$currsplit += (100-$total);
    			}

    			runQuery("INSERT INTO premix_grade_feed_sequence VALUES(NULL,'$gradename','$currL','$currsplit','$k')");

    			++$k;
    		}


    		if(in_array($currL, $overlist))
    		{
    			runQuery("INSERT INTO premix_grade_compositions VALUES(NULL,'$gradename','$currL','$currV','$currS','1','$mintol','$maxtol','$step')");
    		}
    		else
    		{
    			runQuery("INSERT INTO premix_grade_compositions VALUES(NULL,'$gradename','$currL','$currV','$currS','0','$mintol','$maxtol','$step')");
    		}

    		runQuery("INSERT INTO premix_coa_grade_settings VALUES(NULL,'$gradename','$currL','$currL',1,'$i','Chemical')");
    	}


    	
    }


    if(isset($_POST["addsequence"]))
    {


    	$additives = $_POST["feed_additive"];
    	$percent = $_POST["feed_percent"];
    	runQuery("DELETE FROM premix_grade_feed_sequence WHERE gradename='$gradename'");
    	$k=0;
    	for($i=0;$i<count($additives);$i++)
    	{	
    		$currL = $additives[$i];
    		$currsplit = $percent[$i];
    		runQuery("INSERT INTO premix_grade_feed_sequence VALUES(NULL,'$gradename','$currL','$currsplit','$k')");
    		$k++;
    	}
    }


    $result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$gradename'");

    $compositions = [];

    while($row=$result->fetch_assoc())
    {
    	array_push($compositions,[$row["additive"],$row["composition"],$row["splits"],$row["over"]==1?"checked":" ",$row["mintol"],$row["maxtol"],$row["step"]]);
    }

   
    $result = runQuery("SELECT * FROM premix_grade_feed_sequence WHERE gradename='$gradename' ORDER BY ordering");

    $feed_sequence = [];

    while($row=$result->fetch_assoc())
    {
    	array_push($feed_sequence,[$row["additive"],$row["percent"],$row["ordering"]]);
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
					<h5>Premix Grade</h5>
					<span>Edit Premix Grade</span>
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
<h5>Edit Composition</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST" id="composition-form">
<input type="hidden" name="gradename" value="<?php echo $gradename; ?>">

<div class="form-group row">
		<label class="col-sm-2 col-form-label">Add New Additives</label>
			<div class="col-sm-8">
			<select class="form-control"   id="addtives_select">
					<?php
						$result = runQuery("SELECT * FROM premix_additives");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["additive"]."\">".$row["additive"]."</option>";
							}
						}

						$result = runQuery("SELECT * FROM premix_additives_groups");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["groupname"]."\">".$row["groupname"]."</option>";
							}
						}

					?>
			</select>

			<script type="text/javascript" src="/pages/js/jquery.min.js"></script>
			<script type="text/javascript">
			
				selectObj = document.getElementById("addtives_select");
				

				var options = $("#addtives_select option");                    // Collect options         
				options.detach().sort(function(a,b) {               // Detach from select, then Sort
				    var at = $(a).text();
				    var bt = $(b).text();         
				    return (at > bt)?1:((at < bt)?-1:0);            // Tell the sort function how to order
				});
				options.appendTo("#addtives_select");  

				selectObj.selectedIndex = 0;
			</script>
		</div>
		<div class="col-sm-2">
		<button onclick="addAdditive()" type="button" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add</button></div>
		</div>

		<table class="table">
			<th rowspan="1" colspan="1" style="width:15%">Additive</th>
			<th rowspan="1" colspan="1" style="width:15%">Composition %</th>
			<th rowspan="1" colspan="1" style="width:10%">Feed Splits</th>
			<th rowspan="1" colspan="1" style="width:10%">Over 100%</th>
			<th rowspan="1" colspan="1" style="width:15%">Min (%)</th>
			<th rowspan="1" colspan="1"style="width:15%" >Max (%)</th>
			<th rowspan="1" colspan="1" style="width:10%">Round</th>


			<th rowspan="1" colspan="1" ></th>


			<tbody id="additives_div">
				

			</tbody>

		</table>
		<input type="hidden" name="addcomposition" value="">

		<div class="col-sm-12">
			<button type="button" onclick="checkValidComposition(this)" id ="addcomposition" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update Value</button>
			<span class="messages"></span>
		</div>





</form>



<script type="text/javascript">

selectObj = document.getElementById("addtives_select");
let selectedAdditives;
selectedAdditives = []
<?php 
	for($i=0;$i<count($compositions);$i++)
	{

		$currC = $compositions[$i];
?>

	selectedAdditives.push('<?php echo $currC[0]; ?>');
	var curr = '<?php echo $currC[0]; ?>';
	selectObj.remove(curr);
	addNewAdditive('<?php echo $currC[0]; ?>','<?php echo $currC[1]; ?>','<?php echo $currC[2]; ?>','<?php echo $currC[3]; ?>','<?php echo $currC[4]; ?>','<?php echo $currC[5]; ?>','<?php echo $currC[6]; ?>')


<?php 

	}

?>


	function checkValidComposition(btnobj)
	{

		var total = 0;

		var tbody = document.getElementById('additives_div');

		for(var i=0;i<tbody.children.length;i++)
		{
			var curr = tbody.children[i];
		

			if(!curr.children[3].children[0].checked)
			{
				total += parseFloat(curr.children[1].children[0].value);
			}
		}

		if(total==100)
		{
			document.getElementById("composition-form").submit();
		}
		else
		{
			Swal.fire({
				  icon: 'error',
				  title: 'Composition Error',
				  html: "Total Compositions does not add to 100%",
				  showConfirmButton: true,
				  showCancelButton: false,
				  confirmButtonText: 'Ok',
			});
		 }
		  
		
	}
	
	function addAdditive()
	{
		selectObj = document.getElementById("addtives_select");
		var curr = selectObj.value;
		selectObj.remove(curr);
		addNewAdditive(curr,0,1,"",10,10,1)
	}



	function addNewAdditive(name,value,splits,ischecked,mintol,maxtol,step)
	{
		var parentDiv = document.getElementById("additives_div");

		var newDiv = document.createElement("div");
		newDiv.classList.add("form-group");
		newDiv.classList.add("row");

		var row = document.createElement("TR");

		var dumTD = document.createElement("TD");
		dumTD.innerHTML = "<input type='hidden' name='additive_list[]' value='"+name+"'>"+name;
		row.appendChild(dumTD);

		dumTD = document.createElement("TD");
		dumTD.innerHTML = "<input required type='number' name='additive_val[]' min='0' max='100' step='0.01' value='"+value+"'>";
		row.appendChild(dumTD);


		dumTD = document.createElement("TD");
		dumTD.innerHTML = "<input required type='number' name='feedsplits[]' min='1' max='100' step='1' value='"+splits+"'>";
		row.appendChild(dumTD);


		

		if(name=="Iron")
		{
			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input type='checkbox'  style='display:none'  onclick='return false;' name='over[]' "+ischecked+" value='"+name+"''>";
			row.appendChild(dumTD);

			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input required readonly style='display:none' type='number' name='mintols[]' min='0' max='0' step='0.1' value='0'>";
			row.appendChild(dumTD);

			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input required style='display:none' readonly type='number' name='maxtols[]' min='120' max='100' step='0.1' value='100'>";
			row.appendChild(dumTD);

		}

		else
		{
			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input type='checkbox' name='over[]' "+ischecked+" value='"+name+"''>";
			row.appendChild(dumTD);

			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input required type='number' name='mintols[]' min='1' max='100' step='0.1' value='"+mintol+"'>";
			row.appendChild(dumTD);

			dumTD = document.createElement("TD");
			dumTD.innerHTML = "<input required type='number' name='maxtols[]' min='1' max='100' step='0.1' value='"+maxtol+"'>";
			row.appendChild(dumTD);
		}
		

		

		dumTD = document.createElement("TD");
		dumTD.innerHTML = "<input required type='number' name='steps[]'  step='0.01' value='"+step+"'>";
		row.appendChild(dumTD);


		dumTD = document.createElement("TD");
		dumTD.innerHTML = "<button type='button' class='btn btn-danger' data-toggle='tooltip' data-placement='top' title='Remove Additive' onclick='removeSelected(this,\""+name+"\")'><i class='fa fa-trash'></i></button>";
		row.appendChild(dumTD);

		parentDiv.appendChild(row);

	}

	function removeSelected(inObj,name)
	{
		inObj.closest('tr').remove();
		var opt = document.createElement('option');
	    opt.value = name;
	    opt.innerHTML = name;
		selectObj = document.getElementById("addtives_select");
		selectObj.appendChild(opt);

		var options = $("#addtives_select option");                    // Collect options         
		options.detach().sort(function(a,b) {               // Detach from select, then Sort
		    var at = $(a).text();
		    var bt = $(b).text();         
		    return (at > bt)?1:((at < bt)?-1:0);            // Tell the sort function how to order
		});
		options.appendTo("#addtives_select");  

		selectObj.selectedIndex = 0;
	}
</script>


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
<h5>Feed Sequence</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	
	<div class="dt-responsive table-responsive">
<div id="user-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

	<div class="row">
		<div class="col-xs-12 col-sm-12">

<form method="POST" id="feed_seq_form">
	<input type="hidden" name="gradename" value="<?php echo $gradename; ?>">

<div class="col-md-6">
<table id="user-table" class="table table-striped table-bordered nowrap dataTable " role="grid" aria-describedby="user-table_info">
<thead>
 <tr role="row">

	<th rowspan="1" colspan="1" >Additive</th>
	<th rowspan="1" colspan="1">Percent</th>
</tr>
</thead>

<tbody id="feed-seq-div">



	<?php 
	for($i=0;$i<count($feed_sequence);$i++)
	{


	?>

		<tr style="cursor: move;">


			<td><input type="hidden" name ="feed_additive[]" value="<?php echo $feed_sequence[$i][0] ?>"><?php echo $feed_sequence[$i][0] ?></td>
			<td><input type="text" name ="feed_percent[]" value="<?php echo $feed_sequence[$i][1] ?>"></td>


		</tr>



	<?php 
	}
	?>




</tbody>

</table>
</div>
<input type="hidden" name="addsequence" value="">
	<div class="col-sm-12">
			<button type="button" onclick="checkValidSequence(this)" id ="addseq" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update Sequence</button>
			<span class="messages"></span>
		</div>


</form>

</div></div></div>
</div>

<script type="text/javascript">
	
function checkValidSequence(btnObj)
{
	var tbody = document.getElementById('feed-seq-div');
	for(var j=0;j<selectedAdditives.length;j++)
	{
		var total = 0;

		

		for(var i=0;i<tbody.children.length;i++)
		{
			var curr = tbody.children[i];
		

			if(curr.children[0].children[0].value == selectedAdditives[j])
			{
				total += parseFloat(curr.children[1].children[0].value);
			}
		}

		if(total!=100)
		{
			Swal.fire({
				  icon: 'error',
				  title: 'Composition Error',
				  html: "Total Compositions of " + selectedAdditives[j] +" does not add to 100%",
				  showConfirmButton: true,
				  showCancelButton: false,
				  confirmButtonText: 'Ok',
			});

			return
		 }
	}

	document.getElementById('feed_seq_form').submit();
}

</script>



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
<h5>Physical Properties</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">



	
	<div class="dt-responsive table-responsive">
<div id="user-table_wrapper" class="dataTables_wrapper dt-bootstrap4">

	<div class="row">
		<div class="col-sm-12">


			<div class="form-group row ">

			<select id="final-prop" class="form-control col-sm-6">
				
				<?php 

					$result = runQuery("SELECT * FROM processgradesproperties WHERE processname='Final Blend'");

					while($row = $result->fetch_assoc())
					{
						echo "<option data-mpif ='".$row['mpif']."' data-mpif ='".$row['mpif']."' value='".$row['gradeparam']."'>".$row['gradeparam']."</option>";
					}

				?>

			</select>
			</div>


			<div class="col-sm-6">
				<button type="button" class="btn btn-primary pull-right" onclick="addtophysical(document.getElementById('final-prop').value,'','','','')"><i class="fa fa-plus"></i>Add New</button>
					
			</div>

	</div>

<form method="POST" id="physicalprop_form">
	<input type="hidden" name="gradename" value="<?php echo $gradename; ?>">




<div class="col-md-12">
<table id="user-table" class="table table-striped table-bordered nowrap dataTable " role="grid" aria-describedby="user-table_info">
<thead>
 <tr role="row">

	<th>Parameter</th>
	<th>MPIF</th>
	<th>Min</th>
	<th>Max</th>
	<th>Unit</th>
	<th></th>


</tr>
</thead>

<tbody id="physicalprop-div">






</tbody>

</table>
</div>

	<div class="col-sm-12">
			<button type="submit" onclick="" name ="addphysicalprop" class="btn btn-primary pull-right"><i class="fa fa-refresh"></i>Update Physical Properties</button>
			<span class="messages"></span>
		</div>


</form>

<script type="text/javascript">

	

	function addtophysical(param,mpif,min,max,unit)
	{
		tbody = document.getElementById('physicalprop-div');


		tr = document.createElement('tr');
		tr.innerHTML = "<td><input required type='text' class='form-control' name=param[] value='"+param+"'></td><td><input type='text' required class='form-control' name=mpif[] value='"+mpif+"'></td><td><input required type='text' class='form-control' name=min[] value='"+min+"'></td><td><input required type='text' class='form-control' name=max[] value='"+max+"'></td><td><input required type='text' class='form-control' name=unit[] value='"+unit+"'></td><td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();\"><i class=\"fa fa-trash\"></i>Remove</button></td>";

		tbody.appendChild(tr);
	}


	<?php 
		$result2 = runQuery("SELECT * FROM premix_grade_physical WHERE gradename='$gradename'");

		while($row2=$result2->fetch_assoc())
		{
			echo "addtophysical('".$row2['parameter']."','".$row2['mpif']."','".$row2['min']."','".$row2['max']."', '".$row2['units']."');";
		}
	?>

	

</script>

</div></div></div>
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


<?php
    
    include("../../pages/endbody.php");

?>


<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();


  	var fixHelperModified = function(e, tr) {
	    var $originals = tr.children();
	    var $helper = tr.clone();
	    $helper.children().each(function(index) {
	        $(this).width($originals.eq(index).width())
	    });
	    return $helper;
	},
	    updateIndex = function(e, ui) {
	        $('td.index', ui.item.parent()).each(function (i) {
	            $(this).html(i + 1);
	        });
	    };

	$("#feed-seq-div").sortable({
	    helper: fixHelperModified,
	    stop: updateIndex
	}).disableSelection();

  })
</script>


