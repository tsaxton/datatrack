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
	$this->calculateStats();
	$this->bigChanges();
    }

    public function run(){
	$str = "<ul class=\"longterm-analysis\">\n";
	foreach($this->obs as $o){
	    $str .= "\t<li>$o</li>\n";
	}
	$str .= "</ul>\n\n";
	return $str;
    }


    private function calculateStats(){
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
}
?>
