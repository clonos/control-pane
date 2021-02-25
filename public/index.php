<?php

require_once('../php/clonos.php');
require_once('../tpl/Tpl.php');

function get_title($menu_config, $uri_chunks)
{
	global $locale;

	$title = 'Error';
	$qstr = '';
	if(isset($uri_chunks[0])){
		$qstr = trim($uri_chunks[0],'/');
	}

	foreach($menu_config as $link => $val){
		if($qstr == $link){
			$title = $locale->translate($val['title']);
		}
	}

	if($title == 'Error'){
		if(isset(Config::$other_titles[$qstr])){
			$title = $locale->translate($other_titles[$qstr]);
		}
	}

	return $title;
}

$uri = trim($_SERVER['REQUEST_URI'],'/');
$chunks = Utils::gen_uri_chunks($uri);
$menu_config = Config::$menu;

$clonos = new ClonOS($chunks);
$locale = new Localization();
$tpl = new Tpl();
$translate = function($word)
{
	global $locale;
	return $locale->translate($word);
};
$tpl->assign("translate", $translate);

$isDev = (getenv('APPLICATION_ENV') == 'development');
if($isDev){
	unset($menu_config['sqlite']);
}

if(isset($_GET['upload'])){
	include('upload.php');
	CBSD::register_media($path,$file,$ext);
	exit;
}
if(isset($_GET['download'])){
	include('download.php');
	CBSD::register_media($path,$file,$ext);
	exit;
}

$lang = $locale->get_lang();
$_ds = DIRECTORY_SEPARATOR;
$root = trim($_SERVER['DOCUMENT_ROOT'], $_ds);

if(!empty($chunks)) $uri = $chunks[0];

$file_path = $_ds.$root.$_ds.'pages'.$_ds.$uri.$_ds;
$file_name = $file_path.$lang.'.index.php';
$json_name = $file_path.'a.json.php';

if(empty($uri)){
	header('Location: /'.array_key_first($menu_config).'/',true);
	exit;
}

$title = get_title($menu_config, $chunks);

$user_info = $clonos->userAutologin();
if($user_info['error']){
	$user_info['username']='guest';
}

$tpl->assign([
	"user_info" => $user_info,
	"title" => $title,
	"uri" => $uri,
	"lang" => $lang
]);
$tpl->draw("index.1");

if(file_exists($file_name)){
	include($file_name);
} else {
	echo '<h1>'.$locale->translate('Not implemented yet').'!</h1>';
}
$clonos->placeDialogs();

$menu_active='';
if(isset($chunks[0])){
	$menu_active=trim($chunks[0],'/');
}

$tpl->assign([
	"menu_active" => $menu_active,
	"menu_conf" => $menu_config,
	"version" => Config::$version,
	"isDev" => $isDev,
	"langs" => Config::$languages,
]);
$tpl->draw("index.2");