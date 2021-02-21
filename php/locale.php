<?php

class Locale()
{
	public $language='en';
	public $translate_arr=array();

	function __construct($realpath_public)
	{
		(isset($_COOKIE['lang'])) $this->language=$_COOKIE['lang'];
		(!array_key_exists($this->language, Config::languages)) $this->language='en';
		include($realpath_public.'/lang/'.$this->language.'.php');
		$this->translate_arr=$lang;
	}

	public function get_lang()
	{
		return $this->language;
	}

	public function get_available_languages()
	{
		return Config::languages;
	}

	public function translate($phrase)
	{
		if(isset($this->translate_arr[$phrase])) return $this->translate_arr[$phrase];
		return $phrase;
	}
}