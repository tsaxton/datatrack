<?php

class recentAnalysis{

    private $data;
    private $recent;
    private $previous;
    private $yearData;
    private $prevData;
    private $obs;
    private $vals;
    private $pro;

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
	$this->highlight();
	$this->recordCheck();
	$this->proportion();
	$this->streak();
    }

    public function run(){
	$str = "<ul>\n";
	foreach($this->vals as $o){
	    $str .= "\t<li>$o</li>\n";
	}
	foreach($this->obs as $o){
	    $str .= "\t<li>$o</li>\n";
	}
	$str .= "</ul>\n\n";
	return $str;
    }

    private function highlight(){
	foreach($this->data->fields as $field){
	    // need to use $field['field'] for the name of the field
	    $pct = ($this->yearData[$field['field']] - $this->prevData[$field['field']]) / $this->prevData[$field['field']] * 100; 
	    $str = "<span class='field'>{$field['text']}</span>: " . number_format($this->yearData[$field['field']], 0, '.', ',');
	    if($pct == 0){
		$str .= " (no change from {$this->previous})</li>\n";
	    }
	    elseif($pct > 0){
		$pct = number_format($pct, 2, '.', ',');
		$str .= " <span class='data-increase'>($pct% increase from {$this->previous})</span></li>\n";
	    }
	    else{
		$pct = number_format(-$pct, 2, '.', ',');
		$str .= " ($pct% decrease from {$this->previous})</li>\n";
	    }
	    $this->vals[] = $str;
	}
	return $str;
    }

    private function proportion(){
	global $db;
	$proportions = $db->query("select * from proportions where dataset={$this->data->id}");

	foreach($proportions as $p){
	    $this->pro[$p['id']] = $this->yearData[$this->data->fields[$p['top']]['field']] / $this->yearData[$this->data->fields[$p['bottom']]['field']];
	    $pro = number_format(100*$this->pro[$p['id']], 2, '.', ',');
	    $this->obs[] = "{$p['description']}: $pro%";
	}
    }

    private function streak(){
	$str = '';
	foreach($this->data->fields as $field){
	    if($this->yearData[$field['field']] < 0 && $this->prevData[$field['field']] < 0){
		// continuing a multi-year decrease
		$this->obs[] = "<span class='field'>{$field['text']}</span> has now decreased for " . $this->negStreak($this->recent, $field['field']) . " years in a row.";
	    }
	    elseif($this->yearData[$field['field']] < 0){
		// decrease after increasing
		$this->obs[] = "<span class='field'>{$field['text']}</span> decreased after " . $this->posStreak($this->previous, $field['field']) . " years of increasing.";
	    }
	    elseif($this->yearData[$field['field']] > 0 && $this->prevData[$field['field']] > 0){
		// continuing a multi-year increase
		$this->obs[] = "<span class='field'>{$field['text']}</span> has now increased for " . $this->posStreak($this->recent, $field['field']) . " years in a row.";
	    }
	    elseif($this->yearData[$field['field']] > 0){
		// increase after decreasing
		$this->obs[] = "<span class='field'>{$field['text']}</span> increased after " . $this->negStreak($this->previous, $field['field']) . " years of decreasing.";
	    }
	}
	return $str;
    }

    private function negStreak($year, $field){
	$c = 0;
	while($year > 1900){
	    $d = $this->data->getData($year);
	    $p = $this->data->getData($year-1);
	    if($d == NULL || $p == NULL){
		return $c;
	    }
	    if($d[$field]-$p[$field] > 0){
		return $c;
	    }
	    $c++;
	    $year--;
	}
	return $c;
    }

    private function posStreak($year, $field){
    	$c = 0;
	while($year > 1900){
	    $d = $this->data->getData($year);
	    $p = $this->data->getData($year-1);
	    if($d == NULL || $p == NULL){
		return $c;
	    }
	    if($d[$field]-$p[$field] < 0){
		return $c;
	    }
	    $c++;
	    $year--;
	}
	return $c;
    }

    private function recordCheck(){
	foreach($this->data->fields as $field){
	    if($this->yearData[$field['field']] == $this->data->getMax($field['field'])){
		$this->obs[] = "<span class='field'>{$field['text']}</span> hit a record high!";
	    }
	    elseif($this->yearData[$field['field']] == $this->data->getMin($field['field'])){
		$this->obs[] = "<span class='field'>{$field['text']}</span> hit a record low!";
	    }
	    
	    // see how the raw change matches
	    if($this->yearData[$field['field']] == $this->data->getMaxDiff($field['field'], 1)){
		$this->obs[] = "<span class='field'>{$field['text']}</span> had its largest increase in numbers ever.";
	    }
	    elseif($this->yearData[$field['field']] == $this->data->getMinDiff($field['field'], 1)){
		$this->obs[] = "<span class='field'>{$field['text']}</span> had its largest decrease in numbers ever.";
	    }

	    // see how the percent change matches
	    if($this->yearData[$field['field']] == $this->data->getMaxPct($field['field'], 1)){
		$this->obs[] = "<span class='field'>{$field['text']}</span> had its largest percent increase ever.";
	    }
	    if($this->yearData[$field['field']] == $this->data->getMinPct($field['field'], 1)){
		$this->obs[] = "<span class='field'>{$field['text']}</span> had its largest percent decrease ever.";
	    }

	    // see how the proportions match
	    foreach($this->data->proportions as $p){
		if($this->pro[$p['id']] == $this->data->getMaxProp($p['id'])){
		    $this->obs[] = "<span class='field'>{$p['description']}</span> hit a record high.";
		}
		if($this->pro[$p['id']] == $this->data->getMinProp($p['id'])){
		    $this->obs[] = "<span class='field'>{$p['description']}</span> hit a record low.";
		}
	    }
	    
	    // TODO: if part of a streak, find if is a record streak
	}
    }

}
