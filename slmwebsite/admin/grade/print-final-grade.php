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


if(!isset($_GET["grade"]))
{
    $ERR_TITLE = "Error";
    $ERR_MSG = "You are not authorized to view this page.";
    include("../../pages/error.php");
    die();
}

$grade = $_GET["grade"];

$iscumulative = runQuery("SELECT * FROM processgrades WHERE gradename='$grade'")->fetch_assoc()['cumulative'];

if($iscumulative=="YES")
{
    $iscumulative = "Cumulative";
}
else
{
    $iscumulative = "Non-Cumulative";
}


$result = runQuery("SELECT * FROM gradeproperties WHERE gradeproperties.processname='Final Blend' AND gradeproperties.gradename='$grade' ORDER BY ordering");

$alldata = [];

while ($row=$result->fetch_assoc()) {
    $cprop = $row['properties'];

    $result2 = runQuery("SELECT * FROM processgradesproperties WHERE gradeparam='$cprop' and processname='Final Blend'")->fetch_assoc();

    $cunits = runQuery("SELECT * FROM units WHERE id1='Final Blend' AND id2='$cprop'")->fetch_assoc();

    if(is_null($cunits))
    {
        $cunits="";
        if(substr($cprop,0,5)=="Sieve")
        {
            $cunits="%";
        }
    }
    else
    {
        $cunits=$cunits['unit'];
    }

    if(is_null($result2))
    {
        $mpif    = "";
    }
    else
    {
        $mpif = $result2['mpif'];
    }

    $result2 = runQuery("SELECT * FROM final_coa_grade_settings WHERE gradename='$grade' and property='$cprop' ");

    $PRINT = "-";
    if( $result2->num_rows!=0)
    {
        $PRINT = $result2->fetch_assoc()['print'];
        if($PRINT==1)
        {
            $PRINT = "YES";
        }
        else
        {
            $PRINT = "NO";
        }
    }

    array_push($alldata,[$cprop,$row["min"],$row["max"],$cunits,$mpif,$PRINT]);
}


?>   
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - FINAL BLEND GRADE SPECS SHEET</title>
    <link rel="stylesheet" href="grade.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="sheets-of-paper-a4.css">

    <style>
        h5{font-size:15px;}
        h6{font-size:13px;line-height:14px;margin:0;}
        ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: grey;
            opacity:0.2; /* Firefox */
            }

           .address{color:#D0C9C0;} 
    </style>
   
  </head>
  <body>
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
                <div class="card-header" style="color:#ccc;font-size:14px;">SLM SMART GRADE: <?php echo $grade; ?>
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                </div>
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-4 logo">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h2>SPECS SHEET</h2>
                      </div>
                  </div>

                    <div class="row mb-4 mt-3">
                        <div class="col-sm-6">
                            <h5 class="mb-1">GRADE:</h6>
                            <h3>
                                <strong><?php echo $grade; ?></strong>
                            </h3>
                        </div>
                        <div class="col-sm-6">
                            <h5 class="mt-3">Material: Iron Powder</h6>
                            <h5>Cumulative Property: <?php echo $iscumulative; ?></h6>
                        </div>
                    </div>

                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <tr style="font-size:13px;color:#7F8487;">
                                    <th scope="col" colspan="1" class="center">PROPERTY</th>
                                    <th scope="col" colspan="1" class="center">UNIT</th>
                                    <th scope="col" colspan="1" class="center">MINIMUM</th>
                                    <th scope="col" colspan="1" class="center">MAXIMUM</th>
                                    <th scope="col" colspan="1" class="center">STANDARD</th>
                                    <th scope="col" colspan="1" class="center">SHOW IN COA</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                foreach ($alldata as $data) {
                                    
                                

                                ?>
                                <tr>
                                    <td class="left"><?php echo $data[0]; ?></td>
                                    <td class="center"><?php echo $data[3]; ?></td>
                                    <td class="center"><?php echo $data[1]; ?></td>
                                    <td class="center"><?php echo $data[2]; ?></td>
                                    <td class="center"><?php echo $data[4]; ?></td>
                                    <td class="center"><?php echo $data[5]; ?></td>
                                </tr>

                                <?php 
                            }
                                ?>

                                
                            </tbody>
                        </table>
                    </div>



                    <div class="row">
                      <div class="col-sm-8 my-5 address">
                       <!--  <h6><strong>SLM TECHNOLOGY PRIVATE LIMITED</strong></h6>
                        <h6>UDITNAGAR, ROURKELA-769012, ODISHA, INDIA</h6>
                        <h6>TEL: +91-90400-00519</h6>
                        <h6>EMAIL: info@slmmetal.com, URL: www.slmmetal.com</h6>-->   
                      </div>
                      <div class="col-sm-4 my-2 foot">
                      <h6>Printed on:</h6>
                        <p id="current_dateandtime"><?php echo Date("d-m-Y H:ia",strtotime('now')) ?></p>
                        <textarea rows="3" style="border:0;" placeholder="Ver 1.1"></textarea>           
                      </div>
                    </div>
                             
                    
                </div>
            </div>
        </div>
    </div>
</div>


                        </div></div></div></div></div></div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  </body>
  </html>