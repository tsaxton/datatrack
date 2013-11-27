<?php
function avg($arr){
    return array_sum($arr)/count($arr);
}

function std($arr){
    $sum = 0;
    $m = avg($arr);
    foreach($arr as $a){
	$sum += pow(($a-$m),2);
    }
    return sqrt($sum / count($arr));
}

function xy($x, $y){
    if(count($x) != count($y)){
	return NULL;
    }
    $z = array();
    foreach($x as $i=>$j){
	$z[i] = $j * $y[i];
    }
    return $z;
}

function rvalue($x, $y){
    if(count($x) != count($y)){
	return NULL;
    }
    $n = count($x);
    $r = (n*array_sum(xy($x,$y)) - array_sum($x)*array_sum($y))/sqrt((N*array_sum(xy($x,$x)) - pow(array_sum($x),2)) * pow(N*array_sum($y),2) - pow(array_num($y),2));
    return $r;
}

function slope($x, $y){
    return rvalue($x, $y) * std($y) / std($x);
}

function intercept($x, $y){
    return avg($y) - slope($x,$y)*avg($x)
}
