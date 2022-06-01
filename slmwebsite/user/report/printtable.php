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


	 if(!isset($_POST["print"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $printname = $_POST["print"];

     if(!isset($_POST["data"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $data = $_POST["data"];

    //$data = str_replace('class="sorting"',"",$data);
    //$data = preg_replace('/width=".*"/', '', $data);
//data = preg_replace('/width.*px/', '', $data);
    //$data = preg_replace('/style="text-align.*"/', '', $data);
    //$data = preg_replace('/style=".*"/', '', $data);

    $startdate = Date('d-M-Y',strtotime($_POST["startdate"]));
    $enddate = Date('d-M-Y',strtotime($_POST["enddate"]));
    $grade = $_POST["grade"];




?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - STOCK REPORT PRINT</title>
    <link rel="stylesheet" href="anneal-rep.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="sheets-of-paper-a4-landscape.css">
    <style>
        .logo{width: 120px;height: auto;}
    </style>
  </head>
  <body>
    <div class="page" contenteditable="true">
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
                        <h4><?php echo strtoupper($printname ) ?> REPORT FOR <?php echo $startdate ?> to <?php echo $enddate ?></h4>
                      </div>
                  </div>
            
            <hr>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm">
                        <table id="datatable"  class="table table-bordered">
                            <?php 

                            echo $data;
                            ?>
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


<script type="text/javascript">
	




</script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript">
    	$('#datatable [style]').removeAttr('style');
    	$('#datatable [class]').removeAttr('class');
    	$('#datatable [aria-controls]').removeAttr('aria-controls');
    	$('#datatable [aria-label]').removeAttr('aria-label');
    	$('#datatable [aria-sort]').removeAttr('aria-sort');
    	$('#datatable [width]').removeAttr('width');
    </script>
  </body>
  </html>