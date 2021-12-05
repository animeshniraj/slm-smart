<?php
    
	require_once('../../requiredlibs/includeall.php');

	
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
        "Page Title" => "SLM | User Dashboard",
        "Home Link"  => "/user/",
        "Menu"		 => "dashboard_menu",
        "MainMenu"	 => "",

    ];


    include("../pages/userhead.php");
    include("../pages/usermenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-activity bg-c-blue"></i>
				<div class="d-inline">
					<h3>Welcome <span><?php echo $session->user->getFullname(); ?></span></h3>
					<span>Your last login was on 10:59 29th Nov 2021</span>
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

<div class="col-lg-6">

	<div class="card">
		<div class="card-header">
			<h5>Sample Block</h5>
			<div class="card-header-right">
				Some Information
			</div>
		</div>


		<div class="card-block">
			<p> I do most of my writing in the early hours of the morning, and I liked both the mental clarity I got from not eating,
				as well as the little bit of extra time from not having to make breakfast. After anywhere from 2–3 hours of writing, I would eat something.</p>

			<p>And I loved it — my thinking was clear, I had no side effects. I ate good-quality food for the rest of the day. 
				And I thought I was doing my body a favor. After all, the research tells us that fasting is good. 
				And I do truly believe that fasting is a beneficial tool. Just not for everyone, and not all the time.</p>

		</div>

	</div>

</div>

<div class="col-lg-6">

	<div class="card">
		<div class="card-header">
			<h5>Recent Activity</h5>
			<div class="card-header-right">
				click to view
			</div>
		</div>


		<div class="card-block">
			<p><a href="">Melting: Some ID</a></p>
			<p><a href="">Melting: Some ID</a></p>
			<p><a href="">Melting: Some ID</a></p>

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
    
    include("../pages/endbody.php");

?>
