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

     if(!isset($_POST["entrydate"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }

    $entrydate = $_POST["entrydate"];


     if(!isset($_POST["grade"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }

    $grade = $_POST["grade"];


     if(!isset($_POST["batchno"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }

    $batchno = $_POST["batchno"];

     if(!isset($_POST["specdata"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();

    }

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
        input {
                background-color: white;
                color: #000;
                border: none;
                }
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
                        <table class="table table-bordered" style="text-align:center;">
                            <tr>
                                <th>Blend ID</th>
                                <th><?php echo $batchno ?></th>
                                <th>Date & Time</th>
                                <th><?php echo $entrydate ?></th>
                                <th>Grade</th>
                                <th><?php echo $grade ?></th>
                                <th>Batch No.</th>
                                <th><input type="text" placeholder="Click to edit" style="text-align:right;"/></th>
                            </tr>
                        </table>
                    </div>
                </div>


                </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm" style="text-align:center;">
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
                    <div class="table-responsive-sm" style="text-align:center;">
                        <table id="specdatatable"  class="table table-bordered">
                            <?php 

                            echo $specdata;
                            ?>

                            <tfoot id="specdatatablefoot">
                                
                            </tfoot>
                        </table>

                    </div>
                </div>


                </div>

                <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm">
                        <?php
                        $alldata = [];
                        $result = runQuery("SELECT * FROM  gradeproperties LEFT JOIN  processgradesproperties ON processgradesproperties.gradeparam= gradeproperties.properties  WHERE gradeproperties.processname='$printname' AND gradeproperties.gradename='$grade' AND  processgradesproperties.processname='$printname' AND processgradesproperties.class='Chemical'");
                                        while($row=$result->fetch_assoc())
                                        {


                                            array_push($alldata,[$row['properties'],$row['min'],$row['max']]);
                                        }
                        ?>
                        <table id="datatablechemical"  class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Chemical Properties</th>

                                    <?php 
                                        foreach ($alldata as $value) {
                                            echo "<th>".$value[0]."</th>"; 
                                        }

                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Specs</td>
                                    <?php 
                                        foreach ($alldata as $value) {
                                            
                                            if($value[1]=="BAL" || $value[2]=="BAL")
                                            {
                                                echo "<td>".$value[0]."-".$value[1]."</td>"; 
                                            }
                                            elseif($value[1]==0 && $value[2])
                                            {
                                                 echo "<td><".$value[2]."</td>"; 
                                            }
                                            elseif($value[2]==0 && $value[1])
                                            {
                                                 echo "<td>>".$value[1]."</td>"; 
                                            }
                                            else
                                            {
                                                echo "<td></td>";
                                            }

                                        }

                                    ?>

                                </tr>
                                <tr>
                                    <td>Specs</td>
                                    <?php 
                                        foreach ($alldata as $value) {
                                    ?>
                                        <td>
                                        <input type="text" placeholder=""/></td>
                                    <?php
                                        }

                                    ?>

                                </tr>
                            </tbody>
                            <tr>
                                
                            </tr>
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


            noCols = document.getElementById('specdatatable').children[0].children[0].children.length;
        
        var tr = document.createElement('tr');
        tr.innerHTML="<td>Manual Entry</td>";
        for(var i=0;i<noCols-1;i++)
        {
            //tr.innerHTML+="<td><input type=\"text\" placeholder=\"\" /></td>";
            tr.innerHTML+="<td></td>";

        }

        document.getElementById('specdatatablefoot').appendChild(tr);


            
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
                //console.log(1);
                updatetable();
            }

        }

            



         

    </script>
  </body>
  </html>