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

    $data = [];
    $data['sign'] = [];
    $data['sign']["id"] = $_GET["cid"];
    $data['sign']['batch'] = $id;

    $result = runQuery("SELECT * FROM batch_coa_approval WHERE processid='$id'");
    if($result->num_rows==1)
    {
        $result = $result->fetch_assoc();
        $user = $result['approvedby'];
        $data['sign']["approved-by"] = strtoupper(getFullName($user));
        $data['sign']["approved-by-inital"] = getInitial($user);
        $data['sign']["approved-date"] = Date('d-M-Y',strtotime($result['approvaldate']));

        
        $data['sign']["hash"] = md5(serialize($data['sign']));
    }
    else
    {
        $result2 = runQuery("SELECT * FROM premix_coa_approval WHERE premixid='$id'");
        if($result2->num_rows==1)
        {   
            $result2 = $result2->fetch_assoc();
            $user = $result2['approvedby'];
            $data['sign']["approved-by"] = strtoupper(getFullName($user));
            $data['sign']["approved-by-inital"] = getInitial($user);
            $data['sign']["approved-date"] = Date('d-M-Y',strtotime($result['approvaldate']));
            
            $data['sign']["hash"] = md5(serialize($data['sign']));
        }
        else
        {
            $ERR_TITLE = "Error";
            $ERR_MSG = "COA cannot be found.";
            include("../../pages/error.php");
            die();
        }
    }


   $cid = $_GET["cid"];

   $isfinal = true;

   

   $data['basic'] = [];
   $data['basic']['batch'] = $id;
   $data['basic']['cid'] = $cid;


   $result = runQuery("SELECT * FROM dispatch WHERE cid='$cid'");

   if($result->num_rows==1)
   {
   		$result = $result->fetch_assoc();
   		$data['basic']['laid'] = $result['laid'];
   		$dlaid =  $result['laid'];
		$dumC = $result["customer"];
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

		$result2 = runQuery("SELECT * FROM loadingadvice_batches WHERE laid='$dlaid' AND batch='$id'");
		$result2 = $result2->fetch_assoc(); 
		$data['grade'] = $result2['grade'];
		$data['dispatchqty'] = $result2['quantity'];
		$dumpkg = $result2['package'];

		$result2 = runQuery("SELECT * FROM dispatch_package WHERE packagename='$dumpkg'");
		$result2 = $result2->fetch_assoc();

		$data['package'] = $result2['shortname'];
		$data['packageweight'] = $result2['weight'];

		$result2 = runQuery("SELECT * FROM loading_advice WHERE laid='$dlaid'");
		$result2 = $result2->fetch_assoc(); 
		$data['basic']['company'] = $result2['company'];

        if($data['basic']['company']=="SLM Metal")
        {
            $data['basic']['company'] = "SLM METAL PRIVATE LIMITED";
        }
        else
        {
            $data['basic']['company'] = "SLM TECHNOLOGY PRIVATE LIMITED";
        }
       
		$data['dispatchdate'] = Date('d-M-Y',strtotime($result2['entrydate']));


		$result2 = runQuery("SELECT * FROM purchase_order WHERE orderid in (SELECT poid FROM loading_advice WHERE laid='$dlaid')");
		$result2 = $result2->fetch_assoc(); 
		$data['ponumber'] = $result2['orderid'];
		$data['podate'] = Date('d-M-Y',strtotime($result2['entrydate']));


        $data['coanote'] = "";

        $result2 = runQuery("SELECT * FROM coa_notes WHERE cid='$cid' AND batch='$id'");

        if ($result2->num_rows>0) {
            $data['coanote'] = $result2->fetch_assoc()['note'];
        }


   }
   else
   {
   		$ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();
   }



   $result = runQuery("SELECT * FROM processentry WHERE processid='$id'");

   if($result->num_rows==1)
   {
   		$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$id' AND param='$MASS_TITLE'");
   		$data['batchqty'] = $result2->fetch_assoc()['value'];

   		
   		$data['productiondate'] = Date('d-M-Y',strtotime($result->fetch_assoc()['entrytime']));


   		$data['batchdata'] = getDataFinal($id,$data['grade']);
   		
   }
   else
   {
   		$isfinal = false;

        $result2 = runQuery("SELECT * FROM premix_batch WHERE premixid='$id' ")->fetch_assoc();
        $data['batchqty'] = $result2['mass'];

        
        $data['productiondate'] = Date('d-M-Y',strtotime($result2['entrydate']));

        $data['batchdata'] = getDataPremix($id,$data['grade']);
   }






if($isfinal)
{


?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - COA</title>
    <link rel="stylesheet" href="coa.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="sheets-of-paper-a4.css">
    <style>
        .lab-sign{width:230px!important;height:auto;float:right;}
        h6{font-size:15px;}
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

                  <div class="row">
                  <div class="col-sm-4 logo">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h2>CERTIFICATE OF ANALYSIS</h2>
                      </div>
                  </div>

                <div class="row mb-0 mt-3">
                    <div class="col-sm-3">
                            <h6 class="mb-1">GRADE:</h6>
                            <h5>
                                <strong><?php echo $data['grade']; ?></strong>
                            </h5>
                    </div>
                    <div class="col-sm-3">
                    <h6>PROD. CODE:

                        <?php
                            $dum_prodcode ="";
                            $did = $data['basic']['batch'];
                            $result = runQuery("SELECT * FROM processentryparams WHERE processid='$did' AND step='CREATION' AND param='prodcode'");

                            if($result->num_rows==1)
                            {
                                $dum_prodcode = $result->fetch_assoc()['value'];
                            }
                        ?>
                        <div class="mb-1"><?php echo $dum_prodcode; ?></div></h6>                            
                    </div>        
                </div>
                    <div class="row mb-4 mt-0">
                        <div class="col-sm-3">
                            <h6>BATCH NO:<div class="mb-1"><?php echo $data['basic']['batch']; ?></div></h6>

                            <div><p style="font-size:14px;font-weight:bold;line-height:13px;"><?php echo $data['basic']['customer']; ?></p></div>
                            <div><p style="font-size:13px;line-height:13px;"><?php echo $data['basic']['customeraddress']; ?></p></div>
                            <div><p style="font-size:13px;"><?php echo $data['basic']['customercity']; ?>, <?php echo $data['basic']['customerstate']; ?> - <?php echo $data['basic']['customerpincode']; ?></p></div>
                        </div>
                        <div class="col-sm-3">
                        <h6>PROD. DATE: <div class="mb-3"><?php echo Date('d-M-Y',strtotime($data['productiondate'])); ?></div></h6>
                        <h6>DISPATCH DATE: <div class="mb-3"><?php echo Date('d-M-Y',strtotime($data['dispatchdate'])); ?></div></h6>

                        </div>
                        <div class="col-sm-3">
                            <h6>PROD. QTY.: <div class="mb-3"><?php echo $data['batchqty']; ?> KG</div></h6>
                            <h6>DISPATCH QTY.: <div class="mb-3"><?php echo $data['dispatchqty']; ?> KG</div></h6>
                        </div>
                        <div class="col-sm-3">
                        <h6>P.O. DETAILS:<div><?php echo $data['ponumber']; ?></div></h6>
                        <h6>P.O. DATE:<div><?php echo Date('d-M-Y',strtotime($data['podate'])); ?></div></h6>

                        </div>

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

                            		foreach ($data['batchdata'][0] as $prop => $cdata) {
                            			
                            			if($cdata['class']!='Physical')
                            			{
                            				continue;
                            			}
                            		

                            	?>
                                <tr>
                                    <td class="left"><?php echo $prop ?></td>
                                    <td class="center"><?php echo $cdata['mpif'] ?></td>
                                    <td class="center"><?php echo $cdata['min'] ?></td>
                                    <td class="center"><?php echo $cdata['max'] ?></td>
                                    <td class="center"><?php if($cdata['value']){echo $cdata['value'];} ?></td>
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
                                    <th scope="col" colspan="6" class="center">SIEVE ANALYSIS</th>
                                </tr>
                                <tr>
                                  <th scope="row" class="center" style="width:15%">MESH (%)</th>
                                  <th class="center" style="width:15%">MICRON (%)</th>
                                  <td class="center" style="width:20%">&nbsp;</td>
                                  <td class="center" style="width:10%">&nbsp;</td>
                                  <td class="center" style="width:10%">&nbsp;</td>
                                  <td style="width:30%">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody>

                            	<?php
                                    $count = 1;
                            		foreach ($data['batchdata'][1] as $sieve => $cdata) {
                            			
                            			

                            		

                            	?>
                                <tr>
                                    <td class="left"><?php echo $cdata['showname'] ?></td>
                                    <td class="center"><?php echo $cdata['micron'] ?></td>
                                    <?php 
                                        if($count==1)
                                        {
                                            echo "<td style=\"vertical-align : middle;text-align:center;\" class=\"center\" rowspan=\"".count($data['batchdata'][1])."\">MPIF-05</td>";
                                            $count++;
                                        }
                                    ?>
                                    
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

                            		foreach ($data['batchdata'][0] as $prop => $cdata) {
                            			
                            			if($cdata['class']!='Chemical')
                            			{
                            				continue;
                            			}
                            		

                            	?>
                                <tr>
                                  	<td class="left" style="width:30%;"><?php echo $prop ?></td>
                                    <td class="center" style="width:20%;"><?php echo $cdata['mpif'] ?></td>
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
                        
                        <!--<div class="col-sm-4">
                            <h6>GROSS WEIGHT</h6>
                            <h6><?php echo $data['dispatchqty']?> KG</h6>
                        </div> -->
                        <div class="col-sm-12 lab-sign text-right">
                          <img src='generate_sign.php?data=<?php echo serialize($data['sign']); ?>'>
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
<!---- Footer -->
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

<!---- End Footer -->
<div class="row col-sm-12" style="text-align: right;">
   <div class="col-sm-6"></div>
   <div class="col-sm-6" style="font-size: 10px;">Generated on <?php echo Date('d-M-Y H:i',strtotime('now')) ?></div>

</div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  </body>
  </html>
<?php  

}
else
{
   
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - PREMIX COA</title>
    <link rel="stylesheet" href="coa.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="sheets-of-paper-a4.css">
  </head>
  <body>
    <div class="page" contenteditable="true">
    <div id="ui-view" data-select2-id="ui-view">
        <div>
            <div class="card">
                <div class="card-header">PREMIX COA
                    <strong>GRADE: <?php echo $data['grade']; ?></strong>
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                        <i class="fa fa-save"></i> Save</a>
                </div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-sm-4 logo">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h2>CERTIFICATE OF ANALYSIS</h2>
                      </div>
                  </div>


                    <div class="row">
                        <h6 class="mb-1">GRADE:</h6><strong><?php echo $data['grade']; ?></strong>
                    </div>

                    <div class="row mb-4 mt-3">
                       
                        <div class="col-sm-3">
                            <h6>
                               BATCH NO.: 
                            </h6>
                            <div><?php echo $data['basic']['customer']; ?></div>
                            <div><?php echo $data['basic']['customeraddress']; ?></div>
                            <div><?php echo $data['basic']['customercity']; ?>, <?php echo $data['basic']['customerstate']; ?> - <?php echo $data['basic']['customerpincode']; ?></div>
                        </div>
                        <div class="col-sm-3">
                            <h6>PRODUCTION CODE</h6>
                            <div class="mb-3"><?php echo $id; ?></div>
                            <h6>PRODUCTION DATE</h6>
                            <div class="mb-3"><?php echo $data['productiondate']; ?></div>
                            <h6>DISPATCH DATE</h6>
                            <div><?php echo $data['dispatchdate']; ?></div>
                        </div>
                        <div class="col-sm-3">
                            <h6>PRODUCTION QUANTITY</h6>
                            <div class="mb-3"><?php echo $data['batchqty']; ?> KG</div>
                            <h6>DISPATCH QTY</h6>
                            <div><?php echo $data['dispatchqty']; ?> KG</div>
                        </div>
                        <div class="col-sm-3">
                            <h6>PO DETAILS</h6>
                            <div class="mb-3"><?php echo $data['ponumber']; ?></div>
                            <h6>PO DATE</h6>
                            <div><?php echo $data['podate']; ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h6 class="mb-1"><strong>COMPOSITION(%):</strong></h6>

                            <?php 

                                foreach ($data['batchdata'][2] as  $value) {
                                    echo "<div>".$value[0].":&ensp; ".$value[1]."</div>";
                                }

                            ?>
                            
                        </div>                    
                    </div>

                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="1" class="center" style="width:40%">PARAMETER</th>
                                    <th scope="col" colspan="1" class="center" style="width:5%">UNIT</th>
                                    <th scope="col" colspan="1" class="center" style="width:15%">METHOD</th>
                                    <th scope="col" colspan="1" class="center" style="width:15%">MIN</th>
                                    <th scope="col" colspan="1" class="center" style="width:15%">MAX</th>
                                    <th scope="col" colspan="1" class="center" style="width:15%">TEST VALUE</th>
                                </tr>
                                <tr>
                                  <th scope="col" colspan="6" class="center">PHYSICAL PROPERTIES</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                
                                <?php 

                                foreach ($data['batchdata'][0] as $key =>  $value) {
                                        
                                    

                                ?>
                                <tr>
                                    <td class="left"><?php echo $value['showname']; ?></td>
                                    <td class="center"><?php echo $value['unit']; ?></td>
                                    <td class="center"><?php echo $value['mpif']; ?></td>
                                    <td class="center"><?php echo $value['min']; ?></td>
                                    <td class="center"><?php echo $value['max']; ?></td>
                                    <td class="center"><?php echo $value['value']; ?></td>
                                </tr>

                                <?php 

                                    }

                                ?>



                                <tr>
                                  <th scope="col" colspan="6" class="center">CHEMICAL PROPERTIES</th>
                                </tr>

                               <?php 

                                foreach ($data['batchdata'][1] as $key =>  $value) {
                                        
                                    

                                ?>
                                <tr>
                                    <td class="left"><?php echo $value['showname']; ?></td>
                                    <td class="center"><?php echo $value['unit']; ?></td>
                                    <td class="center"></td>
                                    <td class="center"><?php echo $value['min']; ?></td>
                                    <td class="center"><?php echo $value['max']; ?></td>
                                    <td class="center"><?php echo $value['value']; ?></td>
                                </tr>

                                <?php 

                                    }

                                ?>
                                <tr>
                                    <td class="left">Iron </td>
                                    <td class="center">%</td>
                                    <td class="center" colspan="4">BASE</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    

                    <div class="row mb-4 mt-3">
                        
                        <!--<div class="col-sm-4">
                            <h6>GROSS WEIGHT</h6>
                            <h6><?php echo $data['dispatchqty']+$data['packageweight'] ?> KG</h6>
                        </div>-->
                        <div class="col-sm-6 lab-sign text-right">
                          <img src='generate_sign.php?data=<?php echo serialize($data['sign']); ?>'>
                        </div>
                    </div>

                    <div class="row mb-4 mt-3">
                        <?php if($data['coanote']) {?>
                        <div class="col-sm-5" style="color:red">
                            NOTE: <?php echo $data['coanote']; ?>
                        </div>
                    <?php }?>
                    </div>

<!---- Footer -->
                    <div class="row mb-2">
                      <div class="col-sm-9">
                        <p><strong><?php echo $data['basic']['company'] ; ?></strong></p>
                        <p>UDITNAGAR, ROURKELA-769012, ODISHA, INDIA</p>
                        <p>TEL: +91-90400-00519</p>
                        <p>EMAIL: <a href="mailto:info@slmmetal.com">info@slmmetal.com</a>, URL: <a href="https://www.slmmetal.com">www.slmmetal.com</a></p>
                      </div>
                      <div class="col-sm-3 foot">
                      </div>
                    </div>
<!---- End Footer -->
<div class="row col-sm-12" style="text-align: right;">
   <div class="col-sm-6"></div>
   <div class="col-sm-6" style="font-size: 10px;">Generated on <?php echo Date('d-M-Y H:i',strtotime('now')) ?></div>

</div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  </body>
  </html>


<?php  

}


?>

<?php 

function getDataPremix($id,$grade)
{

    $allphysical = [];
    $allchemical = [];
    $compositions = [];

    $noprint = [];


    $result = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$grade'");


    while($row=$result->fetch_assoc())
    {
       if($row['additive']=='Iron')
       {
        array_push($compositions,[$row['additive'],'BASE']);

     
       } 
       else
       {
        $dadditive = $row['additive'];
        $result2 = runQuery("SELECT * FROM premix_coa_grade_settings WHERE gradename='$grade' AND property='$dadditive'");

        if($result2->num_rows==1)
        {   
            $result2 = $result2->fetch_assoc();
            $dadditive = $result2['showname'];

            if($result2['print']==0)
            {
                array_push($noprint,$row['additive']);
                continue;
            }
        }


        array_push($compositions,[$dadditive,$row['composition']]);

        

        $allchemical[$row['additive']] = [];
        $allchemical[$row['additive']]['showname'] = $dadditive;
        $allchemical[$row['additive']]['min'] = $row['mintol'];
        $allchemical[$row['additive']]['max'] = $row['maxtol'];
        $allchemical[$row['additive']]['value'] = [];
        $allchemical[$row['additive']]['unit'] = "%";
       }
   
    }


    


    $result = runQuery("SELECT * FROM premix_grade_physical WHERE gradename='$grade'");
    $dumphysical = [];
    while($row=$result->fetch_assoc())
    {
      
        

        $dphysical = $row['parameter'];


        $result2 = runQuery("SELECT * FROM premix_coa_grade_settings WHERE gradename='$grade' AND property='$dphysical'");

        if($result2->num_rows==1)
        {
            $result2 = $result2->fetch_assoc();
            $dphysical = $result2['showname'];

            if($result2['print']==0)
            {
                array_push($noprint,$row['parameter']);
                continue;
            }
        }

        array_push($dumphysical,$row['parameter']);

        $allphysical[$row['parameter']] = [];
        $allphysical[$row['parameter']]['showname'] =  $dphysical;
        $allphysical[$row['parameter']]['mpif'] = $row['mpif'];
        $allphysical[$row['parameter']]['min'] = $row['min'];
        $allphysical[$row['parameter']]['max'] = $row['max'];

        $allphysical[$row['parameter']]['value'] = [];

        $allphysical[$row['parameter']]['unit'] = $row['units'];
   
    }


   

    $result = runQuery("SELECT * FROM coa_test_data WHERE processid='$id'");

    while($row=$result->fetch_assoc())
    {
       

            if(in_array($row['param'],$noprint))
            {
                continue;
            }

            if($row['type']=="Physical")
            {
                $allphysical[$row['param']]['value'] = $row['value'];
            }
            elseif($row['type']=="Chemical" && $row['param']!='Iron')
            {
                $allchemical[$row['param']]['value'] = $row['value'];
                
            }
        
    }







    return [$allphysical,$allchemical,$compositions];
}

	function getDataFinal($id)
	{

		global $GRADE_TITLE;

		$result = runQuery("SELECT * FROM processentryparams WHERE processid='$id' AND param='$GRADE_TITLE'");
		$result = $result->fetch_assoc();

		$cgrade = $result['value'];



		$result = runQuery("SELECT * FROM gradeproperties WHERE processname='Final Blend' AND gradename = '$cgrade' ORDER BY ordering");


		$allproperties = [];
		$allSieve = [];

        $dumcumulative = runQuery("SELECT * FROM processgrades WHERE gradename='$cgrade'")->fetch_assoc()['cumulative'];
		$isSievecum =  $dumcumulative=="YES";


		while($row=$result->fetch_assoc())
		{

			if(substr($row['properties'],0,5)=="Sieve")
			{
				$allSieve[$row['properties']] =[];
                // print property
                $dumprop1 = $row['properties'];
                $dumisprinted = runQuery("SELECT * FROM final_coa_grade_settings WHERE gradename = '$cgrade' AND property='$dumprop1'")->fetch_assoc()['print'];
				$allSieve[$row['properties']]['printed'] = $dumisprinted==1?true:false;
				$allSieve[$row['properties']]["value"] =[];
                // Seive Cumulative
                
				
				$dums = $row['properties'];
				$result2 = runQuery("SELECT * FROM sieve WHERE name='$dums'");
				$result2 =  $result2->fetch_assoc();

				$allSieve[$row['properties']]['mesh'] = $result2['mesh'];
				$allSieve[$row['properties']]['micron'] = $result2['micron'];
                $allSieve[$row['properties']]["max"] = $row['max'];
                $allSieve[$row['properties']]["min"] = $row['min'];
			}
			else
			{
                $dumprop1 = $row['properties'];
                $dumisprinted = runQuery("SELECT * FROM final_coa_grade_settings WHERE gradename = '$cgrade' AND property='$dumprop1'")->fetch_assoc()['print'];
                if($dumisprinted!=1)
                {
                    continue;
                }

				$allproperties[$row['properties']] = [];
				$cproperty = $row['properties'];
				$result2 = runQuery("SELECT * FROM processgradesproperties WHERE processname='Final Blend' AND gradeparam='$cproperty'");
				$result2 = $result2->fetch_assoc();
				$allproperties[$row['properties']]['mpif'] = $result2['mpif'];
				$allproperties[$row['properties']]['class'] = $result2['class'];
				$allproperties[$row['properties']]["value"] = [];
				$allproperties[$row['properties']]["min"] = $row['min'];
				$allproperties[$row['properties']]["max"] = $row['max'];
			}
		}



		$result = runQuery("SELECT * FROM coa_test_data WHERE processid='$id'");

		while($row2=$result->fetch_assoc())
		{
			
				if($row2['type']=="Sieve")
				{
					$allSieve[$row2['param']]["value"] = $row2['value'];
				}
				else
				{
                    $dumprop1 = $row2['param'];
                      $dumisprinted = runQuery("SELECT * FROM final_coa_grade_settings WHERE gradename = '$cgrade' AND property='$dumprop1'")->fetch_assoc()['print'];
                    if($dumisprinted!=1)
                    {
                        continue;
                    }
					$allproperties[$row2['param']]["value"] = $row2['value'];
				}
			
		}


		

		$carryover = 0;
        $carryWord = "";

       

		foreach ($allSieve as $key => $property) {

            $allSieve[$key]["showname"] =  $property['mesh'];
			if($carryWord == "" && $isSievecum)
            {
                $allSieve[$key]["showname"] =  $property['mesh'];
                $carryWord = $property['mesh'];
            }
            elseif($property['printed'] && $isSievecum)
            {
                $allSieve[$key]["showname"] = "-".$carryWord . "+".$property['mesh'];
                $carryWord = $property['mesh'];
            }
            

           


			if(!$property['printed'])
			{
				$carryover += $property['value'];
                unset( $allSieve[$key]);
				//$allSieve[$key] = [];
			}

			if($carryover!=0 && $property['printed'])
			{
				$allSieve[$key]['value'] += $carryover;
				$carryover = 0;
			}

		}



		return [$allproperties,$allSieve];
	}

?>