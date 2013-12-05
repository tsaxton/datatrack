<div class="row">
    <div class="span2">
	<ul class="nav nav-list">
	    <li class="nav-header">Categories</li>
	    <li><a href="#crime" id="display-crime">Crime</a></li>
	    <li><a href="#transit" id="display-transit">Transportation</a></li>
	    <!--<li>Government</li>-->
	</ul>
	<script type="text/javascript">
		$('#display-transit').click(function(){
			$('.crime').fadeOut('slow');
			$('.other').fadeOut('slow');
			$('.transit').fadeIn('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
		$('#display-crime').click(function(){
			$('.crime').fadeIn('slow');
			$('.other').fadeOut('slow');
			$('.transit').fadeOut('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
		$('#display-other').click(function(){
			$('.crime').fadeOut('slow');
			$('.other').fadeIn('slow');
			$('.transit').fadeOut('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
	</script>
    </div>
    <div id="dashboard-main" class="span8">
<?php
$datasets = $db->query('select * from datasets order by updated');
$i = 0;
$ct = count($datasets);
for($j=0; $j < min(6, $ct); $j++){
    $data[$j] = new recentAnalysis(intval($datasets[$j]['id']));
}
$j = 0;
while($i < 6){
    echo "<div class=\"dashboard-box small-box well\">";
    echo $data[$i%$ct]->obs[$j];
    echo "</div>";
    $i++;
    if($i%$ct == 0){
	$j++;
    }
}
?>
<<<<<<< HEAD
<div id="dashboard-menu">
<h3 class="dashboard-menu-section">Cities</h3>
<ul class="dashboard-menu-list">
<li class="muted">Chicago</li>
</ul>
<h3 class="dashboard-menu-section">Categories</h3>
<ul class="dashboard-menu-list">
<li><a href="#all" id="display-all">All</li>
<li class="muted"><a href="#crime" id="display-crime">Crime</li>
<li><a href="#transit" id="display-transit">Transportation</a></li>
<li class="muted">Government</li>
<li><a href="#other" id="display-other">Other</a></li>
</ul>
</div>
<script type="text/javascript">
	/*$(".small-box").hover(function(){
		$(this).css("background-color", "#cc0000");
	}, function(){
		$(this).css("background-color", "#656565");
	});*/
	$('#display-all').click(function(){
		$('.dashboard-box').appendTo( $('#dashboard-main') );
		$('#dashboard-main').append( $('.small-box') );
	});
	$('#display-transit').click(function(){
		$('.crime').fadeOut( function() { $(this).detach(); });
		$('.other').fadeOut( function() { $(this).detach(); });	
	});
	$('#display-crime').click(function(){
		$('.transit').fadeOut( function() { $(this).detach(); });
		$('.other').fadeOut( function() { $(this).detach(); });
		
	});
	$('#display-other').click(function(){
		$('.other').appendTo( $('#dashboard-main') );
		$('.transit').fadeOut( function() { $(this).detach(); });
		$('.crime').fadeOut( function() { $(this).detach(); });
	});
</script>
<div id="dashboard-main">
	<div class="dashboard-box" id="initial">
		<p>Currently analyzing <span class="lrg-number">2</span> datasets with <span class="lrg-number">163</span> total rows of data from <span class="lrg-number">1</span> source.</p>
	</div>
	
	<div class="dashboard-box">
		<h4></h4>
		<div id="sample-graph">Graph</div>
		<p></p>
		
	</div>
	<div class="dashboard-box transit">
	<p>CTA rail ridership hit a new high this year.</p>
	</div>
	<div class="small-box transit">
	<p>Total CTA ridership (bus & rail) is up 2.56% this year.</p>
	</div>
	<div class="small-box other">
	<p>Total CTA ridership (bus & rail) is up 2.56% this year.</p>
	</div>
<?php
/*
$results = $db->query('select * from datasets order by updated');
foreach($results as $result){
    $mostRecent = new recentAnalysis($result['id']);
    echo $mostRecent->keyObs();
}
*/
?>
=======
    </div>
    <div class="span2">
	<ul class="nav nav-list">
	    <li class="nav-header">Data Sets</li>
<?php
$datasets = $db->query('select * from datasets order by name');
foreach($datasets as $d){
    echo "\t\t\t<li><a href=\"?id=display&dataset={$d['id']}\">{$d['name']}</a></li>\n";
}
?>
	</ul>
    </div>
>>>>>>> 6825fc1404d60a4f89a132326c352f5b492e341f
</div>
