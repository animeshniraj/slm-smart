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

	

    $PAGE = [
        "Page Title" => "View all Dispatch Report | SLM SMART",
        "Home Link"  => "/user/",
        "Menu"		 => "dispatch-report",
        "MainMenu"	 => "dispatch_menu",

    ];

    $myuserid = $session->user->getUserid();
	$myrole = $session->user->getRoleid();


	$flag = false;
	$type= "";
	if(isset($_POST['getdata']))
	{
		$filter = $_POST['filter'];
		$type = $filter;
		$data = [];
		$header = [];

		if($filter=='customer' && isset($_POST['customer']))
		{
			$customer = $_POST['customer'];
			$daterange = $_POST['date'];
			$flag = true;

			$startdate = toServerTime(explode(" - ",$daterange)[0]);
			$enddate = toServerTime(explode(" - ",$daterange)[1] . " 23:59:59");

			$header = ['Batch Number','Grade','Invoice Number','Dispatch Date','PO Date','PO Number','Package','Sales Qty']	;	

			$customername = runQuery("SELECT * FROM external_param WHERE externalid='$customer' AND param='Name'")->fetch_assoc()['value'];

			$title = "Customer: ". $customername. " ($daterange)";


			$result = runQuery("SELECT * FROM dispatch WHERE customer = '$customer' AND entrydate>='$startdate' AND entrydate<='$enddate'");

			
			while($row = $result->fetch_assoc())
			{
				$laid = $row['laid'];
				$cid = $row['cid'];




				$result2 = runQuery("SELECT * FROM dispatch_invoices WHERE cid='$cid'");

				while($row2 = $result2->fetch_assoc())
				{
					$batchnumber = $row2['batch'];
					$result3 = runQuery("SELECT * FROM loadingadvice_batches WHERE laid='$laid' AND batch='$batchnumber'");
					$result3 = $result3->fetch_assoc();

					$result4 = runQuery("SELECT * FROM purchase_order WHERE orderid in (SELECT poid FROM loading_advice WHERE laid ='$laid')");

					
					
					$result4 = $result4->fetch_assoc();

					


					array_push($data,[$batchnumber,$result3['grade'],$row2['invoice'],Date('d-M-Y',strtotime($row['entrydate'])),Date('d-M-Y',strtotime($result4['entrydate'])),$result4['orderid'],$result3['package'],$row2['qty']]);



				}

			}

			
			

		}
		elseif($filter=='grade' && isset($_POST['grade']))
		{
			$grade = $_POST['grade'];
			$daterange = $_POST['date'];
			$flag = true;

			$startdate = toServerTime(explode(" - ",$daterange)[0]);
			$enddate = toServerTime(explode(" - ",$daterange)[1] . " 23:59:59");

			$title = "Grade: ". $grade. " ($daterange)";

			$header = ['Batch Number','Invoice Number','Customer','Dispatch Date','PO Date','PO Number','Package','Sales Qty']	;	
			$result = runQuery("SELECT * FROM loadingadvice_batches WHERE grade='$grade' or grade like '$grade#%'");

			while($row = $result->fetch_assoc())
			{
				$laid = $row['laid'];

				
				$result2 = runQuery("SELECT * FROM dispatch_invoices WHERE cid in (SELECT cid FROM dispatch WHERE laid='$laid') AND batch in (SELECT batch FROM loadingadvice_batches WHERE laid='$laid' AND grade='$grade')");

				while($row2=$result2->fetch_assoc())
				{
					$result3 = runQuery("SELECT * FROM dispatch WHERE laid='$laid'")->fetch_assoc();
					$customerid = $result3['customer'];


					$customername = runQuery("SELECT * FROM external_param WHERE externalid='$customerid' AND param='Name'")->fetch_assoc()['value'];

					$result4 = runQuery("SELECT * FROM purchase_order WHERE orderid in (SELECT poid FROM loading_advice WHERE laid ='$laid')")->fetch_assoc();


					
					


					array_push($data,[$row2['batch'],$row2['invoice'],$customername,Date('d-M-Y',strtotime($result3['entrydate'])),Date('d-M-Y',strtotime($result4['entrydate'])),$result4['orderid'],$row['package'],$row['quantity']]);
				}

			}


		}
		elseif($filter=='batch' && isset($_POST['batchnumber']))
		{
			$batchnumber = $_POST['batchnumber'];
			$flag = true;


			$header = ['Invoice Number','Customer','Dispatch Date','PO Date','PO Number','Package','Sales Qty']	;	
			$result = runQuery("SELECT * FROM loadingadvice_batches WHERE batch='$batchnumber'");



			$title = "Batch Number: ". $batchnumber;

			while($row = $result->fetch_assoc())
			{
				$laid = $row['laid'];
				
				$result2 = runQuery("SELECT * FROM dispatch_invoices WHERE batch='$batchnumber'");

				while($row2=$result2->fetch_assoc())
				{
					$result3 = runQuery("SELECT * FROM dispatch WHERE laid='$laid'")->fetch_assoc();
					$customerid = $result3['customer'];


					$customername = runQuery("SELECT * FROM external_param WHERE externalid='$customerid' AND param='Name'")->fetch_assoc()['value'];

					$result4 = runQuery("SELECT * FROM purchase_order WHERE orderid in (SELECT poid FROM loading_advice WHERE laid ='$laid')")->fetch_assoc();


					
					


					array_push($data,[$row2['invoice'],$customername,Date('d-M-Y',strtotime($result3['entrydate'])),Date('d-M-Y',strtotime($result4['entrydate'])),$result4['orderid'],$row['package'],$row['quantity']]);
				}

			}


			
		}
		else
		{
			$show_alert = true;
			$alert = showAlert("error","Parameters missing","");
		}

	}

    

include("../../pages/userhead.php");
include("../../pages/usermenu.php");

if($show_alert)





?> <div class="pcoded-content">
  <div class="page-header card">
    <div class="row align-items-end">
      <div class="col-lg-8">
        <div class="page-header-title">
          <i class="fa fa-fire bg-c-blue"></i>
          <div class="d-inline">
            <h3>View all Dispatches</h3>
            <span>Select order to edit</span>
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
                  <div class="card-header-right"></div>
                </div>
                <div class="card-block">
                  <form method="POST">
                    <div class="form-group" style="display:flex; justify-content: center">
                      <select onchange="editoption(this);" class="form-control col-sm-3" name="filter">
                        <option selected disabled value=""> Choose a filter</option>
                        <option value="customer">Customer</option>
                        <option value="grade">Grade</option>
                        <option value="batch">Batch Number</option>
                      </select>
                    </div>
                    <div class="form-group" style="display:flex; justify-content: center">
                      <select id="customerin" style="display: none;" class="form-control col-sm-3" name="customer">
                        <option selected disabled value=""> Choose a Customer</option> <?php 

								$result = runQuery("SELECT external_param.externalid,external_param.value FROM external_conn LEFT JOIN external_param ON external_param.externalid = external_conn.externalid WHERE external_conn.type='Customer' AND external_param.param='Name' ORDER by external_param.value");

								if($result->num_rows>0)
								{
									while($row = $result->fetch_assoc())
									{
										echo "<option value=\"".$row["externalid"]."\">".$row["value"]."</option>";
									}
								}

							?>
                      </select>
                    </div>
                    <div class="form-group" style="display:flex; justify-content: center">
                      <input style="display: none;" class="form-control col-sm-3" id="batchnumberin" placeholder="Batch Number" type="text" name="batchnumber" value="">
                    </div>
                    </select>
                </div>
                <script type="text/javascript">
                  function editoption(inobj) {
                    if (inobj.value == "customer") {
                      document.getElementById('customerin').style.display = 'block';
                      document.getElementById('gradein').style.display = 'none';
                      document.getElementById('batchnumberin').style.display = 'none';
                      document.getElementById('getdatabtn').disabled = false;
                      document.getElementById('datein').disabled = false;
                    } else if (inobj.value == "grade") {
                      document.getElementById('customerin').style.display = 'none';
                      document.getElementById('gradein').style.display = 'block';
                      document.getElementById('batchnumberin').style.display = 'none';
                      document.getElementById('getdatabtn').disabled = false;
                      document.getElementById('datein').disabled = false;
                    } else if (inobj.value == "batch") {
                      document.getElementById('customerin').style.display = 'none';
                      document.getElementById('gradein').style.display = 'none';
                      document.getElementById('batchnumberin').style.display = 'block';
                      document.getElementById('getdatabtn').disabled = false;
                      document.getElementById('datein').disabled = true;
                    } else {
                      document.getElementById('getdatabtn').disabled = true;
                    }
                  }
                </script>
                <div class="form-group" style="display:flex; justify-content: center">
                  <select style="display: none;" id="gradein" class="form-control col-sm-3" name="grade">
                    <option disabled selected value=""> Choose a grade</option>
                    <optgroup label="Premix Grades"> <?php

							$result = runQuery("SELECT * FROM premix_grades");



							while($row=$result->fetch_assoc())
							{
								?> <option value="<?php echo $row["gradename"] ?>"> <?php echo $row["gradename"] ?> </option> <?php
							}

						?> </optgroup>
                    <optgroup label="Final Blend Grades"> <?php

							$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");



							while($row=$result->fetch_assoc())
							{
								?> <option value="<?php echo $row["gradename"] ?>"> <?php echo $row["gradename"] ?> </option> <?php
							}

						?> </optgroup>
                  </select>
                </div>
                <div class="form-group" style="display:flex; justify-content: center;">
                  <input type="text" disabled required name="date" id="datein" class="form-control col-sm-4" style="display: inline; text-align: center;" placeholder="Date">
                </div>
                <script>
                  $(function() {
                    $('input[name="date"]').daterangepicker({
                      singleDatePicker: false,
                      timePicker: false,
                      showDropdowns: true,
                      locale: {
                        format: 'DD-MM-YYYY',
                      },
                      minYear: 1901,
                      maxYear: parseInt(moment().format('YYYY'), 10)
                    }, function(start, end, label) {});
                  });
                </script>
                <div class="form-group row justify-content-md-center">
                  <div class="col-sm-3">
                    <button type="submit" disabled name='getdata' id='getdatabtn' class="btn btn-primary btn-block">
                      <i class="feather icon-plus"></i> Get Data </button>
                  </div>
                </div>
                </form>
                <br>
                <br>
                <hr>
                <br>
                <br> <?php
		if($flag)
		{

			if($type=="customer") {
	?> <form method="POST" action="compareids.php" target="_blank">
                  <div class="form-group row">
                    <div class="col-md-8" style="display:none;">
                      <select onchange="checkcompare();" id="select-compare" required class="js-example-basic-multiple " multiple="multiple" name="compareids[]"> <?php 

						foreach ($data as $value) {

							echo "<option data-grade='".$value[1]."' value='".$value[0]."'>".$value[0]."</option>";
							
						}

					?> </select> <?php 

						foreach ($data as $value) {

							
							echo "<input type='hidden' name='dispatchdate[\"".$value[0]."\"]' value='".$value[3]."'>";
							echo "<input type='hidden' name='dispatchqty[\"".$value[0]."\"]' value='".$value[7]."'>";
						}

					?>
                    </div>
                    <div class="col-md-2">
                      <button disabled id="comparebtn" class="btn btn-primary  pull-right" type="submit">Compare</button>
                    </div>
                  </div>
                </form> <?php
		}
		elseif($type=="grade")
		{

		
	?> <form method="POST" action="compareids.php" target="_blank">
                  <div class="form-group row" id="formdiv">
                    <div class="col-md-8" style="display:none;">
                      <select onchange="checkcompare();" id="select-compare" required class="js-example-basic-multiple " multiple="multiple" name="compareids[]"> <?php 

						foreach ($data as $value) {

							echo "<option data-grade='".$grade."' value='".$value[0]."'>".$value[0]."</option>";
							
						}

					?> </select> <?php 

						foreach ($data as $value) {

							
							echo "<input type='hidden' name='dispatchdate[\"".$value[0]."\"]' value='".$value[3]."'>";
							echo "<input type='hidden' name='dispatchqty[\"".$value[0]."\"]' value='".$value[7]."'>";
						}

					?>
                    </div>
                    <div class="col-md-2">
                      <button disabled id="comparebtn" class="btn btn-primary pull-right" type="submit">Compare</button>
                    </div>
                  </div>
                </form> <?php
		}
		
	?> <script type="text/javascript">
                  let selectedcompare = [];

                  function checkcompare() {
                    selectobj = document.getElementById('select-compare');
                    selected = selectobj.options[selectobj.selectedIndex];
                    dumSelected = []
                    for (var j = 0; j < selectobj.options.length; j++) {
                      if (selectobj.options[j].selected) {
                        dumSelected.push(selectobj.options[j])
                        dumSelectedj = j
                      }
                    }
                    if (dumSelected.length > 1) {
                      diff = dumSelected.filter(x => !selectedcompare.includes(x));
                      currGrade = selectedcompare[0].getAttribute('data-grade');
                      newGrade = diff[0].getAttribute('data-grade');
                      if (currGrade != newGrade) {
                        Swal.fire({
                          icon: 'error',
                          title: 'Grade Error',
                          html: "The selected batch does not have the same",
                        }).then((result) => {})
                        document.getElementById('comparebtn').disabled = true;
                      } else {
                        document.getElementById('comparebtn').disabled = false;
                      }
                    } else {
                      selectedcompare = dumSelected;
                      diff = [];
                      document.getElementById('comparebtn').disabled = true;
                    }
                    console.log("selected", selectedcompare)
                    console.log("dum", dumSelected)
                    console.log("diff", diff)
                  }
                </script>
                <div class="form-group" style="display:flex; justify-content: center;">
                  <big> <?php echo $title ?> </big>
                </div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th></th> <?php 

					foreach ($header as $key) {
						echo "<th>".$key."</th>";
					}

				?>
                    </tr>
                  </thead>
                  <tbody> <?php

				foreach ($data as $value) {
					
				

			?> <tr>
                      <td>
                        <input onclick="transfercheck(this)" type="checkbox" value="<?php echo $value[0] ; ?>">
                      </td> <?php 

					foreach ($value as $key) {
						echo "<td>".$key."</td>";
					}

				?>
                    </tr> <?php

				}
			?> </tbody>
                </table>
                <script type="text/javascript">
                  function transfercheck(checkobj) {
                    if (checkobj.checked) {
                      selectobj = document.getElementById('select-compare');
                      for (var j = 0; j < selectobj.options.length; j++) {
                        if (selectobj.options[j].value == checkobj.value) {
                          selectobj.options[j].selected = true;
                        }
                      }
                    } else {
                      selectobj = document.getElementById('select-compare');
                      for (var j = 0; j < selectobj.options.length; j++) {
                        if (selectobj.options[j].value == checkobj.value) {
                          selectobj.options[j].selected = false;
                        }
                      }
                    }
                    checkcompare()
                  }
                </script> <?php

		}
	?>

              </div>



			</div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div> <?php
    
    include("../../pages/endbody.php");

?> <script type="text/javascript">
  $(document).ready(function() {
    $(".js-example-basic-single").select2();
    $(".js-example-basic-multiple").select2();
    // Creation
  });

  function removeProcess(externalid) {
    Swal.fire({
      icon: 'error',
      title: 'Delete Purchase Order',
      html: 'Are you sure you want to delete Purchase Order ' + externalid,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById("deleteprocessid").value = externalid;
        document.getElementById("deleteprocessform").submit();
      }
    })
  }
</script>