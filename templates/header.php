<?php
function dump($var, $label=NULL){
    echo "<pre>";
    if($label){
	echo "$label: ";
    }
    var_dump($var);
    echo "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DataTrack:Chicago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!--Style-->
    <link href="includes/css/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link rel="stylesheet" type="text/css" href="includes/css/jquery.jqplot.css"/>
    <link rel="stylesheet" type="text/css" href="includes/css/datatrack.css"/>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab|Raleway:400,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

    <!--JavaScript-->
    <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/jquery.jqplot.min.js"></script>
<<<<<<< HEAD
    <script language="javascript" type="text/javascript" src="includes/js/jquery-ui-1.10.3.custom.min.js"></script>
    <!--<script language="javascript" type="text/javascript" src="includes/js/bootstrap.min.js"></script>-->
=======
    <script language="javascript" type="text/javascript" src="includes/js/bootstrap.min.js"></script>
>>>>>>> 34ef99ec01b941d331bcec767d4f2add30b31141
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.highlighter.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.cursor.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/jquery.collapse.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/observations.js"></script>

</head>
<body>
<div id="header">
	<h1 id="title">DataTrack <span id="titleRed">CHICAGO</span></h1>
</div>
<div id="menu">
	<ul id="menuList">
		<li class="menuItem"><a href="#">DASHBOARD</a></li>
		<li class="menuItem"><a href="#">DATA SOURCES</a></li>
		<li class="menuItem"><a href="#">ABOUT</a></li>
		<li class="menuItem"><a href="#">HELP</a></li>
	</ul>
</div>

<div class="container-fluid">
    <div class="row-fluid">
	<div class="span6">
	</div>
	<div class="span6">
	</div>
    </div>

<!--Begin main body content-->

