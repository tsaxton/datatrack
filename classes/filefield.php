<?php

$i = $_GET["index"];

$str =  '<div class="col-md-12">
	  		<div class="form-group col-md-5">
	        	<label for="file_1_input">File' . $i . ':</label>
	            <input type="file" id="file_input" name="files[]" class="form-control" placeholder="Add your file here"/>
	        </div>
	        <div class="col-md-2" style="display:none;" name="select_month">
	        	<br>
	            <select class="form-control" name="month[]" id="Monthly">
	                <option value="0">Month</option>
	                <option value="1">January</option>
			    	<option value="2">February</option>
			    	<option value="3">March</option>
			    	<option value="4">April</option>
			    	<option value="5">May</option>
			    	<option value="6">June</option>
					<option value="7">July</option>
			    	<option value="8">August</option>
			    	<option value="9">September></option>
			    	<option value="10">October</option>
			    	<option value="11">November</option>
			    	<option value="12">December</option>
		        </select>
		    </div>                  
			<div class="col-md-2" style="display:none;" name="select_quarter">
				<br>
	            <select class="form-control" name="quarter[]" id="Quarterly">
	            	<option value="0">Quarter</option>
	                <option value="1">Quarter 1</option>
	                <option value="2">Quarter 2</option>
			    	<option value="3">Quarter 3</option>
			    	<option value="4">Quarter 4</option>
		        </select>
		    </div>               
			<div class="col-md-2" style="display:none;" name="select_year">
				<br>
	            <select class="form-control" name="year[]" id="Yearly">
	                <option value="0">Year</option>';

$str2 = '';
//for($j=1950; $j<=date("Y"); $j++){
for($j=date("Y"); $j>=1950; $j--){
	$str2 .= '<option value="$j"> $j </option>';		
}	
					 
$str3 = '</select></div></div>';

echo $str . $str2 . $str3;
?>
