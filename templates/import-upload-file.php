<form class='form' role='form' method='post' action='input-file.php'>

  <div class="form-group">
    <label for="uploadFile">Upload File</label>
    <input type="file" id="UploadFile">
    <p class="help-block">upload your file here.</p>
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

  <button type='submit' class='btn btn-default'>upload</button>
</form>