<?php

trait tUri {
	
	public function get_uri_chunks()
	{
		$uri=trim($_SERVER['REQUEST_URI'],'/');
		$uri_chunks = [];
		//echo $uri;exit;
		
		if(!empty($uri))
		{
			if(isset($this->pages_ids[$uri]))
			{
				$menu_path=$uri;
				$default=$uri;
				//echo $menu_path;exit;
			}else{
				foreach($this->pages_ids as $key=>$val)
				{
					
				}
				
				
			}
		}else{
			
		}
		
		//delete this
		$uri_chunks=explode('/',$uri);
		
		
		/*
		if(!empty($uri)){
			$str=str_replace('/index.php','',$uri);
			$uri_chunks=explode('/',$str);
		}else if(isset($_POST['path'])){
			$str=trim($_POST['path'],'/');
			$uri_chunks=explode('/',$str);
		}
		*/
		return $uri_chunks;
	}

}


/*
    [clonos/overview] => 3
    [clonos/jailscontainers] => 4
    [clonos/instance_jail] => 5
    [clonos/bhyvevms] => 6
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