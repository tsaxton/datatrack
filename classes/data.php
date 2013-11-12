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

    // Analysis
    public $diffs;

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

	$this->collectData();
	$this->calculateDiffs();
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
	    $ret .= "<tr>\n\t\t<td>$year</td>\n\t\t<td>". number_format($this->figures[$year][$field],0,'.', ',') . "</td>\n\t";
	    foreach($this->offsetYear as $o){
		if($o >= $years){
		    continue;
		}
		if($this->diffs[$field][$o][$year]){
		    $max = max($this->diffs[$field][$o]);
		    $min = min(array_diff($this->diffs[$field][$o], array(null, 0)));
		    //dump($max, 'max');
		    //dump($min, 'min');
		    $diff = $this->diffs[$field][$o][$year];
		    $pct = number_format(100 * $diff / $this->figures[$year-$o][$field], 2);
		    $class = "noright";
		    $class2 = "noleft";
		    if($diff == $max){
			$class .= " max";
			$class2 .= " max";
		    }
		    elseif($diff == $min){
			$class .= " min";
			$class2 .= " min";
		    }
		    $ret .= "\t<td class=\"$class\">" . number_format($diff, 0, '.', ',') . "</td>\n\t\t<td class=\"$class2\">$pct%</td>\n\t";
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
	foreach($this->figures as $year=>$vals){
	    foreach($vals as $field=>$v){
		foreach($this->offsetYear as $o){
		    if(array_key_exists($year-$o,$this->figures)){
			$ret[$field][$o][$year] = $v - $this->figures[$year-$o][$field];
		    }
		    else{
			$ret[$field][$o][$year] = NULL;
		    }
		}
	    }
	}
	$this->diffs = $ret;
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

}
