<?php
if(isset($_COOKIE['lang']))
    $lang=$_COOKIE['lang'];
if(empty($lang)) {
    $lang='en';
    setcookie("lang", $lang, time()+3600, "/", "samson.bsdstore.ru", 1);
}
require_once('cbsd.inc.php');
?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/bootstrap/favicon.ico">

    <script src="/js/lang.js" type="text/javascript"></script>
    <script src="/js/misc.js" type="text/javascript"></script>
    <script src="/js/lang/<?php echo $lang; ?>.js" type="text/javascript"></script>

    <title>ClonOS: Free Hosting Platform</title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="/bootstrap/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">
    
    <!-- xajax section-->

    <script type="text/javascript">
	reloadPage = function() {
	    window.location.reload();
	}
    </script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="/bootstrap/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/bootstrap/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
