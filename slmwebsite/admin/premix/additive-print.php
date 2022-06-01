<head>
    <meta charset="utf-8">
    <title>SLM SMART - ADDITIVE FORECAST</title>
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

	isAuthenticated($session,'admin_module');

	

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "premix-additives-forcast",
        "MainMenu"	 => "premix_menu",

    ];


    $alldata = [];
    $result = runQuery("SELECT * FROM premix_grades");

    while($row=$result->fetch_assoc())
    {
    	$curr = $row["gradename"];
    	$alldata[$curr] = [];

    	$result2 = runQuery("SELECT * FROM premix_grade_compositions WHERE gradename='$curr' AND  additive<>'Iron'");
    	while($row2=$result2->fetch_assoc())
    	{
    		array_push($alldata[$curr],[$row2["additive"],$row2["composition"]]);
    	}
    }




    $showmonth = date('F');
	$showmonthInt = date('m');
	$showyear = date('Y');


    if(isset($_POST['deleteProcess']))
    {
    	$externalid = $_POST['externalid'];
    	runQuery("DELETE FROM additive_external WHERE externalid='$externalid'");
    }


    $startdate =  new DateTime(date('Y')."-$showmonthInt-01");
    $endDate = new DateTime(date('Y-m-d',strtotime("tomorrow")));
   

  	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($startdate, $interval, $endDate);




     $allAdditive = [];
    $daily = [];
    $allrecon = [];
    $allAdditiveBuyIncrement = [];

    $result = runQuery("SELECT * FROM premix_additives WHERE additive<>'Iron'");

    while($row=$result->fetch_assoc())
    {	
    	$curr = $row["additive"];

    	$allAdditiveBuyIncrement[$curr] = $row["buyincrement"];

    	$daily[$curr] = [];
    	$allrecon[$curr] =[];

    	foreach ($period as $dt) {
    		$daily[$curr][strval($dt->format('d'))] = 0;
    	}

    	



    	$openingStock =0;
    	$currStock =0;

    	
    	$date = date(date('Y')."-$showmonthInt-01");

    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' AND internalid IN (SELECT internalid FROM additive_internal WHERE entrydate<'$date') GROUP BY additive");

    	if($result2->num_rows>0)
    	{
    		$openingStock = $result2->fetch_assoc()["qty"];
    	}

    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE status='NOTOVER' AND additive='$curr' GROUP BY additive");

    	if($result2->num_rows>0)
    	{
    		$currStock = $result2->fetch_assoc()["qty"];


    		foreach ($period as $dt) {

    			$dumdt = $dt->format('Y-m-d');

    			
    			
    			$result2 = runQuery("SELECT additive,sum(value)as qty FROM premix_batch_params LEFT JOIN additive_internal on additive_internal.internalid=premix_batch_params.param WHERE step = 'BATCH SELECTION' AND additive='$curr' AND premixid IN (SELECT premixid FROM premix_batch WHERE DATE(entrydate)='$dumdt') GROUP BY additive");
	    		if($result2->num_rows>0)
		    	{
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$daily[$curr][strval($dt->format('d'))] += -1*$dumqty;
		    		$currStock = $currStock - $dumqty;
		    	}

		    	$result2 = runQuery("SELECT additive,SUM(mass)as qty FROM additive_internal WHERE DATE(entrydate)='$dumdt' AND additive='$curr' GROUP BY additive");

		    	if($result2->num_rows>0)
		    	{
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$daily[$curr][strval($dt->format('d'))] += 1*$dumqty;
		    		
		    	}

		    	$result2 = runQuery("SELECT SUM(adjustment) as qty FROM stock_reconciliation WHERE internalid IN (SELECT internalid FROM additive_internal WHERE additive='$curr' AND status='NOTOVER') AND DATE(entrytime)='$dumdt' GROUP BY internalid");

		    	if($result2->num_rows>0)
		    	{
		    		if(!isset($allrecon[$curr][strval($dt->format('d'))]))
		    		{
		    			$allrecon[$curr][strval($dt->format('d'))] = 0;
		    		}
		    		$dumqty = $result2->fetch_assoc()["qty"];
		    		$allrecon[$curr][strval($dt->format('d'))] += $dumqty;
		    		$currStock += $dumqty;
		    		
		    	}

    		}

    		    		
    	}
    	


    	array_push($allAdditive,[$row["additive"],$openingStock,$currStock]);


    }



    $result = runQuery("SELECT * FROM premix_additives_groups ");

    while($row=$result->fetch_assoc())
    {	
    	$curr = $row["groupname"];

    	$allAdditiveBuyIncrement[$curr] = 1;
    	$openingStock = 0;
    	$currStock =0;

    	$result2 = runQuery("SELECT * FROM premix_additives_group_member WHERE groupname='$curr' ");
    	while($row2=$result2->fetch_assoc())
    	{
    		$key = array_search($row2["additive"], array_column($allAdditive,0));
    		$openingStock+=$allAdditive[$key][1];
    		$currStock+=$allAdditive[$key][2];
    	}	


    	array_push($allAdditive,[$row["groupname"],$openingStock,$currStock]);

    }






?>




<div class="page" contenteditable="true">
    <div id="ui-view" data-select2-id="ui-view">

    <div class="card">
                <div class="card-header">
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                        <i class="fa fa-save"></i> Download</a>
                </div>
                <div class="card-body">

                    <div class="row">
                      <div class="col-sm-4 logo mb-1">
                        <img src="logo.png">
                      </div>
                      <div class="col-sm-8 pull-right">
                        <h4>ADDITIVE FORECAST</h4>
                      </div>
                    </div>

                    <div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

    <div class="page-body">
        <div class="row">
            <div class="col-lg-12">

                        <table class="table table-striped table-bordered" id="process4table">
                    <thead>
                    <tr>
                        <th>Sl. No</th>
                        <th>Gradename</th>
                        <th>Customer</th>
                        <th>Quantity(kg)</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody id="products-tbody">
                        

                    

                </tbody>

            </table>

            <br>
            <br>
            <br>
            <br>
            <hr style="border-top:2px solid;">
            <br>
            <br>



            <br>
            <br>
            <br>
            <br>


            <table class="table table-striped table-bordered">
            <script type="text/javascript"> let additivedata;additivedata=[]</script>
                <thead>
                    <tr>
                        <th>Additive</th>
                        <th>Required Qty(kg)</th>
                        <th>Current Stock(kg)</th>
                        <th>Procurement Quantity(kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $k =0;
                        foreach ($allAdditive as $additive) {
                        
                    ?>

                    <tr>

                        <td><?php echo $additive[0]; ?></td>
                        <td id="additive-req-<?php echo $k; ?>">0</td>
                        <td id="additive-curr-<?php echo  $k; ?>"><?php echo $additive[2]; ?></td>
                        <td id="additive-proc-<?php echo $k; ?>">0</td>
                        <script type="text/javascript"> 
                            
                            additivedata['<?php echo $additive[0]; ?>'] = [this.document.getElementById('additive-req-<?php echo $k; ?>'),this.document.getElementById('additive-curr-<?php echo $k; ?>'),this.document.getElementById('additive-proc-<?php echo $k; ?>'),<?php echo $allAdditiveBuyIncrement[$additive[0]]?>]</script>
                    </tr>


                    <?php
                    $k++;
                        } 
                    ?>
                </tbody>
            </table>


            <script type="text/javascript">
                
                function addtolist()
                {
                    var gradenamediv = document.getElementById('select-gradename')
                    var gradename = gradenamediv.value;

                    var customerdiv = document.getElementById('select-customer')
                    var customer = customerdiv.value;

                    var quantitydiv = document.getElementById('select-qty')
                    var quantity = quantitydiv.value;

                    if(!quantity || customer =="Choose a customer" || gradename=="Choose a grade")
                    {
                        return;
                    }
                    
                    
                            var tr =  document.createElement('tr');
                            var count = parseInt(document.getElementById('products-tbody').children.length) +1;
                            tr.innerHTML = "<td>"+count+"</td><td>"+gradename+"</td><td>"+customer+"</td><td>"+quantity+"</td><td><button type=\"button\" class=\"btn btn-danger\" onclick=\"this.closest('tr').remove();reeval();\"><i class=\"fa fa-trash\"></i>Remove</button></td>"
                            document.getElementById('products-tbody').appendChild(tr);
                        
                    reeval()
                }

                function reeval()
                {

                    var tbody = document.getElementById('products-tbody');


                    console.log(additivedata)

                    Object.keys(additivedata).forEach(function (key) {

                        additivedata[key][0].innerHTML=0;
                        additivedata[key][2].innerHTML=0;
                    });

                    for(var i=0;i<tbody.children.length;i++)
                    {
                        var curr  = tbody.children[i];

                        var qty = parseFloat(curr.children[3].innerHTML);
                        var c_grade = curr.children[1].innerHTML;

                        var c_grade_data = gradedata[c_grade];

                        for(var j=0;j<c_grade_data.length;j++)
                        {
                            
                            var currAdditive = c_grade_data[j][0];
                            
                            var currpercent = parseFloat(c_grade_data[j][1]);
                            //console.log(currAdditive,currpercent);
                            var req = (currpercent/100)*qty;
                            if(req!=0)
                            {
                                req += parseFloat(additivedata[currAdditive][0].innerHTML);
                                req = Math.round(req*1000)/1000;;
                                
                                //console.log(additivedata[currAdditive]);
                                additivedata[currAdditive][0].innerHTML = req;

                                var proc = req -parseFloat(additivedata[currAdditive][1].innerHTML);

                                if(proc>0)
                                    additivedata[currAdditive][2].innerHTML = 	Math.ceil(proc/additivedata[currAdditive][3])*additivedata[currAdditive][3];	
                            }
                            


                        }

                    }


                    return;
                }


            </script>








            </div>
        </div>
    </div>

</div>
</div>
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


<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>




                </div>
    </div>