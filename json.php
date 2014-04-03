<?php

require('classes/data.php');
require('admin/config.php');

if(array_key_exists('id',$_GET)){
    $id = $_GET['id'];
}
else{
    echo '{}';
    die();
}
$d = new data($id);

if(array_key_exists('field',$_GET)){
    $field = $_GET['field'];
}
else{
    echo '{}';
    die();
}
$var = $d->makeJSON($field);
echo $var;

?>
