<?php
$data = new data(1);
?>
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
</div>
<div class="clearfix"></div>