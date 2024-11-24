<?php
if(isset($_GET['file'])){
	$file=$_GET['file'];
	$filename=$file;
}else{
	header('HTTP/1.0 404 Not Found');
	exit;
}

$res=$clonos->userAutologin();

if(isset($res['id']) && $res['id']>0){

	$file=$clonos->media_import.$file;

	header('Content-disposition: attachment; filename='.$filename);
	header('Content-type: application/octet-stream');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: '.filesize($file));
	header("Pragma: no-cache");
	header("Expires: 0");

	$chunkSize = 1024 * 1024;
	$handle = fopen($file, 'rb');
	while (!feof($handle))
	{
		$buffer = fread($handle, $chunkSize);
		echo $buffer;
		ob_flush();
		flush();
	}
	fclose($handle);

	exit;
}

header('HTTP/1.1 401 Unauthorized');
exit;