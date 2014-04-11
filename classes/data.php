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
    abstract public function sortByYear();
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
}
