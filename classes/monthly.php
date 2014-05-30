<?php

class monthly extends data{
	protected function fromArray($data){
		return;
	}

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

    public function makeTable($field){
		if(!$this->success){
			return;
		}
		
		// set up table header
		$ret = "<table class=\"data\" id=\"$field\">\n\t<tr>\n\t\t<th>Month</th>\n\t\t<th>".ucfirst($field)."</th>\n\t";
		foreach($this->offsetMonth as $o){
			$ret .= "\t<th class=\"noright\">$o month change</th>\n\t\t<th class=\"noleft\">(% change)</th>\n\t";
		}
		$ret .= "</tr>\n";
		// TODO: Finish this function
	}

    public function tableProp(){}
    public function makeJSON($field){
		if(!$this->success){
			return;
		}
		$ret = array();
		foreach($this->figures as $year=>$months){
			foreach($months as $month=>$data){
				if(!array_key_exists($year, $ret)){
					$ret[$year] = array();
				}
				$ret[$year][$month] = $data[$field];
			}
		}
		return json_encode($ret);
	}

    public function calculateDiffs(){
		if(!$this->success){
			return;
		}

		if(!$this->figures){
			$this->initialize();
		}

		$ret = array();
		$pct = array();

		foreach($this->figures as $year=>$months){
			foreach($months as $month=>$vals){
				foreach($this->offsetMonth as $o){
					$targetYear = $year;
					$targetMonth = $month;
					if($targetMonth <= $o){
						$targetYear--;
						$targetMonth = $targetMonth + 12 - $o;
					}
					else{
						$targetMonth -= $o;
					}
					foreach($this->fields as $fieldID=>$fieldData){
						$field = $fieldData['field'];
						if(array_key_exists($targetYear, $this->figures) && array_key_exists($targetMonth, $this->figures[$targetYear])){
							$v = $this->figures[$year][$month][$field];
							$w = $this->figures[$targetYear][$targetMonth][$field];
							$ret[$year][$month][$field][$o] = $v - $w;
							$pct[$year][$month][$field][$o] = ($v-$w)/$w;
						}
						else{
							$ret[$year][$month][$field][$o] = NULL;
							$pct[$year][$month][$field][$o] = NULL;
						}
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
			foreach($this->figures as $year=>$months){
				foreach($months as $month=>$vals){
					$pro = $vals[$this->fields[$p['top']]['field']] / $vals[$this->fields[$p['bottom']]['field']];
					$this->proportionData[$p['id']][$year][$month] = $pro;
				}
			}
		}
	}

    public function mostRecent(){
		if(!$this->success){
			return;
		}

		if(!$this->figures){
			$this->initialize();
		}
		for($year = $date('Y')-1; $year > 1900; $year--){
			if(array_key_exists($year, $this->figures)){
				break;
			}
		}
		if($year == 1901){
			return array(0,0);
		}
		for($month = 12; $month > 0; $month--){
			if(array_key_exists($month, $this->figures[$year])){
				return array($year, $month);
			}
		}
		return array(0,0);
	}

    public function previous($year = NULL, $month = NULL){
		if(!$this->success){
			return;
		}

		if($year == NULL){
			$year = $this->mostRecent()[0];
		}
		if($month == NULL){
			$month = $this->mostRecent()[1];
		}
		if($year == 0 || $month == 0){
			return array(0,0);
		}
		for($year; $year > 1900; $year--){
			for($month = 12; $month > 0; $month--){
				if(array_key_exists($year, $this->figures) && array_key_exists($month, $this->figures[$year])){
					return array($year, $month);
				}
			}
		}
		return array(0,0);
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

    public function extractData($field){
		$ret = array();
		foreach($this->figures as $year=>$months){
			foreach($months as $data){
				$ret[] = $data[$field];
			}
		}
		return $ret;
	}

    public function getAvgDiff($field, $time){}
    public function getAvgPct($field, $time){}
    public function streakDirection($year, $field){}
    public function negStreak($year, $field){}
    public function posStreak($year, $field){}
    public function minYear(){}
    public function getMaxDiff($field, $time){
		$diffs = array();
		foreach($this->diffs as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return max($diffs);
	}
    public function getMinDiff($field, $time){
    	$diffs = array();
		foreach($this->diffs as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return min($diffs);
    }
    public function getMaxPct($field, $time){
    	$diffs = array();
		foreach($this->pct as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return max($diffs);
    } // instead of $this->diffs, using $this->pct
    public function getMinPct($field, $time){
    	$diffs = array();
		foreach($this->pct as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return min($diffs);
    }
    public function getMaxProp($prop){
		//$this->proportionData[$p['id']][$year][$month] = $pro;
		$vals = array();
		foreach($this->proportionData[$prop] as $year){
			foreach($year as $month=>$val){
				$vals[] = $val;
			}
		}
		return max($vals);
	}
    public function getMinProp($prop){}
    public function longStreaks(){}

	public function monthExists($year, $month){
		if(!$this->success){
			return;
		}

		if($this->yearExists($year)){
			return array_key_exists($month, $this->figures[$year]);
		}
		return False;
	}

	public function analyze(){
	}

	public function printRecent(){
	}

	public function keyObs(){
	}

	protected function highlight(){
		return;
	}

	protected function proportion(){
		return;
	}

	protected function streak(){
		return;
	}

	protected function recordCheck(){
		return;
	}

	public function getCategories(){
		return;
	}

	public function getId(){
		return;
	}

	public function run(){
	}

	protected function calculateStats(){
	}

	public function statistics(){
	}

	public function bigChanges(){
	}

	public function longStreak(){
	}

	protected function bestFit(){
	}

	protected function pieceFit(){
	}
}
?>
