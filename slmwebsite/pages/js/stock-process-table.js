function DataModel()
{
	this.model_id = this.generateQuickGuid()
	this.gridOptions = [];
	this.resultdiv = document.getElementById('resultdiv');
	this.gridElement = [];

	this.uid_map = [];

	this.job_queue_data = [];
	this.job_queue_test = [];

	this.max_queue = 1000;

	this.pre_payload = {};

}



DataModel.prototype.generateQuickGuid =  function() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

DataModel.prototype.confirmid = function(key)
{
	if(this.model_id==key)
	{
			return true;
	}
	else
	{
		return false;
	}

	return this.model_id==key;
}


DataModel.prototype.createTable = function(columnDefs,rowData)
{
	this.gridOptions = {
		columnDefs: columnDefs,
  	rowData: rowData,
  	getRowId: (params) => params.data.process_id,
  	pagination: true,
  	paginationPageSize: 100,
	defaultColDef: {
        sortable: true
    },
	onCellClicked: (event) => open_report(event.value,event),

	}

	gridDiv = document.createElement('div');
	gridDiv.id = 'agGridElement';
	//gridDiv.classList.add('ag-theme-alphine')
	gridDiv.classList.add('ag-theme-alpine')
	gridDiv.style.height = "500px";

	this.resultdiv.innerHTML = "";
	this.resultdiv.appendChild(gridDiv);
	this.gridElement = gridDiv;


	new agGrid.Grid(this.gridElement, this.gridOptions);


}

DataModel.prototype.data_fetch_job =function()
{

		if(this.job_queue_data.length==0)
		{
			
			this.test_fetch_job();
			return;
		}

		data = null;

		data = this.job_queue_data.splice(0,this.max_queue);

		var payload = this.pre_payload;

		payload["job"] = data;

		fetchData('fetch_process_data',[["payload",JSON.stringify(payload)]],this.data_fetch_job_callback,false);
			
}

DataModel.prototype.data_fetch_job_callback = function(data)
{

	if(data_model.confirmid(data.model_id))
	{
			
		//var rowNode = data_model.gridOptions.api.getRowNode('aa');

		var updateData = data.data;
		

		Object.keys(updateData).forEach(key => {
		  

		  var currData = updateData[key];

		  var rowNode = data_model.gridOptions.api.getRowNode(key);

		  for (var i = 0; i < currData.length; i++) {
		  	var curr = currData[i]
		  	if(rowNode)
		  	{
		  		rowNode.setDataValue(curr[0], curr[2])
		  	}
		  	else
		  	{
		  		console.log('Error:: Cannot find row->'+key);
		  	}
		  	
		  }
		  

		});

		data_model.data_fetch_job();
	}
	
}

DataModel.prototype.test_fetch_job =function()
{
	if(this.job_queue_test.length==0)
		{
			
			this.gridOptions.api.forEachNode(function(node) { 
				    
				for (var k in node.data){
				    if (node.data.hasOwnProperty(k)) {
				         
				         if(node.data[k]=="▮▮▮▯▯")
				         {
				         	node.setDataValue(k, "-")
				         }

				    }
				}


			});
			hide_loading_arrow();
			show_download()
			return 1;
		}

		data = this.job_queue_test.splice(0,this.max_queue);
		
		var payload = this.pre_payload;

		payload["job"] = data;

		fetchData('test_process_data',[["payload",JSON.stringify(payload)]],this.test_fetch_job_callback,false);
}


DataModel.prototype.test_fetch_job_callback = function(data)
{
	if(data_model.confirmid(data.model_id))
	{
			
		//var rowNode = data_model.gridOptions.api.getRowNode('aa');

		var updateData = data.data;
		

		Object.keys(updateData).forEach(key => {
		  

		  var currData = updateData[key];

		  var rowNode = data_model.gridOptions.api.getRowNode(key);

		  for (var i = 0; i < currData.length; i++) {
		  	var curr = currData[i]
		  	if(rowNode)
		  	{
		  		rowNode.setDataValue(curr[0], curr[2])
		  	}
		  	else
		  	{
		  		console.log('Error:: Cannot find row->'+key);
		  	}
		  }
		  

		});

		data_model.data_fetch_job();
	}
}




