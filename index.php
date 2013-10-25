<?php
//Hello
require('admin/config.php');
include('templates/header.php');
include('classes/data.php');

if(array_key_exists('id',$_GET)){
    $id = $_GET['id'];
}
else{
    $id = 'display';
}

include("logic/$id.php");
include('templates/footer.php');
?>
