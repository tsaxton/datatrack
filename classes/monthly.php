<?php

class monthly extends data{
    public function initialize(){
		if(!$this->id){
			throw new Exception('Data object was improperly initialized.');
		}

		global $db;
		$results = $db->query('select * from datasets where id='.$this->id);

		if(count($results) != 1){
			throw new exception('Too many entries with given id.');
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
		$this->proportions = $db->query('select * from proportions where dataset='.$this->id);

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

		$this->sortData();
		return 1;
	}

    public function sortData(){
		// For now, we won't worry about having multiple things.
		$yearField = $this->getYearField();
		$monthField = $this->getMonthField();

		$ret = array();
		foreach($this->figures as $figure){
			$year = intval($this->extractYear($figure, $yearField));
			$month = $this->extractMonth($figure, $monthField);
			if(is_numeric($month)){
				$month = intval($month);
			}
			else{
				$month = $this->monthString2Int($month);
			}
			if(!array_key_exists($year, $ret)){
				$ret[$year] = array();
			}
			$ret[$year][$month] = $figure;
		}
		$this->figures = $ret;
	}
    public function makeTable($field){}
    public function tableProp(){}
    public function makeJSON($field){}
    public function calculateDiffs(){}
    public function calculateProportions(){}
    public function mostRecent(){}
    public function previous($year = NULL){}
    public function getData($year){}
    public function extractData($field){}
    public function longStreaks(){}
    public function getAvgDiff($field, $time){}
    public function getAvgPct($field, $time){}
    public function streakDirection($year, $field){}
    public function negStreak($year, $field){}
    public function posStreak($year, $field){}
    public function minYear(){}
    public function getMaxDiff($field, $time){}
    public function getMinDiff($field, $time){}
    public function getMaxPct($field, $time){}
    public function getMinPct($field, $time){}
    public function getMaxProp($prop){}
    public function getMinProp($prop){}
}
?>
