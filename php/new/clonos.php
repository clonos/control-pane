<?php
session_start();
if(isset($_GET['section'])){changeSection();}
//$clonos_path='../../clonos/';

require_once($_real_path_php.'t.setup.php');
require_once($_real_path_php.'t.utils.php');
require_once($_real_path_php.'t.locale.php');
require_once($_real_path_php.'t.translate.php');
require_once($_real_path_php.'t.menu.php');
require_once($_real_path_php.'t.route.php');
require_once($_real_path_php.'t.user.php');
require_once($_real_path_php.'t.commands.php');
require_once($_real_path_php.'t.uri.php');
require_once($_real_path_php.'t.cmd.nas.php');


class ClonOS {
	use tUtils, tLocale, tTranslate, tMenu, tUser, tRoute, tCommands, tUri;
	use tSetup, tNAS;
	
	const filenameNASDisksList='/var/db/cixnas/api/disks.json';
	const filenameNASRaidsList='/var/db/cixnas/api/raids.json';
	const filenameNASEnginesList='/var/db/cixnas/api/raids_engine.json';
	
	const TRANSLATE_CACHE_DIR='translate.cache';
	const BACK_FOLDER_NAME='back';
	
	public static $workdir;
	public static $environment;
	public static $language='en';
	public static $default_lang='en';
	public static $server_name;
	public static $realpath;
	public static $realpath_php;
	public static $realpath_public;
	public static $realpath_pages;
	public static $realpath_dialogs;
	public static $realpath_assets;
	public static $realpath_page;
	public static $media_import;
	public static $json_name;
	public static $section;
	
	public $json_req;
	public $sys_vars;
	public $index_file;
	public $uri;
	public $uri1;
	
	
	public $config;
	
	private $_uri_chunks;
	private $_post;
	private $_db=null;
	private $_client_ip;
	private $_dialogs=[];
	private $_cmd_array=[
		'jcreate','jstart','jstop',
		'jrestart','jedit','jremove',
		'jexport','jimport','jclone',
		'jrename','madd','sstart',
		'sstop','projremove','bcreate',
		'bstart','bstop','brestart',
		'bremove','bclone','brename',
		'vm_obtain','removesrc','srcup',
		'removebase','world','repo','forms'
	];
	private $_vars=[];
	private $_db_tasks=null;
	private $_jname;
	private $_menu;
	private $_css=[];
	
	function __construct($realpath,$setup=false)
	{
		self::$realpath=$realpath.DIRECTORY_SEPARATOR;
		self::$realpath_php=self::$realpath.'php/new/';	### После завершения убрать new/
		self::$realpath_public=self::$realpath.'public/';
		self::$realpath_pages=self::$realpath_public.'pages/';
		self::$realpath_dialogs=self::$realpath_public.'dialogs/';
		self::$realpath_assets=self::$realpath.'assets/';
		self::$media_import=self::$realpath.'media_import/';
		
		if($setup===true) return;
		
		self::$workdir=getenv('WORKDIR'); # // /usr/jails
		self::$environment=getenv('APPLICATION_ENV');
		$this->lang_init();
		
		$this->_post=($_SERVER['REQUEST_METHOD']=='POST');
		$this->_vars=$_POST;

		$this->_client_ip=$_SERVER['REMOTE_ADDR'];
		
		$ures=$this->userAutologin();
		$this->sys_vars['authorized']=false;
		if($ures!==false){
			if(isset($ures['id']) && is_numeric($ures['id']) && $ures['id']>0){
				$this->_user_info=$ures;
				$this->_user_info['unregistered']=false;
				$this->sys_vars['authorized']=true;
			}else{
				$this->_user_info['unregistered']=true;
				if($this->json_req) exit;
			}
		}

		$this->config=new Config();
		if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME']))){
			self::$server_name=$_SERVER['SERVER_NAME'];
		} else {
			self::$server_name=$_SERVER['SERVER_ADDR'];
		}
		
		$section=1;
		if(isset($_SESSION['section']))
		{
			$section=$_SESSION['section'];
		}
		$this->_menu=$this->makeMenu($section);
		
		if($section==2)
		{
			$this->addCss('/css/nas.css');
		}

		if(empty($this->_menu))
		{
			echo 'В меню сайта ничего нет. Нужно это исправить!';
			exit;
		}

		$this->pages_ids=array_merge($this->pages_ids,array(
			# top menus
			'settings'=>0,
			'users'=>0,
			'shell'=>0,
			# system pages start
			'json'=>0,
			'download'=>0,
			'upload'=>0,
			'vnc'=>0,
			# system pages end
		));
		//echo '<pre>';print_r($this->pages_ids);exit;

		$this->get_uri_chunks();
		$_uri_chunks=$this->uri['path_chunks'];
//echo '<pre>';print_r($this->uri);exit;
		//$_uri_chunks=$this->get_uri_chunks();
		$this->_uri_chunks=$_uri_chunks;
		if(isset($_uri_chunks[0]))
		{
			self::$section=$_uri_chunks[0];
			$this->route($_uri_chunks[0]);
			//exit;
		}
		
		
/*
    [clonos/overview] => 3
    [clonos/jailscontainers] => 4
    [clonos/instance_jail] => 5
    [clonos/bhyvevms] => 6
    [clonos/vm_packages] => 7
    [clonos/vpnet] => 8
    [clonos/authkey] => 9
    [clonos/media] => 10
    [clonos/imported] => 11
    [clonos/bases] => 13
    [clonos/sources] => 14
    [clonos/tasklog] => 15
    [clonos/sqlite] => 16
    [settings] => 0
    [users] => 0
    [shell] => 0
*/
		//echo $_SERVER['REQUEST_URI'].'<br>';
		//echo '<pre>'.print_r($this->pages_ids,true);exit;
		
		if(isset($_uri_chunks[0]))
		{
			if(isset($_uri_chunks[1]))
			{
				$check_uri=$_uri_chunks[0].'/'.$_uri_chunks[1];
			}else{
				$check_uri=$_uri_chunks[0];
			}
		}else{
			$check_uri='';
		}
		//echo $check_uri;exit;
		
		//if(!isset($_uri_chunks[0]) || !isset($_uri_chunks[1]))
		if(!isset($this->pages_ids[$check_uri]))
		{
			$tmp=array_first($this->menu_tree);
			//$rlpath=$tmp['default'];
			$rlpath=$this->uri['default'];
			if(empty($rlpath)){echo 'empty path!'; exit;}
			
			if(isset($rlpath))
			{
				if($this->uri['need_reload'])
				{
					header('Location: /'.$tmp['default'].'/');
					//echo '<br>Location: /'.$rlpath.'/';
					//echo '<pre>';print_r($this->uri);
				}
			}else{
				header("HTTP/1.1 404 Not Found");
			}
			exit;
		}
		
		if(self::$environment=='development'){
			$sentry_file=self::$realpath_php.'sentry.php';
			if(file_exists($sentry_file)) include($sentry_file);
		}

	}
	
	function route($chunk)	// t.route.php
	{
		switch($chunk)
		{
			case 'json':
				$this->route_json();exit;
				break;
			case 'donwload':
				$this->route_download();exit;
				break;
			case 'upload':
				$this->route_upload();exit;
				break;
			case 'vnc':
				$this->route_vnc();exit;
				break;
		}
	}
	
	function addCss($css)
	{
		$this->_css[]='	<link type="text/css" href="'.$css.'" rel="stylesheet" />';
	}
	function putCss()
	{
		echo(join("\n",$this->_css)."\n");
	}
	
	function start()
	{
		$res=$this->translateF('index','','index.tpl');
		
		if(isset($res['error']))
		{
			if($res['error'])
			{
				echo $res['message'];
				exit;
			}
		}
		
		$this->uri1=$this->_uri_chunks[0];
		if(isset($this->_uri_chunks[1]) && !empty($this->_uri_chunks[1]))
		{
			$this->uri1.='/'.$this->_uri_chunks[1];
		}
		//$this->uri1=$this->_uri_chunks[0].'/'.$this->_uri_chunks[1];
		$file=$this->get_translated_filename();
		//echo $file;exit;
		if(file_exists($file))
		{
			$this->index_file=self::$realpath_page.'index.php';
			//echo $this->index_file;exit;
			//echo '<br>include: '.$file;
			include($file);
		}else{
			echo "Index file not found! file: ".__FILE__.", line: ".__LINE__;
		}
	}
	
}

require_once($_real_path_php.'cbsd.php');
require_once($_real_path_php.'config.php');
require_once($_real_path_php.'db.php');
require_once($_real_path_php.'forms.php');
require_once($_real_path_php.'dialogs.gen.php');

function changeSection()
{
	$section=$_GET['section'];
	if(is_numeric($section))
	{
		$_SESSION['section']=$section;
		$req=$_SERVER['REQUEST_URI'];
		$pat='#(\?|&)section=[\d]+#i';
		$req=preg_replace($pat,'',$req);
		header('Location: '.$req);
		exit;
	}

}
