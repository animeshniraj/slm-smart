function getDeviceStatus(devicename,ip,clientid,inobj)
{
	var postData = new FormData();
           
            
            
            
           
            var xmlhttp = new XMLHttpRequest();
           
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4)
              {
              	if(this.status == 200) {
                
	                
	                 
	                var data = JSON.parse(this.responseText);
	                
	                if(data.Status=="ONLINE")
	                {
	                    
	                    changeStatus(inobj,"ONLINE");
	                    return "ONLINE";

	                }
	                else
	                {
	                	changeStatus(inobj,"OFFLINE");
            			return "OFFLINE";
	                }
                
                
            	}
            	else
            	{
            		changeStatus(inobj,"OFFLINE");
            		return "OFFLINE";
            	}
              
            }
            };
             xmlhttp.timeout = 5000;
             
            xmlhttp.open("POST", "http://"+ip, true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send("clientpass="+clientid);
            

}

function rebootDeviceNow(devicename,ip,clientid,inobj)
{
	var postData = new FormData();
           inobj = document.getElementById(inobj);
            
         
            var xmlhttp = new XMLHttpRequest();
           
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4)
              {
              	if(this.status == 200) {
	                 
	                var data = JSON.parse(this.responseText);
	                
	                if(data.Status=="REBOOTING")
	                {
	                    
	                    changeStatus(inobj,"WAITING");

	                }
                
                
            	}
            	
              
            }
            };
             xmlhttp.timeout = 5000;
             
            xmlhttp.open("POST", "http://"+ip+"/reboot", true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send("clientpass="+clientid);
}
