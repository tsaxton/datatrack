<?php

class recentAnalysis{

    private $data;
    private $recent;
    private $previous;
    private $yearData;
    private $prevData;
    public $obs;
    private $vals;
    private $pro;

    public function __construct($data){
	if(is_numeric($data)){
	    $this->data = new data($data);
	}
	else{
	    $this->data = $data;
	}
	$this->recent = $this->data->mostRecent();
	$this->previous = $this->data->previous($this->recent);
	$this->yearData = $this->data->getData($this->recent);
	$this->prevData = $this->data->getData($this->previous);
	$this->highlight();
	$this->recordCheck();
	$this->proportion();
	$this->streak();
    }

    public function run(){
	$str = "<ul class=\"recent-analysis\">\n";
	if(is_array($this->vals)){
	    foreach($this->vals as $o){
		$str .= "\t<li>$o</li>\n";
	    }
	}
	foreach($this->obs as $o){
	    $str .= "\t<li>$o</li>\n";
	}
	$str .= "</ul>\n\n";
	return $str;
    }

    public function keyObs(){
	return $this->obs[0];
    }

    private function highlight(){
	$str = '';
	foreach($this->data->fields as $field){
	    // need to use $field['field'] for the name of the field
	    if(!is_array($this->yearData) || !is_array($this->prevData)){
		continue;
	    }
	    if(!(array_key_exists($field['field'], $this->yearData) && array_key_exists($field['field'], $this->prevData))){
		continue;
	    }
	    if($this->prevData[$field['field']] == 0){
		continue;
	    }
	    $pct = ($this->yearData[$field['field']] - $this->prevData[$field['field']]) / $this->prevData[$field['field']] * 100; 
	    $str = "<span class='field'>{$field['text']}</span>: " . number_format($this->yearData[$field['field']], 0, '.', ',');
	    if($pct == 0){
		$str .= " (no change from {$this->previous})</li>\n";
	    }
	    elseif($pct > 0){
		$pct = number_format($pct, 2, '.', ',');
		$str .= " <span class=\"data-increase\">$pct% increase from {$this->previous}</span></li>\n";
	    }
	    elseif($pct < 0){
		$pct = number_format(-$pct, 2, '.', ',');
		$str .= " <span class=\"data-decrease\">$pct% decrease from {$this->previous}</span></li>\n";
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
	    $direction = $this->data->streakDirection($this->recent, $field['field']);
		if($direction == 1){
		// continuing a multi-year decrease
		$this->obs[] = "<span class='field'>{$field['text']}</span> has now decreased for " . $this->data->negStreak($this->recent, $field['field']) . " years in a row.";
	    }
	    elseif($direction == 2){
		// decrease after increasing
		$this->obs[] = "<span class='field'>{$field['text']}</span> decreased after " . $this->data->posStreak($this->previous, $field['field']) . " years of increasing.";
	    }
	    elseif($direction == 3){
		// continuing a multi-year increase
		$this->obs[] = "<span class='field'>{$field['text']}</span> has now increased for " . $this->data->posStreak($this->recent, $field['field']) . " years in a row.";
	    }
	    elseif($direction == 4){
		// increase after decreasing
		$this->obs[] = "<span class='field'>{$field['text']}</span> increased after " . $this->data->negStreak($this->previous, $field['field']) . " years of decreasing.";
	    }
	}
	return $str;
    }

    private function recordCheck(){
	foreach($this->data->fields as $field){
	    if(!array_key_exists($field['field'], $this->yearData)){
		continue;
	    }
	    if($this->yearData[$field['field']] == $this->data->getMax($field['field'])){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> hit a record high!</span>";
	    }
	    elseif($this->yearData[$field['field']] == $this->data->getMin($field['field'])){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> hit a record low!</span>";
	    }
	    
	    // see how the raw change matches
	    if($this->yearData[$field['field']] == $this->data->getMaxDiff($field['field'], 1)){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest increase in numbers ever.</span>";
	    }
	    elseif($this->yearData[$field['field']] == $this->data->getMinDiff($field['field'], 1)){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest decrease in numbers ever.</span>";
	    }

	    // see how the percent change matches
	    if($this->yearData[$field['field']] == $this->data->getMaxPct($field['field'], 1)){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent increase ever.</span>";
	    }
	    if($this->yearData[$field['field']] == $this->data->getMinPct($field['field'], 1)){
		$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent decrease ever.</span>";
	    }

	    // see how the proportions match
	    foreach($this->data->proportions as $p){
		if($this->pro[$p['id']] == $this->data->getMaxProp($p['id'])){
		    $this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record high.</span>";
		}
		if($this->pro[$p['id']] == $this->data->getMinProp($p['id'])){
		    $this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record low.</span>";
		}
	    }
	    
	    // TODO: if part of a streak, find if is a record streak
	}
    }

    public function getCategories(){
	$str = '';
	foreach($this->data->categories as $c){
	    $str .= "{$c['category']} ";
	}
	return $str;
    }

    public function getId(){
	return $this->data->id;
    }

}
