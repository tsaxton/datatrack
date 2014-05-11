<?php

$i = $_GET["index"];

$str =  '<div class="col-md-12">
	  		<div class="form-group col-md-5">
	        	<label for="file_1_input">File' . $i . ':</label>
	            <input type="file" id="file_input" class="form-control" placeholder="Add your file here"/>
	        </div>
	        <div class="col-md-2" style="display:none;" name="select_month">
	        	<br>
	            <select class="form-control" id="Monthly">
	                <option value="time">Month</option>
	                <option value="time">January</option>
			    	<option value="time">February</option>
			    	<option value="time">March</option>
			    	<option value="time">April</option>
			    	<option value="time">May</option>
			    	<option value="time">June</option>
			    	<option value="time">August</option>
			    	<option value="time">September></option>
			    	<option value="time">October</option>
			    	<option value="time">November</option>
			    	<option value="time">December</option>
		        </select>
		    </div>                  
			<div class="col-md-2" style="display:none;" name="select_quarter">
				<br>
	            <select class="form-control" id="Quarterly">
	            	<option value="time">Quarter</option>
	                <option value="time">Quarter 1</option>
	                <option value="time">Quarter 2</option>
			    	<option value="time">Quarter 3</option>
			    	<option value="time">Quarter 4</option>
		        </select>
		    </div>               
			<div class="col-md-2" style="display:none;" name="select_year">
				<br>
	            <select class="form-control" id="Yearly">
	                <option value="time">Year</option>';

$str2 = '';
for($j=1950; $j<=date("Y"); $j++){
	$str2 .= "<option> $j </option>";		
}	
					 
$str3 = '</select></div></div>';

echo $str . $str2 . $str3;
?>