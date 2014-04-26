<?php

class cvsparse {
	public $str;
	public $arr;
	public $time;
	public function __construct($str, $time){
		$this->str = $str;
		$arr = parse_csv($str);
		$this->arr = $arr;

		$this->time = $time;

	}

	private function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
	{
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

		private function find_time ()
	{
		$row = 0;
		$column = 0;
		$row_score = 0;
		$column_score = 0;

		foreach($this->arr[0] as $str){
			if (($timestamp = strtotime($str)) === false) {
				$row++;
			} else {
				$row++;
				$row_score++;
			}
		}

		foreach($this->arr as $r)
		{
			$str = $r[0];
			if(($timestamp = strtotime($r))=== false){
				$column++;
			}else{
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

		foreach($this->arr[0] as $str){
			$row_score += strlen($str);
		}

		foreach($this->arr as $r)
		{
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

}