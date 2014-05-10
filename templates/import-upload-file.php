<form class='form' id="input-file" role='form' method='post' action='templates/input-file.php' enctype="multipart/form-data">
<div class="col-md-12">
  	<div class="form-group" id="file-group">
	  	<div class="col-md-12">
	  		<div class="form-group col-md-5">
	        	<label for="file_1_input">File 1:</label>
	            <input type="file" id="file_1_input" class="form-control" placeholder="Add your file here"/>
	        </div>
	        <div class="col-md-2" style="display:none;" id="select_month">
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
			<div class="col-md-2" style="display:none;">
				<br>
	            <select class="form-control" id="Quarterly">
	            	<option value="time">Quarter</option>
	                <option value="time">Quarter 1</option>
	                <option value="time">Quarter 2</option>
			    	<option value="time">Quarter 3</option>
			    	<option value="time">Quarter 4</option>
		        </select>
		    </div>               
			<div class="col-md-2" style="display:none;">
				<br>
	            <select class="form-control" id="Yearly">
	                <option value="time">Year</option>
	                <option value="time">1990</option>
			    	<option value="time">1991</option>
		        </select>
		    </div>               
		</div>
        
        <div class="col-md-12">
	        <div class="form-group col-md-5">
	        	<label for="file_2_input">File 2:</label>
	            <input type="file" id="file_2_input" class="form-control" placeholder="Add your file here"/>
	        </div>
	        <div class="col-md-2">
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
			<div class="col-md-2">
				<br>
	            <select class="form-control" id="Quarterly">
	            	<option value="time">Quarter</option>
	                <option value="time">Quarter 1</option>
	                <option value="time">Quarter 2</option>
			    	<option value="time">Quarter 3</option>
			    	<option value="time">Quarter 4</option>
		        </select>
		    </div>               
			<div class="col-md-2">
				<br>
	            <select class="form-control" id="Yearly">
	                <option value="time">Year</option>
	                <option value="time">1990</option>
			    	<option value="time">1991</option>
		        </select>
		    </div>
		</div>   
    </div>
 <button type="button" class="btn btn-default" onclick="add()">Add</button>
</div>
		

	<div class="radio-inline">
		<br>
		<label>
			<input type="radio" id="timeframe-monthly" name="timeframe" value="monthly"/>
				Monthly
			</label>
	</div>
	<div class="radio-inline">
		<br>
		<label>
			<input type="radio" id="timeframe-quarterly" name="timeframe" value="quarterly"/>
				Quarterly
			</label>
	</div>
	<div class="radio-inline">
		<br>
		<label>
			<input type="radio" id="timeframe-yearly" name="timeframe" value="yearly"/>
				Yearly
		</label>
	</div>

	<div class="form-group">
		<button type='submit' class='btn btn-default'>Submit</button>
	</div>
</form>
