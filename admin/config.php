<?php
error_reporting(-1);
ini_set('display_errors', 'On');

//require_once('includes/meekro.php');

$server = 'us-cdbr-east-04.cleardb.com';
$username = 'bb69d137447097';
$password = '2015136f';
$dbName = 'heroku_51bc4a95169dce1';


$db = mysql_connect($server,$username,$password);
mysql_select_db($dbName);

function dbQuery($sql){
        $results = mysql_query($sql);
        if(!$results){
                return NULL;
        }
        $jk = 0;
        while($row = mysql_fetch_assoc($results)){
                $result[$jk++] = $row;
        }
        return $result;
}

?>
