<?php
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
dump($data);
