<?php
    
	require_once('../../requiredlibs/includeall.php');

	
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

    runQuery("UPDATE usernotification SET status='READ' WHERE userid='$myuserid'");

    $PAGE = [
        "Page Title" => "SLM | Admin Dashboard",
        "Home Link"  => "/admin/",
        "Menu"		 => "settings_menu",
        "MainMenu"	 => "",

    ];


    include("../pages/adminhead.php");
    include("../pages/adminmenu.php");

?>

<style type="text/css">
    
    .notifHover:hover { 

        background-color: #F1F1F1;
        cursor: pointer;

}

</style>



<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather icon-sidebar bg-c-blue"></i>
				<div class="d-inline">
					<h5>All Notifications</h5>
					<span>View all your notification</span>
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

<div class="card-header-right">

</div>
</div>
<div class="card-block">


<ul class="list-view" id = "AllNotif">

                                           


                                        




</ul>

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

	setInterval(function(){
		showAllNotification();
	},5000);

	showAllNotification();
	function showAllNotification()
	{
		var postData = new FormData();
       
        postData.append("action","getNotification");
        postData.append("userid","<?php echo $session->user->getUserid(); ?>");
        
        var xmlhttp = new XMLHttpRequest();
        var userNotifications = "";
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            

            var data = JSON.parse(this.responseText);
           
            if(data.response)
            {
                
            	

            	var notifications = data.notifications;
                var notCount = 0;
            	for(var i=0; i<notifications.length;i++)
            	{
            		if(notifications[i].status=="NEW")
            		{
            			allNotifIds.push(notifications[i].id);
            		}

                    notCount++;

            		var icon = "";
                    if(notifications[i].type=="MAIL")
                    {
                        icon = "fa fa-send";
                    }
                    else
                    {
                        icon = "fa fa-bell";
                    }

                   

            		userNotifications+="<li class=\"notifHover\" style=\"margin: 30px;\" onclick=\"location.href='"+notifications[i].url+"'\"><div class=\"media\"><p style=\"font-size: 25px; margin-right: 20px;\"><i class=\""+icon+"\"></i></p><div class=\"media-body\"><h5 class=\"notification-user\">"+notifications[i].title+"</h5><p class=\"notification-msg\">"+notifications[i].msg+"</p><span class=\"notification-time\">"+notifications[i].agomessage+"</span></div></div></li>";

                    
            	}

            	document.getElementById("AllNotif").innerHTML = userNotifications;
            }
            
        
          }
        };
        xmlhttp.open("POST", "/query/notification.php", true);
        xmlhttp.send(postData);
	}


</script>

<?php
    
    include("../pages/endbody.php");

?>