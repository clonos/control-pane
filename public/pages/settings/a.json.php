<?php
//require_once("../php/cbsd.php");

$html = '';
$hres = $this->getTableChunk('components','tbody');

$res=CBSD::run('/root/bin/web_upgrade listjson', array());
$latest=[];
if($res['retval']==0)
{
	if(!$res['error'])
	{
		$arr=json_decode($res['message'],true);
		if(!empty($arr) && isset($arr['installed']))
		{
			foreach($arr['installed'] as $cmp=>$ver)
			{
				$html_tpl = $hres[1];
				$html_tpl = str_replace('#component#', $cmp, $html_tpl);
				$html_tpl = str_replace('#version#', $ver, $html_tpl);
				$html_tpl = str_replace('#id#', 'updlist_'.$cmp, $html_tpl);
				$html.=$html_tpl;
			}
		}
		if(isset($arr['latest']))
			$latest=$arr['latest'];
	}
	
/*	
	$lst=explode("\n",$res['message']);
	$n=0;
	if(!empty($lst)) foreach($lst as $item)
	{
		$html_tpl1 = $hres[1];
		list($component, $version) = explode(":", $item);
//		printf("{$component} - {$version}<br>\n");
		$vars = [
			'component' => $component,
			'version'   => $version,
		];

		foreach($vars as $var => $val){
			$html_tpl1 = str_replace('#'.$var.'#', $val, $html_tpl1);
		}
		$html .= $html_tpl1;
	}
*/
}

$included_result_array = [
	'tbody' => str_replace(["\n","\r","\t"], '', $html),
	'error' => false,
	'func' => 'fillTable',
	'id' => 'update_files',
	'latest' => $latest
];
