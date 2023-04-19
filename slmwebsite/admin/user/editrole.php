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

	if(!isset($_POST["roleid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();
    }

	$roleid = $_POST["roleid"];

	$rolename = runQuery("SELECT * FROM roles WHERE roleid='$roleid'")->fetch_assoc()['rolename'];

    

    if(isset($_POST['editpermission']))
    {
    	$dumpath = $_POST['path'];
    	$dumpermission  = $_POST['newpermission'];

    	
    	runQuery("UPDATE rolepermission SET permission='$dumpermission' WHERE roleid='$roleid' AND page='$dumpath'");

  
    }

    if(isset($_POST['copypermission']))
    {
    	$copyfrom = $_POST['copyfrom'];
    	
    	runQuery("DELETE FROM rolepermission WHERE roleid='$roleid'");

    	runQuery("INSERT INTO rolepermission (SELECT NULL,'$roleid',page,permission FROM rolepermission WHERE roleid='$copyfrom') ");

  
    }




	

	
    $PAGE = [
        "Page Title" => "SLM | Edit Role",
        "Home Link"  => "/admin/",
        "Menu"		 => "role-showall",
        "MainMenu"	 => "user_menu",	

    ];

    
	$allPermissions = [];
	$allPermissionstrings = [];
	$dumPermission = [];

	

	$result = runQuery("SELECT * FROM rolepermission WHERE roleid='$roleid'");

	while($row = $result->fetch_assoc())
	{
		array_push($allPermissionstrings,$row['page']);
		$dumPermission[$row['page']] = $row['permission'];
	}


	sort($allPermissionstrings,SORT_REGULAR);


	foreach($allPermissionstrings as $path) {
	    $parts = explode('/', $path);
	    array_shift($parts);
	    $cur = &$allPermissions;
	    foreach($parts as $part) {
	        if(!key_exists($part, $cur)) {
	            $cur[$part] = [ 'children' => [],'fullpath' => $path, 'permission'=> $dumPermission[$path]];
	        }

	        $cur = &$cur[$part]['children'];
	        //$cur = &$cur[$part]['children2'];
	    }
	    unset($cur);
	}


	


	//echo "<pre>";
    //print_r($allPermissions);
    //die();



    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

    if($show_alert)
    {
    	echo $alert;
    }







?>

<link rel="stylesheet" type="text/css" href="/pages/css/pickletree.css">



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-user bg-c-blue"></i>
				<div class="d-inline">
					<h2>Edit Role (<?php echo $rolename; ?>)</h2>
					<span>Edit Role Permissions</span>
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
		<h5>Permissions</h5>
			<div class="card-header-right">

			</div>
		</div>
		<div class="card-block">


			<table class="table table-bordered" style="font-weight: bold;">
				
				<?php 

				foreach ($allPermissions as $key => $value) {
					
					?>

						<tr>
							<td style="bold"><?php echo strtoupper($key) ?></td>
						</tr>


					<?php
					echo print_tree($value['children']);
				}

				?>


			</table>


		

		</div>
	</div>

	

</div>



<div class="col-md-6">
	<div class="card">
		<div class="card-header">
		<h5>Copy Permissions</h5>
			<div class="card-header-right">

			</div>
		</div>
		<div class="card-block">
			<form method="POST">
				<input type="hidden" name="roleid" value="<?php echo $roleid; ?>">
			<div class="form-group row">
				<label class="col-md-4 col-form-label">Copy From: </label>
				<div class="col-md-8">
				
				<select required name='copyfrom' class="form-control">
					<?php 

						$result = runQuery("SELECT * FROM roles WHERE (roleid<>'ADMIN' AND roleid<>'$roleid')");

						while($row = $result->fetch_assoc())
						{
					?>

						<option value="<?php echo $row['roleid'] ?>"><?php echo $row['rolename'] ?></option>

					<?php
						}
					?>
				</select>
				</div>
			</div>

			<div class="form-group row">
				
				<div class="col-md-12">
				<button type="submit" name="copypermission" id="submitBtn" class="btn btn-primary pull-right"><i class="fa fa-copy"></i>Copy Permission</button>
				</div>
			
			</div>

			</form>

		</div>
	</div>

	

</div>


<div class="col-md-6">
	<div class="card">
		<div class="card-header">
		<h5>Tree View</h5>
			<div class="card-header-right">

			</div>
		</div>
		<div class="card-block">
	
		<div id="div_tree" class="tree"></div>		
		</div>
	</div>

	

</div>



</div>
</div>

</div>
</div>
</div>
</div>


<script src="/pages/js/pickletree.js" type="text/javascript"></script>
<script>
    const tree = new PickleTree({
        c_target: 'div_tree',
        rowCreateCallback: (node) => {
            //console.log(node)
        },
        switchCallback: (node) => {
            //console.log(node)
        },
        drawCallback: () => {
            //console.log('tree drawed ..');
        },
        dragCallback: (node) => {
            console.log(node);
        },
        dropCallback: (node) => {
            //retuns node with new parent and old parent in 'old_parent' key!!
            console.log(node);
        },
        c_config: {
            //start as folded or unfolded
            foldedStatus: false,
            //for logging
            logMode: false,
            //for switch element
            switchMode: true,
            //for automaticly select childs
            autoChild: true,
            //for automaticly select parents
            autoParent: true,
            //for drag / drop
            drag: false,
            //for ordering
            order: true
        },
        c_data: [{
            n_id: 1,
            n_title: 'User',
            n_parentid: 0,
            n_order_num : 0,
            n_checked: true,
           
        },  {
            n_id: 3,
            n_title: 'Dispatch',
            n_parentid: 1,
            n_order_num : 0,
        }, {
            n_id: 4,
            n_order_num : 0,
            n_title: 'Premix',
            n_parentid: 1
        }, {
            n_id: 5,
            n_order_num : 0,
            n_title: 'Process',
            n_parentid: 1
        }, {
            n_id: 10,
            n_order_num : 0,
            n_title: 'Settings',
            n_parentid: 1
        }, {
            n_id: 11,
            n_order_num : 0,
            n_title: 'Store',
            n_parentid: 1
        }, {
            n_id: 6,
            n_order_num : 0,
            n_title: 'Batch',
            n_parentid: 5
        }, {
            n_id: 7,
            n_order_num : 0,
            n_title: 'New Dispatch',
            n_parentid: 3
        }, {
            n_id: 8,
            n_order_num : 0,
            n_title: 'New Loading Advice',
            n_parentid: 3
        }, {
            n_id: 9,
            n_order_num : 0,
            n_title: 'Additive Fifo',
            n_parentid: 4
        }]
    });
    /*for (let i = 0; i < 3; i++) {
        tree.createNode('Falan_' + i, i);
        for (let t = 0; t < 3; t++) {
            tree.createNode('Falan1_' + t + i, t + '_' + i + '_sub1', [], tree.getNode('node_' + i));
            for (let j = 0; j < 3; j++) {
                tree.createNode('Falan1_' + t + i, t + '_' + i + '_sub2', [], tree.getNode('node_' + t + '_' + i + '_sub1'));
            }
        }
    }*/
</script>



<script type="text/javascript" src="/pages/js/jquery.min.js"></script>




<?php
    
    include("../../pages/endbody.php");

    function print_tree($children,$level=1)
    {
    	$allData = "";
    	global $roleid;
    	foreach ($children as $key => $value) {
    		

    		if(count($value['children'])==0)
    		{
    			$dumData = "<tr>";

    			
    			for($i=0;$i<$level;$i++)
    			{
    				$dumData = $dumData."<td></td>";
    			}
    			



    			
    			if($value['permission'])
    			{
    				if($value['permission']=="ALLOW")
    				{
    					$dumData = $dumData."<td><form method=\"POST\"><input type='hidden' name='path' value='".$value['fullpath']."'><input type='hidden' name='newpermission' value='DENY'><input type='hidden' name='roleid' value='$roleid'>".strtoupper($key)."<button name=\"editpermission\" type='submit' style=\"width: 120px;\" class=\"btn btn-success pull-right\"><i class=\"feather icon-check\"></i>ALLOWED</button></form></td>";
    				}
    				else
    				{
    					$dumData = $dumData."<td><form method=\"POST\"><input type='hidden' name='path' value='".$value['fullpath']."'><input type='hidden' name='newpermission' value='ALLOW'><input type='hidden' name='roleid' value='$roleid'>".strtoupper($key)."<button name=\"editpermission\" type='submit' style=\"width: 120px;\" class=\"btn btn-primary pull-right\"><i class=\"feather icon-slash\"></i>DENIED</button></form></td>";
    				}
    				

    			}
    			else
    			{
    				$dumData = $dumData."<td>".strtoupper($key)."</td>";

    			}

    			$dumData = $dumData."</tr>";
    			$allData = $allData .$dumData;

    		}
    		else
    		{
    			$dumData = "<tr>";

    			for($i=0;$i<$level;$i++)
    			{
    				$dumData = $dumData."<td></td>";
    			}

    			$dumData = $dumData."<td>".strtoupper($key)."</td>";
    			$dumData = $dumData."</tr>";
    			$allData = $allData .$dumData.print_tree($value['children'],$level+1);
    		}

    	}

    	return $allData;
    }

?>

