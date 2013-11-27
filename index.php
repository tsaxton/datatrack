<?php
require('admin/config.php');
include('includes/datatrack.php');
include('templates/header.php');
include('classes/data.php');
include('classes/recentAnalysis.php');
include('classes/longTerm.php');

if(array_key_exists('id',$_GET)){
    $id = $_GET['id'];
}
else{
    $id = 'display';
}

include("templates/$id.php");
include('templates/footer.php');
?>
