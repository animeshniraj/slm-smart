


<head>
    <meta charset="utf-8">
    <title>SLM SMART - ANNEALING REPORT</title>
    <link rel="stylesheet" href="anneal-rep.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="sheets-of-paper-a4.css">
  </head>

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

	

    $PAGE = [
        "Page Title" => "Annealing Report | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "process-annealing-stock",
        "MainMenu"	 => "process_annealing",

    ];
    
    
    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


    $processname = "Annealing";


    if(!isset($_GET["id"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $processid = $_GET["id"];



    $allData = [];



    $result = runQuery("SELECT * FROM processentry WHERE processname='$processname' AND processid ='$processid'");

    $isBlocked = "NO";

    if($result->num_rows==1)
    {

        $row=$result->fetch_assoc();
    	$dum = [];
    	$dum["id"] = $row["processid"];
    	$dum["mass"] = 0;
    	$dum["remaining"] = 0;
    
    	$dum["grade"] = $processname=="Melting"?"Default Grade":"No Grade Selected";
    	$currid = $row["processid"];
    	$isBlocked = $row["islocked"];
        $creationDate = Date('d-M-Y',strtotime($row['entrytime']));
        $dum['feedtime'] = Date('d-M-Y H:i',strtotime($row['entrytime']));
    	$result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND (param = '$MASS_TITLE' OR param='$GRADE_TITLE')");

        $dum['outtime'] = "";

        $dum['feedhr'] = "";
        $dum['feedrate'] = "";
        $dum['feedhrformated'] ="";
        $dum['breaktime'] = 0;



    	while($row2=$result2->fetch_assoc())
    	{

    		if($row2["param"]==$MASS_TITLE)
    		{
    			$dum["mass"] = $row2["value"];
    			$dum["remaining"] = $row2["value"];
    		}
    		if($row2["param"]==$GRADE_TITLE)
    			$dum["grade"] = $row2["value"];
    	}

        $dum['remaining'] -= getChildProcessQuantity($currid);


        $result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND param='Furnace'");
        {
            $dum['furnace'] = $result2->fetch_assoc()['value'];
        }


        $result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND step='PARENT'");
        {
           $rawblendid = $result2->fetch_assoc()['param'];
        }






        




       


        $operational = [];
        $alltime = [];
        $alloparam = [];
        $recipes = [];

        $result2 = runQuery("SELECT * FROM processentryparamstimed WHERE processid='$currid' AND step='OPERATIONAL' ORDER BY entrytime");
        {
            while($row2 = $result2->fetch_assoc())
            {

                if(!isset($operational[$row2['param']]))
                {
                    $operational[$row2['param']] = [];
                }

                 if(!isset($operational[$row2['param']][$row2['entrytime']]))
                {
                    $operational[$row2['param']][$row2['entrytime']] = [];
                }

                if(!in_array($row2['entrytime'],$alltime))
                {
                    array_push($alltime,$row2['entrytime']);
                }

                if(!in_array($row2['param'],$alloparam))
                {
                    if($row2['param']=="Recipe" || $row2['param']=="Comments")
                    {
                       
                    }
                    else{
                        array_push($alloparam,$row2['param']);
                    }
                    
                }
                $operational[$row2['param']][$row2['entrytime']] = $row2['value'];
            }
        }


       


        $notes = [];
        $result2 = runQuery("SELECT * FROM processnotes WHERE processid='$currid' ORDER BY time");
        {
            while($row2=$result2->fetch_assoc())
            {
                $type = explode('::',$row2['note'])[0];
                if($type=='POWER CUT' || $type=='BELT STOP' || $type=='OTHER PROBLEMS')
                {
                    $dum2 = [$type,explode('::',explode('->',$row2['note'])[1])[0],explode('Total Time-> ',$row2['note'])[1]];

                    $dstarttime = strtotime(explode(' - ',$dum2[2])[0]);
                    $dstoptime = strtotime(explode(' - ',$dum2[2])[1]);
                    $dum["breaktime"] += $dstoptime-$dstarttime;

                    array_push($notes,$dum2);
                }
            }
        }




        $result2 = runQuery("SELECT * FROM processentryparams WHERE processid='$currid' AND param='Hopper Discharge Time'");
        {
            if($result->num_rows==1)
            {
                $dum1 = $result2->fetch_assoc()['value'];
                if($dum1)
                {
                    $dum['outtime'] = $dum1;

                    $dum['feedhr'] = ((strtotime($dum['outtime']) - strtotime($dum['feedtime']))-$dum['breaktime'])/3600;
                    $dum['feedrate'] = $dum["mass"]/$dum['feedhr'];
                    $dum['feedhrformated'] = sprintf('%02d:%02d', (int) $dum['feedhr'], fmod($dum['feedhr'], 1) * 60);;
                }
            }
        }

       

    }
    else
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "No data";
        include("../../pages/error.php");
        die();
    }   






?>

<script type="text/javascript">
	
	function changeSelect(inobj,val)
	{
		inobj.value = val;
	}


</script>

<div class="page" contenteditable="false">
    <div id="ui-view" data-select2-id="ui-view">
        <div>
            


<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

<div class="page-body">
<div class="row">
<div class="col-lg-12">


	<div class="card">
                <div class="card-header">
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print Report</a>
                </div>
                <div class="card-body" style="">

                  <div class="row">
                      <div class="col-sm-4 logo">
                          <img src="logo.png" width="250px">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h4>ANNEALING FURNACE INPUT MATERIAL RECORD</h4>
                      </div>
                  </div>

                    <div class="table-responsive-sm">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr style="font-size:14px;">
                                    <td scope="col" colspan="1" class="center" style="width:10%;font-weight:bold;">DATE</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;"><?php echo $creationDate; ?></td>
                                    <td scope="col" colspan="2" class="center" style="width:10%;font-weight:bold;">GRADE</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;"><?php echo $dum["grade"]; ?></td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;font-weight:bold;">FURNACE NO.</td>
                                    <td scope="col" colspan="1" class="center" style="width:35%;"><?php echo $dum["furnace"]; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="table-responsive-sm">
                        <table class="table table-striped table-bordered">
                            <tbody>
                            <tr style="font-size:14px;">
                                    <td scope="col" colspan="1" class="center" style="width:10%;font-weight:bold;">BATCH NO.</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;"><?php echo $rawblendid; ?></td>
                                    <td scope="col" colspan="2" class="center" style="width:10%;font-weight:bold;">FEED TIME</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;"><?php echo Date('H:i',strtotime($dum["feedtime"])); ?></td>
                                    <td scope="col" colspan="1" class="center" style="width:15%;font-weight:bold;">OUTPUT TIME</td>
                                    <td scope="col" colspan="1" class="center" style="width:35%;"><?php echo $dum["outtime"]; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


            <div class="row">
                <div class="col-sm-6">
                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <th scope="col" colspan="<?php echo count($alltime)+1; ?>" style="text-align:center;">OPERATING PARAMETERS</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                    <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%">Recipe: <?php echo $operational['Recipe'][$value] ?></td>
                                       <?php
                                    } ?>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 1</td>
                                    <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 1'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 2</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 2'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 3</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 3'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 4</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 4'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 5</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 5'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 6</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 6'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 7</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 7'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 8</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 8'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 9</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 9'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 10</td>
                                     <?php foreach ($alltime as $value) {
                                       ?>   
                                       <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $operational['Zone 10'][$value] ?></td>
                                       <?php
                                    } ?>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <th scope="col" colspan="3" style="text-align:center;">PROPERTIES OF MATERIAL</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%;text-align:center;">INPUT</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%;text-align:center;">OUTPUT</td>
                                </tr>
                                
                                <?php 

                                    $result = runQuery("SELECT * FROM processtestselection WHERE processid='$processid'");

                                    while($row=$result->fetch_assoc())
                                    {

                                        $dparam  = $row['param'];
                                        $dtid = $row['testid'];

                                        $dval = "-";

                                        $result2 = runQuery("SELECT * FROM processtestparams WHERE testid='$dtid' AND param='$dparam'");

                                        if($result2->num_rows==1)
                                        {
                                            $dval = $result2->fetch_assoc()['value'];
                                        }

                                        $dval2 = "-";

                                        $result2 = runQuery("SELECT AVG(value) as val FROM processtestparams WHERE param='$dparam' AND processid='$rawblendid'")->fetch_assoc();

                                        if($result2['val'])
                                        {
                                            $dval2 = round($result2['val'],3);
                                        }

                                ?>

                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%"><?php echo $row['param'] ?></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"><?php echo $dval2; ?></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"><?php echo $dval; ?></td>
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
                                <th scope="col" colspan="2" class="center" style="text-align:center;">OTHER DETAILS</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php 

                                        $dval = "";
                                        $result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='GENERIC' AND param='Hopper ID'");

                                        if($result->num_rows==1)
                                        {
                                            $dval  = $result->fetch_assoc()["value"];
                                        }
                                    ?>
                                    <td scope="col" colspan="1" class="center" style="width:50%">HOPPER ID</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $dval; ?></td>
                                </tr>
                                <tr>
                                    <?php 

                                        $dval = "";
                                        $result = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND step='GENERIC' AND param='Gas Ratio'");

                                        if($result->num_rows==1)
                                        {
                                            $dval  = $result->fetch_assoc()["value"];
                                        }
                                    ?>
                                    <td scope="col" colspan="1" class="center" style="width:50%">GAS RATIO</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"><?php echo $dval; ?></td>
                                </tr>

                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">TOTAL RUNNING HRS.</td>
                                    <td scope="col" colspan="1" class="center" id = "total_running" style="width:50%"><?php echo $dum["feedhrformated"]; ?></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">FEED RATE KG/HR</td>
                                    <td scope="col" colspan="1" class="center" id = "feed_rate" style="width:50%"><?php echo round($dum["feedrate"],2); ?></td>
                                </tr>
                         </tbody>
                        </table>
                    </div>
                
                </div>
            </div>


                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="left" ></td>
                                <?php
                                    foreach ($alltime as  $tvalue) {
                                            ?>

                                            
                                            <td class="left" >Recipe: <?php echo $operational['Recipe'][$tvalue]; ?></td>

                                            <?php 
                                        }
                                ?>
                                </tr>

                                <tr>
                                    <td class="left" >Time</td>
                                <?php
                                    foreach ($alltime as  $tvalue) {
                                            ?>

                                            
                                            <td class="left" ><?php echo $tvalue; ?></td>

                                            <?php 
                                        }
                                ?>
                                </tr>

                                <tr>
                                    <td class="left" >Comments</td>
                                <?php
                                    foreach ($alltime as  $tvalue) {
                                            ?>

                                            
                                            <td class="left" ><?php echo $operational['Comments'][$tvalue]; ?></td>

                                            <?php 
                                        }
                                ?>
                                </tr>


                            </thead>

                            <tbody>

                                <?php 
                                foreach ($alloparam as $key => $value) {

                                    if(substr($value,0,4)=="Zone")
                                    {
                                        continue;
                                    }
                                    
                                ?>
                                <tr>
                                    <td class="left" style="width:50%"><?php echo $value; ?></td>
                                    <?php 

                                        foreach ($alltime as  $tvalue) {
                                            ?>
                                            <td class="left" ><?php echo $operational[$value][$tvalue]; ?></td>

                                            <?php 
                                        }
                                    ?>
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
                                    <th scope="col" colspan="2" class="center" style="text-align:center;">BREAKDOWN DETAILS</th>
                                    <th scope="col" colspan="1" class="center" style="width:20%;text-align:center;">TOTAL TIME (hrs:mins)</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php 

                                $totaldt = 0;
                                $hours =0;
                                $minutes = 0;

                                    foreach ($notes as $value) {
                                    
                                    $dstarttime = strtotime(explode(' - ',$value[2])[0]);
                                    $dstoptime = strtotime(explode(' - ',$value[2])[1]);
                                    $dt = $dstoptime-$dstarttime;
                                    $totaldt +=$dt;
                                    //$totaldt+= $dt/3600;

                                    $hours = floor($dt / 3600);
                                    $hours = sprintf('%02d', $hours);
                                    $minutes = floor(($dt / 60) % 60);
                                    $minutes = sprintf('%02d', $minutes);


                                ?>
                                <tr>
                                    <td class="left" style="width:30%"><?php echo $value[0] ?></td>
                                    <td class="center" style="width:50%"><?php echo $value[1] ?></td>
                                    <td class="center" style="width:20%"><?php echo $hours.":".$minutes ?></td>
                                </tr>

                                <?php 
                                    }

                                    

                                    $totaldt_hours = floor($totaldt / 3600);
                                    $totaldt_hours = sprintf('%02d', $totaldt_hours);
                                    $totaldt_minutes = floor(($totaldt / 60) % 60);
                                    $totaldt_minutes = sprintf('%02d', $totaldt_minutes);

                                ?>
                                
                            </tbody>
                                <tr>
                                    <td class="left" style="width:30%"></td>
                                    <td class="center" style="width:50%">Total Time</td>
                                    <td class="center" style="width:20%"><?php echo $totaldt_hours.":".$totaldt_minutes ?></td>
                                </tr>
                            <tfoot>
                                
                            </tfoot>
                        </table>
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

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>



<?php
    
    include("../../pages/endbody.php");

?>

<script type="text/javascript">





$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();
  	






  // Creation

  	

  		

  	

});








</script>

