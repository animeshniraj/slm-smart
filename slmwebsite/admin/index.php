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

	isAuthenticated($session,'admin_module');

	

    $PAGE = [
        "Page Title" => "SLM | Welcome to SLM SMART Admin dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "dashboard_menu",
        "MainMenu"	 => "",

    ];


    include("../pages/adminhead.php");
    include("../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h3>Welcome <span><?php echo $session->user->getFullname(); ?></span></h3>
					<?php 
					$myuserid = $session->user->getUserid();
					$result = runQuery("SELECT * FROM loginlog WHERE userid='$myuserid' ORDER BY currtime DESC");
					$lasttime="";
		            if($result->num_rows>0)
		            {
		                $lasttime = $result->fetch_assoc()['lasttime'];

		            }


					?>
					<span>Your last login was on <?php echo Date('d-M-Y H:i',strtotime($lasttime)) ?> </span>
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
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-red">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <h6 class="m-b-5 text-white">Total Melting - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Melting batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/melting.png" style="width:50px;">
		  </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-danger m-r-10">-11%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-blue">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <h6 class="m-b-5 text-white">Total Raw Bags - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Raw Bag batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/raw-bag.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-primary m-r-10">+12%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-green">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
		  <h6 class="m-b-5 text-white">Total Raw Blends - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Raw Blends till date</span>
          </div>
          <div class="col-auto">
			<img src="img/raw-blend.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-success m-r-10">+52%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-yellow">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
		  <h6 class="m-b-5 text-white">Total Annealing - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Annealing batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/annealing.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-warning m-r-10">+52%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-yellow">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
		  <h6 class="m-b-5 text-white">Total Semi Finished - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Semi Finished batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/semi-finished.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-warning m-r-10">+52%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>


  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-blue">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
		  <h6 class="m-b-5 text-white">Total Final Blend - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">14568 kgs</h3>
			<span>12 Final Blend till date</span>
          </div>
          <div class="col-auto">
			<img src="img/final-blend.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-warning m-r-10">+52%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>


  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-green">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
		  <h6 class="m-b-5 text-white">Total Dispatches - July 2022</h6>
            <h3 class="m-b-0 f-w-700 text-white">15000000 kgs</h3>
			<span>12 Dispatches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/dispatch.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label label-warning m-r-10">+52%</span>From Previous Month
        </p>
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