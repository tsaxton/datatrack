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
<li class="muted"><a href="#crime" id="display-crime">Crime</li>
<li><a href="#transit" id="display-transit">Transportation</a></li>
<li class="muted">Government</li>
<li><a href="#other" id="display-other">Other</a></li>
</ul>
</div>
<script type="text/javascript">
	$('#display-transit').click(function(){
		$('.crime').fadeOut( function() { $(this).detach(); });
		$('.other').fadeOut( function() { $(this).detach(); });
	});
	$('#display-crime').click(function(){
		$('.transit').fadeOut( function() { $(this).detach(); });
		$('.other').fadeOut( function() { $(this).detach(); });
		
	});
	$('#display-other').click(function(){
		$('.transit').fadeOut( function() { $(this).detach(); });
		$('.crime').fadeOut( function() { $(this).detach(); });
		$('.other').fadeIn( function() { $(this).attach(); });
	});
</script>
<div id="dashboard-main">
	<div class="dashboard-box" id="initial">
		<p>Currently analyzing <span class="lrg-number">2</span> datasets with <span class="lrg-number">163</span> total rows of data from <span class="lrg-number">1</span> source.</p>
	</div>
	
	<div class="dashboard-box small-box">
		<h4><?$data = new data(1);?></h4>
		<div id="sample-graph">Graph</div>
		<p>And some content/analysis</p>
	</div>
	<div class="dashboard-box small-box transit">
	<p>CTA rail ridership hit a new high this year.</p>
	</div>
	<div class="dashboard-box small-box transit">
	<p>Total CTA ridership (bus & rail) is up 2.56% this year.</p>
	</div>
	<div class="dashboard-box small-box crime">
	<p>Crime in Chicago is down 2.5% last month versus November 2012.</p>
	</div>
	<div class="dashboard-box small-box other">
	<p>There are 138 alternative fuel locations in the City of Chicago.</p>
	</div>
</div>
<div class="clearfix"></div>