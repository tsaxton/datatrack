<form class='form' id="input-paste" role='form' method='post' action='templates/input-paste.php'>

<div class="form-group">
    <label for="uploadFile">Paste your file here.</label>
    <textarea class='form-control' id="file" class="form-control" name="file" rows="15"/>
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
