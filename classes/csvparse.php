<?php

class csvparse {
	public $str;
	public $arr;
	public $time;
	public $location;
	public $location_err = false;

	public function __construct($str, $time){
		$this->str = $str;
		$arr = $this->parse_csv($str);
		$this->arr = $arr;
		$this->time = $time;
		$this->find_time();
	}

	private function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true){
	    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
	    $enc = preg_replace_callback(
	        '/"(.*?)"/s',
	        function ($field) {
	            return urlencode(utf8_encode($field[1]));
	        },
	        $enc
	    );
	    $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
	    return array_map(
	        function ($line) use ($delimiter, $trim_fields) {
	            $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
	            return array_map(
	                function ($field) {
	                    return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
	                },
	                $fields
	            );
	        },
	        $lines
	    );
	}

	private function find_time(){
		$row = 0;
		$column = 0;
		$row_score = 0;
		$column_score = 0;

		foreach($this->arr[0] as $str){
			if (($timestamp = strtotime($str)) === false) {
				$row++;
			}
			else{
				$row++;
				$row_score++;
			}
		}

		foreach($this->arr as $r){
			$str = $r[0];
			if(($timestamp = strtotime($str))=== false){
				$column++;
			}
			else{
				$column++;
				$column_score++;
			}
		}
		$column_score/=$column;
		$row_score/=$row;

		if($column_score > $row_score){
			$this->location = 'column';
			return true;
		}
		elseif($column_score < $row_score){
			$this->location = 'row';
			return true;
		}

		$row_score = 0;
		$column_score = 0;
		$location_err = true;

		foreach($this->arr[0] as $str){
			$row_score += strlen($str);
		}

		foreach($this->arr as $r){
			$str = $r[0];
			$column_score += strlen($str);
		}

		$column_score/=$column;
		$row_score/=$row;

		if($column_score < $row_score){
			$this->location = 'column';
			return true;
		}
		elseif($column_score > $row_score){
			$this->location = 'row';
			return true;
		}

		return false;
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
