<form class='form' role='form' method='post' action='input-url.php'>

	<div class="form-group">
		<label for="url">Link</label>
		<input class="form-control" type="text" name="url" id="url">
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
