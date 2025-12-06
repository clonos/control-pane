<?php
trait tLocale {
	
	function lang_init()
	{
		if(isset($_COOKIE['lang']))
		{
			self::$language=$_COOKIE['lang'];
			return;
		}else{
			$tmplang=isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'en';
			self::$default_lang=explode(',',$tmplang)[0];
			self::$language=self::$default_lang;
		}
		
		//setcookie('lang',$memory_hash,time()+1209600);
	}
	
	function get_lang()
	{
		return self::$language;
	}
}