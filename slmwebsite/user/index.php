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
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/rawbag-new.php">New Raw Bag</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/rawbag-view.php">View Raw Bags</a>
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
                <th>Raw Bag ID</th>
                <th>Date & Time</th>
                <th>Raw Bag No.</th>
                <th>Grade</th>
				<th>Qty</th>
              </tr>
            </thead>
            <tbody>


              <?php
              $cprocess = "Raw Bag";
              $result = runQuery("SELECT * FROM processentry WHERE processname='$cprocess' ORDER BY entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php echo getRawBagNo($cprocessid); ?></td>
                <td><?php echo getProcessGrade($cprocessid) ?></td>
				        <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Raw Blend</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/rawblend-new.php">New Raw Blend</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/rawblend-view.php">View Raw Blends</a>
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
                <th>Raw Blend ID</th>
                <th>Date & Time</th>
                <th>Blend No.</th>
                <th>Grade</th>
				<th>Qty</th>
              </tr>
            </thead>
            <tbody>
              
               <?php
              $cprocess = "Raw Blend";
              $result = runQuery("SELECT * FROM processentry WHERE processname='$cprocess' ORDER BY entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php echo getBlendID($cprocessid); ?></td>
                <td><?php echo getProcessGrade($cprocessid) ?></td>
                <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Annealing</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/annealing-new.php">New Annealing</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/annealing-view.php">View Annealing</a>
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
                <th>Annealing ID</th>
                <th>Date & Time</th>
                <th>Furnace</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $cprocess = "Annealing";
              $result = runQuery("SELECT * FROM processentry  LEFT JOIN processentryparams ON processentryparams.processid=processentry.processid WHERE processentry.processname = '$cprocess' AND processentryparams.param='Furnace' ORDER BY processentry.entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php echo $row["value"];  ?></td>
                <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Semi Finished</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/semifinished-new.php">New Semi Finished</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/semifinished-view.php">View Semi Finished</a>
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
                <th>Semi<br>Finished ID</th>
                <th>Date & Time</th>
                <th>Bin No.</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
               <?php
              $cprocess = "Semi Finished";
              $result = runQuery("SELECT * FROM processentry WHERE processname='$cprocess' ORDER BY entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php echo getBinNo($cprocessid); ?></td>
                <td><?php echo getProcessGrade($cprocessid) ?></td>
                <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Final Blend</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/finalblend-new.php">New Final Blend</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/finalblend-view.php">View Final Blends</a>
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
                <th>Final<br>Blend ID</th>
                <th>Date & Time</th>
                <th>Blend No.</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
               <?php
              $cprocess = "Final Blend";
              $result = runQuery("SELECT * FROM processentry WHERE processname='$cprocess' ORDER BY entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php echo getBlendID($cprocessid); ?></td>
                <td><?php echo getProcessGrade($cprocessid) ?></td>
                <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Batches</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/batch-new.php">New Batch</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/process/batch-view.php">View Batches</a>
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
                <th>Batch ID</th>
                <th>Date</th>
                <th>Final Blend ID</th>
				<th>Grade</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $cprocess = "Batch";
              $result = runQuery("SELECT * FROM processentry WHERE processname='$cprocess' ORDER BY entrytime DESC LIMIT 3");
              while($row=$result->fetch_assoc())
              {

                $cprocessid = $row["processid"];

              ?>

              <tr>
                <td><?php echo $cprocessid; ?></td>
                <td><?php echo Date('Y-M-d h:i A',strtotime($row["entrytime"])) ?></td>
                <td><?php  if(isset(getAllParents($cprocessid)["Parents"][0]["id"])){echo getAllParents($cprocessid)["Parents"][0]["id"];} else{echo "-";} ?></td>
                <td><?php echo getProcessGrade($cprocessid) ?></td>
                <td><?php echo getTotalQuantity($cprocessid); ?></td>
              </tr>

              <?php
                }
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
      <h5>Recent Purchase Orders</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/dispatch/new-purchase.php">New Purchase Order</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/dispatch/purchase-view.php">View Purchase Orders</a>
      </div>
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


              <?php

                $result  = runQuery("SELECT * FROM purchase_order ORDER BY entrydate DESC LIMIT 3");

                while($row = $result->fetch_assoc())
                {
                  $poid = $row["orderid"];

                  
                  $dispatch_qty = "-";

                  $result2 = runQuery("SELECT SUM(value) as qty FROM purchaseorder_params WHERE orderid='$poid' AND step='BATCH'");
                  $total_qty = $result2->fetch_assoc()["qty"];
                  if(!$total_qty)
                  {
                    $total_qty = "-";
                  }


                  $result2 = runQuery("SELECT SUM(quantity) as qty FROM loadingadvice_batches WHERE laid IN (SELECT laid FROM loading_advice WHERE poid ='$poid' AND status='FULFILLED')");
                  $dispatch_qty = $result2->fetch_assoc()["qty"];
                  if(!$dispatch_qty)
                  {
                    $dispatch_qty = "-";
                  }


                  if($row["status"] == "FULFILLED")
                  {
                    $label = "label-success";
                    $label_text = "Closed";
                  }
                  elseif($row["status"] == "LOADING ADVICE")
                  {
                    $label = "label-warning";
                    $label_text = "Loading Advice";
                  }
                  else
                  {
                    $label = "label-danger";
                    $label_text = "Open";
                  }

                  $dumC = $row["customer"];
                  $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
                  $customer_name = $result2->fetch_assoc()["value"];


              ?>

              <tr>
                <td><?php echo $customer_name; ?></td>
                <td><?php echo Date('Y-M-d',strtotime($row["entrydate"])) ?> <br> <?php echo $poid ; ?></td>
                <td><?php echo $total_qty; ?> kg</td>
                <td><?php echo $dispatch_qty; ?> kg</td>
                <td>
                  <label class="label <?php echo $label; ?>"><?php echo $label_text; ?></label>
                </td>
              </tr>

              <?php
                }

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
      <h5>Recent Dispatch</h5>
      <div class="row">
          <a class="col-md-5 mr-1 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/dispatch/new-dispatch.php">New Dispatch</a>
          <a class="col-md-5 btn waves-effect waves-light hor-grd btn-grd-primary" href="/user/dispatch/dispatch-view.php">View Dispatches</a>
      </div>
      <div class="card-block">
        <div class="table-responsive">
          <table class="table table-hover m-b-0" style="font-size:14px;">
            <thead>
              <tr style="text-align:center;">
                <th>Company</th>
                <th>Dispatch Date</th>
                <th>Consignment Number</th>
                <th>Dispatched<br>Qty</th>
              </tr>
            </thead>
            <tbody>

              <?php

                $result = runQuery("SELECT * FROM dispatch ORDER BY entrydate DESC LIMIT 3");

                while($row = $result->fetch_assoc())
                {

                  $cid = $row["cid"];
                  $dumC = $row["customer"];
                  $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
                  $customer_name = $result2->fetch_assoc()["value"];

                  $result2 = runQuery("SELECT SUM(qty) as qty FROM dispatch_invoices WHERE cid='$cid'");
                  $total_qty = $result2->fetch_assoc()["qty"];
                  if(!$total_qty)
                  {
                    $total_qty = "-";
                  }

              ?>

              <tr>
                <td><?php echo $customer_name; ?></td>
                <td><?php echo Date('Y-M-d',strtotime($row["entrydate"])) ?> </td>
                <td><?php echo $cid; ?></td>
                <td><?php echo $total_qty; ?> kg</td>

              </tr>


              <?php
              }

              ?>
              
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
