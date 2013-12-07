<?php

class data{

    // General Fields
    public $id;
    private $name;
    private $updated;
    private $api;
    private $type;
    private $selects;
    private $groups;
    public $offsetYear;
    public $fields;
    public $allFields;
    public $categories;
    public $success;

    // Data
    public $figures;
    public $proportions;

    // Analysis
    public $diffs;
    public $pct;
    public $proportionData = array();

    public function __construct($id){
	$this->id = $id;
	$this->success = TRUE;
	$this->initialize();
	$this->offsetYear = array(1, 2, 5, 10, 25, 50, 100);
    }

    public function initialize(){
	if(!$this->id){
	    throw new Exception('Data object was improperly initialized.');
	}

	global $db;
	$results = $db->query('select * from datasets where id='.$this->id);

	if(count($results) != 1){
	    throw new Exception('Too many entries with given id.');
	}

	$this->name = $results[0]['name'];
	$this->updated = $results[0]['updated'];
	$this->api = $results[0]['api'];
	$this->selects = $results[0]['selects'];
	$this->groups = $results[0]['groups'];

	$fields = $db->query('select * from fields where dataset='.$this->id.' and major=1');
	$this->fields = array();
	foreach($fields as $field){
	    $this->fields[$field['id']] = $field;
	}

	$fields = $db->query('select * from fields where dataset='.$this->id);
	$this->allFields = array();
	foreach($fields as $field){
	    $this->allFields[$field['id']] = $field;
	}

	$this->categories = $db->query('select * from categories where dataset='.$this->id);

	$this->proportions = $db->query("select * from proportions where dataset={$this->id}");

	$result = $this->collectData();
	if(!$result){
	    $this->success = FALSE;
	}
	$this->calculateDiffs();
	$this->calculateProportions();
    }

    public function collectData(){
	global $db;

	$url = "http://data.cityofchicago.org/resource/{$this->api}.json";
	if($this->selects && $this->groups){
	    $url .= "?\$select={$this->selects}&\$group={$this->groups}";
	}
	elseif($this->selects){
	    $url .= "?\$select={$this->selects}";
	}
	elseif($this->groups){
	    $url .= "?\$group={$this->groups}";
	}
	$json = file_get_contents($url);
	if($json){
	    $json = json_decode($json, true);
	    if(count($json) < 10){
		return 0;
	    }
	    $this->figures = $json;
	}
	else{
	    return 0;
	}

	if($this->selects || $this->groups){
	    $this->sortByYear();
	}
	else{
	    $this->sortData();
	}
	return 1;

    }

    public function sortByYear(){
	if(!$this->success){
	    return;
	}

	if(!$this->figures){
	    $this->collectData();
	}
	$in = $this->figures;
	$out = array();
	global $db;
	$results = $db->query("select * from foldSort where dataset={$this->id}");
	$key = $results[0]['keyfield'];
	$value = $results[0]['valuefield'];
	foreach($in as $row){
	    $year = $row['year'];
	    unset($row['year']);
	    if(!array_key_exists($year, $out)){
		$out[$year] = array();
	    }
	    $out[$year][$row[$key]] = $row[$value];
	}
	foreach($out as $year=>$values){
	    foreach($this->allFields as $field){
		if(!array_key_exists($field['field'], $values)){
		    $out[$year][$field['field']] = 0;
		}
	    }
	}
	$this->figures = $out;
    }

    public function sortData(){
	if(!$this->success){
	    return;
	}

if(!$this->figures){
	    $this->collectData();
	}
	if(!array_key_exists(0,$this->figures)){
	    // array already sorted
	    return;
	}
	$in = $this->figures;
	$out = array();
	foreach($in as $row){
	    $year = $row['year'];
	    unset($row['year']);
	    if(!array_key_exists($year, $out)){
		$out[$year] = array();
	    }
	    //$out[$year] = $row;
	    $out[$year] = array_merge($out[$year], $row);
	}
	$this->figures = $out;
    }

    public function makeTable($field){
	if(!$this->success){
	    return;
	}

// set up table header
	$ret = "<table class=\"data\" id=\"$field\">\n\t<tr>\n\t\t<th>Year</th>\n\t\t<th>".ucfirst($field)."</th>\n\t";
	$years = count($this->figures); // number of years of data
	if(array_key_exists(date("Y"), $this->figures)){
	    $years--;
	}
	foreach($this->offsetYear as $o){
	    if($o < $years){
		$ret .= "\t<th class=\"noright\">$o yr. change</th>\n\t\t<th class=\"noleft\">(% change)</th>\n\t";
	    }
	}
	$year = date("Y")-1;
	for($i=0; $i < $years; $i++){
	    $max = $this->getMax($field);
	    $min = $this->getMin($field);
	    $ret .= "<tr>\n\t\t<th>$year</th>\n\t\t<td";
	    $val = $this->figures[$year][$field];
	    if($val == $max){
		$ret .= " class=\"max\"";
	    }
	    elseif($val == $min){
		$ret .= " class=\"min\"";
	    }
	    $ret .= ">". number_format($val,0,'.', ',') . "</td>\n\t";
	    foreach($this->offsetYear as $o){
		if($o >= $years){
		    continue;
		}
		if(!array_key_exists($year,$this->diffs[$field][$o])){
		    continue;
		}
		if($this->diffs[$field][$o][$year]){
		    $max = $this->getMaxDiff($field,$o);
		    $min = $this->getMinDiff($field,$o);
		    $max2 = $this->getMaxPct($field,$o);
		    $min2 = $this->getMinPct($field,$o);
		    $diff = $this->diffs[$field][$o][$year];
		    $pct = $this->pct[$field][$o][$year];
		    $class = "noright";
		    $class2 = "noleft";
		    if($diff == $max){
			$class .= " max";
		    }
		    elseif($diff == $min){
			$class .= " min";
		    }
		    if($pct == $max2){
			$class2 .= " max";
		    }
		    elseif($pct == $min2){
			$class2 .= " min";
		    }
		    $ret .= "\t<td class=\"$class\">" . number_format($diff, 0, '.', ',') . "</td>\n\t\t<td class=\"$class2\">";
		    $ret .= number_format(100 * $pct, 2);
		    $ret .= "</td>\n\t";
		}
		else{
		    $ret .= "\t<td class=\"noright\">-</th>\n\t\t<td class=\"noleft\">-</td>\n\t";
		}
	    }
	    $year--;
	}
	$ret .= "</tr>\n";
	$ret .= "</table>";
	return $ret;
    }

    public function areProportions(){
	if(!$this->success){
	    return;
	}

	return count($this->proportions);
    }
    
    public function tableProp(){
	if(!$this->success){
	    return;
	}

$ret = "<table class=\"data\" id=\"proportions\">\n\t<tr>\n\t\t<th>Year</th>\t";
	foreach($this->proportions as $p){
	    $ret .= "\t\t<th>{$p['description']}</th>\n";
	}
	$ret .= "\t</tr>\n";
	foreach($this->figures as $year=>$data){
	    $ret .= "\t<tr>\n\t\t<th>$year</th>\n";
	    foreach($this->proportions as $p){
		$prop = $this->proportionData[$p['id']][$year];
		$class = "";
		if($prop == $this->getMaxProp($p['id'])){
		    $class = "class=\"max\"";
		}
		elseif($prop == $this->getMinProp($p['id'])){
		    $class = "class=\"min\"";
		}
		$ret .= "\t\t<td $class>" . number_format(100*$prop, 2, '.', ',') . "%</td>\n";
	    }
	    $ret .= "\t</tr>\n";
	}
	$ret .= "</table>\n";
	return $ret;
    }

    public function getName(){
	if(!$this->success){
	    return;
	}

	return $this->name;
    }

    public function makeJSON($field){
	if(!$this->success){
	    return;
	}

	$ret = array();
	foreach($this->figures as $year=>$data){
	    $ret[$year] = $data[$field];
	}
	return json_encode($ret);
    }

    public function calculateDiffs(){
	if(!$this->success){
	    return;
	}

	if(!$this->figures){
	    $this->initialize;
	}
	$ret = array();
	$pct = array();
	foreach($this->figures as $year=>$vals){
	    foreach($vals as $field=>$v){
		foreach($this->offsetYear as $o){
		    if(array_key_exists($year-$o,$this->figures)){
			if(array_key_exists($field,$this->figures[$year-$o])){
			    $w = $this->figures[$year-$o][$field];
			}
			else{
			    $w = 0;
			}
			$ret[$field][$o][$year] = $v - $w;
			if($w == 0){
			    $pct[$field][$o][$year] = NULL;
			}
			else{
			    $pct[$field][$o][$year] = ($v - $w)/$w;
			}
		    }
		    else{
			$ret[$field][$o][$year] = NULL;
			$pct[$field][$o][$year] = NULL;
		    }
		}
	    }
	}
	$this->diffs = $ret;
	$this->pct = $pct;
    }

    public function calculateProportions(){
	if(!$this->success){
	    return;
	}

	foreach($this->proportions as $p){
	    foreach($this->figures as $year=>$vals){
		$pro = $vals[$this->fields[$p['top']]['field']] / $vals[$this->fields[$p['bottom']]['field']];
		$this->proportionData[$p['id']][$year] = $pro;
	    }
	}
    }

    public function mostRecent(){
	if(!$this->success){
	    return;
	}

	if(!$this->figures){
	    $this->initialize;
	}
	for($year = date('Y')-1; $year > 1900; $year--){
	    if(array_key_exists($year, $this->figures)){
		return $year;
	    }
	}
	return 0;
    }

    public function minYear(){
	if(!$this->success){
	    return;
	}

	if(!$this->figures){
	    $this->initialize;
	}
	for($year = $this->mostRecent(); $year > 1900; $year--){
	    if(!$this->yearExists($year)){
		return $year+1;
	    }
	}
    }

    public function previous($year = NULL){
	if(!$this->success){
	    return;
	}

	if($year == NULL){
	    $year = $this->mostRecent();
	}
	for($year--; $year > 1900; $year--){
	    if(array_key_exists($year, $this->figures)){
		return $year;
	    }
	}
	return 0;
    }

    public function getData($year){
	if(!$this->success){
	    return;
	}

	if($this->yearExists($year)){
	    return $this->figures[$year];
	}
	return NULL;
    }

    public function yearExists($year){
	if(!$this->success){
	    return;
	}

	return array_key_exists($year, $this->figures);
    }

    public function getMax($field){
	if(!$this->success){
	    return;
	}

	return max($this->extractData($field));
    }

    public function getMin($field){
	if(!$this->success){
	    return;
	}

	return min($this->extractData($field));
    }

    public function extractData($field){
	if(!$this->success){
	    return;
	}

	// This thing is by no means efficient. In a clean implementation,
	// I think that all of the data should be converted to a better data
	// structure for doing things like this.
	$ret = array();
	foreach($this->figures as $figure){
	    $ret[] = $figure[$field];
	}
	return $ret;
    }

    public function getYears(){
	if(!$this->success){
	    return;
	}

	return array_keys($this->figures);
    }

    public function longStreaks(){
    	if(!$this->success){
	    return;
	}

	$baseYear = $this->mostRecent();
	foreach($this->fields as $field){
	    $year = $baseYear;
	    $max = 0;
	    $min = 0;
	    $maxYear = array();
	    $minYear = array();
	    // This is the lazy slow way to do it
	    while($year > $this->minYear()){
		$neg = $this->negStreak($year, $field['field']);
		$pos = $this->posStreak($year, $field['field']);
		if($pos > $max){
		    unset($maxYear);
		    $maxYear = array();
		    $max = $pos;
		    $maxYear[] = $year;
		}
		elseif($pos == $max){
		    $maxYear[] = $year;
		}
		if($neg > $min){
		    unset($minYear);
		    $minYear = array();
		    $min = $neg;
		    $minYear[] = $year;
		}
		elseif($neg == $min){
		    $minYear[] = $year;
		}
		$year -= max($pos, $neg);
	    }
	    $vals[$field['text']]['increase'] = $max;
	    $vals[$field['text']]['decrease'] = $min;
	    $years[$field['text']]['increase'] = $maxYear;
	    $years[$field['text']]['decrease'] = $minYear;
	}
	$ret[0] = $vals;
	$ret[1] = $years;
	return $ret;
    }

    public function getMaxDiff($field, $time){
	if(!$this->success){
	    return;
	}

	return max($this->diffs[$field][$time]);
    }

    public function getMinDiff($field, $time){
	if(!$this->success){
	    return;
	}

	return min(array_diff($this->diffs[$field][$time], array(null, 0)));
    }

    public function getMaxPct($field, $time){
	if(!$this->success){
	    return;
	}

	return max($this->pct[$field][$time]);
    }

    public function getMinPct($field, $time){
	if(!$this->success){
	    return;
	}

	$temp = array_diff($this->pct[$field][$time], array(null, 0));
	if(count($temp) < 1){ return 0; }
	return min($temp);
    }

    public function getMaxProp($prop){
	if(!$this->success){
	    return;
	}

	return max($this->proportionData[$prop]);
    }

    public function getMinProp($prop){
	if(!$this->success){
	    return;
	}

	return min(array_diff($this->proportionData[$prop], array(null, 0)));
    }

    public function averages($field){
	if(!$this->success){
	    return;
	}

	/* averages($field)
	   INPUT: string or array
	    string - will return the average of the field identified in the string
	    array  - will return the average value of the array
	*/
	if(is_string($field)){
	    $data = $this->extractData($field);
	}
	elseif(is_array($field)){
	    $data = $field;
	}
	else{
	    return NULL;
	}
	return array_sum($data)/count($data);
    }

    public function std($field){
	if(!$this->success){
	    return;
	}

	$data = $this->extractData($field);
	$m = $this->averages($data);
	$sum = 0;
	foreach($data as $d){
	    $sum += pow(($d - $m),2);
	}
	return sqrt($sum / count($data));
    }

    public function median($field){
	if(!$this->success){
	    return;
	}

	$data = $this->extractData($field);
	sort($data);
	$middle = round(count($data) / 2);
	return $data[$middle-1];
    }

    public function getAvgDiff($field, $time){
	if(!$this->success){
	    return;
	}

	return $this->averages($this->diffs[$field][$time]);
    }

    public function getAvgPct($field, $time){
	if(!$this->success){
	    return;
	}

	return $this->averages($this->pct[$field][$time]);
    }

    public function streakDirection($year, $field){
	if(!$this->success){
	    return;
	}

	$current = $this->getData($year);
	$prev = $this->getData($year-1);
	$prior = $this->getData($year-2);
	if($current == NULL || $prev == NULL || $prior == NULL){
	    return 0;
	}
	$thisChange = $current[$field] - $prev[$field];
	$prevChange = $prev[$field] - $prior[$field];
	// need to figure out what to do about 0 change cases besides returning error
	if($thisChange == 0 || $prevChange == 0){
	    return 0;
	}
	// continuing a multi-year decrease
	if($thisChange < 0 && $prevChange < 0){
	    return 1;
	}
	// decrease after increasing
	if($thisChange < 0){
	    return 2;
	}
	// continuing a multi-year increase
	if($thisChange > 0 && $prevChange > 0){
	    return 3;
	}
	// increase after decreasing
	if($thisChange > 0){
	    return 4;
	}
	// error
	return 0;
    }

    public function negStreak($year, $field){
	if(!$this->success){
	    return;
	}

	$c = 0;
	while($year > 1900){
	    $d = $this->getData($year);
	    $p = $this->getData($year-1);
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

    public function posStreak($year, $field){
    	if(!$this->success){
	    return;
	}

	$c = 0;
	while($year > 1900){
	    $d = $this->getData($year);
	    $p = $this->getData($year-1);
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


}
