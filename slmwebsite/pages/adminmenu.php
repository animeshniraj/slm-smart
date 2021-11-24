<?php


?>


<div class="pcoded-main-container">
<div class="pcoded-wrapper">

<nav class="pcoded-navbar">
<div class="nav-list">

<div class="pcoded-inner-navbar main-menu">
<div class="pcoded-navigation-label">Navigation</div>


<ul class="pcoded-item pcoded-left-item">


<li id="dashboard_menu">
<a href="/admin/" class="waves-effect waves-dark">
<span class="pcoded-micon">
<i class="feather icon-home"></i>
</span>
<span class="pcoded-mtext">Dashboard</span>
</a>
</li>


<li id="user_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="feather icon-user"></i></span>
<span class="pcoded-mtext">Users</span>
</a>
	<ul class="pcoded-submenu">
		<li id="user-createuser" class="">
		<a href="/admin/user/createnew.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Create New</span>
		</a>
		</li>

		<li id="user-showall" class="">
		<a href="/admin/user/showall.php" class="waves-effect waves-dark">
		<span class="pcoded-mtext">All Users</span>
		</a>
		</li>

	</ul>
</li>


<li id="processmanager_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="feather icon-command"></i></span>
<span class="pcoded-mtext">Process Manager</span>
</a>
	<ul class="pcoded-submenu">
		
		<li id="processmanager-rawmaterials" class="">
		<a href="/admin/processmanager/rawmaterials.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Raw Materials</span>
		</a>
		</li>

		<li id="processmanager-melting" class="">
		<a href="/admin/processmanager/melting.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Melting</span>
		</a>
		</li>


		<li id="processmanager-rawbag" class="">
		<a href="/admin/processmanager/rawbag.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Raw Bag</span>
		</a>
		</li>

		<li id="processmanager-rawblend" class="">
		<a href="/admin/processmanager/rawblend.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Raw Blend</span>
		</a>
		</li>


		<li id="processmanager-annealing" class="">
		<a href="/admin/processmanager/annealing.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Annealing</span>
		</a>
		</li>


		<li id="processmanager-semifinished" class="">
		<a href="/admin/processmanager/semifinished.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Semi Finished</span>
		</a>
		</li>


		<li id="processmanager-finalblend" class="">
		<a href="/admin/processmanager/finalblend.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Final Blend</span>
		</a>
		</li>



		

	</ul>
</li>


<li id="premix_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="fa fa-flask"></i></span>
<span class="pcoded-mtext">Premix</span>
</a>
	<ul class="pcoded-submenu">
		<li id="premix-additives" class="">
		<a href="/admin/premix/additives.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Additives</span>
		</a>
		</li>


		<li id="premix-additives-forcast" class="">
		<a href="/admin/premix/additive-forcast.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Additives Requirement Forcast</span>
		</a>
		</li>

		<li id="premix-grades" class="">
		<a href="/admin/premix/grades-new.php" class="waves-effect waves-dark">
		<span class="pcoded-mtext">Grades</span>
		</a>
		</li>

	</ul>
</li>




<li id="processgrade_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="fa fa-pencil-square"></i></span>
<span class="pcoded-mtext">Grades</span>
</a>
	<ul class="pcoded-submenu">
		

		<li id="processgrade-melting" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Melting</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-melting-prop" class="">
			<a href="/admin/grade/editprop.php?process=Melting" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-melting-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Melting" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>


		<li id="processgrade-rawbag" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Raw Bag</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-rawbag-prop" class="">
			<a href="/admin/grade/editprop.php?process=Raw Bag" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-rawbag-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Raw Bag" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>

		<li id="processgrade-rawblend" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Raw Blend</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-rawblend-prop" class="">
			<a href="/admin/grade/editprop.php?process=Raw Blend" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-rawblend-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Raw Blend" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>


		<li id="processgrade-annealing" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Annealing</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-annealing-prop" class="">
			<a href="/admin/grade/editprop.php?process=Annealing" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-annealing-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Annealing" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>



		<li id="processgrade-semifinished" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Semi Finished</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-semifinished-prop" class="">
			<a href="/admin/grade/editprop.php?process=Semi Finished" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-semifinished-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Semi Finished" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>


		<li id="processgrade-finalblend" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Final Blend</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="processgrade-finalblend-prop" class="">
			<a href="/admin/grade/editprop.php?process=Final Blend" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Properties</span>
			</a>
			</li>
			<li id="processgrade-finalblend-edit" class="">
			<a href="/admin/grade/editgrade.php?process=Final Blend" class="waves-effect waves-dark">
			<span class="pcoded-mtext">Edit Grades</span>
			</a>
			</li>
		</ul>
		</li>

		<li id="processgrade-sieve" class="">
		<a href="/admin/grade/sieve.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Sieves</span>
		</a>
		</li>









	

	</ul>
</li>




<li id="external_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="fa fa-users"></i></span>
<span class="pcoded-mtext">External</span>
</a>
	

	<ul class="pcoded-submenu">
		

		<li id="external-supplier" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Suppliers</span>
		</a>

		<ul class="pcoded-submenu">

			<li id="external-supplier-new" class="">
			<a href="/admin/external/suppliers/new.php" class="waves-effect waves-dark">
			<span class="pcoded-mtext">New Supplier</span>
			</a>
			</li>

			<li id="external-supplier-view" class="">
			<a href="/admin/external/suppliers/view.php" class="waves-effect waves-dark">
			<span class="pcoded-mtext">View Suppliers</span>
			</a>
			</li>
			
		</ul>
		</li>



	</ul>



	<ul class="pcoded-submenu">
		

		<li id="external-customer" class="pcoded-hasmenu ">
		<a href="javascript:void(0)" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Customers</span>
		</a>

		<ul class="pcoded-submenu">
			<li id="external-customer-new" class="">
			<a href="/admin/external/customers/new.php" class="waves-effect waves-dark">
			<span class="pcoded-mtext">New Customer</span>
			</a>
			</li>
			<li id="external-customer-view" class="">
			<a href="/admin/external/customers/view.php" class="waves-effect waves-dark">
			<span class="pcoded-mtext">View Customers</span>
			</a>
			</li>
		</ul>
		</li>



	</ul>
</li>


<li id="dispatch_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="fa fa-archive"></i></span>
<span class="pcoded-mtext">Dispatch</span>
</a>
	<ul class="pcoded-submenu">
		<li id="dispatch-package" class="">
		<a href="/admin/dispatch/package.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Package</span>
		</a>
		</li>


	</ul>
</li>




<li id="message_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="feather icon-mail"></i></span>
<span class="pcoded-mtext">Messages</span>
</a>
	<ul class="pcoded-submenu">
		<li id="message-compose" class="">
		<a href="/admin/message/compose.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Compose New</span>
		</a>
		</li>

		<li id="message-inbox" class="">
		<a href="/admin/message/inbox.php" class="waves-effect waves-dark">
		<span class="pcoded-mtext">Inbox</span>
		</a>
		</li>

	</ul>
</li>



<li id="device_menu" class="pcoded-hasmenu" dropdown-icon="style1" subitem-icon="style1">
<a href="javascript:void(0)" class="waves-effect waves-dark">
<span class="pcoded-micon"><i class="feather icon-airplay"></i></span>
<span class="pcoded-mtext">Devices</span>
</a>
	<ul class="pcoded-submenu">
		<li id="device-new" class="">
		<a href="/admin/device/new.php" class="waves-effect waves-dark">

		<span class="pcoded-mtext">Add New Device</span>
		</a>
		</li>

		<li id="device-view" class="">
		<a href="/admin/device/view.php" class="waves-effect waves-dark">
		<span class="pcoded-mtext">View Devices</span>
		</a>
		</li>

	</ul>
</li>



<li id="logs_menu">
<a href="/admin/logs.php" class="waves-effect waves-dark">
<span class="pcoded-micon">
<i class="fa fa-book"></i>
</span>
<span class="pcoded-mtext">Logs</span>
</a>
</li>

<li id="settings_menu">
<a href="/admin/settings.php" class="waves-effect waves-dark">
<span class="pcoded-micon">
<i class="feather icon-settings"></i>
</span>
<span class="pcoded-mtext">Settings</span>
</a>
</li>

<li>
<a href="/user/" class="waves-effect waves-dark">
<span class="pcoded-micon">
<i class="feather icon-log-out"></i>
</span>
<span class="pcoded-mtext">Go to User Panel</span>
</a>
</li>

</ul>


</div>
</div>
<script type="text/javascript">
	
	document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("active");

	if("<?php echo $PAGE["MainMenu"] ?>")
	{
		document.getElementById("<?php echo $PAGE["MainMenu"] ?>").classList.add("active");
		document.getElementById("<?php echo $PAGE["MainMenu"] ?>").classList.add("pcoded-trigger");
	}

</script>
</nav>