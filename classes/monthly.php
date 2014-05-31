<?php

class monthly extends data{
	private $months = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

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

		// get full field name
		foreach($this->fields as $currentfield){
			if($currentfield['field'] == $field || $currentfield['text'] == $field){
				$thisField = $currentfield['text'];
				break;
			}
		}
		
		// set up table header
		$ret = "<table class=\"data table table-striped\" id=\"$field\">\n\t<tr>\n\t\t<th>Month</th>\n\t\t<th>".ucfirst($thisField)."</th>\n\t";
		foreach($this->offsetMonth as $o){
			$ret .= "\t<th class=\"noright\">$o month change</th>\n\t\t<th class=\"noleft\">(%)</th>\n\t";
		}
		$ret .= "</tr>\n";
		$current = $this->mostRecent();
		while(1){
			// TODO: Markers on records
			$data = $this->figures[$current[0]][$current[1]][$field];
			$ret .= "\t\n<tr>\n\t\t<th>" . $this->months[$current[1]] . " {$current[0]}</th>\n";
			$ret .= "\t\t<td>$data</td>\n";
			foreach($this->offsetMonth as $o){
				$ret .= "\t\t<td>";
				$diff = $this->diffs[$current[0]][$current[1]][$field][$o];
				if($diff != NULL){
					$ret .= "$diff</td>\n\t\t<td>";
					$pct = $diff/$data*100;
					$ret .= number_format($pct, 2)."</td>\n";
				}
				else{
					$ret .= "-</td>\n\t\t<td>-</td>\n";
				}
			}
			$ret .= "\t</tr>\n";
			//dump($current,'Calling');
			$current = $this->previous($current[0], $current[1]);
			if($current[0] == 0 || $current[1] == 0){
				break;
			}
		}
		$ret .= "</table>\n\n";
		return $ret;
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
		for($year = date('Y')-1; $year > 1900; $year--){
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

	public function mostRecentStr(){
		$ret = $this->mostRecent();
		return $this->months[$ret[1]] . " " . $ret[0];
	}

    public function previous($year = NULL, $month = NULL){
		if(!$this->success){
			return;
		}

		if(is_array($year) && $month == NULL){
			$month = $year[1];
			$year = $year[0];
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

		$month--;
		if($month == 0){
			$month = 12;
			$year--;
		}
		for($year; $year > 1900; $year--){
			if($month == 0){
				$month = 12;
			}
			for($month; $month >= 0; $month--){
				if(array_key_exists($year, $this->figures) && array_key_exists($month, $this->figures[$year])){
					return array($year, $month);
				}
			}
		}
		return array(0,0);
	}

    public function getData($year, $month=NULL){
		if(!$this->success){
			return;
		}
		if(is_array($year) && $month==NULL){
			$month = $year[1];
			$year = $year[0];
		}

		if($this->yearExists($year) && $this->monthExists($year, $month)){
			return $this->figures[$year][$month];
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

    public function getAvgDiff($field, $time){
		if(!$this->success){
			return;
		}
		$ret = array();

		foreach($this->diffs as $year){
			foreach($year as $month){
				array_push($ret, $month[$field][$time]);
			}
		}

		return $this->averages($ret);
	}

    public function getAvgPct($field, $time){
		if(!$this->success){
			return;
		}
		$ret = array();

		foreach($this->pct as $year){
			foreach($year as $month){
				array_push($ret, $month[$field][$time]);
			}
		}

		return $this->averages($ret);
	}

    public function streakDirection($year, $field, $month=NULL){
		if(!$this->success){
			return;
		}

		$current = $this->getData($year, $month);
		$prevMonth = $this->previous($year, $month);
		$prev = $this->getData($prevMonth[0], $prevMonth[1]);
		$prior = $this->getData($this->previous($prevMonth[0], $prevMonth[1]));
		if($current==NULL || $prev==NULL || $prior==NULL){
			return 0;
		}
		$thisChange = $current[$field]-$prev[$field];
		$prevChange = $prev[$field]-$prior[$field];
		// need to figure out what to do about 0 change cases besides returning error
		if($thisChange == 0 || $prevChange == 0){
			return 0;
		}
		// continuing a multi-year decrease
		if($thisChange < 0 && $prevChange < 0){
			return 1;
		}
		// decreasing after increasing
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
		return 0;
	}

    public function negStreak($year, $field, $month){
		if(!$this->success){
			return;
		}

		$c = 0;
		while($year != 0){
			$d = $this->getData($year, $month);
			$prevMonth = $this->previous($year, $month);
			$p = $this->getData($prevMonth[0], $prevMonth[1]);
			if($d == NULL || $p == NULL){
				return $c;
			}
			if($d[$field]-$p[$field] > 0){
				return $c;
			}
			$c++;
			$year = $prevMonth[0];
			$month = $prevMonth[1];
		}
		return $c;
	}

    public function posStreak($year, $field, $month){
		if(!$this->success){
			return;
		}

		$c = 0;
		while($year != 0){
			$d = $this->getData($year, $month);
			$prevMonth = $this->previous($year, $month);
			$p = $this->getData($prevMonth[0], $prevMonth[1]);
			if($d == NULL || $p == NULL){
				return $c;
			}
			if($d[$field]-$p[$field] < 0){
				return $c;
			}
			$c++;
			$year = $prevMonth[0];
			$month = $prevMonth[1];
		}
		return $c;
	}

    public function minYear(){
		if(!$this->success){
			return;
		}

		return min(array_keys($this->figures));
	}

	public function minMonth(){
		if(!$this->success){
			return;
		}

		$yr = $this->minYear();
		return min(array_keys($this->figures[$yr]));
	}

    public function getMaxDiff($field, $time){ // trust issues on this function's implementation
		$diffs = array();
		foreach($this->diffs as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return max($diffs);
	}

    public function getMinDiff($field, $time){ // same here
    	$diffs = array();
		foreach($this->diffs as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return min($diffs);
    }

    public function getMaxPct($field, $time){ // I seem to have already done this function at some point, not sure if I trust it
    	$diffs = array();
		foreach($this->pct as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return max($diffs);
    } // instead of $this->diffs, using $this->pct

    public function getMinPct($field, $time){ // I seem to have already done this function at some point, not sure if I trust it
    	$diffs = array();
		foreach($this->pct as $year=>$months){
			foreach($months as $month=>$data){
				$diffs[] = $data[$field][$time];
			}
		}
		return min($diffs);
    }
    public function getMaxProp($prop){ // trust issues here too
		//$this->proportionData[$p['id']][$year][$month] = $pro;
		$vals = array();
		foreach($this->proportionData[$prop] as $year){
			foreach($year as $month=>$val){
				$vals[] = $val;
			}
		}
		return max($vals);
	}

    public function getMinProp($prop){} // why is this one not done?

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
		if(!$this->success){
			return;
		}

		$i = 0;
		foreach($this->fields as $field){
			$this->stats[$i]['Title'] = $field['text'];
	    	$this->stats[$i]['Average'] = $this->averages($field['field']);
	    	$this->stats[$i]['Standard Deviation'] = $this->std($field['field']);
	    	$this->stats[$i]['Median'] = $this->median($field['field']);
	    	$this->stats[$i]['Max'] = $this->getMax($field['field']);
	    	$this->stats[$i]['Min'] = $this->getMin($field['field']);
	    	$this->stats[$i]['Average Change']['Raw'] = $this->getAvgDiff($field['field'], 1);
	    	$this->stats[$i]['Average Change']['Percent'] = 100*$this->getAvgPct($field['field'], 1);
	    	$i++;
		}
	}

	public function statistics(){
		if(!$this->success){
			return;
		}

		$ret = '';
		foreach($this->stats as $stat){
			$ret .= "<h4>{$stat['Title']}</h4>";
			unset($stat['Title']);
			foreach($stat as $key=>$s){
				if(is_float($s)){
					$ret .= "$key : " . number_format($s, 2, '.', ',') . "<br/>\n";
				}
				elseif(is_array($s)){
					$ret .= "$key:<br/>\n";
					foreach($s as $k=>$val){
						$ret .= "&nbsp;&nbsp;&nbsp;&nbsp;$k: ";
						if(is_float($val)){
							$ret .= number_format($val, 2, '.', ',') . "<br/>\n";
						}
						else{
							$ret .= number_format($val, 0, '.', ',') . "<br/>\n";
						}
					}
				}
				//elseif(is_int($s)){ // is int
				else{
					$ret .= "$key : " . number_format($s, 0, '.', ',') . "<br/>\n";
				}
			}
		}
		return $ret;
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
