<form class='form' id="input-file" role='form' method='post' action='templates/input-file.php'>

  	<div class="form-group">
		<label for="uploadFile">Upload File</label>
		<input type="file" id="file" name="file" class="form-control">
  	</div>
  
	<div class="radio-inline">
		<label>
			<input type="radio" id="timeframe-monthly" name="timeframe" value="monthly"/>
				Monthly
			</label>
	</div>
	<div class="radio-inline">
		<label>
			<input type="radio" id="timeframe-quarterly" name="timeframe" value="quarterly"/>
				Quarterly
			</label>
	</div>
	<div class="radio-inline">
		<label>
			<input type="radio" id="timeframe-yearly" name="timeframe" value="yearly"/>
				Yearly
		</label>
	</div>

	<div class="form-group">
		<button type='submit' class='btn btn-default'>Submit</button>
	</div>
</form>
