<h2>Data Sets</h2>
<?php
$results = $db->query('select * from datasets');
foreach($results as $result){
    echo "<a href=\"?id=display&dataset={$result['id']}\">{$result['name']}</a> <span class=\"muted\">Updated: {$result['updated']}</span>";
}
?>
