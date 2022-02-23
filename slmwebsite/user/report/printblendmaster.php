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
    $specdata = $_POST["specdata"];

    //$data = str_replace('class="sorting"',"",$data);
    //$data = preg_replace('/width=".*"/', '', $data);
//data = preg_replace('/width.*px/', '', $data);
    //$data = preg_replace('/style="text-align.*"/', '', $data);
    //$data = preg_replace('/style=".*"/', '', $data);

    




?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - BLEND MASTER PRINT</title>
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
                        <h4><?php echo strtoupper($printname ) ?> Blend Master</h4>
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



                <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm">
                        <table id="specdatatable"  class="table table-bordered">
                            <?php 

                            echo $specdata;
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
        $('#datatable [onchange]').removeAttr('onchange');


        $('#specdatatable [style]').removeAttr('style');
        $('#specdatatable [class]').removeAttr('class');
        $('#specdatatable [aria-controls]').removeAttr('aria-controls');
        $('#specdatatable [aria-label]').removeAttr('aria-label');
        $('#specdatatable [aria-sort]').removeAttr('aria-sort');
        $('#specdatatable [width]').removeAttr('width');
        $('#specdatatable [onchange]').removeAttr('onchange');

        

        $( document ).ready(function() {
            updatetable()


            
        });
       


        /*
        for(var i=0;i<tbody.children.length-1;i++)
        {
            curr = tbody.children[i];
            

            console.log(curr.children[curr.children.length-1].innerHTML);

            curr.children[curr.children.length-1].innerHTML = curr.children[curr.children.length-1].children[0].value;

            currtr = curr.children[0];

            //console.log(currtr.children[0].find('input'));

             if(!curr.children[0].children[0].children[0].checked)
            {
               curr.remove();
               continue;
            }
            else
            {
                //console.log(curr);
            }



            
        }

        */


        function updatetable()
        {
            var tbody = document.getElementById('blendmasterparenttbody');

            currcontent = tbody.innerHTML;
            var alltr = document.getElementsByName("parentname[]");
            
            for(var i=0; i<alltr.length;i++)
            {
                
                if(!alltr[i].checked)
                {
                   alltr[i].closest('tr').remove();
                }
            }

            var alltr = document.getElementsByName("parentvalues[]");
            
            for(var i=0; i<alltr.length;i++)
            {
                
                
                   alltr[i].closest('td').innerHTML = alltr[i].value;
                
            }

            if(currcontent!=tbody.innerHTML)
            {
                console.log(1);
                updatetable();
            }

        }

            



         

    </script>
  </body>
  </html>