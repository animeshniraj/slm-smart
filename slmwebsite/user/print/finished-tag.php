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

	$processid = $_GET['processid'];
	$gradename = $_GET['grade'];

  if($gradename=="**")
  {
    $gradename="No Grade Selected";
  }

  $result = runQuery("SELECT * FROM processentry WHERE processid='$processid'")->fetch_assoc();

  $entrytime = $result['entrytime'];

  $blendno = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='Blend Number'")->fetch_assoc()['value'];


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Finished Tag</title>


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
  width: 40%;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

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
</style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A5 landscape">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">


  <h3 style="text-align:center;background-color:#000;color:#fff;top:0;margin:0;">MATERIAL IDENTIFICATION TAG</h3>
  <table style="width:100%">
    <tr>
      <th colspan="2"><img src="/pages/png/slmlogo.png" class="center"></th>
      <th colspan="2" ><h4>STAGE: FINISHED BLENDED POWDER</h4></th>
    </tr>
    <tr><td colspan="4" class="bdcen">INSPECTION DUE</td></tr>
    <tr>
      <td colspan="4"><img src="barcode.php?text=<?php echo $processid;?>" class="center"></td>
    </tr>
    <tr>
      <td colspan="2" style="font-weight:bold;">Date</td>
      <td colspan="2" style="text-align:right;"><?php echo Date('d-M-Y',strtotime($entrytime)); ?></td>
    </tr>
    <tr>
      <td colspan="2" style="font-weight:bold;">Grade</td>
      <td colspan="2" style="text-align:right;"><?php echo $gradename; ?></td>
    </tr>
    <tr>
      <td colspan="2" style="font-weight:bold;">Blend ID (Bag No.)</td>
      <td colspan="2" style="text-align:right;"><?php echo $blendno; ?></td>
    </tr>
    <tr>
    </tr>
    <tr>
      <td style="font-weight:bold;">Filled By</td>
      <td style="height:80px;"></td>
      <td style="font-weight:bold;">Weight OK by</td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;font-weight:bold;font-size:18px;">DO NOT DISPATCH</td>
      <td colspan="2" style="text-align:center;font-weight:bold;font-size:18px;">NEXT OPERATION -<br> QC APPROVAL</td>
    </tr>
    <tr>
    </tr>
  </table>
<p style="text-align:right;font-size:12px;font-weight:400;">TAG-07(A)</p>
      
      
    </div>

    <script type="text/javascript">
    // window.print();
    </script>
  

  </section>

</body>

</html>