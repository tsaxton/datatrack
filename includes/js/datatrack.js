$(document).ready(function(){
	$("input[name=timeframe]:radio").change(function(){	
		 if ($("#r1").attr("checked")) {

		 }
	});

	if($('#upload').length != 0){
		$.get('templates/import-upload.php',{},function(response){
			$('#upload').html(response);
			$.get('templates/import-upload-file.php', {}, function(r){
				$('#import-upload-form').html(r);
			})
		})
	}
	
});

$(document).ajaxComplete(function(){
	if($('input[name=import-upload-type]').length != 0){
		$('input[name=import-upload-type]:radio').change(function(){
			$('#import-upload-form').html('Loading...');
			var selected = $('input[name=import-upload-type]:checked', '#import-upload-selection').val();
			$.get('templates/import-upload-'+selected+'.php', {}, function(response){
				$('#import-upload-form').html(response);
			})
		})
	}
	$('#input-paste').ajaxForm({
		beforeSend: function(){
			$('#upload').collapse('hide').html('');
			$('#time').collapse('show').html('Loading...');
		},
		complete: function(response){
			$('#time').html(response.responseText);
		},
		error: function(){
		}
	})
	/*$('#good-date').click(function(){
		$('#time').collapse('hide').html('');
		$('#attributes').clooapse('show').html('Loading...');
		$.get('templates/data-attributes.php', {}, function(response){
			$('#attributes').html(response);
		});
	});*/
	$('#confirm-date').ajaxForm({
		beforeSend: function(){
			$('#time').collapse('hide').html('');
			$('#attributes').collapse('show').html('Loading...');
		},
		complete: function(response){
			$('#attributes').html(response.responseText);
		},
		error: function(){
		}
	})
	$('#fieldSubmission').ajaxForm({
		beforeSend: function(){
			$('#attributes').collapse('hide').html('');
			$('#troubleshooting').collapse('show').html('Loading...');
		},
		complete: function(response){
			console.log(response.responseText);
			if(response.responseText.indexOf("success") != -1){
				$('#troubleshooting').collapse('hide').html('');
				$('#view').collapse('show').html('Your data has been verified. Preparing to load data analysis...');
				window.location.replace('templates/previewData.php');
			}
			else{
				$('#troubleshooting').html(response.responseText);
			}
		},
		error: function(){
		}
	})
});

function add() {
	var files = $('#file-group');
	var n = files.children().length;
	var nextFile = '<div class="col-md-12"><div class="form-group col-md-5"><label for="file#">File '
			+ (n + 1)
			+ ':</label><input type="file" class="form-control" id="File_'
			+ (n + 1) + '_input"placeholder="Add your file here" /></div>'
			+ '<div class="col-md-2">\
	        	<br>\
	            <select class="form-control" id="Monthly">\
	                <option value="time">Month</option>\
	                <option value="time">January</option>\
			    	<option value="time">February</option>\
			    	<option value="time">March</option>\
			    	<option value="time">April</option>\
			    	<option value="time">May</option>\
			    	<option value="time">June</option>\
			    	<option value="time">August</option>\
			    	<option value="time">September></option>\
			    	<option value="time">October</option>\
			    	<option value="time">November</option>\
			    	<option value="time">December</option>\
		        </select>\
		    </div>'
		    + '<div class="col-md-2">\
				<br>\
	            <select class="form-control" id="Quarterly">\
	            	<option value="time">Quarter</option>\
	                <option value="time">Quarter 1</option>\
	                <option value="time">Quarter 2</option>\
			    	<option value="time">Quarter 3</option>\
			    	<option value="time">Quarter 4</option>\
		        </select>\
		    </div>'
		    + '<div class="col-md-2">\
				<br>\
	            <select class="form-control" id="Yearly">\
	                <option value="time">Year</option>\
	                <option value="time">1990</option>\
			    	<option value="time">1991</option>\
		        </select>\
		    </div></div></div>';

	files.append(nextFile);
}

function select_timefield(){
	
	if ( document.radios.timeframe1.checked == true ) {
		alert("hi");
	}

}

