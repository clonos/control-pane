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
	public $title='';	//'Error';
	public $first_key=array();
	private $_public_pages_path='';

	function __construct(Localization $lang, $uri_chunks, $public_pages_path)
	{
		$menu_config = Config::$menu;
		$this->first_key = array_key_first($menu_config);
		$this->_public_pages_path=$public_pages_path;
		$titles=[];
		
		$translate_path=$this->_public_pages_path.'_translate.cache/';
		$translate_file=$translate_path.$lang->get_lang().'.menu.php';
		#echo $translate_path."\n";
		#echo getcwd()."\n";

		if(!is_dir($translate_path))
		{
			mkdir($translate_path);
		}
		
		if(file_exists($translate_file))
		{
			$this->html=file_get_contents($translate_file);
			
			if(getenv('APPLICATION_ENV') != 'development')
			{
				$this->html=preg_replace('#\t<li><a href="/sqlite/".+</li>\n#','',$this->html);
			}
			
			return;
		}

//var_dump($this->_public_pages_path);exit;

		if(getenv('APPLICATION_ENV') != 'development'){
			unset($menu_config['sqlite']);
		}

		$this->html='	<ul class="menu">'.PHP_EOL;

		//$qstr=trim($_SERVER['REQUEST_URI'],'/');
		$qstr='';
		if(isset($uri_chunks[0])){
			$qstr=trim($uri_chunks[0],'/');
		}
		foreach($menu_config as $link=>$val){
			$mname=$lang->translate($val['name']);
			$mtitle=$lang->translate($val['title']);
			$titles[$link]=$mtitle;
			$sel='';
			if($qstr==$link){
				$sel=' class="sel"';
				$this->title=$mtitle;	//$_TITLE
			}

			$icon='empty';
			if(isset($val['icon']) && !empty($val['icon'])) $icon=$val['icon'];
			$span='<span class="'.$icon.'"></span>';
			$this->html.='		<li><a href="/'.$link.'/" title="'.$mtitle.'"'.$sel.'>'.$span.'<span class="mtxt">'.$mname.'</span></a>';
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
				$this->html.= '			</ul>'.PHP_EOL.'	';
			}
			$this->html.= '</li>'.PHP_EOL;
		}

		$this->html.='	</ul>'."\n";

		/*
		if($this->title=='Error'){
			$other_titles = Config::$other_titles;
			if(isset($other_titles[$qstr])){
				$this->title=$lang->translate($other_titles[$qstr]);
			}
		}
		*/
		$other_titles = Config::$other_titles;
		$titles=array_merge($titles,$other_titles);
		
		$this->html.='	<script type="text/javascript">'.PHP_EOL;
		$this->html.="\t\tvar page_titles=".json_encode($titles)."\n";
		$this->html.='	</script>'.PHP_EOL;

		file_put_contents($translate_file,$this->html);
		
	}
}
