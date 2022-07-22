<?php

class Utils
{

	public static function clonos_syslog($msg)
	{
		file_put_contents('/tmp/clonos.log', date("j.n.Y").":".$msg . "\n", FILE_APPEND);
		return 0;
	}


	public static function gen_uri_chunks($uri)
	{
		$uri_chunks = [];
		if(!empty($uri)){
			$str=str_replace('/index.php','',$uri);
			$uri_chunks=explode('/',$str);
		}else if(isset($_POST['path'])){
			$str=trim($_POST['path'],'/');
			$uri_chunks=explode('/',$str);
		}
		return $uri_chunks;
	}
}