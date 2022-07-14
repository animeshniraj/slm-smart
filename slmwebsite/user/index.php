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
        "Page Title" => "SLM | Welcome to SMART",
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
				<img src="/pages/png/user.gif" alt="Logged User" class="logged-icon">
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

<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Melting</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/melting-new.php">New Melting</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/melting-view.php">View Melting</a>
      </div>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Melting ID</th>
                <th>Date & Time</th>
                <th>Furnace</th>
                <th>Day Heat No.</th>
				
              </tr>
            </thead>
            <tbody>

    <?php
        $result = runQuery("SELECT * FROM processentry LEFT JOIN processentryparams ON processentryparams.processid=processentry.processid WHERE processentry.processname = 'Melting' AND processentryparams.param='Furnace' ORDER BY entrytime DESC LIMIT 3");
        if($result->num_rows>0)
        {
          $k=0;
          while($row=$result->fetch_assoc())
          {
    ?>
              <tr>
                <td><?php echo $row["processid"]; ?></td>
                <?php
                $ccid = $row["processid"];
                
                  $dval = runQuery("SELECT * FROM processentryparams WHERE processid='$ccid' AND param='Heat On Time'")->fetch_assoc()['value'];
                ?>
                <td><?php echo Date('Y-M-d h:i A',strtotime($dval)); ?></td>
                <td><?php echo $row["value"]; ?></td>
                <?php
    
    
                  $dval = runQuery("SELECT * FROM processentryparams WHERE processid='$ccid' AND param='Heat No.'")->fetch_assoc()['value'];
                ?>
                <td><?php echo $dval; ?></td>
				      
              </tr>
              <?php
    }}
  ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Raw Bag</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Raw Bag ID</th>
                <th>Date & Time</th>
                <th>Raw Bag No.</th>
                <th>Grade</th>
				<th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>22/06-R-010</td>
                <td>22/06/2022 8:00 PM</td>
                <td>04</td>
                <td>RAW AIP-100</td>
				<td>2000kg</td>
              </tr>
              <tr>
				<td>22/06-R-009</td>
                <td>22/06/2022 8:00 PM</td>
                <td>04</td>
                <td>RAW AIP-100</td>
				<td>2000kg</td>
              </tr>
              <tr>
				<td>22/06-R-004</td>
                <td>22/06/2022 8:00 PM</td>
                <td>04</td>
                <td>RAW AIP-100</td>
				<td>2000kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Raw Blend</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Raw Blend ID</th>
                <th>Date & Time</th>
                <th>Blend No.</th>
                <th>Grade</th>
				<th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>22/06-BS-070</td>
                <td>22/06/2022 8:00 PM</td>
                <td>02</td>
                <td>RAW SIP-100</td>
				<td>2000kg</td>
              </tr>
              <tr>
                <td>22/06-BA-002</td>
                <td>22/06/2022 8:00 PM</td>
                <td>02</td>
                <td>RAW SIP-100</td>
				<td>2000kg</td>
              </tr>
              <tr>
                <td>22/06-BS-069</td>
                <td>22/06/2022 8:00 PM</td>
                <td>02</td>
                <td>RAW SIP-60</td>
				<td>2000kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Annealing</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Annealing ID</th>
                <th>Date & Time</th>
                <th>Furnace</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>22/06-AF1-029</td>
                <td>22/06/2022 8:00 PM</td>
                <td>AF1</td>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-AF1-029</td>
                <td>22/06/2022 8:00 PM</td>
                <td>AF1</td>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-AF1-029</td>
                <td>22/06/2022 8:00 PM</td>
                <td>AF1</td>
                <td>6897 Kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Semi Finished</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Semi<br>Finished ID</th>
                <th>Date & Time</th>
                <th>Bin No.</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>22/06-A-149</td>
                <td>22/06/2022 8:00 PM</td>
                <td>14</td>
				<td>AIP -80 PV</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-A-150</td>
                <td>22/06/2022 8:00 PM</td>
                <td>14</td>
				<td>AIP -80 PV</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-A-151</td>
                <td>22/06/2022 8:00 PM</td>
                <td>14</td>
				<td>AIP -40 PV</th>
                <td>6897 Kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Final Blend</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Final<br>Blend ID</th>
                <th>Date & Time</th>
                <th>Blend No.</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>22/06-TF-026</td>
                <td>22/06/2022 8:00 PM</td>
                <td>1</td>
				<td>F-100</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-TF-026</td>
                <td>22/06/2022 8:00 PM</td>
                <td>1</td>
				<td>F-100</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>22/06-TF-026</td>
                <td>22/06/2022 8:00 PM</td>
                <td>1</td>
				<td>F-100</th>
                <td>6897 Kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Batches</h5>
	  	<div class="card-header-right">
			<ul class="list-unstyled card-option">
			<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
			<li><i class="feather icon-maximize full-card"></i></li>
			<li><i class="feather icon-minus minimize-card"></i></li>
			<li><i class="feather icon-chevron-left open-card-option"></i></li>
			</ul>
		</div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Batch ID</th>
                <th>Date</th>
                <th>Final Blend ID</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>T22IP04070</td>
                <td>22/06/2022</td>
                <td>22/04-TF-043</td>
				<td>SLM 100.29 CUTTING0</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>T22IP04070</td>
                <td>22/06/2022</td>
                <td>22/04-TF-043</td>
				<td>SLM 100.29 CUTTING0</th>
                <td>6897 Kg</td>
              </tr>
              <tr>
                <td>T22IP04070</td>
                <td>22/06/2022</td>
                <td>22/04-TF-043</td>
				<td>SLM 100.29 CUTTING0</th>
                <td>6897 Kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="col-xl-6 col-md-6">
  <div class="card table-card">
    <div class="card-header">
      <h5>Recent Orders</h5>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Company</th>
                <th>PO Date<br>& Number</th>
                <th>Total<br>Qty</th>
                <th>Dispatched<br>Qty</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Apple Company</td>
                <td>23/05/2022 - T-12.05.2022</td>
                <td>120000kg</td>
                <td>120000kg</td>
                <td>
                  <label class="label label-success">Closed</label>
                </td>
              </tr>
              <tr>
                <td>Apple Company</td>
                <td>23/05/2022 - T-13.05.2022</td>
                <td>120000kg</td>
                <td>120000kg</td>
                <td>
                  <label class="label label-success">Closed</label>
                </td>
              </tr>
              <tr>
                <td>Google Company</td>
                <td>23/05/2022 - T-14.05.2022</td>
                <td>120000kg</td>
                <td>20000kg</td>
                <td>
                  <label class="label label-danger">Open</label>
                </td>
              </tr>
              <tr>
                <td>Apple Company</td>
                <td>23/05/2022 - T-18.04.2022</td>
                <td>120000kg</td>
                <td>120000kg</td>
                <td>
                  <label class="label label-success">Closed</label>
                </td>
              </tr>
              <tr>
                <td>Apple Company</td>
                <td>23/05/2022 - T-12.05.2022</td>
                <td>120000kg</td>
                <td>0kg</td>
                <td>
                  <label class="label label-danger">Open</label>
                </td>
              </tr>
              <tr>
                <td>Apple Company</td>
                <td>23/05/2022 - T-12.05.2022</td>
                <td>120000kg</td>
                <td>0kg</td>
                <td>
                  <label class="label label-danger">Open</label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>





<div class="col-lg-3">

	<div class="card">
		<div class="card-header">
			<h5>Dispatch</h5>
			<i class="fa fa-truck"></i>
		</div>


		<div class="card-block">
		<a class="btn waves-effect waves-light hor-grd btn-grd-primary btn-block" href="/user/dispatch/new-purchase.php">Create New Purchase Order</a>
		<a class="btn waves-effect waves-light hor-grd btn-grd-primary btn-block" href="/user/dispatch/dispatch-report.php">Check Dispatch Report</a>
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
