<?php
    
	require_once('../../../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'admin_module');

	$external_type = "Customer";

		if(isset($_POST["addnew"]))
	{
		


		$params = $_POST["Customer_param"];
		$values = $_POST ["Customer_val"];
		$ordering = $_POST["Customer_paramorder"];

		$grades = $_POST["Customer_grade"];

		$prefix = "C-";
    	$sqlprefix = "C-%";


    	$result = runQuery("SELECT MAX(CAST(SUBSTRING_INDEX(externalid, '-', -1) AS SIGNED)) max_val FROM external_conn WHERE externalid LIKE '$sqlprefix'");

    	if($result->num_rows==0)
    	{	
    		$count = 1;
    	}
    	else
    	{
    		$lastID = $result->fetch_assoc()["max_val"];
	    	
	    	$count = intval($lastID)+1;
    	}

    	
		$prefix = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);

		

		runQuery("INSERT INTO external_conn VALUES('$prefix','$external_type')");


		for($i=0;$i<count($params);$i++)
		{
			$currp = $params[$i];
			$currv = $values[$i];
			$curro = $ordering[$i];
			runQuery("INSERT INTO external_param VALUES(NULL,'$prefix','$currp','$currv','$curro')");
		}


		for($i=0;$i<count($grades);$i++)
		{
			$currp = $grades[$i];
			
			runQuery("INSERT INTO external_param VALUES(NULL,'$prefix','Grades','$currp','-1')");
		}



		if($result)
    	{
    			
    			
    				?>
    					<form id="redirectform" method="POST" action="edit.php">
    						<input type="hidden" name="externalid" value="<?php  echo $prefix;?>">
    					</form>
    					<script type="text/javascript">
    						document.getElementById("redirectform").submit();
    					</script>
    				<?php

    			
    			
    	}
	}


	

    $PAGE = [
        "Page Title" => "Add new Customer | SMART SLM",
        "Home Link"  => "/admin/",
        "Menu"		 => "external-customer",
        "MainMenu"	 => "external_menu",

    ];


    include("../../../pages/adminhead.php");
    include("../../../pages/adminmenu.php");

?>





<div class="pcoded-content">

<div class="page-header card">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<!--<i class="feather icon-users bg-c-blue"></i>-->
                <i class="fa fa-address-card bg-c-blue" aria-hidden="true"></i>
				<div class="d-inline">
					<h5>Creating a New Customer</h5>
					<span>Provide basic information for the customer</span>
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
<div class="col-lg-8">


<div class="card">
<div class="card-header">
<h5>Add Customer Details</h5>
<div class="card-header-right">

</div>
</div>
<div class="card-block">


<form method="POST">
	
	
			
			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Customer Name</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_name" placeholder="">

			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="0">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Name">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Type</label>
			<div class="col-sm-9">
			<select required class="form-control" name="<?php echo $external_type;?>_val[]">
				<option value="Domestic">Domestic</option>
				<option value="International">International</option>
			</select>

			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="1">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Type">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Address</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="2">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Address">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">City</label>
			<div class="col-sm-4">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="3">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="City">
			<span class="messages"></span>
			</div>
			
			<label class="col-sm-2 col-form-label">Pincode</label>
			<div class="col-sm-3">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="4">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Pincode">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">State</label>
			<div class="col-sm-4">
			<input list="statelist" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="5">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="State">
			<span class="messages"></span>
			</div>
			
			<label class="col-sm-2 col-form-label">Country</label>
			<div class="col-sm-3">
			<input list="countrylist" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="6">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Country">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Contact Person</label>
			<div class="col-sm-4">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="7">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Contact Person">
			<span class="messages"></span>
			</div>
            </div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Contact Number</label>
			<div class="col-sm-3">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="8">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Contact Number">
			<span class="messages"></span>
			</div>
			
			<label class="col-sm-3 col-form-label">Alternate Number</label>
			<div class="col-sm-3">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="" value="-">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="9">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Alternate Contact Number">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Email</label>
			<div class="col-sm-4">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="10">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Email">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Remarks</label>
			<div class="col-sm-9">
			<input type="text" required class="form-control" name="<?php echo $external_type;?>_val[]" id="<?php echo $external_type;?>_address" placeholder="">
			<input type="hidden" name="<?php echo $external_type;?>_paramorder[]" value="11">
			<input type="hidden" name="<?php echo $external_type;?>_param[]" value="Remarks">
			<span class="messages"></span>
			</div>
			</div>


			<div class="form-group row">
		<label class="col-sm-3 col-form-label">Grades</label>
			<div class="col-sm-9">
			<select required class="js-example-basic-multiple form-control" multiple="multiple"  name="<?php echo $external_type;?>_grade[]" >
				<optgroup label="Premix Grades">
					<?php
						$result = runQuery("SELECT * FROM premix_grades");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["gradename"]."\">".$row["gradename"]."</option>";
							}
						}

					?>

					<optgroup label="Final Blend Grades">
					<?php
						$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");

						if($result->num_rows>0)
						{
							while($row = $result->fetch_assoc())
							{
								echo "<option value=\"".$row["gradename"]."\">".$row["gradename"]."</option>";
							}
						}

					?>
			</select>
		</div>
		
	</div>


			<div class="form-group row">			
			<div class="col-sm-12">
			<button type="submit" name="addnew" class="btn btn-primary pull-right"><i class="fa fa-user-plus"></i>Save <?php echo $external_type;?> Details</button>
			<span class="messages"></span>
			</div>
			</div>

			
	


</form>





</div>
</div>

</div>
</div>
</div>

</div>
</div>
</div>
</div>


<datalist id="countrylist">
    <option value="Afganistan">Afghanistan</option>
   <option value="Albania">Albania</option>
   <option value="Algeria">Algeria</option>
   <option value="American Samoa">American Samoa</option>
   <option value="Andorra">Andorra</option>
   <option value="Angola">Angola</option>
   <option value="Anguilla">Anguilla</option>
   <option value="Antigua & Barbuda">Antigua & Barbuda</option>
   <option value="Argentina">Argentina</option>
   <option value="Armenia">Armenia</option>
   <option value="Aruba">Aruba</option>
   <option value="Australia">Australia</option>
   <option value="Austria">Austria</option>
   <option value="Azerbaijan">Azerbaijan</option>
   <option value="Bahamas">Bahamas</option>
   <option value="Bahrain">Bahrain</option>
   <option value="Bangladesh">Bangladesh</option>
   <option value="Barbados">Barbados</option>
   <option value="Belarus">Belarus</option>
   <option value="Belgium">Belgium</option>
   <option value="Belize">Belize</option>
   <option value="Benin">Benin</option>
   <option value="Bermuda">Bermuda</option>
   <option value="Bhutan">Bhutan</option>
   <option value="Bolivia">Bolivia</option>
   <option value="Bonaire">Bonaire</option>
   <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
   <option value="Botswana">Botswana</option>
   <option value="Brazil">Brazil</option>
   <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
   <option value="Brunei">Brunei</option>
   <option value="Bulgaria">Bulgaria</option>
   <option value="Burkina Faso">Burkina Faso</option>
   <option value="Burundi">Burundi</option>
   <option value="Cambodia">Cambodia</option>
   <option value="Cameroon">Cameroon</option>
   <option value="Canada">Canada</option>
   <option value="Canary Islands">Canary Islands</option>
   <option value="Cape Verde">Cape Verde</option>
   <option value="Cayman Islands">Cayman Islands</option>
   <option value="Central African Republic">Central African Republic</option>
   <option value="Chad">Chad</option>
   <option value="Channel Islands">Channel Islands</option>
   <option value="Chile">Chile</option>
   <option value="China">China</option>
   <option value="Christmas Island">Christmas Island</option>
   <option value="Cocos Island">Cocos Island</option>
   <option value="Colombia">Colombia</option>
   <option value="Comoros">Comoros</option>
   <option value="Congo">Congo</option>
   <option value="Cook Islands">Cook Islands</option>
   <option value="Costa Rica">Costa Rica</option>
   <option value="Cote DIvoire">Cote DIvoire</option>
   <option value="Croatia">Croatia</option>
   <option value="Cuba">Cuba</option>
   <option value="Curaco">Curacao</option>
   <option value="Cyprus">Cyprus</option>
   <option value="Czech Republic">Czech Republic</option>
   <option value="Denmark">Denmark</option>
   <option value="Djibouti">Djibouti</option>
   <option value="Dominica">Dominica</option>
   <option value="Dominican Republic">Dominican Republic</option>
   <option value="East Timor">East Timor</option>
   <option value="Ecuador">Ecuador</option>
   <option value="Egypt">Egypt</option>
   <option value="El Salvador">El Salvador</option>
   <option value="Equatorial Guinea">Equatorial Guinea</option>
   <option value="Eritrea">Eritrea</option>
   <option value="Estonia">Estonia</option>
   <option value="Ethiopia">Ethiopia</option>
   <option value="Falkland Islands">Falkland Islands</option>
   <option value="Faroe Islands">Faroe Islands</option>
   <option value="Fiji">Fiji</option>
   <option value="Finland">Finland</option>
   <option value="France">France</option>
   <option value="French Guiana">French Guiana</option>
   <option value="French Polynesia">French Polynesia</option>
   <option value="French Southern Ter">French Southern Ter</option>
   <option value="Gabon">Gabon</option>
   <option value="Gambia">Gambia</option>
   <option value="Georgia">Georgia</option>
   <option value="Germany">Germany</option>
   <option value="Ghana">Ghana</option>
   <option value="Gibraltar">Gibraltar</option>
   <option value="Great Britain">Great Britain</option>
   <option value="Greece">Greece</option>
   <option value="Greenland">Greenland</option>
   <option value="Grenada">Grenada</option>
   <option value="Guadeloupe">Guadeloupe</option>
   <option value="Guam">Guam</option>
   <option value="Guatemala">Guatemala</option>
   <option value="Guinea">Guinea</option>
   <option value="Guyana">Guyana</option>
   <option value="Haiti">Haiti</option>
   <option value="Hawaii">Hawaii</option>
   <option value="Honduras">Honduras</option>
   <option value="Hong Kong">Hong Kong</option>
   <option value="Hungary">Hungary</option>
   <option value="Iceland">Iceland</option>
   <option value="Indonesia">Indonesia</option>
   <option value="India">India</option>
   <option value="Iran">Iran</option>
   <option value="Iraq">Iraq</option>
   <option value="Ireland">Ireland</option>
   <option value="Isle of Man">Isle of Man</option>
   <option value="Israel">Israel</option>
   <option value="Italy">Italy</option>
   <option value="Jamaica">Jamaica</option>
   <option value="Japan">Japan</option>
   <option value="Jordan">Jordan</option>
   <option value="Kazakhstan">Kazakhstan</option>
   <option value="Kenya">Kenya</option>
   <option value="Kiribati">Kiribati</option>
   <option value="Korea North">Korea North</option>
   <option value="Korea Sout">Korea South</option>
   <option value="Kuwait">Kuwait</option>
   <option value="Kyrgyzstan">Kyrgyzstan</option>
   <option value="Laos">Laos</option>
   <option value="Latvia">Latvia</option>
   <option value="Lebanon">Lebanon</option>
   <option value="Lesotho">Lesotho</option>
   <option value="Liberia">Liberia</option>
   <option value="Libya">Libya</option>
   <option value="Liechtenstein">Liechtenstein</option>
   <option value="Lithuania">Lithuania</option>
   <option value="Luxembourg">Luxembourg</option>
   <option value="Macau">Macau</option>
   <option value="Macedonia">Macedonia</option>
   <option value="Madagascar">Madagascar</option>
   <option value="Malaysia">Malaysia</option>
   <option value="Malawi">Malawi</option>
   <option value="Maldives">Maldives</option>
   <option value="Mali">Mali</option>
   <option value="Malta">Malta</option>
   <option value="Marshall Islands">Marshall Islands</option>
   <option value="Martinique">Martinique</option>
   <option value="Mauritania">Mauritania</option>
   <option value="Mauritius">Mauritius</option>
   <option value="Mayotte">Mayotte</option>
   <option value="Mexico">Mexico</option>
   <option value="Midway Islands">Midway Islands</option>
   <option value="Moldova">Moldova</option>
   <option value="Monaco">Monaco</option>
   <option value="Mongolia">Mongolia</option>
   <option value="Montserrat">Montserrat</option>
   <option value="Morocco">Morocco</option>
   <option value="Mozambique">Mozambique</option>
   <option value="Myanmar">Myanmar</option>
   <option value="Nambia">Nambia</option>
   <option value="Nauru">Nauru</option>
   <option value="Nepal">Nepal</option>
   <option value="Netherland Antilles">Netherland Antilles</option>
   <option value="Netherlands">Netherlands (Holland, Europe)</option>
   <option value="Nevis">Nevis</option>
   <option value="New Caledonia">New Caledonia</option>
   <option value="New Zealand">New Zealand</option>
   <option value="Nicaragua">Nicaragua</option>
   <option value="Niger">Niger</option>
   <option value="Nigeria">Nigeria</option>
   <option value="Niue">Niue</option>
   <option value="Norfolk Island">Norfolk Island</option>
   <option value="Norway">Norway</option>
   <option value="Oman">Oman</option>
   <option value="Pakistan">Pakistan</option>
   <option value="Palau Island">Palau Island</option>
   <option value="Palestine">Palestine</option>
   <option value="Panama">Panama</option>
   <option value="Papua New Guinea">Papua New Guinea</option>
   <option value="Paraguay">Paraguay</option>
   <option value="Peru">Peru</option>
   <option value="Phillipines">Philippines</option>
   <option value="Pitcairn Island">Pitcairn Island</option>
   <option value="Poland">Poland</option>
   <option value="Portugal">Portugal</option>
   <option value="Puerto Rico">Puerto Rico</option>
   <option value="Qatar">Qatar</option>
   <option value="Republic of Montenegro">Republic of Montenegro</option>
   <option value="Republic of Serbia">Republic of Serbia</option>
   <option value="Reunion">Reunion</option>
   <option value="Romania">Romania</option>
   <option value="Russia">Russia</option>
   <option value="Rwanda">Rwanda</option>
   <option value="St Barthelemy">St Barthelemy</option>
   <option value="St Eustatius">St Eustatius</option>
   <option value="St Helena">St Helena</option>
   <option value="St Kitts-Nevis">St Kitts-Nevis</option>
   <option value="St Lucia">St Lucia</option>
   <option value="St Maarten">St Maarten</option>
   <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
   <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
   <option value="Saipan">Saipan</option>
   <option value="Samoa">Samoa</option>
   <option value="Samoa American">Samoa American</option>
   <option value="San Marino">San Marino</option>
   <option value="Sao Tome & Principe">Sao Tome & Principe</option>
   <option value="Saudi Arabia">Saudi Arabia</option>
   <option value="Senegal">Senegal</option>
   <option value="Seychelles">Seychelles</option>
   <option value="Sierra Leone">Sierra Leone</option>
   <option value="Singapore">Singapore</option>
   <option value="Slovakia">Slovakia</option>
   <option value="Slovenia">Slovenia</option>
   <option value="Solomon Islands">Solomon Islands</option>
   <option value="Somalia">Somalia</option>
   <option value="South Africa">South Africa</option>
   <option value="Spain">Spain</option>
   <option value="Sri Lanka">Sri Lanka</option>
   <option value="Sudan">Sudan</option>
   <option value="Suriname">Suriname</option>
   <option value="Swaziland">Swaziland</option>
   <option value="Sweden">Sweden</option>
   <option value="Switzerland">Switzerland</option>
   <option value="Syria">Syria</option>
   <option value="Tahiti">Tahiti</option>
   <option value="Taiwan">Taiwan</option>
   <option value="Tajikistan">Tajikistan</option>
   <option value="Tanzania">Tanzania</option>
   <option value="Thailand">Thailand</option>
   <option value="Togo">Togo</option>
   <option value="Tokelau">Tokelau</option>
   <option value="Tonga">Tonga</option>
   <option value="Trinidad & Tobago">Trinidad & Tobago</option>
   <option value="Tunisia">Tunisia</option>
   <option value="Turkey">Turkey</option>
   <option value="Turkmenistan">Turkmenistan</option>
   <option value="Turks & Caicos Is">Turks & Caicos Is</option>
   <option value="Tuvalu">Tuvalu</option>
   <option value="Uganda">Uganda</option>
   <option value="United Kingdom">United Kingdom</option>
   <option value="Ukraine">Ukraine</option>
   <option value="United Arab Erimates">United Arab Emirates</option>
   <option value="United States of America">United States of America</option>
   <option value="Uraguay">Uruguay</option>
   <option value="Uzbekistan">Uzbekistan</option>
   <option value="Vanuatu">Vanuatu</option>
   <option value="Vatican City State">Vatican City State</option>
   <option value="Venezuela">Venezuela</option>
   <option value="Vietnam">Vietnam</option>
   <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
   <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
   <option value="Wake Island">Wake Island</option>
   <option value="Wallis & Futana Is">Wallis & Futana Is</option>
   <option value="Yemen">Yemen</option>
   <option value="Zaire">Zaire</option>
   <option value="Zambia">Zambia</option>
   <option value="Zimbabwe">Zimbabwe</option>
</datalist>

<datalist id="statelist">
	<option value="Andhra Pradesh">Andhra Pradesh</option>
	<option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
	<option value="Arunachal Pradesh">Arunachal Pradesh</option>
	<option value="Assam">Assam</option>
	<option value="Bihar">Bihar</option>
	<option value="Chandigarh">Chandigarh</option>
	<option value="Chhattisgarh">Chhattisgarh</option>
	<option value="Dadar and Nagar Haveli">Dadar and Nagar Haveli</option>
	<option value="Daman and Diu">Daman and Diu</option>
	<option value="Delhi">Delhi</option>
	<option value="Lakshadweep">Lakshadweep</option>
	<option value="Puducherry">Puducherry</option>
	<option value="Goa">Goa</option>
	<option value="Gujarat">Gujarat</option>
	<option value="Haryana">Haryana</option>
	<option value="Himachal Pradesh">Himachal Pradesh</option>
	<option value="Jammu and Kashmir">Jammu and Kashmir</option>
	<option value="Jharkhand">Jharkhand</option>
	<option value="Karnataka">Karnataka</option>
	<option value="Kerala">Kerala</option>
	<option value="Madhya Pradesh">Madhya Pradesh</option>
	<option value="Maharashtra">Maharashtra</option>
	<option value="Manipur">Manipur</option>
	<option value="Meghalaya">Meghalaya</option>
	<option value="Mizoram">Mizoram</option>
	<option value="Nagaland">Nagaland</option>
	<option value="Odisha">Odisha</option>
	<option value="Punjab">Punjab</option>
	<option value="Rajasthan">Rajasthan</option>
	<option value="Sikkim">Sikkim</option>
	<option value="Tamil Nadu">Tamil Nadu</option>
	<option value="Telangana">Telangana</option>
	<option value="Tripura">Tripura</option>
	<option value="Uttar Pradesh">Uttar Pradesh</option>
	<option value="Uttarakhand">Uttarakhand</option>
	<option value="West Bengal">West Bengal</option>
</datalist>

<script type="text/javascript">
	
	document.getElementById("<?php echo $PAGE["Menu"] ?>").classList.add("pcoded-trigger");

	document.getElementById("<?php echo $PAGE["Menu"] ?>-new").classList.add("active");


</script>
<?php
    
    include("../../../pages/endbody.php");

?>

<script type="text/javascript">
		$(document).ready(function() {
  	$(".js-example-basic-single").select2();
  	$(".js-example-basic-multiple").select2();

  })
</script>