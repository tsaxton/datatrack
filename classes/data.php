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
	public $offsetMonth = [1, 3, 6, 12];
    public $fields;
    public $allFields;
    public $categories;
    public $success = TRUE;
	protected $recent;
	protected $previous;
	protected $yearData;
	protected $prevData;
	public $obs;
	protected $vals;
	protected $pro;
	protected $lt = array();
	protected $stats = array();

    // Data
    public $figures;
    public $proportions = array();

    // Analysis
    public $diffs;
    public $pct;
    public $proportionData = array();

    public function __construct($id){
		if(is_array($id)){
			$this->fromArray($id);
		}
		else{
			$this->id = $id;
			$this->initialize();
		}
    }

	abstract protected function fromArray($data);
    abstract public function initialize();
    abstract public function collectData();
    abstract public function sortData();
    abstract public function makeTable($field);
    abstract public function tableProp();
    abstract public function makeJSON($field);
    abstract public function calculateDiffs();
    abstract public function calculateProportions();
    abstract public function mostRecent();
	abstract public function mostRecentStr();
    abstract public function getData($year, $month);
    abstract public function extractData($field);
    abstract public function longStreaks();
    abstract public function getAvgDiff($field, $time);
    abstract public function getAvgPct($field, $time);
    abstract public function streakDirection($year, $field, $month=NULL);
    abstract public function negStreak($year, $field, $month);
    abstract public function posStreak($year, $field, $month);
    abstract public function minYear();
    abstract public function getMaxDiff($field, $time);
    abstract public function getMinDiff($field, $time);
    abstract public function getMaxPct($field, $time);
    abstract public function getMinPct($field, $time);
    abstract public function getMaxProp($prop);
    abstract public function getMinProp($prop);
	abstract public function previous();
	abstract public function printRecent();
	abstract public function keyObs();
	abstract protected function highlight();
	abstract protected function proportion();
	abstract protected function streak();
	abstract protected function recordCheck();
	abstract public function getCategories();
	abstract public function getId();
	abstract public function run();
	abstract protected function calculateStats();
	abstract public function statistics();
	abstract public function bigChanges();
	abstract public function longStreak();
	abstract protected function bestFit();
	abstract protected function pieceFit();

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

		return max($this->extractData($field)) + 0; // +0 forces to number
    }

    public function getMin($field){
		if(!$this->success){
			return;
		}

		return min($this->extractData($field)) + 0; // +0 forces to number
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
		return $data[$middle-1]+0; // plus zero forces to number
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

	protected function monthInt2String($month){
		switch($month){
		case 1:
			return 'Jan';
		case 2:
			return 'Feb';
		case 3:
			return 'Mar';
		case 4:
			return 'Apr';
		case 5:
			return 'May';
		case 6:
			return 'June';
		case 7:
			return 'July';
		case 8:
			return 'Aug';
		case 9:
			return 'Sept';
		case 10:
			return 'Oct';
		case 11:
			return 'Nov';
		case 12:
			return 'Dec';
		}
		throw new exception('Provided month integer not in range 1-12!');
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
}
