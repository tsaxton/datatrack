$(document).ready(function(){
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
});
