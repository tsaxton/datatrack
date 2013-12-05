<div class="row">
    <div class="span2">
	<ul class="nav nav-list">
	    <li class="nav-header">Categories</li>
	    <li><a href="#crime" id="display-crime">Crime</a></li>
	    <li><a href="#energy" id="display-other">Energy</a></li>
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
	<div class="dashboard-box small-box well transit">
	<p>CTA rail ridership hit a new high this year.</p>
	</div>
	<div class="dashboard-box small-box well transit">
	<p>Total CTA ridership (bus & rail) is up 2.56% this year.</p>
	</div>
	<div class="dashboard-box small-box well crime">
	<p>Crime in Chicago is down 2.5% last month versus November 2012.</p>
	</div>
	<div class="dashboard-box small-box well other">
	<p>There are 138 alternative fuel locations in the City of Chicago.</p>
	</div>
    </div>
    <div class="span2">
    Data Sets
    </div>
</div>
