<?php

$data = new data(1);

echo '<pre>';
var_dump($data);
echo '</pre>';

echo $data->makeTable('bus');
echo $data->makeTable('rail');
