<?php

include('../includes.php');
session_start();

$input = $_SESSION['input'];
$fields = $_SESSION['fields'];
$loc = $input->location;

//dump($fields);

if($loc == 'column'){
	$count = $input->getDataRows();
}
else{
	$count = $input->getDataColumns();
}

$figures = array();
for($i = 1; $i < $count; $i++){
	$data = $input->getData($i);
	//dump($data[0], 'year');
	$year = date('Y', strtotime($data[0]));
	$figures[$year] = array();

	//dump($data,'datarow');
	// TODO: Handle monthly/quarterly case
	foreach($fields as $field){
		$index = $input->getIndex($field['field']);
		$figures[$year][$field['field']] = $data[$index];
		//dump($index, $field['field']);
	}
}
dump($figures);
