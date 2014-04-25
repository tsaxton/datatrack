
<form class='form' role='form' method='post' action='input-paste.php'>

  <div class="form-group">
    <label for="uploadFile">paste your file here.</label>
    <textarea class='form-control' id="PasteFile">
    
	</textarea>
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

  <button type='submit' class='btn btn-default'>paste</button>
</form>