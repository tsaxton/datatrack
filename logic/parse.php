<?php

$json = file_get_contents('http://data.cityofchicago.org/resource/w8km-9pzd.json');
if($json){
    $json = json_decode($json, true);

    $rows = array();

    foreach($json as $data){
	$rows[] = array('year' => $data['year'], 'bus' => $data['bus'], 'rail' => $data['rail']);
    }


    $db->insert('cta_annual',$rows);
}
else{
    echo "Failed to open data";
}
?>
