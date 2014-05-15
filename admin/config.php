<?php
error_reporting(-1);
ini_set('display_errors', 'on');


/*$host = 'localhost';
$username = 'datatrack';
$password = 'journalistdatadashboard';
$dbName   = 'datatrack';*/

$host = 'us-cdbr-east-04.cleardb.com';
$username = 'bb69d137447097';
$password = '2015136f';
$dbName = 'heroku_51bc4a95169dce1';

$db = new MeekroDB($host, $username, $password, $dbName);

?>
