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
				window.location = 'templates/previewData.php';
			}
			else{
				$('#troubleshooting').html(response.responseText);
			}
		},
		error: function(){
		}
	})
});
