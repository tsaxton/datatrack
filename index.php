<?php
include('includes.php');
session_start();
include('templates/header.php');
if(array_key_exists('id',$_GET)){
    $id = $_GET['id'];
}
else{
    $id = 'home';
}

if(($id == 'display' || $id=='dump') && array_key_exists('dataset', $_GET)){
    $dataset = $_GET['dataset'];
}
else{
	$dataset = NULL;
}

include("templates/$id.php");
include('templates/footer.php');
?>
