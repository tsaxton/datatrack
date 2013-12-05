<?php

class longTerm{
    private $data;
    private $obs = array();
    private $stats = array();

    public function __construct($data){
	if(is_numeric($data)){
	    $this->data = new data($data);
	}
	else{
	    $this->data = $data;
	}
	if(!$this->data->success){
	    return;
	}

	$this->calculateStats();
	$this->bigChanges();
	$this->pieceFit();
	$this->bestFit();
    }

    public function run(){
	if(!$this->data->success){
	    return;
	}

	$str = "<ul class=\"longterm-analysis\">\n";
	foreach($this->obs as $o){
	    $str .= "\t<li>$o</li>\n";
	}
	$str .= "</ul>\n\n";
	return $str;
    }


    private function calculateStats(){
	if(!$this->data->success){
	    return;
	}

	$i = 0;
	foreach($this->data->fields as $field){
	    $this->stats[$i]['Title'] = $field['text'];
	    $this->stats[$i]['Average'] = $this->data->averages($field['field']);
	    $this->stats[$i]['Standard Deviation'] = $this->data->std($field['field']);
	    $this->stats[$i]['Median'] = $this->data->median($field['field']);
	    $this->stats[$i]['Max'] = $this->data->getMax($field['field']);
	    $this->stats[$i]['Min'] = $this->data->getMin($field['field']);
	    $this->stats[$i]['Average Change']['Raw'] = $this->data->getAvgDiff($field['field'], 1);
	    $this->stats[$i]['Average Change']['Percent'] = 100*$this->data->getAvgPct($field['field'], 1);
	    $i++;
	}
    }

    public function statistics(){
	if(!$this->data->success){
	    return;
	}

	$ret = '';
	foreach($this->stats as $stat){
	    $ret .= "<h4>{$stat['Title']}</h4>";
	    unset($stat['Title']);
	    foreach($stat as $key=>$s){
		if(is_float($s)){
		    $ret .= "$key: " . number_format($s, 2, '.', ',') . "<br/>\n";
		}
		else if(is_array($s)){
		    $ret .= "$key:<br/>\n";
		    foreach($s as $k=>$val){
			$ret .= "&nbsp;&nbsp;&nbsp;&nbsp;$k: ";
			if(is_float($val)){
			    $ret .= number_format($val, 2, '.', ',') . "<br/>\n";
			}
			else{
			    $ret .= number_format($val, 0, '.', ',') . "<br/>\n";
			}
		    }
		}
		else{
		    $ret .= "$key: " . number_format($s, 0, '.', ',') . "<br/>\n";
		}
	    }
	}
	return $ret;
    }

    public function bigChanges(){
	if(!$this->data->success){
	    return;
	}

	$comparisons = [5, 2, 1, .75, .5];
	# this isn't pretty...
	foreach($this->data->fields as $field){
	    foreach($this->data->pct[$field['field']] as $offset=>$years){
		foreach($years as $year=>$data){
		    foreach($comparisons as $c){
			if(abs($data) > $c){
			    $str = "{$field['text']} ";
			    $str .= $data<0 ? 'decreased ' : 'increased ';
			    $str .= "by more than ";
			    $str .= $c*100;
			    $str .= "% from ";
			    $str .= $year-$offset . "-" . $year;
			    $this->obs[] = $str;
			    break;
			}
		    }
		}
	    }
	}
    }

    public function longStreak(){
	if(!$this->data->success){
	    return;
	}

	$ret = $this->data->longStreaks();
	$vals = $ret[0];
	$years = $ret[1];
	foreach($vals as $text=>$types){
	    echo "<h4>$text</h4>\n";
	    foreach($types as $type=>$val){
		echo "Longest $type: $val years\n";
		$i=0;
		echo "<ul>\n";
		foreach($years[$text][$type] as $y){
		    $startYear = $y - $val;
		    echo "\t<li>$startYear-$y</li>\n";
		}
		echo "</ul>\n";
	    }
	}
    }

    private function bestFit(){
	if(!$this->data->success){
	    return;
	}

	$years = $this->data->getYears();
	foreach($this->data->fields as $field){
	    $data = $this->data->extractData($field['field']);
	    $b = slope($years, $data);
	    if($b > 0){
		$this->obs[] = "<span class='field'>{$field['text']}</span> has been trending upward at rate of " . number_format($b, 2, '.', ',') . ".";
	    }
	    else{
		$this->obs[] = "<span class='field'>{$field['text']}</span> has been trending downward at rate of " . number_format($b, 2, '.', ',') . ".";
	    }
	}
    }

    private function pieceFit(){
	if(!$this->data->success){
	    return;
	}

	$years = $this->data->getYears();
	$minYear = min($years);
	$maxYear = max($years);
	foreach($this->data->fields as $field){
	    $best = PHP_INT_MAX;
	    $bestA1 = NULL;
	    $bestA2 = NULL;
	    $bestB1 = NULL;
	    $bestB2 = NULL;
	    $i = 4;
	    $data = $this->data->extractData($field['field']);
	    if($i < count($years)-4){
		continue;
	    }
	    while($i < count($years)-4){
		$years1 = array_slice($years,0,$i);
		$years2 = array_slice($years,$i+1);
		$data1 = array_slice($data,0,$i);
		$data2 = array_slice($data,$i+1);
		$a1 = intercept($years1, $data1);
		$a2 = intercept($years2, $data2);
		$b1 = slope($years1, $data1);
		$b2 = slope($years2, $data2);
		$residuals = residuals($a1, $b1, $years1, $data1) + residuals($a2, $b2, $years2, $data2);
		if($residuals < $best){
		    $best = $residuals;
		    $bestA1 = $a1;
		    $bestA2 = $a2;
		    $bestB1 = $b1;
		    $bestB2 = $b2;
		    $bestI = $i;
		}
		$i++;
	    }
	    if(!($bestB1 && $bestB2 && $bestA1 && $bestA2)){
		continue;
	    }
	    if($bestB1 < 0 && $bestB2 > 0){
		$this->obs[] = "After trending downward from $minYear-" . ($minYear+$bestI-1) . ", <span class='field'>{$field['text']}</span> trended upward.";
	    }
	    elseif($bestB1 > 0 && $bestB2 < 0){
		$this->obs[] = "After trending upward from $minYear-" . ($minYear+$bestI-1) . ", <span class='field'>{$field['text']}</span> trended downward.";
	    }
	    elseif($bestB2 < 0){
		if($bestB2 > $bestB1){
		    $this->obs[] = "The downward trend slowed from " . ($minYear+$bestI) . "-$maxYear compared to its rate from $minYear-" . ($minYear+$bestI-1) . ".";
		}
		else{
		    $this->obs[] = "The downward trend sped up from " . ($minYear+$bestI) . "-$maxYear compared to its rate from $minYear-" . ($minYear+$bestI-1) . ".";
		}
	    }
	    else{
		if($bestB2 > $bestB1){
		    $this->obs[] = "The upward trend sped up from " . $minYear+$bestI . "-$maxYear compared to its rate from $minYear-" . $minYear+$bestI-1 . ".";
		}
		else{
		    $this->obs[] = "The upward trend slowed from " . $minYear+$bestI . "-$maxYear compared to its rate from $minYear-" . $minYear+$bestI-1 . ".";
		}
	    }
	}
    }
}
?>
