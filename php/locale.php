<?php

class Locale
{
	private $language='en';
	private $translate_arr=array();

	function __construct($realpath_public)
	{
		(isset($_COOKIE['lang'])) AND $this->language=$_COOKIE['lang'];
		(!array_key_exists($this->language, Config::$languages)) AND $this->language='en';
		include($realpath_public.'/lang/'.$this->language.'.php');
		$this->translate_arr=$lang;
	}

	public function get_lang()
	{
		return $this->language;
	}

	public function translate($phrase)
	{
		return (isset($this->translate_arr[$phrase])) ? $this->translate_arr[$phrase] : $phrase;
	}
}