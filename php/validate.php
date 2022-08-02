<?php

class Validate {

	private $f;

	function __construct(array $pool)
	{
		$this->f = $pool;
	}

	public static function short_string($string, $exact_len = 0)
	{
		if (filter_var($string, FILTER_SANITIZE_STRING) != $string){
			throw new Exception($string." string did not pass the validation");
		}
		$len = strlen($string);
		if ($exact_len > 0){
			if ($len != $exact_len) {
				throw new Exception($string." string did not pass the lenght validation");
			}
		} else {
			if ($len < 1 || $len > 34){
				throw new Exception($string." string did not pass the lenght validation");
			}
		}
	}

	public static function url($url)
	{
		if (filter_var($url, FILTER_SANITIZE_URL) != $url){
			throw new Exception($string." string did not pass the validation");
		}
	}

	public static function long_string($string)
	{
		if (filter_var($string, FILTER_SANITIZE_STRING) != $string){
			throw new Exception($string." string did not pass the validation");
		}
		$len = strlen($string);
		if ($len < 1 || $len > 150){
			throw new Exception($string." string did not pass the lenght validation");
		}
	}

	public function exists($key)
	{
		return isset($this->f[$key]);
	}

	public function add_default($key, $val)
	{
		// NOTE this appends to f and it will stay there
		if (!isset($this->f[$key])){
			$this->f[$key] = $val;
		}
	}

	public function all()
	{
		foreach($this->f as $f){
			if (filter_var($f, FILTER_SANITIZE_STRING) != $f){
				throw new Exception($f." string did not pass the validation");
			}
		}

		return $this->f;
	}

	public function these(array $list)
	{
		if (empty($this->f)) {
			throw new Exception("Validation data pool is empty");
		}

		foreach($list as $e => $type){
			if (!isset($this->f[$e])){
				throw new Exception($e.' is not set in form');
			}
		}

		$r = [];

		foreach($list as $e => $type){

			switch($type){
				case 1: # INT
					$r[$e] = (int)$this->f[$e];
					break;
				case 2: # INT 0 not accepted
					$r[$e] = (int)$this->f[$e];
					if($r[$e] == 0){
						throw new Exception($e." can't be 0");
					}
					break;
				case 3: # SHORT STRING
					if (filter_var($e, FILTER_SANITIZE_STRING) != $e){
						throw new Exception($e." string did not pass the validation");
					}
					$len = strlen($this->f[$e]);
					if ($len < 1 || $len > 34){
						throw new Exception($e." string did not pass the lenght validation");
					}
					$r[$e] = $this->f[$e];
					break;
				case 4: # LONG STRING
					if (filter_var($e, FILTER_SANITIZE_STRING) != $e){
						throw new Exception($e." string did not pass the validation");
					}
					$len = strlen($this->f[$e]);
					if ($len < 1 || $len > 150){
						throw new Exception($e." string did not pass the lenght validation");
					}
					$r[$e] = $this->f[$e];
					break;
				case 5: # STRING WITH SPECIAL CHARS
					if (filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS) != $e){
						throw new Exception($e." string did not pass the validation");
					}
					$len = strlen($this->f[$e]);
					if ($len < 1 || $len > 20){
						throw new Exception($e." string did not pass the lenght validation");
					}
					$r[$e] = $this->f[$e];
					break;
				case 6: # IP v4
					if (filter_var($e, FILTER_FLAG_IPV4) != $e){
						throw new Exception($e." string did not pass the validation");
					}
					$r[$e] = $this->f[$e];
					break;
			}

			switch($e){
				case 'password':
					if ($len < 6){
						throw new Exception("Minimal password lenght is 6");
					}
					break;
			}
		}

		return $r;
	}

}