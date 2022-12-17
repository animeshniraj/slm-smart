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
            <?php 

              $dumdata  = load_process_data("Melting");
            ?>
            <h6 class="m-b-5 text-white">Total Melting - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Melting batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/melting.png" style="width:50px;">
		  </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-blue">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_process_data("Raw Bag");
            ?>
            <h6 class="m-b-5 text-white">Total Raw Bags - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Raw Bag batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/raw-bag.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-green">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_process_data("Raw Blend");
            ?>
		  <h6 class="m-b-5 text-white">Total Raw Blends - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Raw Blends till date</span>
          </div>
          <div class="col-auto">
			<img src="img/raw-blend.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-yellow">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_process_data("Annealing");
            ?>
		  <h6 class="m-b-5 text-white">Total Annealing - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Annealing batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/annealing.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-yellow">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_process_data("Semi Finished");
            ?>
		  <h6 class="m-b-5 text-white">Total Semi Finished - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Semi Finished batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/semi-finished.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>


  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-blue">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_process_data("Batch");
            ?>
		  <h6 class="m-b-5 text-white">Total Batch - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Final Batches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/final-blend.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
        </p>
      </div>
    </div>
  </div>


  <div class="col-xl-3 col-md-6">
    <div class="card prod-p-card card-green">
      <div class="card-body">
        <div class="row align-items-center m-b-30">
          <div class="col">
            <?php 

              $dumdata  = load_dispatch_data();
            ?>
		  <h6 class="m-b-5 text-white">Total Dispatches - <?php echo date('M Y'); ?></h6>
            <h3 class="m-b-0 f-w-700 text-white"><?php echo $dumdata[0]; ?> kgs</h3>
			<span><?php echo $dumdata[1]; ?> Dispatches till date</span>
          </div>
          <div class="col-auto">
			<img src="img/dispatch.png" style="width:50px;">
          </div>
        </div>
        <p class="m-b-0 text-white">
          <span class="label <?php if($dumdata[2]>0){echo "label-success";} else{echo "label-danger";} ?> m-r-10"><?php echo $dumdata[2]; ?>%</span>From Previous Month
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


    function load_process_data($processname)
    {

      global $MASS_TITLE;
      $total_prod = 0;
      $total_num = 0;
      $change = "-";

      $thismonth = intval(date('m', strtotime("this month")));
      $thisyear = date('Y', strtotime("this month"));

      $prevmonth = intval(date('m', strtotime("last month")));
      $prevyear = date('Y', strtotime("last month"));

      $result = runQuery("SELECT * FROM processentry WHERE MONTH(entrytime) = $thismonth AND YEAR(entrytime) = $thisyear AND processname='$processname'");
      $total_num = $result->num_rows;

      $result = runQuery("SELECT SUM(value) as val FROM processentryparams WHERE param='$MASS_TITLE' AND processid IN (SELECT processid FROM processentry WHERE MONTH(entrytime) = $thismonth AND YEAR(entrytime) = $thisyear AND processname='$processname')");
  
      $total_prod = round($result->fetch_assoc()['val']);


      $result = runQuery("SELECT SUM(value) as val FROM processentryparams WHERE param='$MASS_TITLE' AND processid IN (SELECT processid FROM processentry WHERE MONTH(entrytime) = $prevmonth AND YEAR(entrytime) = $prevyear AND processname='$processname')");
  
      $total_prod_last_month = round($result->fetch_assoc()['val']);

      if($total_num>0 && $total_prod_last_month)
      {
        $change = round((($total_prod-$total_prod_last_month)/$total_prod_last_month)*100);
      }

      

      return [$total_prod,$total_num,$change];
    }

    function load_dispatch_data()
    {
      $total_prod = 0;
      $total_num = 0;
      $change = "-";


      $thismonth = intval(date('m', strtotime("this month")));
      $thisyear = date('Y', strtotime("this month"));

      $prevmonth = intval(date('m', strtotime("last month")));
      $prevyear = date('Y', strtotime("last month"));

      $result = runQuery("SELECT * FROM dispatch WHERE MONTH(entrydate) = $thismonth AND YEAR(entrydate) = $thisyear ");
      $total_num = $result->num_rows;

      $result = runQuery("SELECT SUM(qty) as val FROM dispatch_invoices where cid IN (SELECT cid FROM dispatch WHERE MONTH(entrydate) = $thismonth AND YEAR(entrydate) = $thisyear)");
      $total_prod = round($result->fetch_assoc()['val']);

      $result = runQuery("SELECT SUM(qty) as val FROM dispatch_invoices where cid IN (SELECT cid FROM dispatch WHERE MONTH(entrydate) = $prevmonth AND YEAR(entrydate) = $prevyear)");
      $total_prod_last_month = round($result->fetch_assoc()['val']);

      if($total_num>0 && $total_prod_last_month)
      {
        $change = round((($total_prod-$total_prod_last_month)/$total_prod_last_month)*100);
      }

      return [$total_prod,$total_num,$change];
    }

?>