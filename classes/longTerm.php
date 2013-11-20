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
		if(is_array($s)){
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
    }

    public function longStreak(){
    }
}
?>
