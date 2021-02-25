<?php

/**
*  TPL 1.0 - a RainTpl fork
*  --------
*  maintained by Momchil Bozhinov (momchil@bojinov.info)
*  ------------
*/

class Tpl {

	public $vars = [];

	protected $config = [
		'charset' => 'UTF-8',
		'tpl_dir' => '../templates/',
		'cache_dir' => '../cache/',
		'auto_escape' => true,
		'remove_comments' => false,
		'production_ready' => false
	];

	public function configure($my_conf)
	{
		(!is_array($my_conf)) AND die("Invalid config");

		foreach ($my_conf as $my=>$val){
			if (isset($this->config[$my])){
				$this->config[$my] = $val;
			}
		}
	}

	/**
	* Draw the template
	*
	* @param string $filePath: name of the template file
	* @param bool $returnString: if the method should return a string or echo the output
	*
	* @return void, string: depending of the $returnString
	*/
	public function draw($filePath, $returnString = FALSE)
	{
		extract($this->vars);
		ob_start();

		$fileName = basename($filePath);
		$filePathCached = $this->config['cache_dir'] . $fileName . ".stpl.php";

		if (!$this->config['production_ready']){ # in case all is already cached
			// set paths
			$filePath = $this->config['tpl_dir'] . $fileName . '.html';
			// The results of this function are cached
			$fileTime = (int)@filemtime($filePath);
			$fileTimeCached = (int)@filemtime($filePathCached);

			// Check if template exists (although there are other reasons for this to be false)
			if ($fileTime == 0) {
				die('Template ' . $fileName . ' not found!');
			}

			// Compile the template if the original has been updated 
			if ($fileTimeCached == 0 || $fileTimeCached < $fileTime) {
				require_once("Parser.php");
				$html = (new Parser($this->config))->compileFile($filePath);
				$html = str_replace("?>\n", "?>\n\n", $html);
				$ok = file_put_contents($filePathCached, $html);
				if ($ok === false) {
					die("Cache is not writable");
				}
			}
		}

		require $filePathCached;
		$output = ob_get_clean();

		if ($returnString){
			return $output;
		} else {
			echo $output;
		}
	}

	public function assign($variable, $value)
	{
		if (is_array($variable)){
			$this->vars = $variable + $this->vars;
		} else {
			$this->vars[$variable] = $value;
		}
	}

	public function assign_my_defines()
	{
		$defines = get_defined_constants(true);
		if (isset($defines['user'])){
			foreach($defines['user'] as $variable => $value){
				$this->vars[$variable] = $value;
			}
		}
	}

}

?>