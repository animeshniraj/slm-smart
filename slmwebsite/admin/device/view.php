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

	$myuserid = $session->user->getUserid();

	if(isset($_POST["deletedevice"]))
	{
		$devicename  = $_POST["devicename"];

		runQuery("DELETE FROM devices WHERE devicename='$devicename'");
		
	}


	if(isset($_POST["editdevice"]))
	{
		$devicename  = $_POST["devicename"];
		$deviceip  = $_POST["deviceip"];
		$hostname  = $_POST["hostname"];

		runQuery("UPDATE devices SET deviceip='$deviceip', hostname='$hostname' WHERE devicename='$devicename'");
		
	}

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "device-view",
        "MainMenu"	 => "device_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

?>


<script src="/pages/js/devices.js"></script>

<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-airplay bg-c-blue"></i>
				<div class="d-inline">
					<h5>All Devices</h5>
					<span>View all devices</span>
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
<h5>Sample Block</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">
<style type="text/css">
	.devices_css{
		
		display: flex;
		justify-content: center;
		flex-wrap: wrap;


	}

	.device_block{
		border: 2px solid #4099ff;
		border-radius: 10px;
		padding: 10px;
		margin: 20px;
		width: 400px;
		min-height: 150px;
		overflow: hidden;

		
	}


	.device_status{
		position: relative;
		bottom: 0px;
		margin-left: -25px;
		margin-bottom: -10px;
		width: 420px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		
	}

	.online
	{
		background-color: rgba(0,128,0,0.8);
		font-weight: bold;
		color: white;
	}
	.offline
	{
		
		background-color: rgba(255,0,0,0.75);
		font-weight: bold;
		color: white;
	}
	.waiting
	{
		background-color: rgba(253,213,0,0.9);

		font-weight: bold;
		color: black;
	}
</style>
<script type="text/javascript">
	function changeStatus(inobj,status)
{
	inobj.classList.remove('offline');
	inobj.classList.remove('waiting');
	inobj.classList.remove('online');
	

	if(status=="WAITING")
	{
		inobj.classList.add('waiting');
		inobj.innerHTML = "<i class=\"fa fa-circle-o-notch rotate-refresh \"></i> ATTEMPTING TO CONNECT"
	}
	else if(status=="ONLINE")
	{
		inobj.classList.add('online');
		inobj.innerHTML = "<i class=\"fa fa-link \"></i> ONLINE"
	}
	else if(status=="OFFLINE")
	{
		inobj.classList.add('offline');
		inobj.innerHTML = "<i class=\"fa fa-unlink \"></i> OFFLINE"
	}
}
</script>

<div class="devices_css">

	<?php 

			$result = runQuery("SELECT * FROM devices");
			if($result->num_rows>0)
			{

				while($row=$result->fetch_assoc())
				{



	?>

<div class="device_block">
  <?php

 	if($row["type"]=="SCALE")
 	{
 		echo "<p style=\"text-align:center\"><img src=\"/pages/svg/scale.svg\" width=\"40px;\"></p><h3 style=\"text-align: center; font-weight:bold; \">".$row["devicename"]."</h3>";
 	}
 	else if($row["type"]=="PRINTER")
 	{
 		echo "<p style=\"text-align:center\"><img src=\"/pages/svg/printer.svg\" width=\"40px;\"></p><h3 style=\"text-align: center; font-weight:bold; \">".$row["devicename"]."</h3>";
 	}



  ?>
  <br>
  <table>
  	<tr>
  		<th>Hostname:</th>
  		<td style="padding-left: 10px;"><?php echo $row["hostname"]; ?></td>
  	</tr>
  	<tr>
  		<th>Device IP:</th>
  		<td style="padding-left: 10px;"><?php echo $row["deviceip"]; ?></td>
  	</tr>

  </table>

<br>
<div style="display: flex; justify-content: space-around;">
	<button class="btn btn-primary btn-round" onclick="editDevice('<?php echo $row["devicename"]; ?>','<?php echo $row["hostname"]; ?>','<?php echo $row["deviceip"]; ?>')"><i class="fa fa-edit"></i>Edit</button>

	<button class="btn btn-warning btn-round" onclick="testDevice('<?php echo $row["devicename"]; ?>','<?php echo $row["hostname"]; ?>','<?php echo $row["deviceip"]; ?>')"><i class="fa fa-tachometer"></i>Test</button>

		<button class="btn btn-inverse btn-round" onclick="rebootDevice('<?php echo $row["devicename"]; ?>','<?php echo $row["deviceip"]; ?>','device-<?php echo str_replace(" ","",$row["devicename"])?>')"><i class="fa fa-refresh"></i>Reboot</button>

	<button class="btn btn-danger btn-round" onclick="deleteDevice('<?php echo $row["devicename"]; ?>')"><i class="fa fa-trash"></i>Delete</button>

</div>

<br>

  <div id="device-<?php echo str_replace(" ","",$row["devicename"])?>" class="device_status">
  	
  </div>
  <script type="text/javascript">
  	var dumObj = document.getElementById("device-<?php echo str_replace(" ","",$row["devicename"])?>");
  	changeStatus(dumObj,"WAITING");

  	getDeviceStatus('<?php echo $row["devicename"]; ?>','<?php echo $row["deviceip"]; ?>','<?php echo $DEVICE_KEY?>',document.getElementById("device-<?php echo str_replace(" ","",$row["devicename"])?>"));

  	setInterval(function(){
  		changeStatus(document.getElementById("device-<?php echo str_replace(" ","",$row["devicename"])?>"),"WAITING");
  		getDeviceStatus('<?php echo $row["devicename"]; ?>','<?php echo $row["deviceip"]; ?>','<?php echo $DEVICE_KEY?>',document.getElementById("device-<?php echo str_replace(" ","",$row["devicename"])?>"));
  	},30000)

  </script>

</div>


	<?php 
		}}
	?>

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

<div class="modal fade" id="testdevicemodal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Testing Device</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body" id="testdevicemodalbody">





</div>
<div class="modal-footer">
<button type="button" class="btn btn-default waves-effect " data-dismiss="modal" onclick="endtest()">Close</button>

</div>
</div>
</div>
</div>



<?php
    
    include("../../pages/endbody.php");

?>

<script type="text/javascript">

function editDevice(devicename,hostname,deviceip)
{
	Swal.fire({
		  
		  title: 'Edit '+devicename,
		  html: '<div class="form-group row"><label class="col-sm-4">Hostname</label><input value="'+hostname+'" id="edit-hostname" class="form-control col-sm-8" type="text"></div><div class="form-group row"><label class="col-sm-4">Device IP</label><input id="edit-deviceip" value="'+deviceip+'" class="form-control col-sm-8" type="text"></div>',
		  confirmButtonText: 'Confirm',
		  cancelButtonText: 'Cancel',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		var form  = document.createElement("form");
			  		form.setAttribute("method","POST");

			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","devicename");
			  		input.setAttribute("value",devicename);
			  		form.appendChild(input);

			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","hostname");
			  		input.setAttribute("value",document.getElementById('edit-hostname').value);
			  		form.appendChild(input);

			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","deviceip");
			  		input.setAttribute("value",document.getElementById('edit-deviceip').value);
			  		form.appendChild(input);

			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","editdevice");
			  		input.setAttribute("value","");
			  		form.appendChild(input);

			  		document.body.appendChild(form);
			  		form.submit();

				}
			})
}

function rebootDevice(devicename,deviceip,inobj)
{
	var clientpass ='<?php echo $DEVICE_KEY?>';
	Swal.fire({
		  icon: 'question',
		  title: 'Reboot Device',
		  html: 'Are you sure you want to reboot '+devicename,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		
			  		rebootDeviceNow(devicename,deviceip,clientpass,inobj);
			  		

				}
			})
}



let testinterval ;
function testDevice(devicename,hostname,deviceip)
{
	$("#testdevicemodal").modal('show');
	 consoleobj = document.getElementById('testdevicemodalbody');
	 consoleobj.innerHTML = "Starting Test on "+devicename+"<br>"

	 
	 testinterval = setInterval(function(){

	 	testDeviceOnce(devicename,deviceip)


	 },3000)
	 

}

function testDeviceOnce(devicename,ip)
{	


					var clientpass ='<?php echo $DEVICE_KEY?>';
					var postData = new FormData();
           consoleobj = document.getElementById('testdevicemodalbody');
            
         
            var xmlhttp = new XMLHttpRequest();
            consoleobj.innerHTML += "Testing..<br>Reading requested<br>"
           
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4)
              {
              	if(this.status == 200) {
	                 
	                var data = JSON.parse(this.responseText);
	                
	                if(data.Status=="ONLINE")
	                {
	                    
	                	consoleobj.innerHTML += "Reading Received-><br>"+data.Data+"<br>End of Reading<br>"
	                }
	                else
	                {
	                	consoleobj.innerHTML += "Error Received-><br>"+data.Error+"<br>End of Error<br>"
	                }
                
                
            	}
            	
            	
              
            }
            };
             xmlhttp.timeout = 5000;
             
            xmlhttp.open("POST", "http://"+ip+"/read", true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send("clientpass="+clientpass);
}
	
function deleteDevice(devicename)
{
	Swal.fire({
		  icon: 'error',
		  title: 'Delete Device',
		  html: 'Are you sure you want to delete '+devicename,
		  confirmButtonText: 'Yes',
		  cancelButtonText: 'No',
		  showCancelButton: true,
		  
		}).then((result) => {
			  if (result.isConfirmed) {
			    		

			  		var form  = document.createElement("form");
			  		form.setAttribute("method","POST");
			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","devicename");
			  		input.setAttribute("value",devicename);
			  		form.appendChild(input);

			  		var input = document.createElement("input");
			  		input.setAttribute("type","hidden");
			  		input.setAttribute("name","deletedevice");
			  		input.setAttribute("value","");
			  		form.appendChild(input);

			  		document.body.appendChild(form);
			  		form.submit();

				}
			})
}

function endtest()
{
	clearInterval(testinterval);
}


</script>