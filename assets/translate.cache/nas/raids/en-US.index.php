<h1>RAIDS</h1>

<?php



$db = new Db('file','/zmirror/jails/formfile/dsk.sqlite');
if($db->error){
	echo 'DB not found';
	return;
}

$db_path = $db->getFileName();
$res_html = (new DialogsGen($db_path))->generate();
//$res_html = '<h1>'.$this->translate('Helper settings: '.$hash).'</h1>'.$res_html;
echo $res_html;