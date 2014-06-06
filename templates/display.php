<?php
if($dataset != NULL){
	$results = $db->queryFirstRow('select * from datasets where id='.$dataset);
	if(!$results){
		die();
	}
	if($results['type'] == 'yearly'){
		$data = new yearly($dataset);
	}
	elseif($results['type'] == 'monthly'){
		$data = new monthly($dataset);
	}
}
elseif(array_key_exists('csv', $_SESSION)){
	$csv = $_SESSION['csv'];
	switch($csv->type){
	case 'monthly':
		$data = new monthly($csv->data);
		break;
	case 'quarterly':
		//$data = new quarterly($csv->data);
		break;
	case 'yearly':
		$data = new yearly($csv->data);
		break;
	}
	$data->setName($csv->name);
}
else{
	die('No data set selected');
}
$_SESSION['data'] = $data; // store the current data set as the most recent one
#$recent = new recentAnalysis($data);
$data->analyze();
//$long = new longTerm($data);
?>
<div class = "importer">
<div class = "container">
<?php
if(!array_key_exists('dataset', $_GET)){
?>
      <div class = "row">
          <div class = "col-lg-4">
          		<div class = "grayBox"><h1> 1. Upload Data</h1></div>
          </div>
          <div class = "col-lg-4">
             <div class = "grayBox"><h1> 2. Check Data</h1></div>
          </div>
          <div class = "col-lg-4">
              <div class = "purpleBox"><h1> 3. Analyze</h1></div>
          </div>
      </div>
	<div class="row">
		<div class="col-sm-12 well well-sm" style="margin-top: 10px;">
			Bookmark the following link to save your data!<br/>
			<a href="?id=display&dataset=<?=$data->getId()?>"><?=$rootURL?>?id=display&dataset=<?=$data->getId()?></a>
		</div>
	</div>
<?php
}
?>

<div class = "topTrends">
	<div class="container">

		<div class="row">
			<div class="col-lg-12">
				<h1 class = "displayHeadline"><?=$data->getName()?> Analysis</h1>
			</div>
		</div>



<!--<script>
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
    var labelArray = [];
<?php
foreach($data->fields as $field){
    echo "\tvar vals = [];\n\tlabelArray.push('{$field['text']}');\n\tvar json = ajaxData('{$data->id}', '{$field['field']}');\n\tfor(var j in json){\n\t\tvals.push([parseInt(j), parseInt(json[j])]);\n\t}\n\tchartData.push(vals);\n\n";
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
	legend:{
	    show: true,
	    location: 'e',
	    labels: labelArray,
	    placement: 'outsideGrid',
	    border: 'none'
	}
      });
});
</script>-->
<div class="row-fluid tabbable">
	<div class="span3">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#recent" data-toggle="tab">Most Recent Data</a></li>
			<li><a href="#longterm" data-toggle="tab">Long-Term Trends</a></li>
			<li><a href="#streaks" data-toggle="tab">Streaks</a></li>
			<li><a href="#stats" data-toggle="tab">Statistics</a></li>
		</ul>
	</div>
	<div class="span4 tab-content">
		<div id="recent" class="tab-pane active">
			<h2>Most Recent Data (<?=$data->mostRecentStr();?>)</h2>
			<!-- Begin Recent Analysis -->
			<?=$data->printRecent();?>
			<!-- End Recent Analysis -->
		</div>
		<div id="longterm" class="tab-pane">
			<h2>Long-Term Trends</h2>
			<!-- Begin Long Term Analysis -->
			<?=$data->run();?>
			<!-- End Long Term Analysis -->
		</div>
		<div id="streaks" class="tab-pane">
			<h2>Streaks</h2>
			<?=$data->longStreak();?>
		</div>
		<div id="stats" class="tab-pane">
			<h2>Statistics</h2>
			<?=$data->statistics();?>
		</div>
	</div><!-- /.tab-content -->
	
</div>
</div>

<div>
	<select class="form-control" id="graphSelection">
  			<?php
  			foreach($data->fields as $field){
    			// This isn't quite the full code, you'll need to do <option> HTML tags
    			// This is just printing the names of what you need
  				echo "<option id=\"".preg_replace("/[^A-za-z0-9]/", '', $field['field'])."\">" .$field['text'] ."</option>";
    			// In the <option> tag, value="{$field['id']}"
  			}
  			?>
  	</select>
</div>

<div class = "lineGraph">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div id="graph" class="line"></div>
				<!--img class = "line" src = "/includes/img/linechart_symbols.png"-->
				<button class="Yes"> Get Link </button> <button class = "No"> Compare </button>
				<button class = "showMe"> Add / Edit Data </button>
				
				
			</div>
		</div>			
	</div>
</div>
    <!--<div class="span5 offset1 chart" id="chart3">
</div> -->
</div>

<div class="row-fluid">
	<div class="col-md-12">
		<h2>Data Tables</h2>
	</div>
</div>

<div class="row-fluid tabbable">
	<div class="col-md-2">
		<ul class="nav nav-tabs nav-stacked">
			<!-- Begin data table tabs -->
			<?php
			$first = 1;
			foreach($data->fields as $id=>$field){
				$class = '';
				if($first){
					$class = "class=\"active\"";
					$first = 0;
				}
	//preg_replace("/[^A-Za-z0-9]/", '', $string);
				echo "\t\t<li $class><a href=\"#tab$id\" data-toggle=\"tab\">{$field['text']}</a></li>";
    //echo "\t\t<li $class><a href=\"#".str_replace(' ','',$field['field'])."\" data-toggle=\"tab\">{$field['text']}</a></li>";
			}
			if($data->areProportions()){
				?>
				<li><a href="#props" data-toggle="tab">Proportions</a></li>
				<?php
			}

			?>
			<!-- End data table tabs -->
		</ul>
	</div>
	
	<div class="col-md-10 tab-content">
		<!-- Begin data tables -->
		<?php
		$first = 1;
		foreach($data->fields as $id=>$field){
			$active = '';
			if($first){
				$active = 'active';
				$first = 0;
			}
			echo "\t\t<div id=\"tab$id\" class=\"tab-pane $active\">\n\t\t\t<h3>{$field['text']}</h3>\n\t\t\t<!-- Begin Data Table: {$field['text']} -->\n";
    //echo "\t\t<div id=\"".str_replace(' ','',$field['field'])."\" class=\"tab-pane $active\">\n\t\t\t<h3>{$field['text']}</h3>\n\t\t\t<!-- Begin Data Table: {$field['text']} -->\n";
			echo $data->makeTable($field['field']);
			echo "\t\t\t<!-- End Data Table: {$field['text']} -->\n\t\t</div>\n";		
			//echo "<div style=\"display:none;\" title=\"".preg_replace("/[^A-za-z0-9]/", '', $field['field'])."\" class=\"invisibleData\"></div>";

		}
		if($data->areProportions()){
			?>
			<div id="props" class="tab-pane">
				<h3>Proportions</h3>
				<?=$data->tableProp();?>
			</div>
			<?php
		}

		?>
		<!-- End data tables -->
	</div><!-- /.tab-content -->
</div>

