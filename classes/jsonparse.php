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

	public function parse_json($json){
		$json = json_decode($json, true);
		$this->arr = $json;
		return $json;
	}


	private function find_time(){
 HEAD:templates/jsonparse.php

		$dates = [‘year’, ‘month’, ‘day’, ‘date’];
		$dateslength = count($dates);
		foreach($this->arr as $key=>$value){
			for($dates = 0; $dates < $dateslength; $dates++){
				if(&key == $date){
					return $key;
				}
				elseif (gettype($value) == "array") {
					$valuelength = count($value);
					for($value = 0; $dates < $valuelength; $value++){


					}
				}
=======
		foreach($this->arr as $arr){
			$result = $this->timeRecursion($arr, 0);
			if($result){
				$this->location = $result;
				return $result;
>>>>>>> dc3dcf9d82908643f75a18edfe68f4969a7fb5b4:classes/jsonparse.php
			}
		}
		return false;
		// TODO: Case where date isn't a field, but rather is a key
	}

	private function timeRecursion($arr, $level, $ret = array()){
		$dates = ['year', 'month', 'day', 'quarter', 'date'];
		if(is_array($arr)){
			foreach($arr as $key=>$value){
				foreach($dates as $date){
					if(strpos(strtolower($key), $date) !== false){
						array_push($ret, array('key', $key, $level));
					}
				}
				if(is_array($value)){
					$ret = array_merge($ret, timeRecursion($value, $level+1));
				}
			}
		}
		$seen = array();
		foreach($ret as $key=>$field){
			if(isset($seen[$field[1]])){
				unset($seen[$key]);
			}
			else{
				$seen[$field[1]] = 1;
			}
		}
		unset($seen);
		dump($ret);
		return $ret;
	}

	public function confirmDates(){
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
