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
			$('.Crime').fadeOut('slow');
			$('.Other').fadeOut('slow');
			$('.Transportation').fadeIn('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
		$('#display-crime').click(function(){
			$('.Crime').fadeIn('slow');
			$('.Other').fadeOut('slow');
			$('.Transportation').fadeOut('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
		$('#display-other').click(function(){
			$('.Crime').fadeOut('slow');
			$('.Other').fadeIn('slow');
			$('.Transportation').fadeOut('slow');
			$('.active').removeClass('active');
			self.addClass('active');
		});
	</script>
    </div>
    <div id="dashboard-main" class="span8">
<?php
$datasets = dbQuery('select * from datasets order by updated');
$i = 0;
$ct = count($datasets);
$sets = 6;
$k = 0;
for($j=0; $j < min($sets, $ct); $j++){
    $temp = new recentAnalysis(intval($datasets[$j]['id']));
    if($temp->success()){
	$data[$k++] = $temp;
    }
    else{
	$sets++;
	$ct--;
    }
}
$j = 0;
while($i < 6){
	$categories = $data[$i%$ct]->getCategories();
    echo "<div class=\"dashboard-box small-box well $categories\">";
    echo "<a href=\"?id=display&dataset=" . $data[$i%$ct]->getId() . "\">";
    echo $data[$i%$ct]->obs[$j];
    echo "</a></div>";
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
$datasets = dbQuery('select * from datasets order by name');
foreach($datasets as $d){
    echo "\t\t\t<li><a href=\"?id=display&dataset={$d['id']}\">{$d['name']}</a></li>\n";
}
?>
	</ul>
    </div>
</div>
