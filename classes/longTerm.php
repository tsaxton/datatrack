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
