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
	$quantity = $_GET['quantity'];

	if($gradename=="**")
	{
		$gradename="No Grade Selected";
	}

	$result = runQuery("SELECT * FROM processentry WHERE processid='$processid'")->fetch_assoc();

	$entrytime = $result['entrytime'];



	$binno = runQuery("SELECT * FROM processentryparams WHERE processid='$processid' AND param='Bin Number'")->fetch_assoc()['value'];


?>

<!DOCTYPE html>

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
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Semi Finished Tag</title>
		<link rel="stylesheet" type="text/css" href="paper.css">
	</head>
<body class="A5 landscape">
  <section class="sheet padding-10mm">


	<h3 style="text-align:center;background-color:#000;color:#fff;top:0;">MATERIAL IDENTIFICATION TAG</h3>
	<table style="width:100%">
		<tr>
			<th colspan="2"><img src="/pages/png/slmlogo.png" class="center"></th>
			<th colspan="2" ><h4>STAGE: SEMI FINISHED</h4></th>
		</tr>
		<tr>
			<td colspan="4"><img src="barcode.php?text=<?php echo $processid;?>" class="center"></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold;">Grade</td>
			<td colspan="2" style="text-align:right;"><?php echo $gradename;?></td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td style="font-weight:bold;">Date</td>
			<td style="text-align:right;"><?php echo Date('d-M-Y',strtotime($entrytime)); ?></td>
			<td style="font-weight:bold;">Bin No.</td>
			<td style="text-align:right;"><?php echo $binno; ?></td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td style="font-weight:bold;">Quantity (KGS)</td>
			<td style="text-align:right;"><?php echo $quantity;?>kg</td>
			<td style="font-weight:bold;">Signature</td>
			<td style="height:80px;"></td>

		</tr>
		<tr>
			<td colspan="4" style="text-align:center;font-weight:bold;font-size:18px;">NEXT OPERATION BLENDING</td>
		</tr>
	</table>
<p style="text-align:right;font-size:12px;font-weight:400;">TAG-04(D)</p>
</section>	
			

		<script type="text/javascript">
		// window.print();
		</script>

	</body>
</html>