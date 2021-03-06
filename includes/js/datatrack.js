$(document).ready(function(){

	if($('#upload').length != 0){
		$.get('templates/import-upload.php',{},function(response){
			$('#upload').html(response);
			$.get('templates/import-upload-file.php', {}, function(r){
				$('#import-upload-form').html(r);
			})
		})
	}

	$('#checkall').click(function(e){
		e.preventDefault();
		$('input:checkbox').attr('checked', 'checked').prop('checked', true);
	});

	$('#uncheckall').click(function(e){
		e.preventDefault();
		$('input:checkbox').removeAttr('checked').prop('checked', false);
	});

	if($('#graph').length != 0){
		var optionID = $('#graphSelection option:selected').attr('id');
		var title = $('#graphSelection option:selected').html();
		drawGraph(optionID, title);

		$('#graphSelection').change(function(){
			var optionID = $('#graphSelection option:selected').attr('id');
			var title = $('#graphSelection option:selected').html();
			drawGraph(optionID, title);
		});
	}

	
});

function drawGraph(id, title){
	$('#graph').empty();
	graphData = [];
	result = getData(id);
	graphData.push(result);
	var Series = [];
	Series.push({label: title});

	var option = {
		legend: { 
			show: true,
			location: 'ne',
		},
		series: Series
	};
	$.jqplot('graph', graphData, option);
}

function getData(id){
	var column1 = [];
	$('table#' + id + ' tbody tr th:nth-child(1)').each(function(){
		//console.log($(this).text());
		column1.push(parseFloat($(this).text()));
	});
	column1.shift();
	//console.log(column1);

	var column2 = [];
	$('table#' + id + ' tbody tr td:nth-child(2)').each(function(){
		var num = $(this).text();
		num = num.replace(",", "");
		column2.push(parseFloat(num));
	});
	//console.log(column2);

	var result = [];
	for (var i = 0; i <= column1.length - 1; i++) {
		var temp = [];
		temp.push(column1[i])
		temp.push(column2[i]);
		result.push(temp);
	}
	return result;
}

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
	$('#additionalFile').click(function(e){
		e.preventDefault();
	});
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

var i = 1;


function add(){ // link that says add a file has ID "additionalFile"
	i++;
	var t = "none";
	var radioyear = "none";
    //e.preventDefault();
    if($('#timeframe-monthly').is(':checked')){
    	t = "month";
    }
    if($('#timeframe-quarterly').is(':checked')){
    	t = "quarter";
    }
    if($('#timeframe-yearly').is(':checked')){
    	radioyear = "year";
    }
    $.get('classes/filefield.php',{index:i,timefield:t,checkyear:radioyear},function(response){ // display folder is the equivalent of our tempates folder
		$('#file-group').append(response);
    })
}

function select_timeMonth(){
	if($('#timeframe-monthly').is(':checked')){
		$("div[name=select_month]").each(function() {
			$(this).css("display", "block");
		})
		$("div[name=select_quarter]").each(function (){
			$(this).css("display", "none");
		})
	}

}

function select_timeQuarter(){
	if($('#timeframe-quarterly').is(':checked')){
		$("div[name=select_month]").each(function() {
			$(this).css("display", "none");
		})
		$("div[name=select_quarter]").each(function (){
			$(this).css("display", "block");
		})
	}
}

function select_timeYear(){	
	if($('#timeframe-yearly').is(':checked')){
		$("div[name=select_year]").each(function (){
			$(this).css("display", "block");
		})
	}
}


