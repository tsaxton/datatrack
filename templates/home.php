<div class="row-fluid">
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
$sets = 6;
for($j=0; $j < min($sets, $ct); $j++){
    $temp = new recentAnalysis(intval($datasets[$j]['id']));
    if($temp->success()){
	$data[$j] = $temp;
    }
    else{
	$sets++;
	$ct--;
    }
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
</div>
