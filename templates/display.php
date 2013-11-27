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
	title: "<?=$data->getName();?>",
	//title: "CTA Annual Ridership",
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
<div class="row-fluid tabbable">
    <div class="span2">
      <ul class="nav nav-tabs nav-stacked">
	<li class="active"><a href="#recent" data-toggle="tab">Most Recent Data</a></li>
	<li><a href="#longterm" data-toggle="tab">Long-Term Trends</a></li>
	<li><a href="#stats" data-toggle="tab">Statistics</a></li>
      </ul>
    </div>
      <div class="span4 tab-content">
	<div id="recent" class="tab-pane active">
	    <h3>Most Recent Data (<?=$data->mostRecent();?>)</h3>
	    <!-- Begin Recent Analysis -->
	    <?=$recent->run();?>
	    <!-- End Recent Analysis -->
	</div>
	<div id="longterm" class="tab-pane">
	<h4>Pane 2 Content</h4>
	  <p> and so on ...</p>
	</div>
	<div id="stats" class="tab-pane">
	    <h3>Statistics</h3>
	    <?=$long->statistics();?>
	</div>
      </div><!-- /.tab-content -->
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

<!--<div class="row-fluid tabbable">
    <div class="span2">
      <ul class="nav nav-tabs nav-stacked">
	<li class="active"><a href="#recent" data-toggle="tab">Most Recent Data</a></li>
	<li><a href="#longterm" data-toggle="tab">Long-Term Trends</a></li>
	<li><a href="#stats" data-toggle="tab">Statistics</a></li>
      </ul>
    </div>
      <div class="span10 tab-content">
	<div id="recent" class="tab-pane active">
	    <h3>Most Recent Data (<?=$data->mostRecent();?>)</h3>
	    
	    <?=$recent->run();?>
	    
	</div>
	<div id="longterm" class="tab-pane">
	<h4>Pane 2 Content</h4>
	  <p> and so on ...</p>
	</div>
	<div id="stats" class="tab-pane">
	    <h3>Statistics</h3>
	    <?=$long->statistics();?>
	</div>
      </div>
    </div>
</div>-->
