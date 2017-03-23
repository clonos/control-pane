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
	public $name='';
	public $title='Error';
	public $path='';
	public $first_key=array();
	
	function __construct($menu_config=array(),$parent)
	{
		if(!empty($menu_config))
		{
			reset($menu_config);
			$this->first_key=key($menu_config);
		}
		
		$this->html='<ul class="menu">'.PHP_EOL;

		//$qstr=trim($_SERVER['REQUEST_URI'],'/');
		$qstr=trim($parent->uri_chunks[0],'/');
		$this->path=$qstr;	//$_MENU_PATH
		if(!empty($menu_config))foreach($menu_config as $key=>$val)
		{
			$mname=$parent->translate($val['name']);
			$mtitle=$parent->translate($val['title']);
			
			$link=$key;
			$sel='';
			if($qstr==$key){
				$sel=' class="sel"';
				$this->title=$mtitle;	//$_TITLE
				$this->name=$mname;		//$_MENU_NAME
			}
			
			$icon='empty';
			if(isset($val['icon']) && !empty($val['icon'])) $icon=$val['icon'];
			$span='<span class="'.$icon.'"></span>';
			$this->html.='	<li><a href="/'.$link.'/" title="'.$mtitle.'"'.$sel.'>'.$span.'<span class="mtxt">'.$mname.'</span></a>';
			if(!empty($val['submenu']))
			{
				$this->html.= PHP_EOL.'		<ul class="submenu">'.PHP_EOL;
				foreach($val['submenu'] as $k=>$s)
				{
					$sname=$parent->translate($s['name']);
					$stitle=$parent->translate($s['title']);
					
					$slink=$link.'/'.$k;
					$sl=$link.'_'.$k;
					$ssel='';
					if($qstr==$sl){
						$ssel=' class="sel"';
						$this->title=$stitle;
						$this->name=$sname;
					}
					$this->html.= '			<li><a href="/'.$slink.'/" title="'.$stitle.'"'.$ssel.'>'.$sname.'</a></li>'.PHP_EOL;
				}
				$this->html.= '		</ul>'.PHP_EOL.'	';
			}
			$this->html.= '</li>'.PHP_EOL;
		}

		$this->html.='</ul>';
		
		if($this->title=='Error')
		{
			if(isset($parent->config->other_titles[$qstr]))
				$this->title=$parent->translate($parent->config->other_titles[$qstr]);
		}
	}
}
