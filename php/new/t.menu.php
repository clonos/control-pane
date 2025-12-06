<?php
trait tMenu {
	
	private $menu_arr;
	private $html;
	private $titles=[];
	
	private $menu_tree;
	private $childs;
	private $pages_ids;
	private $parents_arr;
	private $parents_groups;
	private $not_ends;
	private $ends;
	private $defaults;
	private $default_index;
	
	function makeMenu($section=1)
	{
		$db=new Db('clonos');
		if(!$db->isConnected())	return array('error'=>true,'error_message'=>'db connection lost!');
		
		if(isset($dbres['error']) && $dbres['error'])
		{
			echo $dbres['error_message'];
			exit;
		}
		//
		$dbres=$db->select("select id,parent_id,link,name,title,icon,sort_num,devmode from menu where section=? and visible=true order by sort_num asc",[
			[$section,PDO::PARAM_INT]
		]);

//		$dbres=$db->select("select id,parent_id,link,name,title,icon,sort_num,devmode from menu where visible=true order by sort_num asc",[]);
		
		if(isset($dbres['error']) && $dbres['error'])
		{
			echo $dbres['info'];
			exit;
		}
/*
		$test=[];
		$test[1]=[
			'start'=>'<div>',
			'middle'=>'hello',
			1=>[
				'start'=>'<b>',
				'middle'=>'hi',
				'end'=>'</b>',
			],
			2=>[
				'start'=>'<i>',
				'middle'=>'wow',
				'end'=>'</i>',
			],
			'end'=>'</div>',
		];
		
		echo '<pre>';	//print_r($test);
		array_walk_recursive($test, function($item,$key){echo $item."\n";});
		
		//echo implode('',$test);
		exit;
*/
/*
		array_walk_recursive($this->menu_tree, function($item,$key){
			print_r($key);
			echo " - ";
			print_r($item);
			echo "\n";
			//echo $item."\n";
			//exit;
		});
*/



	#$dbres[5]['parent_id']=3;
	#$dbres[6]['parent_id']=8;
//echo '<pre>';print_r($dbres);exit;
	$this->makeMenuArr($dbres);
	//print_r($this->menu_tree);exit;
	$this->makeMenuHtml();
	return $this->html;
	
	//exit;
/*
		if(isset($dbres['error']) && $dbres['error'])
		{
			echo $dbres['error_message'];
			exit;
		}
		//print_r($dbres);

		$menu_arr=[];
		foreach($dbres as $key=>$val)
		{
			$id=$val['id'];
		//if($id==8)$val['parent_id']=3;
			$parent_id=$val['parent_id'];

			# translate this:
			#$val['name']=translate($val['name']);
			#$val['title']=translate($val['title']);
			
			if(isset($menu_arr[$parent_id]))
			{
				$menu_arr[$parent_id]['submenu'][$id]=$val;
			}else{
				$menu_arr[$id]=$val;
			}
		}
		
		$qstr='';
		if(isset($uri_chunks[0])){
			$qstr=trim($uri_chunks[0],'/');
		}

		$this->html='	<ul class="menu">'.PHP_EOL;
		foreach($menu_arr as $key=>$val)
		{
			$mname=$this->translate($val['name']);
			$mtitle=$this->translate($val['title']);
			//$titles[$link]=$mtitle;
			$sel='';
			
			$link=$val['link'];
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
					$sname=$this->translate($s['name']);
					$stitle=$this->translate($s['title']);
					$sublink=$s['link'];

					$slink=$link.'/'.$sublink;
					$sl=$link.'_'.$sublink;
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
*/
		return $this->html;

	}
	
	private function makeMenuArr($arr)
	{
		if(!empty($arr))foreach($arr as &$t)
		{
		#	Здесь идут корневые пункты меню - town, community и т.д.
			$id=$t['id'];
			$parent_id=$t['parent_id'];
			
			if(!isset($this->parents_arr[$parent_id]))
			{
				$t['parents_group'][]=$parent_id;	// [1]
				
				$t['root_path']=$t['link'];
				$this->menu_tree[$id]=$t;
				$this->parents_arr[$id]=&$this->menu_tree[$id];
				$this->parents_groups[$parent_id][$t['link']]=$t;	// [1]
				$childs[0][$t['link']]=$t;
			}else{
				$parent=$this->parents_arr[$parent_id];	// [2]
				$t['parents_group']=$parent['parents_group'];	// [2]
				$t['parents_group'][]=$parent_id;	// [2]
				
				$parent_root=$this->parents_arr[$parent_id]['root_path'];
				$t['root_path']=$parent_root.='/'.$t['link'];
				$this->parents_groups[$parent_id][$t['link']]=$t;	// [2]
				$this->parents_arr[$parent_id]['node'][$t['id']]=$t;
				$this->parents_arr[$t['id']]=&$this->parents_arr[$parent_id]['node'][$t['id']];
				$this->childs[$parent_id][$t['link']]=$t;
			}
		}
		
		/* Создаём дефолтные пути по меню */
		for($n=count($this->parents_arr);$n>0;$n--)
		{
			if(isset($this->parents_arr[$n]))
			{
				$parent=$this->parents_arr[$n];
				if(isset($parent['node']))
				{
					if(!empty($parent['node']))
					{
						foreach($parent['node'] as $key=>&$par)
						{
							if($par['devmode']==0)	// && $par['on']==1
							{
								$default=isset($par['default'])?$par['default']:$par['root_path'];
								$this->parents_arr[$n]['default']=$default;
								break;
							}
						}
					}
				}else{
					if($this->parents_arr[$n]['parent_id']==0)
					{
						$this->parents_arr[$n]['default']=$this->parents_arr[$n]['root_path'];
						$this->pages_ids[$this->parents_arr[$n]['root_path']]=$this->parents_arr[$n]['id'];
					}
				}
			}
		}
		/* Пути созданы */
		
		/* Создаём массивы с конечными и неконечными путями */
		foreach($this->parents_arr as &$parent)
		{
			if(isset($parent['default']))	// && isset($parent['node']))
			{
				if(isset($parent['node']))
					$this->not_ends[]=$parent['root_path'];
				else
					$this->ends[$parent['root_path']]=$parent['parents_group'];
				$this->defaults[$parent['root_path']]=$parent['default'];
			}else{
				$this->ends[$parent['root_path']]=$parent['parents_group'];
				$this->pages_ids[$parent['root_path']]=$parent['id'];
			}
		}
		//pr($this->parents_arr);exit;
		/* Закончили */
		
		/* И всё-таки подчистим :) */
		if(!empty($this->parents_groups))
			foreach($this->parents_groups as &$pg)
				foreach($pg as &$item) unset($item['parents_group']);
		/* Почистили */
		
		/* Определяем первый дефолтный путь */
		if(!empty($this->menu_tree))foreach($this->menu_tree as $item1)
		{
			if(isset($item1['default']) && !$item1['devmode'])	// && $item1['on']
				if($this->default_index==''){$this->default_index=$item1['default'];break;}
		}
		/* Определили */
		
		//echo '<pre>';
		//print_r($this->menu_tree);//exit;
		//print_r($this->pages_ids);//exit;
		//print_r($this->not_ends);
		//print_r($this->ends);
		//print_r($this->parents_groups);
		//print_r($this->parents_arr);
		//print_r($this->defaults);
		//print_r($this->default_index);
		
		//exit;
		#//print_r($parents_arr);
		
		//$this->saveMenu();
	}
	
/*
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

*/

	private $mtpl_ul_start='<ul class="menu">';
	private $mtpl_sul_start='<ul class="submenu">';
	private $mtpl_start='<li><a href="/#link#/" title="#title#"#sel#>#icon_span#<span class="mtxt">#mname#</span></a>'.PHP_EOL;
	private $mtpl_end='</li>'.PHP_EOL;
	private $mtpl_ul_end='</ul>';
	private $mtitles;

	private function makeMenuHtml()
	{
		$this->mtitles=[];
		$arr=array_first($this->menu_tree)['node'];
		//echo "<pre>";print_r($arr);exit;
		$mk_html = function($arr,$sm) use (&$mk_html) {
			$html='';
			foreach($arr as $key=>$val)
			{
				$sel='';
				$icon='empty';
				if(isset($val['icon']) && !empty($val['icon'])) $icon=$val['icon'];
				$icon_span='<span class="'.$icon.'"></span>';
				
				$tpl=$this->mtpl_start;
				$tpl=str_replace(array(
					'#link#','#title#','#sel#','#icon_span#','#mname#'
				),array(
					$val['root_path'],$val['title'],$sel,$icon_span,$val['name']
				),$tpl);
				$html.=$tpl;
				$this->mtitles[]=$val['title'];
				
				if(isset($val['node']))
				{
					$html.=$this->mtpl_sul_start.$mk_html($val['node'],'submenu').$this->mtpl_ul_end;
				}
			}
			return $html;
		};
		//$this->html=$this->mtpl_ul_start.$mk_html($this->menu_tree,'').$this->mtpl_ul_end;
		
		$this->html=$this->mtpl_ul_start.$mk_html($arr,'').$this->mtpl_ul_end;
		
		$this->html.=PHP_EOL.'	<script type="text/javascript">'.PHP_EOL;
		$this->html.="\t\tvar page_titles=".json_encode($this->mtitles)."\n";
		$this->html.='	</script>'.PHP_EOL;

		
		//echo join(','.PHP_EOL,$this->mtitles);exit;
	}
}

/*
    [0] => Array
        (
            [parent_id] => 1
            [link] => overview
            [name] => Overview
            [title] => Summary Overview
            [icon] => icon-chart-bar
            [sort_num] => 1
            [devmode] => 0
        )

    [1] => Array
        (
            [parent_id] => 1
            [link] => jailscontainers
            [name] => Jails containers
            [title] => Jails containers control panel
            [icon] => icon-server
            [sort_num] => 5
            [devmode] => 0
        )
*/