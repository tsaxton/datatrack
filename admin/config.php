<?php
error_reporting(-1);
ini_set('display_errors', 'on');


$host = 'localhost';
$username = 'datatrack';
$password = 'journalistdatadashboard';
$dbName   = 'datatrack';

$db = new MeekroDB($host, $username, $password, $dbName);

?>
