<?php

$json = json_decode(file_get_contents('http://data.cityofchicago.org/resource/w8km-9pzd.json'), true);

$sql = "INSERT INTO cta_annual (year, bus, rail) VALUES";
foreach($json as $data){
    $sql .= " ({$data['year']}, {$data['bus']}, {$data['rail']}),";
}
$sql = substr($sql,0,-1);
echo $sql . '<br>';
?>
