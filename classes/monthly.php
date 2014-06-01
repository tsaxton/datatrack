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

    public function negStreak($year, $field, $month=NULL){
		if(!$this->success){
			return;
		}

		if($month==NULL && is_array($year)){
			$month = $year[1];
			$year = $year[0];
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

    public function posStreak($year, $field, $month=NULL){
		if(!$this->success){
			return;
		}
		if($month==NULL && is_array($year)){
			$month = $year[1];
			$year = $year[0];
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

    public function getMinProp($prop){
		$vals = array();
		foreach($this->proportionData[$prop] as $year){
			foreach($year as $month=>$val){
				$vals[] = $val;
			}
		}
		return min($vals);
	}

    public function longStreaks(){
		if(!$this->success){
			return;
		}

		$base = $this->mostRecent();
		foreach($this->fields as $field){
			$year = $base[0];
			$month = $base[1];
			$max = 0;
			$min = 0;
			$maxTime = array();
			$minTime = array();

			while($base[0] != 0 && $base[1] != 0){
				$year = $base[0];
				$month = $base[1];
				$neg = $this->negStreak($year, $field['field']);
				$pos = $this->posStreak($year, $field['field']);
				if($pos > $max){
					unset($maxTime);
					$maxTime = array();
					$max = $pos;
					array_push($maxTime, $base);
				}
				elseif($pos == $max){
					array_push($maxTime, $base);
				}
				if($neg > $min){
					unset($minTime);
					$minTime = array();
					$min = $neg;
					array_push($minTime, $base);
				}
				elseif($neg == $min){
					array_push($minTime, $base);
				}
				$base = $this->previous($year, $month);
			}

			$vals[$field['text']]['increase'] = $max;
			$vals[$field['text']]['decrease'] = $min;
			$years[$field['text']]['increase'] = $maxTime;
			$years[$field['text']]['decrease'] = $minTime;
		}
		return array($vals, $years);
	}

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
		if(!$this->success){
			return;
		}
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
		if(!$this->success){
			return;
		}

		return $this->obs[0];
	}

	private function timeString($year, $month = NULL){
		if(is_array($year) && $month == NULL){
			$month = $year[1];
			$year = $year[0];
		}
		return $this->months[$month] . " " . $year;
	}

	protected function highlight(){
		if(!$this->success){
			return;
		}

		$str = '';
		$time = $this->mostRecent();
		$dataString = $this->timeString($time);
		$data = $this->getData($time[0], $time[1]);
		$prevTime = $this->previous($time[0], $time[1]);
		$prev = $this->getData($prevTime[0], $prevTime[1]);
		$prevString = $this->timeString($prevTime);
		foreach($this->fields as $field){
			if($prev[$field['field']] == 0 || $prev[$field['field']] == NULL){
				continue;
			}
			$pct = ($data[$field['field']] - $prev[$field['field']]) / $prev[$field['field']] * 100;
			$str = "<span class='field'>{$field['text']}</span>: " . number_format($data[$field['field']], 0, '.', ',');
			if($pct == 0){
				$str .= " (no change from $prevString)</li>\n";
			}
			elseif($pct > 0){
				$pct = number_format($pct, 2, '.', ',');
				$str .= " <span class=\"data-increase\">$pct% increase from $prevString</span></li>\n";
			}
			elseif($pct < 0){
				$pct = number_format(-$pct, 2, '.', ',');
				$str .= " <span class=\"data-decrease\">$pct% decrease from $prevString</span></li>\n";
			}
			$this->vals[] = $str;
		}
		return $str;
	}

	protected function proportion(){
		if(!$this->success){
			return;
		}

		foreach($this->proportions as $p){
			$this->pro[$p['id']] = $this->yearData[$this->fields[$p['top']]['field']] / $this->yearData[$this->fields[$p['bottom']]['field']];
			$pro = number_format(100*$this->pro[$p['id']], 2, '.', ',');
			$this->obs[] = "{$p['description']}: $pro%";
		}
	}

	protected function streak(){
		if(!$this->success){
			return;
		}

		$str = '';
		foreach($this->fields as $field){
			$direction = $this->streakDirection($this->recent[0], $field['field'], $this->recent[1]);
			switch($direction){
			case 1:
				// continuing a multi-month decrease
				$this->obs[] = "<span class='field'>{$field['text']}</span> has now decreased for " . $this->negStreak($this->recent, $field['field']) . " months in a row.";
				break;
			case 2:
				// decrease after increasing
				$this->obs[] = "<span class='field'>{$field['text']}</span> decreased after " . $this->posStreak($this->previous, $field['field']) . " months of increasing.";
				break;
			case 3:
				// continuing a multi-month increase
				$this->obs[] = "<span class='field'>{$field['text']}</span> has now increased for " . $this->posStreak($this->recent, $field['field']) . " months in a row.";
				break;
			case 4:
				// increase after decreasing
				$this->obs[] = "<span class='field'>{$field['text']}</span> increased after " . $this->negStreak($this->previous, $field['field']) . " months of decreasing.";
				break;
			}
		}
	}

	protected function recordCheck(){
		if(!$this->success){
			return;
		}

		foreach($this->fields as $field){
			if(!array_key_exists($field['field'], $this->yearData)){
				continue;
			}

			if($this->yearData[$field['field']] == $this->getMax($field['field'])){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> hit a record high!</span>";
			}
			elseif($this->yearData[$field['field']] == $this->getMin($field['field'])){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> hit a record low!</span>";
			}
	    	
	    	// see how the raw change matches
	    	if(abs($this->yearData[$field['field']] - $this->prevData[$field['field']]) == abs($this->getMaxDiff($field['field'], 1))){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest increase in numbers ever.</span>";
	    	}
	    	elseif(abs($this->yearData[$field['field']] - $this->prevData[$field['field']]) == abs($this->getMinDiff($field['field'], 1))){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest decrease in numbers ever.</span>";
	    	}

	    	// see how the percent change matches
	    	if(abs(($this->yearData[$field['field']] - $this->prevData[$field['field']]) / $this->prevData[$field['field']]) == abs($this->getMaxPct($field['field'], 1))){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent increase ever.</span>";
	    	}
	    	if(abs(($this->yearData[$field['field']] - $this->prevData[$field['field']]) / $this->prevData[$field['field']]) == abs($this->getMinPct($field['field'], 1))){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent decrease ever.</span>";
	    	}

	    	foreach($this->proportions as $p){
				if($this->pro[$p['id']] == $this->getMaxProp($p['id'])){
		    		$this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record high.</span>";
				}
				if($this->pro[$p['id']] == $this->getMinProp($p['id'])){
		    		$this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record low.</span>";
				}
	    	}

			// TODO: if part of a streak, find if it is a record streak
	
		}
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
