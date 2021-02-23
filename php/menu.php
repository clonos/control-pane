<?php
/*
	'news'=>array(
		'name'=>'Новости',
		'title'=>'Новости сети',
	),
	'connect'=>array(
		'name'=>'Подключение к сети',
		'title'=>'Подключитесь к сети прямо сейчас!',
		'submenu'=>array(
			'map'=>array(
				'name'=>'Зона обслуживания',
				'title'=>'Зона обслуживания абонентов',
			),
			'wifi'=>array(
				'name'=>'Wi-Fi зоны',
				'title'=>'Бесплатные Wi-Fi зоны г. Кириши',
			),
			'docs'=>array(
				'name'=>'Документы',
				'title'=>'Документы'
			)
		)
	),
*/

class Menu
{
	public $html=array();
	public $title='Error';
	public $first_key=array();

	function __construct($_REALPATH,$uri)
	{
		$realpath_public=$_REALPATH.'/public/'; # /usr/home/web/cp/clonos/public/
		$lang = new Locale($realpath_public);
		$menu_config = Config::$menu;
		$this->first_key = array_key_first($menu_config);

		if(getenv('APPLICATION_ENV') != 'development'){
			unset($menu_config['sqlite']);
		}

		$this->html='<ul class="menu">'.PHP_EOL;

		//$qstr=trim($_SERVER['REQUEST_URI'],'/');
		$qstr='';
		$uri_chunks=Utils::gen_uri_chunks($uri);
		if(isset($uri_chunks[0])){
			$qstr=trim($uri_chunks[0],'/');
		}
		if(!empty($menu_config))foreach($menu_config as $link=>$val){
			$mname=$lang->translate($val['name']);
			$mtitle=$lang->translate($val['title']);
			$sel='';
			if($qstr==$link){
				$sel=' class="sel"';
				$this->title=$mtitle;	//$_TITLE
			}

			$icon='empty';
			if(isset($val['icon']) && !empty($val['icon'])) $icon=$val['icon'];
			$span='<span class="'.$icon.'"></span>';
			$this->html.='	<li><a href="/'.$link.'/" title="'.$mtitle.'"'.$sel.'>'.$span.'<span class="mtxt">'.$mname.'</span></a>';
			if(!empty($val['submenu'])){
				$this->html.= PHP_EOL.'		<ul class="submenu">'.PHP_EOL;
				foreach($val['submenu'] as $k=>$s){
					$sname=$lang->translate($s['name']);
					$stitle=$lang->translate($s['title']);

					$slink=$link.'/'.$k;
					$sl=$link.'_'.$k;
					$ssel='';
					if($qstr==$sl){
						$ssel=' class="sel"';
						$this->title=$stitle;
					}
					$this->html.= '			<li><a href="/'.$slink.'/" title="'.$stitle.'"'.$ssel.'>'.$sname.'</a></li>'.PHP_EOL;
				}
				$this->html.= '		</ul>'.PHP_EOL.'	';
			}
			$this->html.= '</li>'.PHP_EOL;
		}

		$this->html.='</ul>';

		if($this->title=='Error'){
			$other_titles = Config::$other_titles;
			if(isset($other_titles[$qstr])){
				$this->title=$lang->translate($other_titles[$qstr]);
			}
		}
	}
}
