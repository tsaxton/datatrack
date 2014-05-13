<?php
include('../includes.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();

if(array_key_exists('files', $_FILES)){
	$_SESSION['csv'] = new multicsv($_FILES['files'], $month, $quarter, $year);
}

if(!array_key_exists('csv', $_SESSION)){
	return "Error!";
}

if(array_key_exists('response', $_POST)){
	echo $_SESSION['csv']->fixProblem($_POST['response']);
}

echo $_SESSION['csv']->continueAnalysis(NULL);

dump($_SESSION['csv']);
