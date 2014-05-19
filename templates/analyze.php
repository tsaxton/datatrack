<?php
//include('../includes.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(array_key_exists('files', $_FILES)){
	$month = $_POST['months'];
	$quarter = $_POST['quarters'];
	$year = $_POST['year'];
	$type = $_POST['timeframe'];
	$_SESSION['csv'] = new multicsv($_FILES['files'], $month, $quarter, $year, $type);
}

if(!array_key_exists('csv', $_SESSION)){
	return "Error!";
}

if(array_key_exists('response', $_POST)){
	echo $_SESSION['csv']->fixProblem($_POST['response']);
}

echo $_SESSION['csv']->continueAnalysis(NULL);

//dump($_SESSION['csv']);
