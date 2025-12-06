<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>ClonOS — <?php #echo $menu->title; ?></title>
	<link href="/images/favicon.ico?" rel="shortcut icon" type="image/x-icon" />
	<script src="/js/jquery.js" type="text/javascript"></script>
	<script src="/js/clonos.js" type="text/javascript"></script>
	<script src="/js/dmuploader.js" type="text/javascript"></script>
	<script src="/js/smoothie.js" type="text/javascript"></script>
	<script src="/js/noty/packaged/jquery.noty.packaged.min.js" type="text/javascript"></script>
	<link type="text/css" href="/css/reset.css" rel="stylesheet" />
	<link type="text/css" href="/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/font/clonos.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
	<script src="/lang/<?php echo self::$language; ?>.js" type="text/javascript"></script>
	<style type="text/css">html{background-color:#aaa;} .hide{display:none;}</style>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<script type="text/javascript">
		_first_start=true;
		err_messages={add:function(arr){for(n in arr){err_messages[n]=arr[n];}}};
		<?php if(isset($user_info_txt)) echo $user_info_txt; ?>
	</script>
</head>
<script type="text/javascript">
	try{
		var theme=localStorage.getItem('Theme') || 'light';
		var cs=['light','dark'];
		for(c=0,cl=cs.length;c<cl;c++)
		{
			var css=cs[c];
			var disabled=(theme==css)?'':' disabled="disabled"';
			var hcss=$('<link rel="stylesheet" href="/css/themes/'+css+'.css" id="'+css+'" class="alternate"'+disabled+'>');
			$('head').append(hcss);
			$('#'+css).get(0).disabled=(theme!=css);
		}
	}catch(e){}
</script>
<body class="gadget1 login <?php echo $this->uri1;?>">

<main>
<div class="main"><div id="content">
<div id="ctop">
<?php
//echo $this->index_file;exit;
if(file_exists($this->index_file)){
	$this->translateF('pages',$this->uri1,'index.php');
	$incfile=$this->get_translated_filename();
//	echo $incfile;
//	echo "Переделать путь";
	include($incfile);	//$this->index_file
} else {
	echo '<h1><span id="trlt-43">Not implemented yet</span>!</h1>';
}
$this->placeDialogs();
?>
</div>
<div id="cdown"><span class="split-close"></span>
<div id="cinfo">
		<div class="left">
			<dl id="summaryInfo">
				<dt>Имя клетки:</dt>
				<dd>Jail1</dd>
			</dl>
		</div>
		<div class="right">
			<h2><span id="trlt-44">CPU usage</span>, %:</h2>
			<div class="graph v-black g--summary-cpu l-cpu"></div>
			<br />
			<h2><span id="trlt-45">Memory usage</span>, %:</h2>
			<div class="graph v-black g--summary-mem l-mem"></div>
			<br />
			<h2><span id="trlt-46">I/O storage</span>, iops:</h2>
			<div class="graph v-black g--summary-iops l-read,write pr-no te-iops"></div>
			<br />
			<h2><span id="trlt-46">I/O storage</span>, bit per seconds:</h2>
			<div class="graph v-black g--summary-bps l-read,write pr-no te-bps"></div>
		</div>
</div>
</div>
</div></div>
</main>

<div class="menu">
<div id="menu">
	<div class="closer"></div>
<?php
//echo $menu->html;
?>
	<div id="console"></div>
</div>
</div>

<header>
	<div class="top-right">
		<span class="txt">
			<input type="checkbox" name="trlt" id="trlt-chk" onchange="clonos.trltOn();" /><label for="trlt-chk"> Translate ON</label>
			<span class="space"></span>
			<a href="https://www.patreon.com/clonos" target="_blank"><span id="trlt-47">DONATE</span></a>
			<span class="space"></span>
			<span id="trlt-48">VERSION</span>: <?php file_get_contents(self::$realpath.'version'); ?>
			<span class="space"></span>
			<span id="trlt-49">THEMES</span>:
		</span>
		<span class="ch_theme">
			 <span class="light"></span><span class="dark"></span>
		</span>
	</div>
	<div class="header">
	<span id="title"><?php #echo $menu->title; ?></span>
	<ul>
		<li class="mhome"><a href="/">Home</a></li>
<?php // if($clonos->environment=='development') { ?>
		<li><a href="/settings/"><span id="trlt-50">Settings</span></a></li>
<?php // } ?>
		<li><a href="/users/"><span id="trlt-51">Users</span></a></li>
		<li><a target="_blank" href="/shell/">&gt;&gt;<span id="trlt-52">Console</span></a></li>
<!--
		<li><a href="/profile/"><span id="trlt-53">Profile</span></a></li>
		<li><a href="/support/"><span id="trlt-54">Support</span></a></li>
-->
		<li><a name="">
			<select id="lng-sel">
<?php
foreach(Config::$languages as $lng=>$lngname){
	$sel = (self::$language==$lng) ? ' selected="selected"' : '';
	echo '				<option value="'.$lng.'"'.$sel.'>'.$lngname.'</option>'.PHP_EOL;
}
?>
			</select>
		</a></li>
		<li><a onclick="clonos.logout();" class="link" id="user-login"><?php echo $this->_user_info['username']; ?></a></li>
	</ul>
	</div>
</header>

<div class="login-area<?php if(!$this->_user_info['error']) echo ' hide'; ?>"><?php echo $this->placeDialogByName('system-login'); ?>
	<div class="ccopy">ClonOS — is a powerfull system for&hellip;</div>
	<div class="ccopy">Cloud computing, Lightweight containerization, Virtualization, etc&hellip;</div>
</div>

<div class="spinner"></div>
<div class="online icon-online" id="net-stat" onclick="ws_debug();"></div>
</body>
</html>