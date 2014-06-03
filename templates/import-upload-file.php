<div class = "importHeadline">
      <h1> Import Data Tool</h1>
      </div>

<form class='form' id="input-file" role='form' method='post' action='?id=analyze' enctype="multipart/form-data">
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<label for="name">Dataset Name</label>
				<input type="text" class="form-control" id="name" name="name"/>
			</div>
		</div>
	</div>
	<div class = "row">
		<div class = "col-lg-6">
			<div class="radio-inline">
				<br>
				<label>
					<input type="radio" id="timeframe-monthly" name="timeframe" onclick="select_timeMonth()" value="monthly"/>
					Monthly
				</label>
			</div>
	<!--<div class="radio-inline">
		<br>
		<label>
			<input type="radio" id="timeframe-quarterly" name="timeframe" onclick="select_timeQuarter()" value="quarterly"/>
				Quarterly
			</label>
	</div>-->
			<div class="radio-inline">
				<br>
				<label>
					<input checked type="radio" id="timeframe-yearly" name="timeframe" onclick="select_timeYear()" value="yearly"/>
					Yearly
				</label>
			</div>
    	</div>
	</div>
	<div class = "row">
		<div>
  			<div ="form-group" id="file-group">
        		<div class="row">
	        		<div class="form-group col-lg-6">
	            		<input type="file" id="file_input" name="files[]" class="form-control" placeholder="Add your file here"/>
	        		</div>
	        		<div class="col-md-2" style="display:none;" name="select_month">
	            		<select class="form-control" id="Monthly" name="month[]">
	                		<option value="0">Month</option>
	                		<option value="1">January</option>
			    			<option value="2">February</option>
			    			<option value="3">March</option>
			    			<option value="4">April</option>
			    			<option value="5">May</option>
			    			<option value="6">June</option>
							<option value="7">July</option>
			    			<option value="8">August</option>
			    			<option value="9">September</option>
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
					<div class="col-md-2" name="select_year">
	        			<select class="form-control" name="year[]" id="Yearly">
		        			<option value="time">Year</option>   
							<?php
								//for($i=1950; $i<=date("Y"); $i++){
								for($i=date("Y"); $i >= 1950; $i--){
										echo "<option value='$i'>$i</option>";
								}	
							?>  
		    			</select>   
		    		</div>
				</div>  
    		</div>
    	</div>
	</div>
    
    <div class = "row">
    	<div class = "col-lg-12">
			<div class="form-group">
 	 	 	 	 <a id="additionalFile" href="#" onclick="add()"><span class="glyphicon glyphicon-plus"></span> Click Here to Add Another File</a>
			</div>
		</div>
	</div>
	<div class="form-group">
		<button type='submit' class='btn btn-default'>Submit</button>
	</div>
</form>

