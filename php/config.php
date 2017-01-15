<?php
class Config
{
	/* Список языков, используемых в проекте */
	public $languages=array(
		'en'=>'English',
		'ru'=>'Russian',
	);

	/* Меню проекта */
	/* Так же можно использовать подменю (в menu.php есть пример) */
	public $menu=array(
		'overview'=>array(
			'name'=>'Overview',
			'title'=>'Summary Overview',	// заголовки лучше делать более полными, чем просто повторение пункта меню
			'icon'=>'icon-chart-bar',
		),
		'jailscontainers'=>array(
			'name'=>'Jails containers',
			'title'=>'Jails containers control panel',
			'icon'=>'icon-server',
		),
		
		'instance_jail'=>array(
			'name'=>'Template for jail',
			'title'=>'Helpers and wizard for containers',
			'icon'=>'icon-cubes',
		),
		
		'bhyvevms'=>array(
			'name'=>'Bhyve VMs',
			'title'=>'Virtual machine control panel',
			'icon'=>'icon-th-list',
		),
		
		'nodes'=>array(
			'name'=>'Nodes',
			'title'=>'Nodes control panel',
			'icon'=>'icon-buffer',
		),
		
		'vpnet'=>array(
			'name'=>'Virtual Private Network',
			'title'=>'Manage for virtual private networks',
			'icon'=>'icon-plug',
		),
		
		'authkey'=>array(
			'name'=>'Authkeys',
			'title'=>'Manage for SSH auth key',
			'icon'=>'icon-key',
		),
		
		'media'=>array(
			'name'=>'Storage Media',
			'title'=>'Virtual Media Manager',
			'icon'=>'icon-inbox',
		),
		
		'repo'=>array(
			'name'=>'Repository',
			'title'=>'Remote repository',
			'icon'=>'icon-globe',
		),
		
		'bases'=>array(
			'name'=>'FreeBSD Bases',
			'title'=>'FreeBSD bases manager',
			'icon'=>'icon-database',
		),
		
		'sources'=>array(
			'name'=>'FreeBSD Sources',
			'title'=>'FreeBSD sources manager',
			'icon'=>'icon-edit',
		),
		
		'jail_marketplace'=>array(
			'name'=>'Jail Marketplace',
			'title'=>'Public remote containers marketplace',
			'icon'=>'icon-flag',
		),
		
		'bhyve_marketplace'=>array(
			'name'=>'Bhyve Marketplace',
			'title'=>'Public remote virtual machine marketplace',
			'icon'=>'icon-flag-checkered',
		),
		
		'tasklog'=>array(
			'name'=>'TaskLog',
			'title'=>'System task log',
			'icon'=>'icon-list-alt',
		),

		'sqlite'=>array(
			'name'=>'SQLite admin',
			'title'=>'SQLite admin interface',
			'icon'=>'icon-wpforms',
		),

	);
	
	public $os_types=array(
		array(
			'os'=>'FreeBSD',
			'items'=>array(
				array('name'=>'FreeBSD 11.0-RELEASE','type'=>'freebsd',
						'profile'=>'FreeBSD-x64-11.0','obtain'=>true),
				array('name'=>'FreeBSD pfSense 2.4.0-DEVELOP','type'=>'freebsd',
						'profile'=>'pfSense-2-LATEST-amd64','obtain'=>false),
				array('name'=>'FreeBSD OPNsense-16.7','type'=>'freebsd',
						'profile'=>'OPNsense-16-RELEASE-amd64','obtain'=>false),
			),
		),
		array(
			'os'=>'Linux',
			'items'=>array(
				array('name'=>'Linux Arch 2016','type'=>'linux',
						'profile'=>'ArchLinux-x86-2016','obtain'=>false),
				array('name'=>'Linux CentOS 7','type'=>'linux',
						'profile'=>'CentOS-7-x86_64','obtain'=>false),
				array('name'=>'Linux Debian 7','type'=>'linux',
						'profile'=>'Debian-x86-7','obtain'=>false),
				array('name'=>'Linux Debian 8','type'=>'linux',
						'profile'=>'Debian-x86-8','obtain'=>false),
				array('name'=>'Linux Open Suse 42','type'=>'linux',
						'profile'=>'opensuse-x86-42','obtain'=>false),
				array('name'=>'Linux Ubuntu 16.04','type'=>'linux',
						'profile'=>'ubuntuserver-x86-16.04','obtain'=>true),
			),
		),
		array(
			'os'=>'Windows',
			'items'=>array(
				array('name'=>'Windows 10','type'=>'windows',
						'profile'=>'10_86x_64x','obtain'=>false),
			),
		)
	);
	function os_types_create($obtain='new')
	{
		$obtain=($obtain=='obtain');
		
		$html='';
		foreach($this->os_types as $num1=>$os)
		{
			$obtain_count=0;
			$html_tmp='					<optgroup label="'.$os['os'].'">'.PHP_EOL;
			$items=$os['items'];
			foreach($items as $num2=>$item)
			{
				if(!$obtain || $item['obtain'])
					$html_tmp.='						<option value="'.$num1.'.'.$num2.'">'.$item['name'].'</option>'.PHP_EOL;
				if($item['obtain']) $obtain_count++;
			}
			$html_tmp.='					</optgroup>'.PHP_EOL;
			
			if(!$obtain || $obtain_count>0) $html.=$html_tmp;
		}
		return $html;
	}
	
	
	function authkeys_list()
	{
		$db=new Db('base','authkey');
		$res=$db->select('SELECT idx,name FROM authkey;');
		
		$html='';
		if(!empty($res))foreach($res as $item)
		{
			$html.='					<option value="'.$item['idx'].'">'.$item['name'].'</option>'.PHP_EOL;
		}
		return $html;
	}
}