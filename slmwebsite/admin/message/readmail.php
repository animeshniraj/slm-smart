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

    if(!isset($_GET["mail"]))
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.1";
        include("../../pages/error.php");
        die();
    }

    $code = $_GET["mail"];
    $result = runQuery("SELECT * FROM maildetails WHERE uniqueid='$code'");
    
    $mailData = $result->fetch_assoc();
    $mailid = $mailData["mailid"];

    $result = runQuery("SELECT * FROM mailrecipients WHERE mailid='$mailid' AND recipient='$myuserid'");
    
    if($result->num_rows!=1)
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../../pages/error.php");
        die();
    }

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
                    <h5><?php echo $mailData["subject"]; ?></h5>
                    <span>Recipient: 

                        <?php

                            $result = runQuery("SELECT * FROM mailrecipients WHERE mailid='$mailid'");
                            $allData = "";
                            if($result->num_rows>0)
                            {
                                while($row=$result->fetch_assoc())
                                {
                                    $allData = $allData.getFullName($row["recipient"]).", ";
                                }
                            }
                            echo substr($allData,0,-2);
                        ?>


                    </span>
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
<div class="card-block" style="position: relative; min-height: 600px;">

    
    <div style="position: absolute; bottom: 0px; margin: 10px;">

            <div id="messagesDiv">
                
               
            </div>
            
            <div class="input-group input-group-button">
            <textarea rows="1" cols="500" class="form-control" placeholder="" id="newmessage" ></textarea>
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" onclick="sendMail();"><i class="fa fa-send"></i>Send Message</button>
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
</div>


<script type="text/javascript">
	


	getMails();
	setInterval(function(){
		getMails();
	},10000);
	function getMails()
	{
		
        var postData = new FormData();
        
       
        postData.append("action","readMail");
        postData.append("userid","<?php echo $myuserid; ?>");
        postData.append("id","<?php echo $mailid; ?>");
        

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                var messages = data.messages;
                var allMsgData = "";
                for(var i=0; i<messages.length;i++)
                {
                    if(messages[i].isyou)
                    {
                        allMsgData+= "<blockquote class=\"blockquote blockquote-reverse\"><p class=\"m-b-0\">"+messages[i].message+"</p><footer class=\"blockquote-footer\">You, <i>"+messages[i].sendtime+"</i></footer></blockquote>";
                    }
                    else
                    {
                        allMsgData+= "<blockquote class=\"blockquote\"><p class=\"m-b-0\">"+messages[i].message+"</p><footer class=\"blockquote-footer\">"+messages[i].sender+", <i>"+messages[i].sendtime+"</i></footer></blockquote>";
                    }
                }

                document.getElementById("messagesDiv").innerHTML = allMsgData;
                var objDiv = document.getElementById("messagesDiv");
                objDiv.scrollTop = objDiv.scrollHeight;
            }
            
            
        
          }
        };
        xmlhttp.open("POST", "/query/mail.php", true);
        xmlhttp.send(postData);
	}

    function sendMail()
    {
        var newmessage = document.getElementById("newmessage").value;
        var postData = new FormData();
       
        postData.append("action","sendMail");
        postData.append("userid","<?php echo $myuserid; ?>");
        postData.append("id","<?php echo $mailid; ?>");
        postData.append("newmessage",newmessage);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                document.getElementById("newmessage").value = "";
                getMails();
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

<script type="text/javascript">
    
    var itemContainer = $("#messagesDiv");
    itemContainer.slimScroll({
        height: '500px',
        start: 'bottom',
        alwaysVisible: true
    });

</script>

