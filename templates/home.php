<header>
            <div class = "container">
       			<h1>trendable <img src = "includes/img/trendablePicLogo.png"></h1>
               <p> Easily import, knit together and analyze time series data.</p>
              <!--<input placeholder = "Paste data or URL of data in JSON, XML or CSV format here.">
              <button class="analyze"> Analyze </button> <button title = "Upload" class = "upload"> <img src = "includes/img/upload.png"> </button>-->
              <a href = "/?id=import"><button class="analyze" title = "Import Data"><img src = "includes/img/upload.png"> Import Data </button> </a><button title = "Datasets" class = "upload"> <span class="glyphicon glyphicon-cloud"></span> My Data </button>
            </div>
       		</header>

<!--<div class = "examples">
            <div class = "container">
       				<h2> Examples: </h2>
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div> 
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div> 
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div> 
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div>
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div>
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div>
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div>
                  <div class = "exampleBox"><img src ="http://www.thegamejar.com/wp-content/uploads/2012/12/graph3.jpg"> <h3> stuff </h3> </div> 
            </div>
       		</div>

<div class="row-fluid">
    <div class="span2">
	<ul class="nav nav-list">
	    <li class="nav-header">Categories</li>
	    <li><a href="#crime" id="display-crime">Crime</a></li>
	    <li><a href="#transit" id="display-transit">Transportation</a></li>
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
/*$datasets = $db->query('select * from datasets order by updated');
$i = 0;
$ct = count($datasets);
$sets = 6;
$k = 0;
for($j=0; $j < min($sets, $ct); $j++){
	switch($datasets[$j]['type']){
		case 'monthly':
			$temp = new monthly(intval($datasets[$j]['id']));
			break;
		case 'quarterly':
			break;
		case 'yearly':
			$temp = new yearly(intval($datasets[$j]['id']));
			break;
	}
    if($temp->success){
		$temp->analyze();
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
	if(array_key_exists($j,$data[$i%$ct]->obs)){
    echo "<div class=\"dashboard-box small-box well $categories\">";
    echo "<a href=\"?id=display&dataset=" . $data[$i%$ct]->getId() . "\">";
    echo $data[$i%$ct]->obs[$j];
    echo "</a></div>";
    $i++;
	}
    if($i%$ct == 0){
	$j++;
    }
}*/
?>
    </div>
    <div class="span2">
	<ul class="nav nav-list">
	    <li class="nav-header">Data Sets</li>
<?php
/*$datasets = $db->query('select * from datasets order by name');
foreach($datasets as $d){
    echo "\t\t\t<li><a href=\"?id=display&dataset={$d['id']}\">{$d['name']}</a></li>\n";
}*/
?>
	</ul>
    </div>
</div>-->
