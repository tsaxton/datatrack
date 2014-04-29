<?php
include('../includes.php');

session_start();

$fields = $_SESSION['input']->confirmFields();

// TODO: In multiple files, allow them to choose if it is a data listing category, or a data set category
?>
<form id="fieldSubmission" action="templates/confirm.php" method="post">
<p>If you do not wish to include any of the following fields, uncheck them.</p>
<?php
foreach($fields as $field){
	echo "<div class=\"checkbox\"><label for=\"$field-check\"><input type=\"checkbox\" checked name=\"$field\" id=\"$field-check\"> $field</label></div>\n";
}
?>
<p><strong><a href="#" id="bad-fields">None of these are my fields; help me identify fields.</a></strong></p>
<button type="submit" class="btn btn-default">Submit</button>
</form>
