<?php
require('admin/config.php');
include('includes/datatrack.php');
include('templates/header.php');
include('classes/data.php');
include('classes/monthly.php');
include('classes/yearly.php');
include('classes/recentAnalysis.php');
include('classes/longTerm.php');

if(array_key_exists('id',$_GET)){
    $id = $_GET['id'];
}
else{
    $id = 'home';
}

if(($id == 'display' || $id == 'dump') && !array_key_exists('dataset', $_GET)){
    $id = 'home';
}
elseif($id == 'display' || $id=='dump'){
    $dataset = $_GET['dataset'];
}

include("templates/$id.php");
include('templates/footer.php');
?>
