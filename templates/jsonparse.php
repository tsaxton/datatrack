<?php

class jsonparse {
	public $str;
	public $arr;
	public $time;
	public $location;
	public $location_err = false;

	public function __construct($str, $time){
		$this->str = $str;
		$arr = $this->parse_json($str);
		$this->arr = $arr;
		$this->time = $time;
		$this->find_time();
	}

	public function parse_json(){
		$json = json_decode($json, true);
		$this->arr = $json;
		return $json;
	}


	private function find_time(){

		echo "<h1>hello world!!!</h1>";

		$dates = [‘year’, ‘month’, ‘day’, ‘date’];
		$dateslength = count($dates);
		foreach($this->arr as $key=>$value){
			for($dates = 0; $dates < $arrlength; $dates++){
				if(&key == $date){
					return $key;
				}
				elseif (gettype($value) == "array") {
					$valuelength = count($value);
					for($value = 0; $dates < $valuelength; $value++){


					}
				}
			}
		return null;
		}
	}

	public function confirmDates(){
		//if (($timestamp = strtotime($str)) === false) {
		switch($this->time){
		case 'quarterly':
			$format = 'm/Y';
			break;
		case 'monthly':
			$format = 'm/Y';
			break;
		case 'yearly':
			$format = 'Y';
			break;
		}

		$dates = array();
		if(!$this->location_err && $this->location == 'column'){
			foreach($this->arr as $r){
				$str = $r[0];
				if(($timestamp = strtotime($str)) != false){
					array_push($dates, date('Y', $timestamp));
				}
			}
		}
		elseif(!$this->location_err && $this->location == 'row'){
			echo "This other case!";
			foreach($this->arr[0] as $str){
				if(($timestamp = strtotime($str)) != false){
					array_push($dates, date('Y', $timestamp));
				}
			}
		}
		elseif(!$this->location_err){
			$this->location_err = true;
		}
		if($this->location_err){
			// TODO: More complex interface for the event that the dates are not correct.
		}
		return $dates;
	}

	public function confirmFields(){
		$fields = array();

		if($this->location == 'column'){
			for($i = 1; $i < count($this->arr[0]); $i++){
				array_push($fields, $this->arr[0][$i]);
			}
		}
		elseif($this->location == 'row'){
			for($i = 1; $i < count($this->arr); $i++){
				array_push($fields, $this->arr[$i][0]);
			}
		}
		else{
			// TODO: error case
		}
		return $fields;
	}

	public function getDataRows(){
		return count($this->arr);
	}

	public function getDataColumns(){
		return count($this->arr[0]);
	}

	public function getData($index){
		if($this->location == 'column'){
			return $this->arr[$index];
		}
		elseif($this->location == 'row'){
			$ret = array();
			foreach($this->arr as $row){
				array_push($ret, $row[$index]);
			}
			return $ret;
		}
		return 0;
	}

	public function getIndex($field){
		$field = preg_replace("/[^A-Za-z0-9 ]/", '', $field);
		//dump($field, 'field for getIndex');
		if($this->location == 'column'){
			foreach($this->arr[0] as $ind=>$foo){
				//dump(preg_replace("/[^A-Za-z0-9]/", '', $foo));
 				if(preg_replace("/[^A-Za-z0-9]/", '', $foo) == $field){
					return $ind;
				}
			}
		}
		elseif($this->location == 'row'){
			foreach($this->arr as $ind=>$row){
				if($row[0] == $field){
					return $ind;
				}
			}
		}
		return 23;
	}

}
