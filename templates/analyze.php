<div class = "importer">
<div class = "container">
      <div class = "row">
          <div class = "col-lg-4">
          		<div class = "grayBox"><h1> 1. Upload Data</h1></div>
          </div>
          <div class = "col-lg-4">
             <div class = "purpleBox"><h1> 2. Check Data</h1></div>
          </div>
          <div class = "col-lg-4">
              <div class = "grayBox"><h1> 3. Analyze</h1></div>
          </div>
      </div>
      <div class = "space"></div>
<?php
//include('../includes.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(array_key_exists('files', $_FILES)){
	$month = $_POST['months'];
	$quarter = $_POST['quarters'];
	$year = $_POST['year'];
	$type = $_POST['timeframe'];
	$title = $_POST['name'];
	$_SESSION['csv'] = new multicsv($_FILES['files'], $month, $quarter, $year, $type, $title);
}

if(!array_key_exists('csv', $_SESSION)){
	return "Error!";
}

if(array_key_exists('response', $_POST)){
	echo $_SESSION['csv']->fixProblem($_POST['response']);
}

echo $_SESSION['csv']->continueAnalysis(NULL);

//dump($_SESSION['csv']);
