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
    <link rel="stylesheet" href="printbm.css" media="all" />
    <link rel="stylesheet" href="/../../pages/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="print-bm-all.css">
  </head>
<style>
     @media print {
      
    .table-bordered th {border: 1px solid #000 !important;}
     
    .table-bordered td {border: 1px solid #000 !important;}
     }
</style>
  <body>
    <section class="sheet">
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
                        <h4><?php echo strtoupper($printname ) ?> MASTER</h4>
                      </div>
                  </div>
            
           
            <hr>

             <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-bordered bms w-auto" style="text-align:center;">
                            <tr>
                                <th>Blend ID</th>
                                <th><?php echo $batchno ?></th>
                                <th>Date & Time</th>
                                <th><?php echo $entrydate ?></th>
                                <th>Grade</th>
                                <th><?php echo $grade ?></th>
                                <th>Blend Number</th>
                                <th><?php echo getBlendID($batchno) ?></th>
                            </tr>
                        </table>
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm" style="text-align:center;">
                        <table id="datatable"  class="table table-striped table-bordered w-auto">
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
                        <table id="specdatatable"  class="table table-striped table-bordered w-auto">
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
                    <div class="table-responsive-sm" style="text-align:center;">
                        <?php
                        $alldata = [];
                        $result = runQuery("SELECT * FROM  gradeproperties LEFT JOIN  processgradesproperties ON processgradesproperties.gradeparam= gradeproperties.properties  WHERE gradeproperties.processname='$printname' AND gradeproperties.gradename='$grade' AND  processgradesproperties.processname='$printname' AND processgradesproperties.class='Chemical'");
                                        while($row=$result->fetch_assoc())
                                        {


                                            array_push($alldata,[$row['properties'],$row['min'],$row['max']]);
                                        }
                        ?>
                        <table id="datatablechemical"  class="table table-striped table-bordered w-auto table-xs">
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
                                    <td>Actual Value</td>
                                    <?php 
                                        foreach ($alldata as $value) {
                                    ?>
                                        <td>
                                        <input type="text" placeholder="" style="width:80px;"></td>
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

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-bordered w-auto" style="text-align:center;">
                            <tr>
                                <th>Blend Time</th>
                                <th>Packaging</th>
                                <th>Batch No.</th>
                            </tr>
                            <tr>
                                <th><input type="text" placeholder="" style="width:100px;"></td></th>
                                <th><input type="text" placeholder="" style="width:100px;"></td></th>
                                <th><input type="text" placeholder="" style="width:100px;"></td></th>
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

</section>
<script type="text/javascript">
	




</script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="/../../pages/js/jquery.min.js"></script>
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

        
        let send_list,heading_data;
        send_list = [];
        heading_data = [];                              
        

        $( document ).ready(function() {
            updatetable()
            rename_header()

            noCols = document.getElementById('specdatatable').children[0].children[0].children.length;
        
        var tr = document.createElement('tr');
        tr.innerHTML="<td>Actual Value</td>";
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

        
        function rename_header()
        {
            var heads = document.getElementById('blendmasterparentthead').children[0];
            omit_header = ["bag id","date","grade", "bal qty", "blend qty"]
            
            for(var i =0; i< heads.children.length;i++)
            {
                curr = heads.children[i]
                
                if(curr.localName !="th")
                    continue
                
                if(omit_header.includes(curr.innerHTML.toLowerCase()))
                    continue
                
                send_list.push(curr.innerHTML)
                heading_data.push(curr)
                
            }


            var postData = new FormData();
       
            postData.append("action","getShortnames");
            postData.append("processname","<?php echo $printname?>");
            postData.append("properties",send_list);
            
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                
                
                var data = JSON.parse(this.responseText);
            
                if(data.response)
                {
                    
                    

                   for(var i=0;i<data.names.length;i++)
                    {
 
                        heading_data[i].innerHTML = data.names[i];
                    }


                }
                
            
            }
            };
            xmlhttp.open("POST", "/query/report.php", true);
            xmlhttp.send(postData);


        }

            



         

    </script>
  </body>
  </html>