<?php

class multicsv{

	private $files = array();
	private $arrays = array();
	private $months;
	private $years;
	private $quarters;
	private $step;

	public function __construct($files, $month, $quarter, $year, $type){
		foreach($files['error'] as $i=>$error){
			if($error == 0){
				$this->files[$i] = file_get_contents($files['tmp_name'][$i]);
			}
		}
		$this->months = $month;
		$this->years = $year;
		$this->quarters = $quarter;
		$this->type = $type;
		$this->step = 0;
	}

	public function fixProblem($args){
		switch($this->step){
			case 1:
			case 4:
				if($args == 'yes'){
					$mean = $this->meanRowLength();
					$problems = array();
					foreach($this->arrays as $i=>$file){
						foreach($file as $j=>$row){
							$rowLen = $this->realRowLength($row);
							if($rowLen == 0){
								unset($this->arrays[$i][$j]);
							}
							elseif($rowLen != $mean){
								echo "Trying to unset \$this->arrays[$i][$j]";
								unset($this->arrays[$i][$j]);
							}
						}
					}
				}
				break;
			case 3:
				if($args == 'yes'){
					$problems = $this->missingColumns();
					foreach($this->arrays as $i=>$table){
						$row = $table[0];
						foreach($problems as $problem){
							$column = array_search($problem, $row);
							if($coulmn === false){
								continue;
							}
							foreach($table as $j=>$row){
								unset($row[$column]);
							}
						}
					}
				}
				break;
			case 2:
				if($args == 'yes'){
					$problems = $this->missingRows();
					foreach($this->arrays as $i=>$table){
						foreach($problems as $problem){
							foreach($table as $j=>$row){
								if($row[0] == $problem){
									unset($this->arrays[$i][$j]);
									break;
								}
							}
						}
					}
				}
				break;
		}
		$this->step++;
	}

	public function continueAnalysis($args){
		while(1){
			foreach($this->arrays as $i=>$arr){
				$this->arrays[$i] = array_values($this->arrays[$i]); // rekey the array to make sure we're not missing row numbers
			}
			switch($this->step){
			case 0:
				// Parse CSV file into array, and return an error if CSV is not the format
				foreach($this->files as $i=>$file){
					$this->arrays[$i] = $this->parse_csv($file);
					if(count($this->arrays[$i]) == 0 || count($this->arrays[$i][0]) == 0){
						return $this->errorMessage("Your file may not have been a CSV, or may have been empty. If you uploaded an Excel file, please Save As CSV.");
					}
				}
				foreach($this->arrays as $i=>$file){
					foreach($file as $j=>$row){
						if($this->realRowLength($row) == 0){
							unset($this->arrays[$i][$j]);
						}
						$rowLen = count($row)-1;
						foreach(array_reverse($row) as $k=>$value){
							if(strlen($value) == 0){
								unset($this->arrays[$i][$j][$rowLen-$k]);
							}
							else{
								break;
							}
						}
					}
				}
				break;
			case 4:
				// Take a second stab at removing junk rows, but with no prompt this time
				$mean = $this->meanArrLength();
				foreach($this->arrays as $i=>$file){
					foreach($file as $j=>$row){
						if(count($row) != $mean){
							unset($this->arrays[$i][$j]);
						}
					}
				}
			case 1:
				// Check CSV file for unusual lengths, and if there's a problem, return a question
				$problems = $this->getWeirdRows();
				if(!empty($problems)){
					$message = $this->formulateProblemMessage($problems);
					return $this->yesNo($message);
				}
				break;
			case 3:
				// Check heading column names, and alert if there are differences
				$problems = $this->missingColumns();
				if(!empty($problems)){
					return $this->yesNo($this->ignoreQuestion('col', $problems));
					//return $this->errorMessage("Your files do not have the same labels on their rows and/or columns.");
				}
				break;
			case 2:
				// Check heading row names, and alert if there are differences
				$problems = $this->missingRows();
				if(!empty($problems)){
					return $this->yesNo($this->ignoreQuestion('row', $problems));
				}
				break;
			case 5:
				// Check for rows with no numeric data; if not the first row, then get rid of it
				foreach($this->arrays as $i=>$file){
					foreach($file as $j=>$row){
						if($j == 0){
							continue;
						}
						$switch = false;
						foreach($row as $k=>$col){
							if(is_numeric($col)){
								$switch = true;
								break;
							}
						}
						if($switch != true){
							unset($this->arrays[$i][$j]);
						}
					}
				}
			case 6:
				// Check for date fields, if necessary
				// if a case reads "this is a problem" it can just return an error message, using the $this->errorMessage($msg) function
				switch($this->type){
				case 'yearly':
					if(is_array($this->years) && count($this->years) != count($this->files)){
						// this is a problem
					}
					if(!is_array($this->years)){
						// need to find years in the data
					}
					break;
				case 'monthly':
					if(!is_array($this->months) && !is_array($this->years)){
						// month and year contained in array
					}
					elseif(!is_array($this->months) && is_array($this->years) && count($this->years) != count($this->files)){
						// this is a problem
					}
					elseif(!is_array($this->months) && is_array($this->years)){
						// only months are hidden in the data
					}
					elseif(is_array($this->months) && !is_array($this->years)){
						// some really low-priority stuff where the months are files with each year in it
					}
					elseif(is_array($this->months) && is_array($this->years) && (count($this->years) != count($this->files) || count($this->months) != count($this->files))){
						// this is a problem
					}
					break;
				case 'quarterly':
					if(!is_array($this->quarters) && !is_array($this->years)){
						// quarter and year contained in array
					}
					elseif(!is_array($this->quarters) && is_array($this->years) && count($this->years) != count($this->files)){
						// this is a problem
					}
					elseif(!is_array($this->quarters) && is_array($this->years)){
						// only quarters are hidden in the data
					}
					elseif(is_array($this->quarters) && !is_array($this->years)){
						// some really low-priority stuff where the quarters are files with each year in it
					}
					elseif(is_array($this->quarters) && is_array($this->years) && (count($this->years) != count($this->files) || count($this->quarters) != count($this->files))){
						// this is a problem
					}
					break;
				}
				break;
			case 7:
				// Ask which one is category and which one is type
				break;
			case 8:
				return "Success!";
			}
			$this->step++;
		}
	}

	private function getWeirdRows(){
		$mean = $this->meanRowLength();
		$problems = array();
		foreach($this->arrays as $i=>$file){
			foreach($file as $j=>$row){
				$rowLen = $this->realRowLength($row);
				if($rowLen == 0){
					unset($this->arrays[$i][$j]);
				}
				elseif($rowLen != $mean){
					//array_push($problems, array($i, $j));
					unset($this->arrays[$i][$j]);
				}
			}
		}
		return $problems;
	}

	private function realRowLength($row){
		foreach($row as $thing){
			$len = 0;
			if(strlen($thing) != 0){
				$len++;
			}
			return $len;
		}
	}

	private function meanArrLength(){
		$rowLens = array();
		foreach($this->arrays as $i=>$file){
			foreach($file as $j=>$row){
				if(array_key_exists(count($row), $rowLens)){
					$rowLens[count($row)]++;
				}
				else{
					$rowLens[count($row)] = 1;
				}
			}
		}
		$max = max($rowLens);
		foreach($rowLens as $len=>$val){
			if($val == $max){
				return $len;
			}
		}
		return 0;
	}

	private function meanRowLength(){
		$rowLens = array();
		foreach($this->arrays[0] as $row){
			$len = $this->realRowLength($row);
			if(array_key_exists($len, $rowLens)){
				$rowLens[$len]++;
			}
			else{
				$rowLens[$len] = 1;
			}
		}
		$max = max($rowLens);
		foreach($rowLens as $len=>$val){
			if($val == $max){
				return $len;
			}
		}
		return 0;
	}


	private function errorMessage($message){
		return "<div class='alert alert-danger'>$message</div>";
	}

	private function ignoreQuestion($type, $problems){
		$ret = "The following" . ($type == 'col' ? ' column(s) ' : ' row(s) ') . " were not found in all files: <ul>";
		foreach($problems as $problem){
			$ret .= "<li>$problem</li>";
		}
		$ret .= "</ul>Would you like to ignore these " . ($type == 'col' ? 'column(s)' : 'row(s)') . "? (Otherwise, files missing these will be ignored in analysis of these options.)";
		return $ret;
	}

	private function missingColumns(){
		$measurements = array();
		foreach($this->arrays as $file){
			$heading = $file[0];
			foreach($heading as $column){
				if(array_key_exists($column, $measurements)){
					$measurements[$column]++;
				}
				else{
					$measurements[$column] = 1;
				}
			}
		}
		$problems = array();
		foreach($measurements as $column=>$count){
			if($count != count($this->arrays)){
				array_push($problems, $column);
			}
		}
		return $problems;
	}

	private function missingRows(){
		$measurements = array();
		foreach($this->arrays as $file){
			foreach($file as $row){
				$thing = $row[0];
				if(array_key_exists($thing, $measurements)){
					$measurements[$thing]++;
				}
				else{
					$measurements[$thing] = 1;
				}
			}
		}
		$problems = array();
		foreach($measurements as $row=>$count){
			if($count != count($this->arrays)){
				array_push($problems, $row);
			}
		}
		return $problems;
	}

	private function formulateProblemMessage($problems){
		foreach($problems as $problem){
			$message .= "In file {$problem[0]}:";
			$message .= "<table><tr>";
			foreach($this->arrays[$problem[0]][$problem[1]] as $value){
				$message .= "<td>$value</td>";
			}
			$message .= "</tr></table>";
		}
		return $message;
	}

	private function radioButtons($options, $message){
		$str = '<div class="well"><p>' . $message . '</p>';
		$str .= '<form action="analyze.php" method="POST" id="confirmationDialog">';
		foreach($options as $i=>$option){
			$str .= '<div class="radio">';
			$str .= "<label for='$i'><input type='radio' name='response' value='$i'/> $option</label></div>";
		}
		$str .= "<div class='form-group'><button type='submit' class='btn btn-default'>Continue</button></div>";
		$str .= '</form></div>';
	}

	private function checkButtons($options, $message){
		$str = '<div class="well"><p>' . $message . '</p>';
		$str .= '<form action="analyze.php" method="POST" id="confirmationDialog">';
		foreach($options as $i=>$option){
			$str .= '<div class="checkbox">';
			$str .= "<label for='$i'><input type='checkbox' name='response' value='$i'/> $option</label></div>";
		}
		$str .= "<div class='form-group'><button type='submit' class='btn btn-default'>Continue</button></div>";
		$str .= '</form></div>';
	}

	private function yesNo($message){
		$str = "<div class='well'><p>$message</p><p><strong>Responding No will result in the process being exited. You will need to edit your files manually to fix the error.</strong><div class='row'><form action='analyze.php' method='POST' id='confirmationDialog'><div class='col-md-6'><button type='submit' name='response' value='yes' class='btn btn-success'>Yes</button></div><div class='col-md-6'><button type='submit' name='response' value='no' class='btn btn-danger'>No</button></div></form></div></div>";
		return $str;
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
}
