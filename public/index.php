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
	<link type="text/css" href="/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/font/clonos.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
	<script src="/lang/<?php echo $lang; ?>.js" type="text/javascript"></script>
	<style type="text/css">html{background-color:#aaa;} .hide{display:none;}</style>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body class="gadget1">

<main><div class="main"><div id="content">
<?php
if(file_exists($file_name)) include($file_name); else
{
	echo '<h1>'.$clonos->translate('File not found').'!</h1>';
}
$clonos->placeDialogs();
?>
</div></div></main>

<div class="menu"><div id="menu">
	<div class="closer"></div>
<?php
echo $clonos->menu->html;
?><div id="console"></div>
</div></div>

<header><div class="header">
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
	</ul>
</div></header>


</body>
</html>