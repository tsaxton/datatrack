<?php

class recentAnalysis{

    private $data;
    private $recent;
    private $previous;
    private $yearData;
    private $prevData;

    public function __construct($data){
	if(is_numeric($data)){
	    $this->data = new data($data);
	}
	else{
	    $this->data = $data;
	}
	$this->recent = $data->mostRecent();
	$this->previous = $data->previous($this->recent);
	$this->yearData = $this->data->getData($this->recent);
	$this->prevData = $this->data->getData($this->previous);
    }

    public function run(){
	$str = "<ul>\n\t";
	$str .= $this->highlight();
	$str .= $this->proportion();
	$str .= "</ul>\n\n";
	return $str;
    }

    private function highlight(){
    // returns string telling the change from the previous data, and the exact value
	$str = '';
	foreach($this->data->fields as $field){
	    // need to use $field['field'] for the name of the field
	    $pct = ($this->yearData[$field['field']] - $this->prevData[$field['field']]) / $this->prevData[$field['field']] * 100; 
	    $str .= "\t<li>{$field['text']}: " . number_format($this->yearData[$field['field']], 0, '.', ',');
	    if($pct == 0){
		$str .= " (no change from {$this->previous})</li>\n";
	    }
	    elseif($pct > 0){
		$pct = number_format($pct, 2, '.', ',');
		$str .= " ($pct% increase from {$this->previous})</li>\n";
	    }
	    else{
		$pct = number_format(-$pct, 2, '.', ',');
		$str .= " ($pct% decrease from {$this->previous})</li>\n";
	    }
	}
	return $str;
    }

    private function proportion(){
	global $db;
	$proportions = $db->query("select * from proportions where dataset={$this->data->id}");

	$str = '';
	foreach($proportions as $p){
	    //dump($this->yearData);
	    $pro = 100 * $this->yearData[$this->data->fields[$p['top']]['field']] / $this->yearData[$this->data->fields[$p['bottom']]['field']];
	    $pro = number_format($pro, 2, '.', ',');
	    $str .= "<li>{$p['description']}: $pro%</li>\n";
	}
	return $str;
    }

    private function streak(){
	$str = '';
	foreach($this->data->fields as $field){
	    if(($this->yearData[$field] > 0 && $this->prevData[$field] < 0) ||
	       ($this->yearData[$field] < 0 && $this->prevData[$field] > 0)){
		$str .= diffStreak();
	    }
	    else{
		$str .= sameStreak();
	    }
	}
	return $str;
    }

    private function diffStreak(){
    }

    private function sameStreak(){
    }

}
