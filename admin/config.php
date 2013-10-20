<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once('includes/meekro.php');

$username = 'datatrack';
$password = 'journalistdatadashboard';
$dbName   = 'datatrack';

$db = new MeekroDB('localhost', $username, $password, $dbName);

?>
