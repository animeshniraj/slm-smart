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



	if(!isset($_POST["laid"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $laid = $_POST["laid"];


    if(!isset($_POST["delivery_date"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $delivery_date = $_POST["delivery_date"];


    if(!isset($_POST["customer"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $customer = $_POST["customer"];

    if(!isset($_POST["customer_address"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $customer_address = $_POST["customer_address"];


    $customer_address = $customer_address[0] . ", " . $customer_address[1] . ", " . $customer_address[2];

	if(!isset($_POST["datagrade"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $datagrade = $_POST["datagrade"];



    if(!isset($_POST["databatch"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $databatch = $_POST["databatch"];


     if(!isset($_POST["datapkg"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $datapkg = $_POST["datapkg"];


      if(!isset($_POST["dataqty"]))
    {
    	$ERR_TITLE = "Error";
    	$ERR_MSG = "You are not authorized to view this page.";
    	include("../../pages/error.php");
    	die();

    }

    $dataqty = $_POST["dataqty"];



	require_once('../process/helper.php');
    $PAGE = [
        "Page Title" => "Edit Loading Advice | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "loadingadvice-view",
        "MainMenu"	 => "dispatch_menu",

    ];

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>SLM SMART - Loading Advice</title>


  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style type="text/css">
  @page { size: A5 landscape }
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width:100%;
}

table, th, td {
   border-collapse: collapse;
}

 .tbl { border-collapse: collapse; }
  .tbl th, .tbl td { padding: 5px; border: solid 1px #000; }
  .tbl th { background-color: dimgray; }


th, td {
  padding: 5px;
  width: 200px!important;
}
.crop{width: 150px;
height:40px;
object-fit:cover;}

tr.noBorder td {
  border: 0;
}
.bdcen{font-weight:bold;text-align: center;}

input {
background-color: white;
color: #000;
border: none;
}

select {
background-color: white;
color: #000;
border: none;
}
</style>

<script type="text/javascript" src="/pages/js/jquery.min.js"></script>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 portrait" style="font-family:Arial">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">
  <h3 style="text-align:center;background-color:#000;color:#fff;top:0;margin:0; width:40%;margin:0 auto;">LOADING ADVICE -  <?php echo $laid; ?></h3>
  <table style="width:100%;font-size: 12px;">
    <tr>
      <th colspan="1"><img src="/pages/png/slmlogo.png" class="center"><p style="font-size:10px;">TAG ID - SLM/MKT/DOC-16</p></th>
      <th colspan="3" style="text-align:right;"><h2 style="margin-bottom:0;">SLM TECHNOLOGY PVT. LTD.</h2><p style="margin-top: 0;">PLOT NO. 185/A, IDCO PLOT, KALUNGA-770031, ODISHA</p></th>
    </tr>
    <tr><td colspan="4" ><strong>Name of Supervisor: </strong><input type="text" placeholder="Click to edit" /> </td></tr>
    <tr>
      <td colspan="3"><strong>Name of Party:</strong><input type="text" value="<?php echo $customer; ?>" /> </td>
      <td colspan="1"><strong>Date:</strong> <?php echo Date('d-M-Y',strtotime($delivery_date)) ?></td>
    </tr>
    <tr>
      <td colspan="3"><strong>Destination:</strong> <?php echo $customer_address; ?></td>
      <td colspan="1"><strong>Vehicle No.:</strong> <input type="text" placeholder="Click to edit" style="width:115px;" /></td>
    </tr>
</table>

<table class="tbl" style="font-size: 12px;">
  <thead style="color:#fff;">
    <th style="width:10%">Sl. No.</th>
    <th style="width:40%">Grade</th>
    <th style="width:15%"style="width:15%">Batch/Lot No.</th>
    <th style="width:15%;text-align:right">Qty. in Kgs</th>
    <th style="width:10%">Qty. in Units</th>
    <th style="width:10%">Package</th>
  </thead>
  <tbody>

  	<?php 

  	$total = 0;

  		for($i=0;$i<count($datagrade);$i++)
  		{
  			$total+=$dataqty[$i];
  	?>
    <tr>
      <td style="width:10%"><?php echo $i+1; ?></td>
      <td style="width:40%"><?php echo $datagrade[$i];?></td>
      <td style="width:15%"><?php echo $databatch[$i];?></td>
      <td style="width:15%;text-align:right"><?php echo $dataqty[$i];?></td>
      <td style="width:10%;text-align:right"><input type="number" onchange="addtotal()" min="0" name="batchqty[]" value="0" placeholder="Click to edit" style="width:80px; text-align: right;" /></td>
      <td style="width:10%;text-align:right">
        <select id="packageselect-<?php echo $i; ?>" name="package">
          <?php

							$result2 = runQuery("SELECT * FROM dispatch_package");

							while($row2=$result2->fetch_assoc())
							{
								?>


								<option value="<?php echo $row2["packagename"] ?>"><?php echo $row2["packagename"] ?></option>

								<?php
							}

						?>
        </select>

        <script type="text/javascript">
        	document.getElementById("packageselect-<?php echo $i; ?>").value="<?php echo $datapkg[$i];?>";
        </script>
      </td>
    </tr>

    <?php 

    	}

    ?>
   
   </tbody>

   <tfoot>
   	 <tr style="font-weight: bold;">
      <td colspan="3" style="width: 65%;text-align:right;">TOTAL</td>
      <td style="width: 15%;text-align:right"><?php echo $total; ?></td>
      <td style="width: 10%;text-align:right" id="totalqty-div">0</td>
      <td style="width:10%;text-align:right">
        
      </td>
    </tr>
    <tr>
      <td colspan="6">Notes: <input type="text" placeholder="Click to edit"/></td>
    </tr>
   </tfoot>
  
  </table>
      
  <script type="text/javascript">
   	
   	addtotal();
   	function addtotal()
   	{
   		var elements = document.querySelectorAll("input[name='batchqty[]']")
   		var total = 0;
   		for(var i=0;i<elements.length;i++)
   		{
   			total += parseFloat(elements[i].value);

   		}

   		if(total)
   		{
   			document.getElementById("totalqty-div").innerHTML = total;
   		}
   		
   	}
   </script>
    <table style="width:100%;font-size: 12px;margin-top:1rem;">

    <tr>
      <th colspan="2" ><input type="text" placeholder="Click to edit" style="text-align:center;"/></th>
      <th colspan="2" ><input type="text" placeholder="Click to edit" style="text-align:center;"/></th>
      <th colspan="2" ><input type="text" placeholder="Click to edit" style="text-align:center;"/></th>
    </tr>
    <tr>
      <th colspan="2">Prepared by</th>
      <th colspan="2">Passed by</th>
      <th colspan="2">Loaded by</th>
    </tr>
    </table>
      
    </div>

    <script type="text/javascript">
    // window.print();
    </script>
  

  </section>

</body>

</html>