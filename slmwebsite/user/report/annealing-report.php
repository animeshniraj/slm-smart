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

	echo "Error";
    die();

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

    






    include("../../pages/userhead.php");
    include("../../pages/usermenu.php");





?>

<div class="card">
                <div class="card-header">Batch ID: AF2-21-002
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                        <i class="fa fa-save"></i> Download</a>
                </div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-sm-4 logo">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 certificate">
                        <h4>ANNEALING FURNACE INPUT MATERIAL RECORD</h4>
                      </div>
                  </div>

                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:20%">DATE</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%"></td>
                                    <td scope="col" colspan="2" class="center" style="width:20%">GRADE</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:18%">FURNACE NO.</td>
                                    <td scope="col" colspan="1" class="center" style="width:12%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:20%">BATCH NO.</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%"></td>
                                    <td scope="col" colspan="2" class="center" style="width:20%">FEED TIME</td>
                                    <td scope="col" colspan="1" class="center" style="width:15%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:18%">OUTPUT TIME</td>
                                    <td scope="col" colspan="1" class="center" style="width:12%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


            <div class="row">
                <div class="col-sm-6">
                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <th scope="col" colspan="2" class="center">OPERATING PARAMETERS</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:50%">TEMP °C</td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 1</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 2</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 3</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 4</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 5</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 6</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 7</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 8</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 9</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">Zone 10</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <th scope="col" colspan="3" class="center">PROPERTIES OF MATERIAL</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%">INPUT</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%">OUTPUT</td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%">AD</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%">GD</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%">H2 Loss</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:33%">Thickness</td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                    <td scope="col" colspan="1" class="center" style="width:33%"></td>
                                </tr>
                         </tbody>
                        </table>
                    </div>

                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <thead>
                                <th scope="col" colspan="2" class="center">OTHER DETAILS</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">HOPPER ID</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">GAS RATIO</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">TOTAL RUNNING HRS.</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td scope="col" colspan="1" class="center" style="width:50%">FEED RATE KG/HR</td>
                                    <td scope="col" colspan="1" class="center" style="width:50%"></td>
                                </tr>
                         </tbody>
                        </table>
                    </div>
                </div>
            </div>


                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="left" style="width:50%">BELT SPEED (MM/MIN)</td>
                                    <td class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td class="left" style="width:50%">CRACK AMMONIA FLOW (NM3/HR)</td>
                                    <td class="center" style="width:50%"></td>
                                </tr>
                                <tr>
                                    <td class="left" style="width:50%">NITROGEN FLOW (NM3/HR)</td>
                                    <td class="center" style="width:50%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="table-responsive-sm">
                        <table class="table table-bordered">
                             <thead>
                                <tr>
                                    <th scope="col" colspan="2" class="center">BREAKDOWN DETAILS</th>
                                    <th scope="col" colspan="1" class="center" style="width:20%">TOTAL TIME</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="left" style="width:30%">NOTES 1</td>
                                    <td class="center" style="width:50%"></td>
                                    <td class="center" style="width:20%"></td>
                                </tr>
                                <tr>
                                    <td class="left" style="width:30%">NOTES 1</td>
                                    <td class="center" style="width:50%"></td>
                                    <td class="center" style="width:20%"></td>
                                </tr>
                                <tr>
                                    <td class="left" style="width:30%">NOTES 1</td>
                                    <td class="center" style="width:50%"></td>
                                    <td class="center" style="width:20%"></td>
                                </tr>
                                <tr>
                                    <td class="left" style="width:30%">NOTES 1</td>
                                    <td class="center" style="width:50%"></td>
                                    <td class="center" style="width:20%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                <div class="remarks">
                    <h5>Remarks:</h5>
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics.</p>
                </div>

            </div>



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