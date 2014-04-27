<?php
include('../includes.php');

session_start();

//dump($_POST);

$csv = new csvparse($_POST['file'], $_POST['timeframe']);
$_SESSION['input'] = $csv;

$dates = $_SESSION['input']->confirmDates();

?>
<p>Are these the <strong>starting dates</strong> for your data sets?</p>
<table class="table table-striped">
<?php
foreach($dates as $date){
	echo "\t<tr>\n\t\t<td>$date</td>\n\t</tr>\n";
}
?>
</table>

<!--<div class="row">
<form action="templates/data-attributes.php" method="post" id="date-yes" class="col-md-1" role="form">
	<input type="hidden" value="1" name="okay"/>
	<button type="submit" class="btn btn-success">Yes</button>
</form>

<form action="templates/date-wizard.php" method="post" id="date-no" class="col-md-1" role="form">
	<input type="hidden" value="1" name="okay"/>
	<button type="submit" class="btn btn-danger">No</button>
</form>
</div>-->

<form class="form form-inline" id="confirm-date" action="templates/data-attributes.php" method="POST">
<button type="submit" class="btn btn-success" id="good-date">Yes</button>
</form>
<form class="form form-inline" id="reject-date" action="templates/date-wizard.php" method="POST">
<button type="submit" class="btn btn-danger" id="bad-date">No</button>
</form>
