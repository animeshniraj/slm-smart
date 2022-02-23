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



	if(!isset($_POST["compareids"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

   $ids = $_POST["compareids"];


   $result = runQuery("SELECT * FROM processentry WHERE processid='$ids[0]'");

   $data = [];
   if($result->num_rows==1)
   {

   		$count = 0;
   		$header = ["Batch Number","Dispatch Date","Dispatch Qty"];
   		foreach ($ids as $id) {
   			
   			$data[$count] = [];
   			$currid = "\"".$id."\"";
   			
   			$data[$count]['Batch Number'] = $id;
   			$data[$count]['Dispatch Date'] = $_POST['dispatchdate'][$currid];
   			$data[$count]['Dispatch Qty'] = $_POST['dispatchqty'][$currid];

   			

   			$result = runQuery("SELECT * FROM processtest WHERE processid='$id'");

			while($row=$result->fetch_assoc())
			{
				$dumtestid = $row["testid"];
				$result2 = runQuery("SELECT * FROM processtestparams WHERE testid='$dumtestid'");
				while($row2 = $result2->fetch_assoc())
				{
					if(!isset($data[$count][$row2['param']]['value']))
					{
						$data[$count][$row2['param']] =[];
						$data[$count][$row2['param']]['value'] =[];

						if(!in_array($row2['param'], $header))
						{
							array_push($header,$row2['param']);
						}
					}
					array_push($data[$count][$row2['param']]['value'],$row2['value']);
					
				}
			}

			foreach ($data[$count] as $key => $property) {

				if($key =="Batch Number" || $key =="Dispatch Date" || $key =="Dispatch Qty")
				{
					continue;
				}
				$data[$count][$key] = array_sum($property["value"])/count($property["value"]);


			}



   			$count++;
   		}


   }

   else
   {


   		$count = 0;
   		$header = ["Batch Number","Dispatch Date","Dispatch Qty"];
   		foreach ($ids as $id) {
   			$data[$count] = [];
   			$currid = "\"".$id."\"";
   			
   			$data[$count]['Batch Number'] = $id;
   			$data[$count]['Dispatch Date'] = $_POST['dispatchdate'][$currid];
   			$data[$count]['Dispatch Qty'] = $_POST['dispatchqty'][$currid];



   			$result = runQuery("SELECT * FROM premix_batch_test WHERE premixid='$id'");

			while($row=$result->fetch_assoc())
			{
				$dumtestid = $row["testid"];
				$result2 = runQuery("SELECT * FROM premix_batch_testparams WHERE testid='$dumtestid'");
				while($row2 = $result2->fetch_assoc())
				{
					if(!isset($data[$count][$row2['param']]['value']))
					{
						$data[$count][$row2['param']] =[];
						$data[$count][$row2['param']]['value'] =[];

						if(!in_array($row2['param'], $header))
						{
							array_push($header,$row2['param']);
						}
					}
					array_push($data[$count][$row2['param']]['value'],$row2['value']);
					
				}
			}

			foreach ($data[$count] as $key => $property) {

				if($key =="Batch Number" || $key =="Dispatch Date" || $key =="Dispatch Qty")
				{
					continue;
				}
				$data[$count][$key] = array_sum($property["value"])/count($property["value"]);


			}


   			$count++;
   		}
   }




?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SLM SMART - Compare</title>
    <link rel="stylesheet" href="coa.css" media="all" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
  </head>
  <body>
    <div class="container-fluid">
    <div id="ui-view" data-select2-id="ui-view">
        <div>
            <div class="card">

                <div class="card-body">

                  <div class="row">
                      <div class="col-sm-3 logo">
                        <img src="logo.png">
                      </div>

                  </div>

                    
                  <br>
                  <br>
                    <div class="table-responsive-sm">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="<?php echo count($header); ?>" class="center"><h2>Comparison</h2></th>
                                </tr>
                                <tr>
                                  <?php  

                                  	foreach ($header as $key) {
                                  		echo "<th>".$key."</th>";
                                  	}

                                  ?>
                                </tr>
                            </thead>
                            <tbody>

                            	<?php 

                            		for ($i=0; $i < count($data); $i++) { 
                            	?>

                            		<tr>
                            			<?php 
                            				foreach ($header as $key) {
                            					
                            			?>
                            				<td> <?php echo $data[$i][$key] ?></td>

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






                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  </body>
  </html>