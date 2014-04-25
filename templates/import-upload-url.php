<form class='form' role='form' method='post' action='input-file.php'>

  <div class="form-group">
    <label for="URL">link</label>
    <input class="form-control" type="text" id="URL">
    <p class="help-block">input your link here.</p>
  </div>

  <form id="data-type-selection" role="form">
	<div class="radio-inline">
		<label>
			<input type="radio" id="data-type-monthly" checked name="data-type" value="monthly"/>
				Monthly
			</label>
	</div>
	<div class="radio-inline">
		<label>
			<input type="radio" id="data-type-Quarterly" name="data-type" value="Quarterly"/>
				Quarterly
			</label>
	</div>
	<div class="radio-inline">
		<label>
			<input type="radio" id="data-type--Yearly" name="data-type" value="Yearly"/>
				Yearly
		</label>
	</div>
</form>

  <button type='submit' class='btn btn-default'>submit</button>
</form>
