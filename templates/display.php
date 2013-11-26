<?php
$data = new data(1);
$recent = new recentAnalysis($data);
$long = new longTerm($data);
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
	highlighter:{
	    show: true,
	    sizeAdjust: 7.5
	},
      });
});
</script>
<div class="row-fluid">
    <div class="span6">
	<h3>Most Recent Data (<?=$data->mostRecent();?>)</h3>
	<!-- Begin Recent Analysis -->
<?php
echo $recent->run();
?>
	<!-- End Recent Analysis -->
	<h3>Key Observations</h3>
	<ul>
	    <li>The largest one-year ridership increase overall was between 2007-2008 by percent.</li>
	    <li>The largest one-year ridership decrease overall was between 1992-1993 by percent.</li>
	    <li>Ridership dropped by more than 1/4 from 1988-1998.</li>
	</ul>

	<h3>Statistics</h3>
	<?=$long->statistics();?>
    </div>

    <div class="span5 offset1 chart" id="chart3">
    </div>
</div>
<div class="row-fluid">
    <div class="span12"><!-- Begin data tables -->
<?php
foreach($data->fields as $field){
    echo "<h3>{$field['text']}</h3>";
    echo $data->makeTable($field['field']);
}

echo "<h3>Proportions</h3>";
echo $data->tableProp();
?>
    </div><!-- End data tables -->
</div>
