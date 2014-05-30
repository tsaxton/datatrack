<?php

class yearly extends data{

	protected function fromArray($data){
		$this->name = 'User Data';
		$year = reset($data);
		$fields = array();
		$i = 0;
		foreach($year as $field=>$val){
			array_push($fields, array('id'=>$i++, 'field'=>$field, 'text'=>$field, 'major'=>1));
		}
		$this->fields = $fields;
		$this->allFields = $fields;
		$this->figures = $data;

		$this->calculateDiffs();
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
		$ret .="</th>\n\t";
		$year = date("Y")-1;
		while(!array_key_exists($year, $this->figures)){
			$year--;
		}
		for($i=0; $i < $years; $i++){
			$max = $this->getMax($field);
			$min = $this->getMin($field);
			while(!array_key_exists($year, $this->figures)){
				$year--;
				continue;
			}
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
			$this->initialize();
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
			$this->initialize();
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

	public function analyze(){
		if(!$this->success){
			return;
		}
		$this->recent = $this->mostRecent();
		$this->previous = $this->previous($this->recent);
		$this->yearData = $this->getData($this->recent);
		$this->prevData = $this->getData($this->previous);
		$this->highlight();
		$this->recordCheck();
		$this->proportion();
		$this->streak();
		$this->calculateStats();
		$this->bigChanges();
		$this->pieceFit();
		$this->bestFit();
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

	protected function highlight(){
		if(!$this->success){
	    	return;
		}

		$str = '';
		foreach($this->fields as $field){
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
	    	$direction = $this->streakDirection($this->recent, $field['field']);
			if($direction == 1){
				// continuing a multi-year decrease
				$this->obs[] = "<span class='field'>{$field['text']}</span> has now decreased for " . $this->negStreak($this->recent, $field['field']) . " years in a row.";
	    	}
	    	elseif($direction == 2){
				// decrease after increasing
				$this->obs[] = "<span class='field'>{$field['text']}</span> decreased after " . $this->posStreak($this->previous, $field['field']) . " years of increasing.";
	    	}
	    	elseif($direction == 3){
				// continuing a multi-year increase
				$this->obs[] = "<span class='field'>{$field['text']}</span> has now increased for " . $this->posStreak($this->recent, $field['field']) . " years in a row.";
	    	}
	    	elseif($direction == 4){
				// increase after decreasing
				$this->obs[] = "<span class='field'>{$field['text']}</span> increased after " . $this->negStreak($this->previous, $field['field']) . " years of decreasing.";
	    	}
		}
		return $str;
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
	    	if($this->yearData[$field['field']] == $this->getMaxDiff($field['field'], 1)){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest increase in numbers ever.</span>";
	    	}
	    	elseif($this->yearData[$field['field']] == $this->getMinDiff($field['field'], 1)){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest decrease in numbers ever.</span>";
	    	}

	    	// see how the percent change matches
	    	if($this->yearData[$field['field']] == $this->getMaxPct($field['field'], 1)){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent increase ever.</span>";
	    	}
	    	if($this->yearData[$field['field']] == $this->getMinPct($field['field'], 1)){
				$this->obs[] = "<span class='record'><span class='field'>{$field['text']}</span> had its largest percent decrease ever.</span>";
	    	}

	    	// see how the proportions match
	    	foreach($this->proportions as $p){
				if($this->pro[$p['id']] == $this->getMaxProp($p['id'])){
		    		$this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record high.</span>";
				}
				if($this->pro[$p['id']] == $this->getMinProp($p['id'])){
		    		$this->obs[] = "<span class='record'><span class='field'>{$p['description']}</span> hit a record low.</span>";
				}
	    	}
	    
	    	// TODO: if part of a streak, find if is a record streak
		}
    }

    public function getCategories(){
		if(!$this->success){
	    	return;
		}

		$str = '';
		foreach($this->categories as $c){
	    	$str .= "{$c['category']} ";
		}
		return $str;
    }

    public function getId(){
		return $this->id;
    }

	public function run(){
		if(!$this->success){
			return;
		}

		$str = "<ul class=\"longterm-analysis\">\n";
		foreach($this->lt as $o){
			$str .= "\t<li>$o</li>\n";
		}
		$str .= "</ul>\n\n";
		return $str;
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
		$i++;
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
		if(!$this->success){
			return;
		}

		$comparisons = [5, 2, 1, .75, .5];
		// this isn't pretty...
		foreach($this->fields as $field){
			foreach($this->pct[$field['field']] as $offset=>$years){
				foreach($years as $year=>$data){
					foreach($comparisons as $c){
						if(abs($data) > $c){
							$str = "{$field['text']} ";
							$str .= $data<0 ? 'decreased ' : 'increased ';
							$str .= "by more than ";
							$str .= $c*100;
							$str .= "% from ";
							$str .= $year-$offset . '-' . $year;
							$this->lt[] = $str;
							break;
						}
					}
				}
			}
		}
	}

	public function longStreak(){
		if(!$this->success){
			return;
		}

		$ret = $this->longStreaks();
		$vals = $ret[0];
		$years = $ret[1];
		foreach($vals as $text=>$types){
			echo "<h4>$text</h4>\n";
			foreach($types as $type=>$val){
				echo "Longtest $type: $val years\n";
				$i = 0;
				echo "<ul>\n";
				foreach($years[$text][$type] as $y){
					$startYear = $y-$val;
					echo "\t<li>$startYear-$y</li>\n";
				}
				echo "</ul>";
			}
		}
	}

	protected function bestFit(){
		if(!$this->success){
			return;
		}

		$years = $this->getYears();
		foreach($this->fields as $field){
			$data = $this->extractData($field['field']);
			$b = slope($years, $data);
			if($b > 0){
				$this->lt[] = "<span class='field'>{$field['text']}</span> has been trending upward at a rate of " . number_format($b, 2, '.', ',') . ".";
			}
			else{
				$this->lt[] = "<span class='field'>{$field['text']}</span> has been trending downward at a rate of " . number_format($b, 2, '.', ',') . ".";
			}
		}
	}


	protected function pieceFit(){
		if(!$this->success){
			return;
		}

		$years = $this->getYears();
		$minYear = min($years);
		$maxYear = max($years);
		foreach($this->fields as $field){
			$best = PHP_INT_MAX;
			$bestA1 = NULL;
			$bestA2 = NULL;
			$bestB1 = NULL;
			$bestB2 = NULL;
			$bestI = NULL;
			$i = 4;
			$data = $this->extractData($field['field']);
			if($i < count($years)-4){
				continue;
			}
			while($i < count($years)-4){
				$years1 = array_slice($years,0,$i);
				$years2 = array_slice($years,$i+1);
				$data1 = array_slice($data,0,$i);
				$data2 = array_slice($data,$i+1);
				$a1 = intercept($years1, $data1);
				$a2 = intercept($years2, $data2);
				$b1 = slope($years1, $data1);
				$b2 = slope($years2, $data2);
				$residuals = residuals($a1, $b1, $years1, $data1) + residuals($a2, $b2, $years2, $data2);
				if($residuals < $best){
					$best = $residuals;
					$bestA1 = $a1;
					$bestA2 = $a2;
					$bestB1 = $b1;
					$bestB2 = $b2;
					$bestI = $i;
				}
				$i++;
			}
			if(!($bestB1 && $bestB2 && $bestA1 && $bestA2)){
				continue;
			}
			if($bestB1 < 0 && $bestB2 > 0){
				$this->obs[] = "After trending downward from $minYear-" . ($minYear+$bestI-1) . ", <span class='field'>{$field['text']}</span> trended upward.";
			}
			elseif($bestB1 > 0 && $bestB2 < 0){
				$this->obs[] = "After trending upward from $minYear-" . ($minYear+$bestI-1) . ", <span class='field'>{$field['text']}</span> trended downward.";
			}
			elseif($bestB2 < 0){
				if($bestB2 > $bestB1){
		    		$this->obs[] = "The downward trend slowed from " . ($minYear+$bestI) . "-$maxYear compared to its rate from $minYear-" . ($minYear+$bestI-1) . ".";
				}
				else{
		    		$this->obs[] = "The downward trend sped up from " . ($minYear+$bestI) . "-$maxYear compared to its rate from $minYear-" . ($minYear+$bestI-1) . ".";
				}
			}
			else{
				if($bestB2 > $bestB1){
		    		$this->obs[] = "The upward trend sped up from " . $minYear+$bestI . "-$maxYear compared to its rate from $minYear-" . $minYear+$bestI-1 . ".";
				}
				else{
		    		$this->obs[] = "The upward trend slowed from " . $minYear+$bestI . "-$maxYear compared to its rate from $minYear-" . $minYear+$bestI-1 . ".";
				}
			}
		}
	}
}
