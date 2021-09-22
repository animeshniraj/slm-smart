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

	$myuserid = $session->user->getUserid();

    $PAGE = [
        "Page Title" => "SLM | Messages",
        "Home Link"  => "/admin/",
        "Menu"		 => "message-inbox",
        "MainMenu"	 => "message_menu",

    ];


    include("../../pages/adminhead.php");
    include("../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-mail bg-c-blue"></i>
				<div class="d-inline">
					<h5>Inbox</h5>
					<span>View all your messages.</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">

<div class="page-body">
<div class="row">
<div class="col-lg-12">


<div class="card">
<div class="card-header">
<h5>All Mails</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">

<div class="table-responsive">
<table id="mails" class="table table-hover">
	

</table>
</div>

<div class="form-group row">
		<label class="col-sm-10"></label>
		<div class="col-sm-2">
		<button onclick="updateLimit();" class="btn btn-primary m-b-0"><i class="fa fa-refresh"></i>Show More</button>
		</div>
		</div>

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
	
	var mailLimit = 10;

	getMails();
	setInterval(function(){
		getMails();
	},10000);

	function updateLimit()
	{
		mailLimit+=10;
	}

	function getMails()
	{
		
        var postData = new FormData();
       
        postData.append("action","getMails");
        postData.append("userid","<?php echo $myuserid; ?>");
        postData.append("limit",mailLimit);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                var allmails = "";

                var mails = data.mails;

                for(var i=0;i<mails.length;i++)
                {
                	var currmail = mails[i];

                	if(currmail.new)
                	{
                		allmails += "<tr class=\"table-info\" onclick=\"window.location='readmail.php?mail="+currmail.code+"'\"><td><a href=\"readmail.php?mail="+currmail.code+"\"><i class=\"fa fa-envelope\" style=\"margin-right: 20px;\"></i>"+currmail.sender+"</a></td><td><a href=\"readmail.php?mail="+currmail.code+"\">"+currmail.subject+"</a></td><td>"+currmail.sendtime+"</td></tr>";
                	}
                	else
                	{
                		allmails += "<tr onclick=\"window.location='readmail.php?mail="+currmail.code+"'\"><td><a href=\"readmail.php?mail="+currmail.code+"\"><i class=\"fa fa-envelope\" style=\"margin-right: 20px;\"></i>"+currmail.sender+"</a></td><td><a href=\"readmail.php?mail="+currmail.code+"\">"+currmail.subject+"</a></td><td>"+currmail.sendtime+"</td></tr>";
                	}
                	
                }

                document.getElementById("mails").innerHTML = allmails;


            }
            
            
        
          }
        };
        xmlhttp.open("POST", "/query/mail.php", true);
        xmlhttp.send(postData);
	}

</script>

<?php
    
    include("../../pages/endbody.php");

?>

