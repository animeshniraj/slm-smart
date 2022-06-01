<?php
    
	require_once('../../../requiredlibs/includeall.php');

	
	
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

 .tbl { border-collapse: collapse; width:100%;font-size:14px;}
  .tbl th, .tbl td { padding: 15px; border: solid 1px #000; }
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
<body class="A5 landscape" style="font-family:Arial">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">
  <h3 style="text-align:center;background-color:#000;color:#fff;top:0;margin:0; width:40%;margin:0 auto;">Dispatch - Dispatch ID</h3>
    <table style="width:100%;font-size: 12px;">
        <tr>
        <th colspan="1"><img src="/pages/png/slmlogo.png" class="center"></th>
        <th colspan="3" style="text-align:right;"><h2 style="margin-bottom:0;">SLM TECHNOLOGY PVT. LTD.</h2><p style="margin-top: 0;">PLOT NO. 185/A, IDCO PLOT, KALUNGA-770031, ODISHA</p></th>
        </tr>
    </table>
<br>
<table class="tbl">

  <tbody>
        <tr>
            <td><strong>MATERIAL</strong></td>
            <td><input type="text" placeholder="Click to edit" /></td>
        </tr>
        <tr>
            <td><strong>BATCH NUMBER</strong></td>
            <td>T21IPO08452</td>
        </tr>
  
        <tr>
            <td><strong>GRADE</strong></td>
            <td>SLM 40.29C</td>
        </tr>
 
        <tr>
            <td><strong>DATE OF PRODUCTION</strong></td>
            <td>30/08/2021</td>
        </tr>
        <tr>
            <td><strong>QUANTITY(KGS)</strong></td>
            <td><input type="number" placeholder="Click to edit" /></td>
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