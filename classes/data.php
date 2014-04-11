<?php

abstract class data{

    // General Fields
    public $id;
    protected $name;
    protected $updated;
    protected $api;
    protected $type;
    protected $selects;
    protected $groups;
    public $offsetYear = [1, 2, 5, 10, 25, 50, 100];
    public $fields;
    public $allFields;
    public $categories;
    public $success = TRUE;

    // Data
    public $figures;
    public $proportions;

    // Analysis
    public $diffs;
    public $pct;
    public $proportionData = array();

    public function __construct($id){
		$this->id = $id;
		$this->initialize();
    }

    abstract public function initialize();
    abstract public function collectData();
    abstract public function sortData();
    abstract public function makeTable($field);
    abstract public function tableProp();
    abstract public function makeJSON($field);
    abstract public function calculateDiffs();
    abstract public function calculateProportions();
    abstract public function mostRecent();
    abstract public function previous($year = NULL);
    abstract public function getData($year);
    abstract public function extractData($field);
    abstract public function longStreaks();
    abstract public function getAvgDiff($field, $time);
    abstract public function getAvgPct($field, $time);
    abstract public function streakDirection($year, $field);
    abstract public function negStreak($year, $field);
    abstract public function posStreak($year, $field);
    abstract public function minYear();
    abstract public function getMaxDiff($field, $time);
    abstract public function getMinDiff($field, $time);
    abstract public function getMaxPct($field, $time);
    abstract public function getMinPct($field, $time);
    abstract public function getMaxProp($prop);
    abstract public function getMinProp($prop);

    public function areProportions(){
		if(!$this->success){
			return;
		}

		return count($this->proportions);
    }
    
    public function getName(){
		if(!$this->success){
			return;
		}

		return $this->name;
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

    public function getYears(){
		if(!$this->success){
			return;
		}

		return array_keys($this->figures);
    }

    public function averages($field){
		if(!$this->success){
			return;
		}

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

	protected function getYearField(){
		global $db;
		$results = $db->query('select * from datefields where dataset='.$this->id);
		foreach($results as $result){
			if($result['type'] == 'year'){
				return array($result['field'], 'year');
			}
		}
		$formats = array('month/year', 'month/day/year', 'month day, year', 'year.month.day', 'day.month.year');
		foreach($results as $result){
			foreach($formats as $format){
				if($result['type'] == $format){
					return array($result['field'], $format);
				}
			}
		}
	}

	protected function getMonthField(){
		global $db;
		$results = $db->query('select * from datefields where dataset='.$this->id);
		foreach($results as $result){
			if($result['type'] == 'month'){
				return array($result['field'], 'month');
			}
		}
		$formats = array('month/year', 'month/day/year', 'month day, year', 'year.month.day', 'day.month.year');
		foreach($results as $result){
			foreach($formats as $format){
				if($result['type'] == $format){
					return array($result['field'], $format);
				}
			}
		}
	}

	protected function extractYear($piece, $format){
		switch($format[1]){
		case 'year':
			return $piece[$format[0]];
			break;
		case 'month/year':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $piece[$format][0] = 01/2014
			// $date = array('01', '2014')
			if(count($date) == 2){
				return $date[1];
			}
			elseif(count($date) > 2){
				// $date = array('04', '11', '2014')
				return $date[count($date)-1];
			}
			break;
		case 'month/day/year':
		case 'day.month.year':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $date = array('04', '11', '2014')
			if(count($date)==3){
				return $date[2];
			}
			elseif(count($date)>3){
				return $date[count($date)-1];
			}
			elseif(count($date)==2){
				return $date[1];
			}
			break;
		case 'month day, year':
			break;
		case 'year.month.day':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $date = array('2014', '11', '04')
			return $date[0];
			break;
		}
		throw new exception('Year format and field contents do not match!');
	}

	protected function extractMonth($piece, $format){
		switch($format[1]){
		case 'month':
			return $piece[$format[0]];
			break;
		case 'month/year':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $piece[$format][0] = 01/2014
			// $date = array('01', '2014')
			if(count($date) == 2){
				return $date[0];
			}
			break;
		case 'month/day/year':
			$date = split("/|\.|-", $piece[$format[0]]);
			if(count($date)==3){
				return $date[0];
			}
			elseif(count($date)==2){
				return $date[0];
			}
			break;
		case 'day.month.year':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $date = array('04', '11', '2014')
			if(count($date)==3){
				return $date[1];
			}
			elseif(count($date)==2){
				return $date[0];
			}
			break;
		case 'month day, year':
			break;
		case 'year.month.day':
			$date = split("/|\.|-", $piece[$format[0]]);
			// $date = array('2014', '11', '04')
			return $date[1];
			break;
		}
		throw new exception('Year format and field contents do not match!');
	}

	protected function monthString2Int($month){
		switch(strtolower($month)){
		case 'jan':
		case 'jan.':
		case 'january':
			return 1;
		case 'feb':
		case 'feb.':
		case 'febr':
		case 'febr.':
		case 'february':
			return 2;
		case 'mar':
		case 'mar.':
		case 'march':
			return 3;
		case 'apr':
		case 'apr.':
		case 'april':
			return 4;
		case 'may':
			return 5;
		case 'jun':
		case 'jun.':
		case 'june':
			return 6;
		case 'jul.':
		case 'jul':
		case 'july':
			return 7;
		case 'aug.':
		case 'aug':
		case 'august':
			return 8;
		case 'sep':
		case 'sep.':
		case 'sept':
		case 'sept.':
		case 'september':
			return 9;
		case 'oct':
		case 'oct.':
		case 'october':
			return 10;
		case 'nov':
		case 'nov.':
		case 'november':
			return 11;
		case 'dec':
		case 'dec.':
		case 'december':
			return 12;
		}
		throw new exception("Can't recognize abbreviation used for month!");
	}
}
