<?php
include('../includes.php');
session_start();


$fields = array();

$i = 0;
foreach($_POST as $field=>$junk){
	$fields[$i++] = array('id'=>NULL, 'dataset'=>NULL, 'field'=>$field, 'text'=>$field, 'major'=>1);
}

$_SESSION['fields'] = $fields;
?>
success
