$(function() {
  $('input[name="starttime"]').daterangepicker({
    singleDatePicker: true,
    timePicker: false,
    showDropdowns: true,
    locale: 
    {    
    	format: 'DD-MM-YYYY',
    },
  	
    minYear: 1901,
    maxYear: parseInt(moment().format('YYYY'),10)
  }, function(start, end, label) {
    
  });

});

$(function() {
  $('input[name="stoptime"]').daterangepicker({
    singleDatePicker: true,
    timePicker: false,
    showDropdowns: true,
    locale: 
    {    
    	format: 'DD-MM-YYYY',
    },
  	
    minYear: 1901,
    maxYear: parseInt(moment().format('YYYY'),10)
  }, function(start, end, label) {
    
  });

});

$(document).ready(function() {
  	$(".js-example-basic-multiple").select2();

  	load_grades()

 })

let gradenameElement = document.getElementById('gradename');
let starttimeElement = document.getElementById('starttime');
let stoptimeElement = document.getElementById('stoptime');
let processnameElement = document.getElementById('processname');



function load_data()
{
	setToloading();
	start_loading_arrow();
	prepareSearchQuery();

}




function load_grades()
{
	processnameElement = document.getElementById('processname');
	gradenameElement = document.getElementById('gradename');

	gradenameElement.disabled = true;
	

	formdata = [
		['process',processnameElement.value]
	]

	fetchData('get_process_grade',formdata,setGrades)

}

function load_properties()
{

	resetProperties()

	processnameElement = document.getElementById('processname');


	formdata = [
		['process',processnameElement.value]
	]

	dumGrades = $("#gradename").val();
	
	for (var i = 0; i < dumGrades.length; i++) {
		formdata.push(['grade[]',dumGrades[i]])
	}

	fetchData('get_process_properties',formdata,setProperties)
}


function setProperties(data)
{
	data = data.data;
	selected_properties = [];

	for (var i = 0; i < data.length; i++) {

		var dum = [];
		dum["property"] = data[i].property;
		dum["selected"] = false;
		selected_properties.push(dum)
	}

	processProperties()
}

function processProperties()
{

	var tbody = document.getElementById('properties-modal-tbody');

	for (var i = 0; i < selected_properties.length; i++) {
		var curr = selected_properties[i];

		var tr = document.createElement('tr');
		tr.innerHTML = "<td>"+curr.property+"</td>"

		if(curr.selected)
		{
			tr.innerHTML += "<td><input type='checkbox' name='properties-send' onchange='confirm_properties()' selected></td>";
		}
		else
		{
			tr.innerHTML += "<td><input type='checkbox' name='properties-send' onchange='confirm_properties()'></td>";
		}
		tbody.appendChild(tr);

	}
}

function confirm_properties()
{
	var tbody = document.getElementById('properties-modal-tbody');
	properties_div = document.getElementById('properties-selected-div');

	var dumSelected = []
	var dumSelected_prop = []
	for (var i = 0; i < tbody.children.length; i++) {
		curr = tbody.children[i];

		var dum = [];
		dum["property"] = curr.children[0].innerHTML;
		dum["selected"] = curr.children[1].children[0].checked;
		dumSelected.push(dum)
		if(dum["selected"])
		{
			dumSelected_prop.push(dum["property"])
		}
		

	}

	selected_properties = dumSelected;
	properties_div.innerHTML = dumSelected_prop.join(", ")


}

function setGrades(data)
{
	
	resetgradenames()
	resetProperties()

	initSearchQuery(data);
	gradenameElement = document.getElementById('gradename');
	gradenameElement.disabled = false;
	gradedata = data.data;
	


	for (var i = 0; i < gradedata.length; i++) {
		var options = document.createElement("option");
		options.value = gradedata[i]["gradename"]
		options.innerHTML = gradedata[i]["gradename"]
		gradenameElement.appendChild(options)
	}


	if(gradenameElement.children.length==1 && gradenameElement.value!="")
	{
		load_properties()
	}

}

function setTable(columnData,rowData)
{

}



function initSearchQuery(data)
{
	var basic = data.basic_properties
	var additonal_prop = data.additional_properties
	var tbody = document.getElementById('additional-filter-modal-tbody');

	tbody.innerHTML = "";
	selected_filters =[];

	additional_filters = data.additional_filters
	additional_properties = data.additional_properties
	basic_properties = data.basic_properties
	
	for (var key in additional_filters) {
	    if (additional_filters.hasOwnProperty(key)) {

	        selected_filters.push([key,[]])

					var tr = document.createElement('tr');

					var th = document.createElement('th');
					th.innerHTML = key;
					tr.appendChild(th)


					var td = document.createElement('td');
					var select = document.createElement('select');

					select.classList.add('form-control')
					select.classList.add('js-example-basic-multiple');
					select.classList.add('additional-filter');
					select.multiple = "multiple";
					select.onchange = function(){confirm_filters()}

					for (var i = 0; i < additional_filters[key].Options.length; i++) {
						var curr = additional_filters[key].Options[i];
						var option = document.createElement('option');
						option.value = curr;
						option.innerHTML = curr;
						
						select.options.add(option);

					}

					td.appendChild(select)

					tr.appendChild(td)

					tbody.appendChild(tr);
	    }

	    
	}

	$(".additional-filter").select2();

}


function confirm_filters()
{
		var tbody = document.getElementById('additional-filter-modal-tbody');

		var dumSelected = []

		for (var i = 0; i < tbody.children.length; i++) {
			curr = tbody.children[i];

			dum = [curr.children[0].innerHTML,$(curr.children[1].children[0]).val()]


			dumSelected.push(dum)			
		}

		selected_filters = dumSelected;
}


function prepareSearchQuery()
{
	search_query_payload = {};

	search_query_payload["process"] = document.getElementById('processname').value;
	search_query_payload["start_date"] = document.getElementById('starttime').value;
	search_query_payload["end_date"] = document.getElementById('stoptime').value;
	search_query_payload["filter_data_by"] = document.getElementById('stoptime').value;




	if(basic_properties.filter_date_from_entry)
	{
		search_query_payload["filter_data_by"] = "BACKEND_CREATE"
	}
	else
	{
		search_query_payload["filter_data_by"] =  basic_properties.filter_date_from_property;
	}

	search_query_payload["grades"] = $("#gradename").val()

	search_query_payload["filter"] = {}

	search_query_payload["filter"]["additional"] = selected_filters;
	search_query_payload["filter"]["test"] = []

	for (var i = 0; i < selected_properties.length; i++) {
		curr = selected_properties[i];
		if(curr.selected)
		{
			var dum = {};
			dum.property = curr.property
			dum.selected = curr.selected

			search_query_payload["filter"]["test"].push(dum);
		}
	}

	data_model = new DataModel();

	search_query_payload['model_id'] = data_model.model_id;


	fetchData('initDataHandshake',[["payload",JSON.stringify(search_query_payload)]],handshake,false)


}





function handshake(data)
{
	if(data_model.confirmid(data.model_id))
	{
			
			data_model.createTable(data.columnDef,data.rowData)
			data_model.uid_map = data.uid_map;

			var payload ={}
			payload["model_id"] =  data.model_id
			payload["uid_map"] = data.uid_map;
			payload["process"] = data.process;

			data_model.job_queue_data = data.fetch_data_list;
			data_model.job_queue_test = data.test_data_list;
			data_model.pre_payload = payload;

			data_model.data_fetch_job();

	}


	
}


function fetchData(action,formdata,callback,print=false)
{

	var postData = new FormData();
       
    postData.append("action",action);

    for (var i = 0; i < formdata.length; i++) {
    	 postData.append(formdata[i][0],formdata[i][1]);
    }


    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        
        if(print)
        {
        	console.log(this.responseText)
        }


       	var data = JSON.parse(this.responseText);

       	if(data.response)
       	{
       		callback(data)
       	}
       	else
       	{
       		setError(data.msg);
			return;
       	}
       
       	
    
      }
    };
    xmlhttp.open("POST", "/query/stock.php", true);
    xmlhttp.send(postData);
}

















function titleicontoRefresh()
{
	var titleicon = document.getElementById('titleicon');
	titleicon.classList.remove("fa-signal");
	titleicon.classList.add("fa-refresh");

}
function titleicontonormal()
{
	var titleicon = document.getElementById('titleicon');
	titleicon.classList.remove("fa-refresh");
	titleicon.classList.add("fa-signal");
	
}
function reloadCurrPage()
{
	window.location = window.location.href.split("?")[0];
}

function resetgradenames()
{
	gradenameElement = document.getElementById('gradename');
	gradenameElement.length = 0;
}

function resetProperties()
{
	properties_div = document.getElementById('properties-selected-div');
	properties_table= document.getElementById('properties-modal-tbody');
	properties_div.innerHTML="No Properties Selected";
	properties_table.innerHTML = '';
	selected_properties = {}
	selected_filters = {};
}

function setToloading()
{
	resultdiv = document.getElementById('resultdiv');

	resultdiv.innerHTML = "<div class='container'>\n  <div class='loader'>\n    <div class='loader--dot'></div>\n    <div class='loader--dot'></div>\n    <div class='loader--dot'></div>\n    <div class='loader--dot'></div>\n    <div class='loader--dot'></div>\n    <div class='loader--dot'></div>\n    <div class='loader--text'></div>\n  </div>\n</div>";

}


function setError(message="Unknown Error has occured!")
{
	resultdiv = document.getElementById('resultdiv');

	resultdiv.innerHTML = "<div class=\"alert alert-danger icons-alert\">\n<p><strong>Error::</strong>"+message+"</p>\n</div>"
}


function showPropertyModal()
{
	$("#properties-modal").modal('show')
}

function showAdditionalFilterModal()
{
	$("#additional-filter-modal").modal('show')
}


function select_all_grade()
{
	$("#gradename > option").prop("selected", true);
  	$("#gradename").trigger("change"); 
}

function select_all_property(obj)
{
		
		checkboxes = document.getElementsByName('properties-send');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
      checkboxes[i].checked = obj.checked;
    }
    confirm_properties();
}

function start_loading_arrow()
{
	document.getElementById('loading_arrow').style.display = "block";
}

function hide_loading_arrow()
{
	document.getElementById('loading_arrow').style.display = "none";
}


