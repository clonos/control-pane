<?php
//include_once($_REALPATH.'/forms.php');

class ClonOS
{
	public $workdir='';
	public $realpath_php='';
	public $realpath_public='';
	private $_post=false;
	private $_db=null;
	
/*
	public $projectId=0;
	public $jailId=0;
	public $moduleId=0;
	public $helper='';
	public $mode='';
	public $form='';

	private $_vars=array();
	private $_db_tasks=null;
	private $_db_jails=null;
*/

	const CBSD_CMD='env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ';
	
	function cbsd_cmd($cmd)
	{
		$descriptorspec = array(
			0 => array('pipe','r'),
			1 => array('pipe','w'),
			2 => array('pipe','r')
		);
//echo self::CBSD_CMD.$cmd;exit;
		$process = proc_open(self::CBSD_CMD.trim($cmd),$descriptorspec,$pipes,null,null);

		$error=false;
		$error_message='';
		$message='';
		if (is_resource($process))
		{
			$buf=stream_get_contents($pipes[1]);
			$buf0=stream_get_contents($pipes[0]);
			$buf1=stream_get_contents($pipes[2]);
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);

			$task_id=-1;
			$return_value = proc_close($process);
			if($return_value!=0)
			{
				$error=true;
				$error_message=$buf;
				//$log_file='/tmp';
				//if(file_exists())
			}else{
				$message=trim($buf);
			}
			//echo self::CBSD_CMD.$cmd;
			return array('cmd'=>$cmd,'retval'=>$return_value, 'message'=>$message, 'error'=>$error,'error_message'=>$error_message);
		}
	}
	
	function __construct($_REALPATH)	# /usr/home/web/cp/clonos
	{
		$this->workdir=getenv('WORKDIR');
			# // /usr/jails
			
		$this->realpath_php=$_REALPATH.'/php/';
			# /usr/home/web/cp/clonos/php/
			
		$this->realpath_php=$_REALPATH.'/public/';
			# /usr/home/web/cp/clonos/public/
		
		include('config.php');
		include('db.php');
		include('menu.php');
		
		$this->config=new Config();
		$this->menu=new Menu($this->config->menu);
		
		
		
		
		
		
		
		return;
//echo base64_encode(file_get_contents($rp.'/images/tree-minus.gif'));exit;
		if(substr($rp,-7)=='/webdev')
		{
			$this->realpath=substr($rp,0,-7);
		}else{
			$this->realpath=$rp;
		}
		include_once($this->realpath.'/db.php');
		
		
		$this->_db=new Db('sqlite_webdev');
		$this->_db_tasks=new Db('sqlite_cbsd','tasks');
		$this->_db_jails=new Db('sqlite_cbsd','jails');
		$this->_post=($_SERVER['REQUEST_METHOD']=='POST');
		
		if(isset($_POST['groupsUpdate'])) return;
		
		if($this->_post)
		{
			$this->_vars=$_POST;
			unset($_POST);
			
			$this->projectId=intval($this->_vars['project']);
			$this->jailId=intval($this->_vars['jail']);
			$this->moduleId=intval($this->_vars['module']);
			if(isset($this->_vars['helper']))
				$this->helper=$this->_vars['helper'];
			$this->mode=$this->_vars['mode'];
			if(isset($this->_vars['form_data'])) $this->form=$this->_vars['form_data'];
			
			switch($this->mode)
			{
				case 'getProjectsList':
					$projects=$this->getProjectsList();
					echo json_encode(array('projects'=>$projects));
					return;break;
				case 'getJailsList':
					$projects=$this->getProjectsList();
					$jails=$this->getJailsList();
					echo json_encode(array('jails'=>$jails,'projects'=>$projects));
					return;break;
				case 'getModulesList':
					$jails=$this->getJailsList();
					$modules=$this->getModulesList();
					echo json_encode(array('jails'=>$jails,'modules'=>$modules));
					return;break;
				case 'getModuleSettings':
					$modules=$this->getModulesList();
					$settings=$this->getModuleSettings();
					echo json_encode(array('modules'=>$modules,'settings'=>$settings));
					return;break;
				case 'getHelpersList':
					$jails=$this->getJailsList();
					$helpers=$this->getHelpersList();
					echo json_encode(array('jails'=>$jails,'helpers'=>$helpers));
					return;break;
				case 'getHelper':
					//$jails=$this->getJailsList();
					$modules=$this->getHelpersList();
					$helper=$this->getHelper();
					echo json_encode(array('modules'=>$modules,'helpers'=>$helper));
					return;break;
				case 'installHelper':
					$res=$this->installHelper();
					$modules=$this->getHelpersList();
					$helper=$this->getHelper();
					echo json_encode(array('modules'=>$modules,'helpers'=>$helper,'res'=>$res));
					return;break;
				case 'saveHelperValues':
					$res=$this->saveHelperValues();
					echo json_encode($res);
					return;break;
				case 'getServicesList':
					$jails=$this->getJailsList();
					$services=$this->getServicesList();
					echo json_encode(array('jails'=>$jails,'services'=>$services));
					return;break;
				case 'getUsersList':
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users));
					return;break;
				case 'getModulesListForInstall':
//$this->updateCountsModules();
					$modules=$this->getModulesListForInstallHtml();
					echo json_encode(array('html'=>$modules));
					return;break;
/*
				case 'getInstalledModulesList':
					$jails=$this->getJailsList();
					$modules=$this->getInstalledModules();
					echo json_encode(array('jails'=>$jails,'html'=>$modules));
					return;break;
*/
				case 'addProject':
					echo json_encode($this->projectAdd());
					return;break;
				case 'editProject':
					echo json_encode($this->projectEdit());
					return;break;
				case 'addJail':
					echo json_encode($this->addJail());
					return;break;
				case 'editJail':
					echo json_encode($this->editJail());
					return;break;
/*
				case 'jailClone':
					echo json_encode($this->jailClone());
					return;break;
*/
/*
				case 'addModule':
					$this->addModule();
					return;break;
*/
/*
				case 'removeModules':
					$this->removeModules();
					return;break;
*/
				case 'jailStart':
					echo json_encode($this->jailStart($this->form['jail_name']));
					return;break;
				case 'getTasksStatus':
					echo json_encode($this->_getTasksStatus($this->form['jsonObj']));
					return;break;
				case 'getJailSettings':
					echo json_encode($this->getJailSettings($this->form['id']));
					return;break;
				case 'getExportedFiles':
					echo json_encode($this->getExportedFiles());
					return;break;
				case 'getImportedFileInfo':
					echo json_encode($this->getImportedFileInfo($this->form));
					return;break;
				case 'addNewUser':
					$new_user=$this->addNewUser($this->form);
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users,'new_user'=>$new_user));
					return;break;
				case 'editUser':
					$edit_user=$this->editUser($this->form);
					$user=array();
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users,'new_user'=>$edit_user));
					return;break;
				case 'getTaskLog':
					$jails=$this->getJailsList();
					$log=$this->getTaskLog();
					echo json_encode(array('jails'=>$jails,'tasklog'=>$log));
					return;break;
				case 'getTaskLogItem':
					$jails=$this->getJailsList();
					$item=$this->getTaskLogItem();
					echo json_encode(array('jails'=>$jails,'item'=>$item));
					return;break;
				case 'getForm':
					$res=$this->getForm();
					echo json_encode($res);
					return;break;
			}
		}
	}
	
	function check_locktime($nodeip)
	{
		$lockfile=$this->workdir."/ftmp/shmux_${nodeip}.lock";
		if (!file_exists($lockfile)) {
			return 0;
		}
		
		$cur_time = time();
		$st_time=filemtime($lockfile);
		
		$difftime=(( $cur_time - $st_time ) / 60 );
		if ( $difftime > 1 ) {
			return round($difftime);;
		} else {
			return 0; //lock exist but too fresh
		}
	}

	function get_node_info($nodename,$value)
	{
		$db = new SQLite3($this->realpath."/var/db/nodes.sqlite"); $db->busyTimeout(5000);
		if (!$db) return;
		$sql = "SELECT $value FROM nodelist WHERE nodename=\"$nodename\"";

		$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
		$row = array();

		while($res = $result->fetchArray(SQLITE3_ASSOC)){
			if(!isset($res["$value"])) return;
			return $res["$value"];
		}
	}
	
/*
	function getProjectsListOnStart()
	{
		$query='select * from projects';
		$res=$this->_db->select($query);
		echo '	var projects=',json_encode($res),PHP_EOL;
	}
*/

/*
	function getTaskStatus($task_id)
	{
		$status=$this->_db_tasks->selectAssoc("select status,logfile,errcode from taskd where id='{$task_id}'");
		if($status['errcode']>0)
		{
			$status['errmsg']=file_get_contents($status['logfile']);
		}
		return $status;
	}
*/
	function _getTasksStatus($jsonObj)
	{
		$tasks=array();
		$obj=json_decode($jsonObj,true);
		
		if(isset($obj['proj_ops'])) return $this->GetProjectTasksStatus($obj);
		if(isset($obj['mod_ops'])) return $this->GetModulesTasksStatus($obj);
		
		$ops_array=array('jcreate','jstart','jstop','jedit','jremove','jexport','jimport','jclone','madd','sstart','sstop','projremove');	//,'mremove'
		$stat_array=array(
			'jcreate'=>array(get_translate('Creating'),get_translate('Not running')),
			'jstart'=>array(get_translate('Starting'),get_translate('Launched')),
			'jstop'=>array(get_translate('Stopping'),get_translate('Stopped')),
			'jedit'=>array(get_translate('Saving'),get_translate('Saved')),
			'jremove'=>array(get_translate('Removing'),get_translate('Removed')),
			'jexport'=>array(get_translate('Exporting'),get_translate('Exported')),
			'jimport'=>array(get_translate('Importing'),get_translate('Imported')),
			'jclone'=>array(get_translate('Cloning'),get_translate('Cloned')),
			'madd'=>array(get_translate('Installing'),get_translate('Installed')),
			//'mremove'=>array('Removing','Removed'),
			'sstart'=>array(get_translate('Starting'),get_translate('Started')),
			'sstop'=>array(get_translate('Stopping'),get_translate('Stopped')),
			//'projremove'=>array('Removing','Removed'),
		);
		if(!empty($obj)) foreach($obj as $key=>$task)
		{
			$op=$task['operation'];
			$status=$task['status'];
			if(in_array($op,$ops_array))
			{
				$res=false;
				if($status==-1)
				{
					switch($op)
					{
						case 'jstart':	$res=$this->jailStart('jail'.$key,$key);break;
						case 'jstop':	$res=$this->jailStop('jail'.$key,$key);break;
						case 'jedit':	$res=$this->jailEdit('jail'.$key);break;
						case 'jremove':	$res=$this->jailRemove('jail'.$key,$key);break;
						case 'jexport':	$res=$this->jailExport('jail'.$key,$task['jname'],$key);break;
						case 'jimport':	$res=$this->jailImport('jail'.$key,$task['jname'],$key);break;
						case 'jclone':	$res=$this->jailClone('jail'.$key,$key,$obj[$key]);break;
						case 'madd':	$res=$this->moduleAdd('jail'.$key,$task['jname'],$key);break;
						//case 'mremove':	$res=$this->moduleRemove('jail'.$key,$task['jname'],$key);break;
						case 'sstart':	$res=$this->serviceStart($task);break;
						case 'sstop':	$res=$this->serviceStop($task);break;
						//case 'projremove':	$res=$this->projectRemove($key,$task);break;
					}
				}
				
				if($res!==false)
				{
					if($res['error'])
						$obj[$key]['retval']=$res['retval'];
					if(!empty($res['error_message']))
						$obj[$key]['error_message']=$res['error_message'];

					if(isset($res['message']))
					{
						$task_id=intval($res['message']);
						if($task_id>0)
						{
							$tasks[]=$task_id;
							$obj[$key]['task_id']=$task_id;
							//$obj[$key]['txt_log']=file_get_contents('/tmp/taskd.'.$task_id.'.log');
						}
					}
				}else{
					$tasks[]=$task['task_id'];
				}
			}
		}
		
		$ids=join(',',$tasks);
		if(!empty($ids))
		{
			$query="select id,status,logfile,errcode from taskd where id in ({$ids})";
			$statuses=$this->_db_tasks->select($query);
			//print_r($statuses);
			if(!empty($obj)) foreach($obj as $key=>$task)
			{
				if(!empty($statuses)) foreach($statuses as $stat)
				{
					if($task['task_id']==$stat['id'])
					{
						$obj[$key]['status']=$stat['status'];
						$num=($stat['status']<2?0:1);
						$obj[$key]['txt_status']=$stat_array[$obj[$key]['operation']][$num];
						if($stat['errcode']>0)
						{
							$obj[$key]['errmsg']=file_get_contents($stat['logfile']);
							$obj[$key]['txt_status']=get_translate('Error');
						}
					#	Удаляем джейл
						if($stat['status']==2 && $task['operation']=='jremove')
						{
							$this->jailRemoveFromDb($stat['errcode'],$task);
						}
					#	Удаляем модуль
					/*
						if($stat['status']==2 && $task['operation']=='mremove')
						{
							$this->moduleRemoveFromDb($stat['errcode'],$task);
						}
					*/
					#	Возвращаем IP клонированному джейлу, если он был присвоен по DHCP
						if($stat['status']==2 && $task['operation']=='jclone')
						{
							//$obj[$key]['new_ip']=$this->getJailIpOnJcloneEnd($key);
						}
					}
				}
			}
		}
		
		if(isset($res['cloned']) && $res['cloned'])
		{
			$obj[-1]['jails']=$this->getJailsList();
		}

		return $obj;
	}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function GhzConvert($Hz=0)
	{
		$h=1;$l='Mhz';
		if($Hz>1000){$h=1000;$l='Ghz';}
		
		return round($Hz/$h,2).' '.$l;
	}
	
	function fileSizeConvert($bytes, $bytes_in_mb=1024)
	{
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow($bytes_in_mb, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow($bytes_in_mb, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow($bytes_in_mb, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => $bytes_in_mb
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

		$result='0 MB';
		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
}

function translate($phrase)
{
	$lang=getLang();
	$file=getLangFilePath($lang);
	if(!file_exists($file)) $file=getLangFilePath('en');
	if(!file_exists($file)) return;
	require($file);
	
	if(isset($lang[$phrase]))
		echo $lang[$phrase];
	else
		echo $phrase;
}
function get_translate($phrase)
{
	$lang=getLang();
	$file=getLangFilePath($lang);
	if(!file_exists($file)) $file=getLangFilePath('en');
	require($file);
	
	if(isset($lang[$phrase]))
		return $lang[$phrase];
	else
		return $phrase;
}

function getLang()
{
	if(isset($_COOKIE['lang']))
		$lang=$_COOKIE['lang'];
	if(empty($lang)) $lang='en';
	return $lang;
}
function getLangFilePath($lang)
{
	global $_REALPATH;
	return $_REALPATH.'/public/lang/'.$lang.'.php';
}