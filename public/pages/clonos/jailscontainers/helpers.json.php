<?php
$hash = $this->url_hash;
if(empty($hash)){
#	Узнаём список хелперов
	$jails_helpers = [];
	$db = new Db('clonos');
	if (!$db->error){
		$res = $db->select("select module from jails_helpers_list", []);
		if(!empty($res)){
			foreach($res as $r){
				$jails_helpers[] = $r['module'];
			}
		}
	}
	$lst = [];
	foreach($jails_helpers as $helper){
		$db = new Db('helper', ['jname' => $jail_name, 'helper' => $helper]);
		if(!$db->error)	// !error — значит хелпер установлен
		{
			$res = $db->selectOne("select longdesc from system", []);
			if(isset($res['longdesc'])){
				$description = $res['longdesc'];
			} else {
				$description = $this->translate('no data').'&hellip; ('.$file_name.')';
			}
			$lst[] = ['helper' => $helper, 'description' => $description];
		} else {
			$hlst[] = $helper;
		}
	}

	$html = '';
	$html_tpl = '';
	$empty_logo = '/images/logo/empty.png';
	$hres = $this->getTableChunk('helpers','tbody');
	if($hres !== false){
		$html_tpl = $hres[1];
	}
	if(!empty($lst) && !empty($html_tpl)){
		foreach($lst as $item){
			$tpl = $html_tpl;
			$logo_file = 'images/logo/'.$item['helper'].'.png';
			$logo = file_exists($this->realpath_public.$logo_file) ? '/'.$logo_file : $empty_logo;
			$vars = [
				'nth-num' => 'nth0',
				'logo' => $logo,
				'name' => $item['helper'],
				'description' => $item['description'],
				'opentitle' => $this->translate('Open')
			];

			foreach($vars as $var => $val){
				$tpl = str_replace('#'.$var.'#', $val, $tpl);
			}
			$html .= $tpl;
		}
	} else {
		$html = '<tr><td colspan="3">'.$this->translate('No installed helpers').'</td></tr>';
	}

	// Определяем список хелперов, доступных для установки в клетку
	$helpers_list_html = '<ul class="helpers-list">';
	foreach($hlst as $item){
		$logo_file = 'images/logo/'.$item.'.png';
		$logo = file_exists($this->realpath_public.$logo_file) ? '/'.$logo_file : $empty_logo;
		$helpers_list_html .= '<li><input type="checkbox" name="'.$item.'" id="'.$item.'"><label for="'.$item.'"><img src="'.$logo.'" />&nbsp; '.$item.'</label></li>';
	}
	$helpers_list_html .= '</ul>';
	$html = str_replace(["\n","\r","\t"], '', $html);

	$included_result_array = [
		'tbody' => $html,
		'error' => false,
		'func' => 'fillTable',
		'id' => 'helperslist',
		'helpers_list' => $helpers_list_html
	];
	return;
} else {
#	Открываем настройки хелпера
	$db = new Db('helper', ['jname' => $jail_name, 'helper' => $hash]);
	if($db->error){
		$included_result_array = ['error' => true,'errorMessage' => 'No helper database!'];
		return;
	}

	$db_path = $db->getFileName();
	$res_html = (new Forms($jail_name, $hash, $db_path))->generate();
	$res_html = '<h1>'.$this->translate('Helper settings: '.$hash).'</h1>'.$res_html;
}

$included_result_array = [
	'html' => $res_html,
	'func' => 'fillTab'
];