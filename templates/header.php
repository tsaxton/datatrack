<!DOCTYPE html>
<html>
<head>
    <title>Trendable: Data Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!--Style-->
    <link rel="stylesheet" type="text/css" href="includes/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="includes/css/jquery.jqplot.css"/>
    <link rel="stylesheet" type="text/css" href="includes/css/trendableData.css"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"> 

    <!--JavaScript-->
    <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
    <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
    
    <!--JavaScript for Data Analysis-->
    <script language="javascript" type="text/javascript" src="includes/js/jquery.jqplot.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.highlighter.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.cursor.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/observations.js"></script>
	<script language="javascript" type="text/javascript" src="includes/js/datatrack.js"></script>
	<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>

	<!-- Font 
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab|Raleway:400,300' rel='stylesheet' type='text/css'> -->

	<!--<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">-->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> 
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/?id=home">
                    <img src="includes/css/trendable!logo.png" alt="" width = "200px">
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="/?id=import">Import Data</a>
                    </li>
                    <li>
                        <a href="/?id=sets"> Dashboard</a>
                    </li>
                    <li>
                        <a href="/?id=help">Help</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>

<!-- <div class="container-fluid"> -->

<!--Begin main body content-->

