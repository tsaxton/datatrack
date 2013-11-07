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

    var chartData = [];
<?php
foreach($data->fields as $field){
    echo "\tvar vals = [];\n\tvar json = ajaxData('{$data->id}', '{$field['field']}');\n\tfor(var j in json){\n\t\tvals.push([parseInt(j), parseInt(json[j])]);\n\t}\n\tchartData.push(vals);\n\n";
}
?>
    /*var vals = [];
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
    }*/

    //var plot2 = $.jqplot('chart3', [vals, vals2, vals3],{
    var plot2 = $.jqplot('chart3', chartData,{
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
<?php
foreach($data->fields as $field){
    echo "<h3>{$field['text']}</h3>";
    echo $data->makeTable($field['field']);
}
?>
    </div><!-- End data table -->
</div>
