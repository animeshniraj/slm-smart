

<!DOCTYPE html>
<html>
<head>
	<title>Error</title>
	<script src="/pages/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" type="text/css" href="/pages/css/sweetalert2.min.css">
<link href="/pages/font/opensan.css" rel="stylesheet">
<link href="/pages/font/opensand.css" rel="stylesheet">
</head>
<body>

<script type="text/javascript">
	Swal.fire({
		icon: "error",
		title: "<?php echo $ERR_TITLE; ?>",
		html: "<?php echo $ERR_MSG; ?><br><a href='/user/'>Go Back to User Dashboard</a>",
		showCancelButton: false,
  		showConfirmButton: false,
        allowEscapeKey: false,
        allowOutsideClick: false,
	})
</script>

</body>
</html>