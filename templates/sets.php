<div class="row">
    <div class="span12">
    <!-- Begin Data Sets Content -->
<h2>Data Sets</h2>
<?php
$results = dbQuery('select * from datasets');
foreach($results as $result){
    echo "<a href=\"?id=display&dataset={$result['id']}\">{$result['name']}</a> <span class=\"muted\">Updated: {$result['updated']}</span><br/>";
}
?>
    <!-- End Data Sets Content -->
    </div>
</div>
