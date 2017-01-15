<!DOCTYPE html>
<?php
$_REALPATH=realpath('../');
include($_REALPATH.'/php/clonos.php');
$clonos=new ClonOS($_REALPATH);

$lang=$clonos->getLang();
$uri=trim($_SERVER['REQUEST_URI'],DIRECTORY_SEPARATOR);
$root=trim($_SERVER['DOCUMENT_ROOT'],DIRECTORY_SEPARATOR);
$_ds=DIRECTORY_SEPARATOR;
$file_path=$_ds.$root.$_ds.'pages'.$_ds.$uri.$_ds;
$file_name=$file_path.$lang.'.index.php';
$json_name=$file_path.'a.json.php';

if(empty($uri))
{
	$key=$clonos->menu->first_key;
	header('Location: /'.$key.'/',true);
	exit;
}

error_reporting(E_ALL);
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>ClonOS — <?php echo $clonos->menu->title; ?></title>
	<link href="/images/favicon.ico?" rel="shortcut icon" type="image/x-icon" />
	<script src="/js/jquery.js" type="text/javascript"></script>
	<script src="/js/clonos.js" type="text/javascript"></script>
	<script src="/js/noty/packaged/jquery.noty.packaged.min.js" type="text/javascript"></script>
	<link type="text/css" href="/css/reset.css" rel="stylesheet" />
	<link type="text/css" href="/css/styles-old.css" rel="stylesheet" />
	<link type="text/css" href="/font/clonos.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
	<script src="/lang/<?php echo $lang; ?>.js" type="text/javascript"></script>
	<style type="text/css">html{background-color:#aaa;} .hide{display:none;}</style>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<nav id="nav">
	<span id="title"><?php echo $clonos->menu->title; ?></span>
	<ul>
		<li class="mhome"><a href="/">Home</a></li>
		<li><a href="/settings/"><?php echo $clonos->translate('Settings'); ?></a></li>
		<li><a href="/profile/"><?php echo $clonos->translate('Profile'); ?></a></li>
		<li><a href="/support/"><?php echo $clonos->translate('Support'); ?></a></li>
		<li><a name="">
			<select id="lng-sel">
<?php
$_languages=$clonos->config->languages;
if(isset($_languages))foreach($_languages as $lng=>$lngname)
{
	if($lang==$lng) $sel=' selected="selected"'; else $sel='';
	echo '				<option value="'.$lng.'"'.$sel.'>'.$lngname.'</option>'.PHP_EOL;
}
?>
			</select>
		</a></li>
<!--		<li><a href="">...</a></li>	-->
	</ul>
</nav>

<div id="menu">
<?php
echo $clonos->menu->html;
?>
</div>

<main><div id="content">
<?php
/*
    [HTTP_ACCEPT_LANGUAGE] => ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4
    [HTTP_USER_AGENT] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36
    [REMOTE_ADDR] => 79.173.124.136
    [DOCUMENT_ROOT] => /usr/home/web/cp/clonos/public
    [DOCUMENT_URI] => /index.php
    [REQUEST_URI] => /overview/
    [REQUEST_METHOD] => GET
    [SCRIPT_FILENAME] => /usr/home/web/cp/clonos/public/index.php
    [WORKDIR] => /usr/jails
    [APPLICATION_ENV] => production
*/
if(file_exists($file_name)) include($file_name); else
{
	echo '<h1>'.$clonos->translate('File not found').'!</h1>';
}
$clonos->placeDialogs();
?>	
</div></main>

</body>
</html>