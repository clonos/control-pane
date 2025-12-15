<?php
/*
/clonos/
/clonos/overview/
/clonos/overview/test/last/?mode=w#test;last
/clonos/nofound/link.php
*/

trait tUri {
	
	public function get_uri_chunks()
	{
		$uri=[
			'query'=>'',
			'gets'=>'',
			'default'=>'',
			'mode'=>'',
			'mode_var'=>'',
			'section'=>'',
			'menu_path'=>'',
			'path'=>'',
			'path_chunks'=>[],
			'params'=>'',
			'page'=>0,
			'root'=>'',
			'page_root'=>'',
			'need_reload'=>false,
			'error_page'=>'',
			'hidden_gets'=>'',
		];
		$uri['query']=preg_replace('#/{1,}#','/',$_SERVER['REQUEST_URI']);	//
		$uri['query']=trim($uri['query'],'/');
		
		$uri_chunks = [];
		//echo $uri;exit;
		
		if(empty($this->menu_tree))
		{
			echo "Site menu is empty";
			return false;
		}
		
		//echo '<pre>'.print_r($this->pages_ids,true);exit;
		if(!empty($uri['query']))
		{
			if(isset($this->pages_ids[$uri['query']]))
			{
				$uri['path']=$uri['query'];
				$uri['default']=$uri['query'];
				$uri['path_chunks']=explode('/',$uri['query']);
				$uri['section']=$uri['path_chunks'][0];
			}else{
				
				if(preg_match('#^([^\&]+)\?(.+)#i',$uri['query'],$res))
				{
					$uri['query']=$res[1];
					$uri['gets']=$this->explode_vars($res[2]);
				}

				$this->uri['chunks']=explode('/',trim($uri['query'],'/'));
				if(isset($this->uri['chunks']) && !empty($this->uri['chunks'][0]))
					$uri['section']=$this->uri['chunks'][0];
				
				//echo '<pre>'.print_r($this->pages_ids,true);exit;
				//print_r($this->uri['chunks']);exit;
				
				$itsok=false;
				$pt=[];
				//echo '<pre>';print_r($this->pages_ids);exit;
				foreach($this->uri['chunks'] as $chunk)
				{
					$pt[]=$chunk;
					$path=implode('/',$pt);
					array_shift($this->uri['chunks']);
					if(isset($this->pages_ids[$path]))
					{
						$uri['path_chunks']=$pt;
						$uri['path']=$path;
						$uri['endpath_chunks']=$this->uri['chunks'];
						//print_r($uri);
						//print_r($this->uri);
						//break;
						$itsok=true;
					}
				}
				
				if(!$itsok)
				{
					$arr=$this->getMenuFirstElement();
					$uri['default']=$arr['root_path'];
					$uri['need_reload']=true;
				}
				
				//exit;
				
				/*
				foreach($this->pages_ids as $path=>$val)
				{
					$arr=explode('/',$uri['query']);
					foreach($arr as $k=>$v)
					{
						echo implode('/',$pt).'</br>';
						if(str_starts_with($path,implode('/',$pt)))
						{
							$pt[]=$v;
							array_shift($arr);
						}
					}
				}
				print_r($pt);
				print_r($arr);
				exit;
				*/
				
			}
		}else{
			$arr=$this->getMenuFirstElement();
			//$arr=array_first($this->menu_tree)['node'];
			//$arr=array_first($arr);
			//echo '<pre>';print_r($arr);exit;
			$uri['default']=$arr['root_path'];
			$uri['need_reload']=true;
			//$uri['menu_path']=$uri['query'];
			//$uri['default']=$uri['query'];
		}

		//print_r($uri['path_chunks']);exit;
//echo '<pre>';print_r($uri);exit;

		
		//delete this
		//$uri_chunks=explode('/',$uri['query']);
		
		
		/*
		if(!empty($uri)){
			$str=str_replace('/index.php','',$uri);
			$uri_chunks=explode('/',$str);
		}else if(isset($_POST['path'])){
			$str=trim($_POST['path'],'/');
			$uri_chunks=explode('/',$str);
		}
		*/
		$this->uri=$uri;
		return $uri['path_chunks'];
	}
	
	private function explode_vars($str)
	{
		$arr=array();
		$res=explode("&",$str);
		$pat="#([a-z0-9_]+)=([a-z0-9_;]+)#i";
		if(!empty($res))
			foreach($res as $vars)
			{
				if(preg_match($pat,$vars,$res1))
					$arr[$res1[1]]=str_replace("'","’",$res1[2]);
					# ® ° ± µ ¶ ™ › ¤ § © ‘ ’ Ћ ‘ “ ” • ‚ „ … † ‡ € ‰ ‹
			}
		return($arr);
	}


}


/*
    [clonos/overview] => 3
    [clonos/containers] => 4
    [clonos/instance_jail] => 5
    [clonos/vms] => 6
    [clonos/vm_packages] => 7
    [clonos/vpnet] => 8
    [clonos/authkey] => 9
    [clonos/media] => 10
    [clonos/imported] => 11
    [clonos/bases] => 13
    [clonos/sources] => 14
    [clonos/tasklog] => 15
    [clonos/sqlite] => 16
    [settings] => 0
    [users] => 0
    [shell] => 0
*/