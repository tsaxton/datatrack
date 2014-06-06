<div class = "container wizard">
<div class="row">
    <div class="span12">
    <!-- Begin Data Sets Content -->
<h2>Data Sets</h2>
</div>
<?php
$results = $db->query('select * from datasets');
foreach($results as $result){
    echo "<a href=\"?id=display&dataset={$result['id']}\">{$result['name']}</a><br/>";
}
?>
    <!-- End Data Sets Content -->
    </div>
</div>
