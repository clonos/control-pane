<?php
class Config
{
	/* Список языков, используемых в проекте */
	public $languages=array(
		'en'=>'English',
		'ru'=>'Russian',
		'de'=>'Deutch',
	);

	/* Меню проекта */
	/* Так же можно использовать подменю (в menu.php есть пример) */
	public $menu=array(
		'overview'=>array(
			'name'=>'Overview',
			'title'=>'Summary Overview',	// заголовки лучше делать более полными, чем просто повторение пункта меню
		),
		'jailscontainers'=>array(
			'name'=>'Jails containers',
			'title'=>'Jails containers control panel',
		),
		
		'instance_jail'=>array(
			'name'=>'Template for jail',
			'title'=>'Helpers and wizard for containers',
		),
		
		'bhyvevms'=>array(
			'name'=>'Bhyve VMs',
			'title'=>'Virtual machine control panel',
		),
		
		'nodes'=>array(
			'name'=>'Nodes',
			'title'=>'Nodes control panel',
		),
		
		'vpnet'=>array(
			'name'=>'Virtual Private Network',
			'title'=>'Manage for virtual private networks',
		),
		
		'authkey'=>array(
			'name'=>'Authkey',
			'title'=>'Manage for SSH auth key',
		),
		
		'media'=>array(
			'name'=>'Storage Media',
			'title'=>'Virtual Media Manager',
		),
		
		'repo'=>array(
			'name'=>'Repository',
			'title'=>'Remote repository',
		),
		
		'bases'=>array(
			'name'=>'FreeBSD Bases',
			'title'=>'FreeBSD bases manager',
		),
		
		'sources'=>array(
			'name'=>'FreeBSD Sources',
			'title'=>'FreeBSD sources manager',
		),
		
		'jail_marketplace'=>array(
			'name'=>'Jail Marketplace',
			'title'=>'Public remote containers marketplace',
		),
		
		'bhyve_marketplace'=>array(
			'name'=>'Bhyve Marketplace',
			'title'=>'Public remote virtual machine marketplace',
		),
		
		'tasklog'=>array(
			'name'=>'TaskLog',
			'title'=>'System task log',
		),

		'sqlite'=>array(
			'name'=>'SQLite admin',
			'title'=>'SQLite admin interface',
		),

	);
}