<?php

class data{

    // General Fields
    public $id;
    private $name;
    private $updated;
    private $api;
    private $type;
    private $offsetYear = [1, 2, 5, 10, 25, 50, 100];
    public $fields;

    // Data
    private $figures;
    public $proportions;

    // Analysis
    public $diffs;
    public $pct;
    public $proportionData = array();

    public function __construct($id){
	$this->id = $id;
	$this->initialize();
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

	$fields = $db->query('select * from fields where dataset='.$this->id);
	$this->fields = array();
	foreach($fields as $field){
	    $this->fields[$field['id']] = $field;
	}
	$this->proportions = $db->query("select * from proportions where dataset={$this->id}");

	$this->collectData();
	$this->calculateDiffs();
	$this->calculateProportions();
    }

    public function collectData(){
	global $db;

	$json = file_get_contents("http://data.cityofchicago.org/resource/{$this->api}.json");
	if($json){
	    $json = json_decode($json, true);
	    $this->figures = $json;
	}
	else{
	    die();
	}

	$this->sortData();

    }

    public function sortData(){
	if(!$this->figures){
	    $this->collectData();
	}
	if(!array_key_exists(0,$this->figures)){
	    // array already sorted
	    return;
	}

	if(array_key_exists('day', $this->figures[0])){
	    $this->type = 'day';
	    $this->sortDaily();
	}
	elseif(array_key_exists('month', $this->figures[0])){
	    $this->type = 'month';
	    $this->sortMonthly();
	}
	else{
	    $this->type = 'year';
	    $this->sortYearly();
	}
    }

    private function sortDaily(){
	$in = $this->figures;
	$out = array();

	foreach($in as $row){
	    $day = $row['day'];
	    $month = $row['month'];
	    $year = $row['year'];
	    unset($row['day']);
	    unset($row['month']);
	    unset($row['year']);
	    $out[$year][$month][$day] = $row;
	}

	$this->figures = $out;
	return;
    }

    private function sortMonthly(){
	$in = $this->figures;
	$out = array();

	foreach($in as $row){
	    $month = $row['month'];
	    $year = $row['year'];
	    unset($row['month']);
	    unset($row['year']);
	    $out[$year][$month] = $row;
	}

	$this->figures = $out;
	return;
    }

    private function sortYearly(){
	$in = $this->figures;
	$out = array();
	foreach($in as $row){
	    $year = $row['year'];
	    unset($row['year']);
	    $out[$year] = $row;
	}
	$this->figures = $out;
	return;
    }

    public function makeTable($field){
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->tableYear($field);
	}
    }

    private function tableYear($field = NULL){
	// set up table header
	$ret = "<table class=\"data\" id=\"$field\">\n\t<tr>\n\t\t<th>Year</th>\n\t\t<th>".ucfirst($field)."</th>\n\t";
	$years = count($this->figures); // number of years of data
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

    public function tableProp(){
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->tablePropYear();
	}
    }

    private function tablePropYear(){
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
	return $this->name;
    }

    public function makeJSON($field){
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->JSONYear($field);
	}
    }

    private function JSONYear($field){
	$ret = array();
	foreach($this->figures as $year=>$data){
	    $ret[$year] = $data[$field];
	}
	return json_encode($ret);
    }

    public function calculateDiffs(){
	if(!$this->figures){
	    $this->initialize;
	}
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		$this->yearDiffs();
		break;
	}
    }

    private function yearDiffs(){
	$ret = array();
	$pct = array();
	foreach($this->figures as $year=>$vals){
	    foreach($vals as $field=>$v){
		foreach($this->offsetYear as $o){
		    if(array_key_exists($year-$o,$this->figures)){
			$w = $this->figures[$year-$o][$field];
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
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->yearProportions();
	}
    }

    private function yearProportions(){
	foreach($this->proportions as $p){
	    foreach($this->figures as $year=>$vals){
		$pro = $vals[$this->fields[$p['top']]['field']] / $vals[$this->fields[$p['bottom']]['field']];
		$this->proportionData[$p['id']][$year] = $pro;
	    }
	}
    }

    public function mostRecent(){
	if(!$this->figures){
	    $this->initialize;
	}
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->mostRecentYear();
	}
    }

    private function mostRecentYear(){
	for($year = date('Y'); $year > 1900; $year--){
	    if(array_key_exists($year, $this->figures)){
		return $year;
	    }
	}
	return 0;
    }

    public function previous($date = NULL){
	if($date == NULL){
	    $date = $this->mostRecent();
	}
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->previousYear($date);
	}
    }

    private function previousYear($year){
	for($year--; $year > 1900; $year--){
	    if(array_key_exists($year, $this->figures)){
		return $year;
	    }
	}
	return 0;
    }

    public function getData($date){
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->getDataByYear($date);
	}
    }

    public function getDataByYear($year){
	if(array_key_exists($year, $this->figures)){
	    return $this->figures[$year];
	}
	return NULL;
    }

    public function getMax($field){
	return max($this->extractData($field));
    }

    public function getMin($field){
	return min($this->extractData($field));
    }

    private function extractData($field){
	switch($this->type){
	    case 'day':
		break;
	    case 'month':
		break;
	    case 'year':
		return $this->extractDataFromYear($field);
	}
    }

    private function extractDataFromYear($field){
	// This thing is by no means efficient. In a clean implementation,
	// I think that all of the data should be converted to a better data
	// structure for doing things like this.
	$ret = array();
	foreach($this->figures as $figure){
	    $ret[] = $figure[$field];
	}
	return $ret;
    }

    public function getMaxDiff($field, $time){
	return max($this->diffs[$field][$time]);
    }

    public function getMinDiff($field, $time){
	return min(array_diff($this->diffs[$field][$time], array(null, 0)));
    }

    public function getMaxPct($field, $time){
	return max($this->pct[$field][$time]);
    }

    public function getMinPct($field, $time){
	return min(array_diff($this->pct[$field][$time], array(null, 0)));
    }

    public function getMaxProp($prop){
	return max($this->proportionData[$prop]);
    }

    public function getMinProp($prop){
	return min(array_diff($this->proportionData[$prop], array(null, 0)));
    }

    public function averages($field){
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
	$data = $this->extractData($field);
	$m = $this->averages($data);
	$sum = 0;
	foreach($data as $d){
	    $sum += pow(($d - $m),2);
	}
	return sqrt($sum / count($data));
    }

    public function median($field){
	$data = $this->extractData($field);
	sort($data);
	$middle = round(count($data) / 2);
	return $data[$middle-1];
    }

    public function getAvgDiff($field, $time){
	return $this->averages($this->diffs[$field][$time]);
    }

    public function getAvgPct($field, $time){
	return $this->averages($this->pct[$field][$time]);
    }


}
