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

    $myuserid = $session->user->getUserid();
    $myrole = $session->user->getRoleid();

    $editidPermission = false;

    if($myrole =="ADMIN" OR $myrole =="Production_Supervisor")
    {
        $editidPermission = true;
    }



    if(!isset($_GET["id"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }

   $id = $_GET["id"];

   if(!isset($_GET["cid"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }


$cid = $_GET["cid"];

   $isfinal = true;

   $data = [];

   $data['basic'] = [];
   $data['basic']['batch'] = $id;
   $data['basic']['cid'] = $cid;



   $result = runQuery("SELECT * FROM sdispatch_batches WHERE processid='$id' AND cid='$cid'");

   if($result->num_rows!=1)
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "Unknown Error";
        include("../../pages/error.php");
        die();

    }

    $result = $result->fetch_assoc();

   $data['grade'] =  $result['grade'];
   $data['dispatchqty'] = $result['quantity'];
   $data['coanote'] = $result['quantity'];
   $data['prodcode'] = $result['prodcode'];

   $dcid = 'SAMPLE/'.$cid;

   $result = runQuery("SELECT * FROM coa_notes WHERE batch='$id' AND cid='$dcid'");
   if($result->num_rows==1)
   {
    $data['coanote'] = $result->fetch_assoc()['note'];
   }


        $result = runQuery("SELECT * FROM sample_dispatch WHERE  cid='$cid'");
        $result = $result->fetch_assoc();
        $dumC = $result["customer"];
        $data['basic']['company'] = $result["company"];
        $data['dispatchdate'] = $result["entrydate"];
        $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Name'");
        $result2 = $result2->fetch_assoc(); 
        $data['basic']['customerid'] = $dumC;
        $data['basic']['customer'] = $result2['value'];

        $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Address'");
        $result2 = $result2->fetch_assoc(); 
        $data['basic']['customeraddress'] = $result2['value'];

        $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='City'");
        $result2 = $result2->fetch_assoc(); 
        $data['basic']['customercity'] = $result2['value'];

        $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='State'");
        $result2 = $result2->fetch_assoc(); 
        $data['basic']['customerstate'] = $result2['value'];
        $result2 = runQuery("SELECT * FROM external_param WHERE externalid='$dumC' AND param='Pincode'");
        $result2 = $result2->fetch_assoc(); 
        $data['basic']['customerpincode'] = $result2['value'];



        $testdata = [];

        $result2 = runQuery("SELECT * FROM sdispatch_test WHERE cid='$cid'");

        while($row2=$result2->fetch_assoc())
        {
            $testdata[$row2['property']] = [];
            $testdata[$row2['property']]['value'] = $row2['value'];

            $dprop = $row2['property'];

            $testdata[$row2['property']]['min'] = "";
            $testdata[$row2['property']]['max'] = "";
            $testdata[$row2['property']]['mpif'] = "";

            $testdata[$row2['property']]['class'] = "Physical";

            $dumGrade = $data['grade'];

            $result3 = runQuery("SELECT * FROM gradeproperties WHERE properties='$dprop'  AND gradename='$dumGrade' AND processname='Final Blend'");

            if($result3->num_rows>0)
            {
                $result3=$result3->fetch_assoc();

                $testdata[$row2['property']]['min'] = $result3['min'];
                $testdata[$row2['property']]['max'] = $result3['max'];
                

                
            }


             $result3 = runQuery("SELECT * FROM processgradesproperties WHERE gradeparam='$dprop'");
            if($result3->num_rows>0)
            {
                $result3=$result3->fetch_assoc();

                $testdata[$row2['property']]['mpif'] = $result3['mpif'];


                if($result3['class'])
                {
                    $testdata[$row2['property']]['class'] = $result3['class'];
                }

                
            }

           



        }



?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - COA</title>
    <link rel="stylesheet" href="coa.css" media="all" />
    <link rel="stylesheet" href="/../../pages/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="coa-a4.css">
    <style>
        .page .lab-sign{width:230px!important;height:auto;float:right;}
       h5{font-size:20px;}
        h6{font-size:13px;}

        @media print {
            .table{font-size:14px;}
            .table .table-borderless{border:#fff solid !important;}
            
            .table-bordered td {border: 2px solid #000 !important;}
        }
    </style>
  </head>
  <body>
  <div class="page" contenteditable="false">
    <div id="ui-view" data-select2-id="ui-view">
        <div>
            <div class="card">
                <div class="card-header">
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>

                </div>
                <div class="card-body">

                 <!-- <div class="row">
                  <div class="col-sm-4 logo">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h2>CERTIFICATE OF ANALYSIS</h2>
                      </div>
                  </div> -->

                    <div class="row mb-4 mt-3">
                        <div class="col-sm-6">
                            <h5 class="mb-1">GRADE:<strong><?php echo $data['grade']; ?></strong></h5>
                        </div>
                    </div>  

                    <!--
                        <div><p style="font-size:14px;font-weight:bold;line-height:13px;"><?php echo $data['basic']['customer']; ?></p></div>
                            <div><p style="font-size:13px;line-height:13px;"><?php echo $data['basic']['customeraddress']; ?></p></div>
                            <div><p style="font-size:13px;"><?php echo $data['basic']['customercity']; ?>, <?php echo $data['basic']['customerstate']; ?> - <?php echo $data['basic']['customerpincode']; ?></p></div>
                        </div>
                        <div class="col-sm-3">
                            <h6>PROD. DATE
                            <div class="mb-3"><?php echo Date('d-M-Y',strtotime(getEntryTime($id))); ?></div></h6>
                            <h6>DISPATCH DATE
                            <div><?php echo Date('d-M-Y',strtotime($data['dispatchdate'])); ?></div></h6>
                        </div>
                        <div class="col-sm-3">
                            <h6>PROD. QTY.
                            <div class="mb-3"><?php echo getTotalQuantity($id) ; ?> KG</div></h6>
                            <h6>DISPATCH QTY.
                            <div><?php echo $data['dispatchqty']; ?> KG</div></h6>
                        </div>
                        <div class="col-sm-3">
                            <h6>P.O. DETAILS
                            <div class="mb-3"><?php echo $id; ?></div></h6>
                             <h6>PROD. CODE
                            <div class="mb-3"><?php echo $data['prodcode']; ?></div></h6>
        
                        </div>
                         
                    </div>-->

                    <div class="table-responsive-sm">
                        <table class="table table-borderless" style="border:#fff solid !important; margin-top:15px;">
                                <tr>
                                    <td scope="col" colspan="1" style="width:20%"><h6>PROD. DATE: <div class="mb-3"><?php echo Date('d-M-Y',strtotime(getEntryTime($id))); ?></div></h6></td>
                                    <td scope="col" colspan="1" style="width:25%;vertical-align:top;"><h6>P.O. DETAILS:<div><?php echo $id; ?></div></h6></td>
                                </tr>
                                <tr style="border:#fff solid !important;">
                                    <td scope="col" colspan="1" style="width:35%">
                                        <h6 class="word"><div>CUSTOMER:<br><?php echo $data['basic']['customer']; ?></div>
                                       
                                        <div style="text-transform: uppercase"><?php echo $data['basic']['customercity']; ?>, <?php echo $data['basic']['customerstate']; ?></div></h6>
                                    </td>
                                    <td scope="col" colspan="1" style="width:20%;vertical-align:top;"><h6>DISPATCH DATE: <div class="mb-3"><?php echo Date('d-M-Y',strtotime($data['dispatchdate'])); ?></div></h6></td>
                                    <td scope="col" colspan="1" style="width:20%;vertical-align:top;"><h6>DISPATCH QTY.: <div class="mb-3"><?php echo getTotalQuantity($id) ; ?> KG</div></h6></td>
                                    <td scope="col" colspan="1" style="width:25%;vertical-align:top;"><h6>P.O. DATE:<div><?php echo Date('d-M-Y',strtotime(getEntryTime($id))); ?></div></h6></td>
                                </tr>
                        </table>
                    </div>


                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="1" class="center" style="width:30%">PROPERTY</th>
                                    <th scope="col" colspan="1" class="center" style="width:20%">STANDARD</th>
                                    <th scope="col" colspan="2" class="center" style="width:20%">SPECIFICATION</th>
                                    <th scope="col" colspan="1" class="center" style="width:30%">OBSERVATION</th>
                                </tr>
                                <tr>
                                  <th scope="row">&nbsp;</th>
                                  <td>&nbsp;</td>
                                  <td class="center">MIN</td>
                                  <td class="center">MAX</td>
                                  <td>&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody>

                            	<?php

                            		foreach ($testdata as $prop => $cdata) {
                            			
                            			if($cdata['class']!='Physical')
                            			{
                            				continue;
                            			}
                            		

                            	?>
                                <tr>
                                    <td class="left"><?php echo $prop ?></td>
                                    <td class="center"><?php echo "MPIF ".$cdata['mpif'] ?></td>
                                    <td class="center"><?php echo $cdata['min'] ?></td>
                                    <td class="center"><?php echo $cdata['max'] ?></td>
                                    <td class="center"><?php echo $cdata['value'] ?></td>
                                </tr>


                                <?php

									}                                	
                                ?>
                                
                            </tbody>
                        </table>
                    </div>

                    

                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="5" class="center">CHEMICAL ANALYSIS</th>
                                </tr>

                                </thead>

                                <tbody>

                                	<?php

                            		foreach ($testdata as $prop => $cdata) {
                            			
                            			if($cdata['class']!='Chemical')
                            			{
                            				continue;
                            			}
                            		

                            	?>
                                <tr>
                                  	<td class="left" style="width:30%;"><?php echo $prop ?></td>
                                    <td class="center" style="width:20%;"><?php echo "MPIF ".$cdata['mpif'] ?></td>
                                    <td class="center" style="width:10%;"><?php echo $cdata['min'] ?></td>
                                    <td class="center" style="width:10%;"><?php echo $cdata['max'] ?></td>
                                    <td class="center" style="width:30%;"><?php echo $cdata['value'] ?></td>
                                </tr>
                            
                            
                                <?php
                                	}

                                ?>
                            </tbody>
                        </table>
                    </div>
<!---- Packaging Details -->

                    <div class="row mb-4 mt-3">
                          <!--  <div class="col-sm-5">
                        <h6 class="mb-1">PACKAGING DETAILS</h6>
                            <h5><?php echo $data['package']; ?></h5>
                            <div class="mt-2">PACKAGING MATERIAL DETAILS</div>
                            <div><?php echo $data['packageweight']. "kg " . $data['package']; ?></div>
                                
                        </div>-->
                        <div class="col-sm-4">
                            <h6>NET WEIGHT</h6>
                            <h6><?php echo $data['dispatchqty'] ?> KG</h6>
                        </div>
                       
                        <div class="col-sm-4 lab-sign">
                          <img src="slm_stamp.png">
                        </div>
                    </div>
<!---- End of Packaging details -->
                    <?php if($data['coanote']) {?>
                    <div class="row mb-4 mt-3">
                        <div class="col-sm-5" style="color:red">
                            NOTE: <?php echo $data['coanote']; ?>
                        </div>
                    </div>
                <?php } ?>
<!---- Footer 
                    <div class="row mb-2">
                      <div class="col-sm-9 footer">
                        <p><strong><?php echo $data['basic']['company'] ; ?></strong></p>
                        <pack>UDITNAGAR, ROURKELA-769012, ODISHA, INDIA</h6>
                        <p>TEL: +91-90400-00519</p>
                        <p>EMAIL: info@slmmetal.com, URL: www.slmmetal.com</p>
                      </div>
                      <div class="col-sm-3 foot">
                        
                      </div>
                    </div>
 End Footer -->
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  </body>
  </html>