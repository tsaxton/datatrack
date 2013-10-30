<?php
    $data = new data(1);
?>
<div class="row">
    <div class="span12">
	<h2><?=$data->getName()?></h2>
    </div>
</div>

<script>
        $(document).ready(function(){

            var ajaxData = function(id, field) {
            var ret = null;
            $.ajax({
              async: false,
              url: 'json.php?id=' + id + '&field=' + field,
              dataType:"json",
              success: function(data) {
                ret = data;
              }
            });
            return ret;
          };

	var vals = [];
	var json = ajaxData('1', 'total');
	for(var j in json){
	    vals.push([parseInt(j), parseInt(json[j])]);
	}
	var vals2 = [];
	var json2 = ajaxData('1', 'bus');
	for(var j in json2){
	    vals2.push([parseInt(j), parseInt(json2[j])]);
	}
	var vals3 = [];
	var json3 = ajaxData('1', 'rail');
	for(var j in json3){
	    vals3.push([parseInt(j), parseInt(json3[j])]);
	}

        var plot2 = $.jqplot('chart3', [vals, vals2, vals3],{
            title: "CTA Annual Ridership",
	    axes:{
		xaxis: {
		    tickOptions: {formatString: '%d'},
		}
	    },
	    series:[
	      {
		// Change our line width and use a diamond shaped marker.
		lineWidth:2,
		markerOptions: { style:'dimaond' }
	      }],
          });

        });
</script>
<div class="row">
    <div class="span6">
	<h3>Key Observations</h3>
	<ul>
	    <li>From 2011-2012, bus ridership grew much more slowly than rail ridership.</li>
	    <li>The largest one-year ridership increase overall was between 2007-2008 by percent.</li>
	    <li>The largest one-year ridership decrease overall was between 1992-1993 by percent.</li>
	    <li>Ridership dropped by more than 1/4 from 1988-1998.</li>
	</ul>
    </div>

    <div class="span5 offset1 chart" id="chart3">
    </div>
</div>
<div class="row">
    <div class="span12"><!-- Begin data table -->
<h3>Total Ridership (Bus & 'L')</h3>
<?=$data->makeTable('total');?>
<h3>Bus Ridership</h3>
<?=$data->makeTable('bus');?>
<h3>Elevated Train Ridership</h3>
<?=$data->makeTable('rail');?>
    </div><!-- End data table -->
</div>
