<?php

class Utils
{
	public static gen_uri_chunks($uri)
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