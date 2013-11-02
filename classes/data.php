<?php

class data{

    // General Fields
    private $id;
    private $name;
    private $updated;
    private $api;
    private $datatable;
    private $type;

    // Data
    private $figures;

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
	$this->datatable = $results[0]['datatable'];

	$this->collectData();
    }

    public function collectData(){
	if(!$this->datatable){
	    $this->initialize();
	}

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

    private function yearLimit($var){
	return ($var < count($this->figures));
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
	    $row['total'] = array_sum($row);
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
	$offset = [1, 2, 5, 10, 25, 50, 100];
	foreach($offset as $key=>$o){
	    if($o < $years){
		$ret .= "\t<th class=\"noright\">$o yr. change</th>\n\t\t<th>(% change)</th>\n\t";
	    }
	    else{
		unset($offset[$key]);
	    }
	}
	$year = date("Y")-1;
	for($i=0; $i < $years; $i++){
	    $ret .= "<tr>\n\t\t<td>$year</td>\n\t\t<td>{$this->figures[$year][$field]}</td>\n\t";
	    foreach($offset as $o){
		if(array_key_exists($year-$o,$this->figures)){
		    $diff = $this->figures[$year][$field] - $this->figures[$year - $o][$field];
		    $pct = number_format(100 * $diff / $this->figures[$year-$o][$field], 2);
		    $ret .= "\t<td class=\"noright\">$diff</td>\n\t\t<td class=\"noleft\">$pct</td>\n\t";
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

}
