<?php
    
	require_once('../../../requiredlibs/includeall.php');
  include('phpqrcode.php');

	if(!isset($_GET["id"]))
  {
    $ERR_TITLE = "Error";
    $ERR_MSG = "You are not authorized to view this page.";
    include("../../pages/error.php");
    die();

  }

	$batchid = $_GET["id"];

  if(!isset($_GET["cid"]))
  {
    $ERR_TITLE = "Error";
    $ERR_MSG = "You are not authorized to view this page.";
    include("../../pages/error.php");
    die();

  }

  $cid = $_GET["cid"];
  $grade = "";
  $proddate = "";
  $qty = "";

  $result = runQuery("SELECT * FROM loadingadvice_batches  WHERE laid='$cid' AND batch='$batchid'");



  if($result->num_rows!=0)
  {
    $result =$result->fetch_assoc();
    $qty = $result['quantity'];
    $grade = $result['grade'];
  }

  $result = runQuery("SELECT * FROM processentry WHERE processid='$batchid'");

  if($result->num_rows==1)
  {
    $proddate = Date('d-M-Y',strtotime($result->fetch_assoc()['entrytime']));
  }
  else
  {
    $result = runQuery("SELECT * FROM premix_batch WHERE premixid='$batchid'");
    if($result->num_rows==1)
    {
      $proddate = Date('d-M-Y',strtotime($result->fetch_assoc()['entrydate']));
    }

  }


  $NEW_LINE = "%0A";

 $qrdata = "";
 //$qrdata .= "Loading Advice: ".$cid.$NEW_LINE;
 $qrdata .= "Batch: ".$batchid.$NEW_LINE;
 $qrdata .= "Grade: ".$grade.$NEW_LINE;
 $qrdata .= "Quantity: ".$qty.$NEW_LINE;
 $qrdata .= "Production Date: ".$proddate.$NEW_LINE;


  
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>SLM SMART - Dispatch Print</title>


  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style type="text/css">
  @page { size: A4 portrait }
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width:100%;
}

table, th, td {
   border-collapse: collapse;
}

 .tbl { border-collapse: collapse; width:100%;font-size:14px;}
  .tbl th, .tbl td { padding:10px 15px; border: solid 1px #000; }
  .tbl th { background-color: #fff; }


th, td {
  padding: 2px 5px;
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
  <h3 style="text-align:center;background-color:#000;color:#fff;top:0;margin:0; width:40%;margin:0 auto;">Dispatch Tag</h3>
    <table style="width:100%;font-size: 12px;">
        <tr>
        <th>
          <img src="/pages/png/slmlogo.png">
          <h2 style="margin-bottom:0;">SLM TECHNOLOGY PVT. LTD.</h2><p style="margin-top: 0;">PLOT NO. 185/A, IDCO PLOT, KALUNGA-770031, ODISHA</p>
        </th>
        <th width="30%">
          <img src="get_qr.php?data=<?php echo $qrdata;?>" width="150px;">
          <p style="font-size:12px;color:#ccc;margin-top:-1rem;font-weight:100;">Scan this for details</p>
        </th>
        </tr>
    </table>
<br>
<table class="tbl">

  <tbody>
        <tr>
            <td><strong>MATERIAL</strong></td>
            <td><input type="text" placeholder="Iron Powder" /></td>
        </tr>
        <tr>
            <td><strong>BATCH NUMBER</strong></td>
            <td><?php echo $batchid; ?></td>
        </tr>
  
        <tr>
            <td><strong>GRADE</strong></td>
            <td><?php echo $grade; ?></td>
        </tr>
 
        <tr>
            <td><strong>DATE OF PRODUCTION</strong></td>
            <td><?php echo $proddate; ?></td>
        </tr>
        <tr>
            <td><strong>QUANTITY(KGS)</strong></td>
            <td><input type="number" min=0 step="0.001" value="<?php echo $qty; ?>" placeholder="Click to edit" /></td>
        </tr>
        <tr>
          <td style="border:0;"><input type="text" placeholder=".." /></td>
          <td style="border:0;"><input type="text" placeholder=".." /></td>
        </tr>
        <tr>
          <td style="border:0;"><input type="text" placeholder=".." /></td>
          <td style="border:0;"><input type="text" placeholder=".." /></td>
        </tr>


    
   
   </tbody>
  
  </table>
      
      
    </div>

    <script type="text/javascript">
    // window.print();
    </script>
  

  </section>

</body>

</html>