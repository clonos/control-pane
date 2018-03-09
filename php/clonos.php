<?php
//include_once($_REALPATH.'/forms.php');

class ClonOS
{
	public $server_name='';
	public $workdir='';
	public $realpath_php='';
	public $realpath_public='';
	public $realpath_page='';
	public $uri_chunks=array();
	public $json_name='';
	public $language='en';
	public $language_file_loaded=false;
	public $translate_arr=array();
	public $table_templates=array();
	public $url_hash='';
	public $media_import='';
	
	private $_post=false;
	private $_db=null;
	private $_client_ip='';
	private $_dialogs=array();
	private $_cmd_array=array('jcreate','jstart','jstop','jrestart','jedit','jremove','jexport','jimport','jclone','jrename','madd','sstart','sstop','projremove','bcreate','bstart','bstop','brestart','bremove','bclone','brename','vm_obtain','removesrc','srcup','removebase','world','repo','forms');
	private $_user_info=array(
		'id'=>0,
		'username'=>'guest',
		'unregistered'=>true,
	);
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
		
		$process = proc_open(self::CBSD_CMD.trim($cmd),$descriptorspec,$pipes,null,null);
		
		$full_cmd=self::CBSD_CMD.trim($cmd);
		
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
			}else{
				$message=trim($buf);
			}
			
			return array('cmd'=>$cmd,'full_cmd'=>$full_cmd,'retval'=>$return_value, 'message'=>$message, 'error'=>$error,'error_message'=>$error_message);
		}
	}
	
	function __construct($_REALPATH,$uri='')	# /usr/home/web/cp/clonos
	{
		$this->_post=($_SERVER['REQUEST_METHOD']=='POST');
		$this->_vars=$_POST;
		if(isset($_COOKIE['lang'])) $this->language=$_COOKIE['lang'];

		$this->workdir=getenv('WORKDIR');
			# // /usr/jails
			
		$this->realpath_php=$_REALPATH.'/php/';
			# /usr/home/web/cp/clonos/php/
			
		$this->realpath_public=$_REALPATH.'/public/';
			# /usr/home/web/cp/clonos/public/
		
		$this->media_import=$_REALPATH.'/media_import/';
		
		if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME'])))
			$this->server_name=$_SERVER['SERVER_NAME'];
		else
			$this->server_name=$_SERVER['SERVER_ADDR'];
		
		if(!empty($uri))
		{
			$str=str_replace('/index.php','',$uri);
			$this->uri_chunks=explode('/',$str);
		}else if(isset($this->_vars['path'])){
			$str=trim($this->_vars['path'],'/');
			$this->uri_chunks=explode('/',$str);
		}
	
		$translate_filename=$this->realpath_public.'/lang/'.$this->language.'.php';
		$translate_filename_alt=$this->realpath_public.'/lang/en.php';
		if(file_exists($translate_filename)) $t_filename=$translate_filename; else $t_filename=$translate_filename_alt;
		include($t_filename);
		$this->translate_arr=$lang;
		unset($lang);
		unset($t_filename);
		
		$this->_client_ip=$_SERVER['REMOTE_ADDR'];
		
		if(isset($this->_vars['path']))
		{
			//$this->realpath_page=$this->realpath_public.'pages/'.trim($this->_vars['path'],'/').'/';
			//echo $this->_vars['path'];
			//print_r($this->uri_chunks);
			$this->realpath_page=$this->realpath_public.'pages/'.$this->uri_chunks[0].'/';
			$this->json_name=$this->realpath_page.'a.json.php';
			//echo $this->realpath_page;
		}else if($_SERVER['REQUEST_URI']){
			//$this->realpath_page=$this->realpath_public.'pages/'.trim($_SERVER['REQUEST_URI'],'/').'/';
			if(isset($this->uri_chunks[0]))
				$this->realpath_page=$this->realpath_public.'pages/'.$this->uri_chunks[0].'/';
		}
		
		if(isset($this->_vars['hash']))
		{
			$this->url_hash=preg_replace('/^#/','',$this->_vars['hash']);
		}
//		$this->json_name=$this->realpath_php.'pages'
//		$clonos->json_name=$file_path.'a.json.php';
		
		include('config.php');
		include('db.php');
		include('forms.php');
		include('menu.php');
		
		$this->_db_tasks=new Db('base','cbsdtaskd');
		$this->_db_local=new Db('base','local');
		
		$this->config=new Config();
		$this->menu=new Menu($this->config->menu,$this);
		
		if(isset($this->_vars['mode'])) $this->mode=$this->_vars['mode'];
		if(isset($this->_vars['form_data'])) $this->form=$this->_vars['form_data'];
		
		$ures=$this->userAutologin();
		if($ures!==false)
		{
			if(isset($ures['id']) && is_numeric($ures['id']) && $ures['id']>0)
			{
				$this->_user_info=$ures;
				$this->_user_info['unregistered']=false;
			}else{
				$this->_user_info['unregistered']=true;
			}
		}
		
		if($this->_post && isset($this->mode))
		{
			if(isset($this->_user_info['error']) && $this->_user_info['error'])
			{
				if($this->mode!='login')
				{
					echo json_encode(array('error'=>true,'unregistered_user'=>true));
					exit;
				}
			}

			unset($_POST);
			switch($this->mode)
			{
				case 'login':
					echo json_encode($this->login());
					return;break;
				case 'getTasksStatus':
					echo json_encode($this->_getTasksStatus($this->form['jsonObj']));
					return;break;
				case 'getJsonPage':
					if(file_exists($this->json_name)) include($this->json_name); else echo '{}';
					return;break;
				case 'freejname':
					echo json_encode($this->getFreeJname());
					break;

				case 'helpersAdd':
					echo json_encode($this->helpersAdd($this->mode));
					return;break;
				case 'addHelperGroup':
					echo json_encode($this->addHelperGroup($this->mode));
					return;break;
				case 'addJailHelperGroup':
					echo json_encode($this->addJailHelperGroup());
					return;break;
				case 'deleteJailHelperGroup':
					echo json_encode($this->deleteJailHelperGroup());
					return;break;
				case 'deleteHelperGroup':
					echo json_encode($this->deleteHelperGroup($this->mode));
					return;break;
				case 'jailRestart':
					echo json_encode($this->jailRestart());
					return;break;
				case 'jailStart':
					echo json_encode($this->jailStart());
					return;break;
				case 'jailStop':
					echo json_encode($this->jailStop());
					return;break;
				case 'jailRemove':
					echo json_encode($this->jailRemove());
					return;break;
				case 'saveJailHelperValues':
					echo json_encode($this->saveJailHelperValues());
					return;break;
				case 'saveHelperValues':
					$redirect='/jailscontainers/';
				case 'jailAdd':
					if(!isset($redirect)) $redirect='';
					echo json_encode($this->jailAdd($redirect));
					return;break;
				case 'jailClone':
					echo json_encode($this->jailClone());
					return;break;
				case 'jailRename':
					echo json_encode($this->jailRename());
					return;break;
				case 'bhyveRename':
					echo json_encode($this->bhyveRename());
					return;break;
				case 'jailEdit':
					echo json_encode($this->jailEdit());
					return;break;
				case 'jailEditVars':
					echo json_encode($this->jailEditVars());
					return;break;
				case 'jailCloneVars':
					echo json_encode($this->jailCloneVars());
					return;break;
				case 'jailRenameVars':
					echo json_encode($this->jailRenameVars());
					return;break;
				case 'bhyveRenameVars':
					echo json_encode($this->bhyveRenameVars());
					return;break;
				case 'bhyveRestart':
					echo json_encode($this->bhyveRestart());
					return;break;
				case 'bhyveStart':
					echo json_encode($this->bhyveStart());
					return;break;
				case 'bhyveStop':
					echo json_encode($this->bhyveStop());
					return;break;
				case 'bhyveAdd':
					echo json_encode($this->bhyveAdd());
					return;break;
				case 'bhyveRemove':
					echo json_encode($this->bhyveRemove());
					return;break;
				case 'bhyveEdit':
					echo json_encode($this->bhyveEdit());
					return;break;
				case 'bhyveEditVars':
					echo json_encode($this->bhyveEditVars());
					return;break;
				case 'bhyveObtain':
					echo json_encode($this->bhyveObtain());
					return;break;
				case 'bhyveClone':
					echo json_encode($this->bhyveClone());
					return;break;

				
				case 'authkeyAdd':
					echo json_encode($this->authkeyAdd());
					return;break;
				case 'authkeyRemove':
					echo json_encode($this->authkeyRemove());
					return;break;
					
				case 'vpnetAdd':
					echo json_encode($this->vpnetAdd());
					return;break;
				case 'vpnetRemove':
					echo json_encode($this->vpnetRemove());
					return;break;
					
				case 'updateBhyveISO':
					echo json_encode($this->updateBhyveISO());
					return;break;
				case 'mediaAdd':
					//echo json_encode($this->mediaAdd());
					return;break;
				case 'mediaRemove':
					echo json_encode($this->mediaRemove());
					return;break;
				case 'logLoad':
					echo json_encode($this->logLoad());
					return;break;
				case 'logFlush':
					echo json_encode($this->logFlush());
					return;break;
				case 'basesCompile':
					echo json_encode($this->basesCompile());
					return;break;
				case 'repoCompile':
					echo json_encode($this->repoCompile());
					return;break;
				case 'srcUpdate':
					echo json_encode($this->srcUpdate());
					return;break;
				case 'srcRemove':
					echo json_encode($this->srcRemove());
					return;break;
				case 'baseRemove':
					echo json_encode($this->baseRemove());
					return;break;
				case 'usersAdd':
					echo json_encode($this->usersAdd());
					return;break;
				case 'usersEdit':
					echo json_encode($this->usersEdit());
					return;break;
				case 'userRemove':
					echo json_encode($this->userRemove());
					return;break;
				case 'userGetInfo':
					echo json_encode($this->userGetInfo());
					return;break;
				case 'userEditInfo':
					echo json_encode($this->userEditInfo());
					return;break;
				case 'vmTemplateAdd':
					echo json_encode($this->vmTemplateAdd());
					return;break;
				case 'vmTemplateEditInfo':
					echo json_encode($this->vmTemplateEditInfo());
					return;break;
				case 'vmTemplateEdit':
					echo json_encode($this->vmTemplateEdit());
					return;break;
				case 'vmTemplateRemove':
					echo json_encode($this->vmTemplateRemove());
					return;break;
					
				case 'getImportedImageInfo':
					echo json_encode($this->getImportedImageInfo());
					return;break;
				case 'imageImport':
					echo json_encode($this->imageImport());
					return;break;
				case 'imageExport':
					echo json_encode($this->imageExport());
					return;break;
				case 'imageRemove':
					echo json_encode($this->imageRemove());
					return;break;
					
/*				case 'saveHelperValues':
					echo json_encode($this->saveHelperValues());
					return;break;
*/
			}
		}
	}
	
	function login()
	{
		$form=$this->_vars['form_data'];
		
		return $this->userRegisterCheck($form);
		//array('message'=>'unregistered user','errorCode'=>1)
	}
	
	function redis_publish($key,$message)
	{
		if(empty($key) || empty($message)) return false;
		$redis=new Redis();
		$redis->connect('10.0.0.3',6379);
		$res=$redis->publish($key,$message);
	}
	
	function getLang()
	{
		return $this->language;
	}
	function translate($phrase)
	{
		if(isset($this->translate_arr[$phrase]))
			return $this->translate_arr[$phrase];
		else
			return $phrase;
	}

	function getTableChunk($table_name='',$tag)
	{
		if(empty($table_name)) return false;
		if(isset($this->table_templates[$table_name][$tag])) return $this->table_templates[$table_name][$tag];
		
		$file_name=$this->realpath_page.$table_name.'.table';
		if(!file_exists($file_name)) return false;
		$file=file_get_contents($file_name);
		$pat='#[\s]*?<'.$tag.'[^>]*>(.*)<\/'.$tag.'>#iUs';
 		if(preg_match($pat,$file,$res))
		{
			$this->table_templates[$table_name][$tag]=$res;
			return $res;
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
			return round($difftime);
		} else {
			return 0; //lock exist but too fresh
		}
	}
	
	function check_vmonline($vm)
	{
		$vmmdir="/dev/vmm";
		
		if(!file_exists($vmmdir)) return 0;
		
		if($handle=opendir($vmmdir))
		{
			while(false!==($entry=readdir($handle)))
			{
				if($entry[0]==".") continue;
				if($vm==$entry) return 1;
			}
			closedir($handle);
		}
		
		return 0;
	}

/* 	function get_node_info($nodename,$value)
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
	} */
	
	function getRunningTasks($ids=array())
	{
		$check_arr=array(
			'jcreate'=>'Creating',
			'jstart'=>'Starting',
			'jstop'=>'Stopping',
			'jrestart'=>'Restarting',
			'jremove'=>'Removing',
			'jexport'=>'Exporting',
			'jclone'=>'Cloning',
			'bcreate'=>'Creating',
			'bstart'=>'Starting',
			'bstop'=>'Stopping',
			'brestart'=>'Restarting',
			'bremove'=>'Removing',
			'bclone'=>'Cloning',
			'vm_obtain'=>'Creating',
			'removesrc'=>'Removing',
			'srcup'=>'Updating',
			'removebase'=>'Removing',
			'world'=>'Compiling',
			'repo'=>'Fetching',
			'imgremove'=>'Removing',
		);
		
		$res=array();
		if(!empty($ids))
		{
			$tid=join("','",$ids);
			$query="select id,cmd,status,jname from taskd where status<2 and jname in ('{$tid}')";
			//echo $query;
			$cmd='';
			$txt_status='';
			$tasks=$this->_db_tasks->select($query);
			if(!empty($tasks)) foreach($tasks as $task)
			{
				$rid=preg_replace('/^#/','',$task['jname']);
				foreach($check_arr as $key=>$val)
				{
					if(strpos($task['cmd'],$key)!==false)
					{
						$cmd=$key;
						$txt_status=$val;
						break;
					}
				}
				$res[$rid]['status']=$task['status'];
				$res[$rid]['task_cmd']=$cmd;
				$res[$rid]['txt_status']=$txt_status;
				$res[$rid]['task_id']=$task['id'];
			}
			return $res;
		}
		return null;
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
		//return $jsonObj;
		$tasks=array();
		$obj=json_decode($jsonObj,true);
		
		if(isset($obj['proj_ops'])) return $this->GetProjectTasksStatus($obj);
		if(isset($obj['mod_ops'])) return $this->GetModulesTasksStatus($obj);
		
		$ops_array=$this->_cmd_array;
		$stat_array=array(
			'jcreate'=>array($this->translate('Creating'),$this->translate('Created')),
			'jstart'=>array($this->translate('Starting'),$this->translate('Launched')),
			'jstop'=>array($this->translate('Stopping'),$this->translate('Stopped')),
			'jrestart'=>array($this->translate('Restarting'),$this->translate('Restarted')),
			'jedit'=>array($this->translate('Saving'),$this->translate('Saved')),
			'jremove'=>array($this->translate('Removing'),$this->translate('Removed')),
			'jexport'=>array($this->translate('Exporting'),$this->translate('Exported')),
			'jimport'=>array($this->translate('Importing'),$this->translate('Imported')),
			'jclone'=>array($this->translate('Cloning'),$this->translate('Cloned')),
			'madd'=>array($this->translate('Installing'),$this->translate('Installed')),
			//'mremove'=>array('Removing','Removed'),
			'sstart'=>array($this->translate('Starting'),$this->translate('Started')),
			'sstop'=>array($this->translate('Stopping'),$this->translate('Stopped')),
			'vm_obtain'=>array($this->translate('Creating'),$this->translate('Created')),
			'srcup'=>array($this->translate('Updating'),$this->translate('Updated')),
			'world'=>array($this->translate('Compiling'),$this->translate('Compiled')),
			'repo'=>array($this->translate('Fetching'),$this->translate('Fetched')),
			//'projremove'=>array('Removing','Removed'),
		);
		$stat_array['bcreate']=&$stat_array['jcreate'];
		$stat_array['bstart']=&$stat_array['jstart'];
		$stat_array['bstop']=&$stat_array['jstop'];
		$stat_array['brestart']=&$stat_array['jrestart'];
		$stat_array['bremove']=&$stat_array['jremove'];
		$stat_array['bclone']=&$stat_array['jclone'];
		$stat_array['removesrc']=&$stat_array['jremove'];
		$stat_array['removebase']=&$stat_array['jremove'];
		$stat_array['imgremove']=&$stat_array['jremove'];
		
		
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
						case 'jstart':	$res=$this->jailStart($key);break;
						case 'jstop':	$res=$this->jailStop($key);break;
						case 'jrestart':$res=$this->jailRestart($key);break;
						//case 'jedit':	$res=$this->jailEdit('jail'.$key);break;
						case 'jremove':	$res=$this->jailRemove($key);break;
						
						case 'bstart':	$res=$this->bhyveStart($key);break;
						case 'bstop':	$res=$this->bhyveStop($key);break;
						case 'brestart':$res=$this->bhyveRestart($key);break;
						case 'bremove':	$res=$this->bhyveRemove($key);break;
						case 'removesrc':	$res=$this->srcRemove($key);break;
						case 'srcup':	$res=$this->srcUpdate($key);break;
						case 'removebase':	$res=$this->baseRemove($key);break;
						
						//case 'jexport':	$res=$this->jailExport('jail'.$key,$task['jname'],$key);break;
						//case 'jimport':	$res=$this->jailImport('jail'.$key,$task['jname'],$key);break;
						//case 'jclone':	$res=$this->jailClone('jail'.$key,$key,$obj[$key]);break;
						//case 'madd':	$res=$this->moduleAdd('jail'.$key,$task['jname'],$key);break;
						////case 'mremove':	$res=$this->moduleRemove('jail'.$key,$task['jname'],$key);break;
						//case 'sstart':	$res=$this->serviceStart($task);break;
						//case 'sstop':	$res=$this->serviceStop($task);break;
						////case 'projremove':	$res=$this->projectRemove($key,$task);break;
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
			
			if($status==-1) $obj[$key]['status']=0;
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
							$obj[$key]['txt_status']=$this->translate('Error');
						}
					#	Возвращаем IP клонированному джейлу, если он был присвоен по DHCP
						if($stat['status']==2)
						{
							switch($task['operation'])
							{
								case 'jcreate':
								case 'jclone':
									$res=$this->getJailInfo($obj[$key]['jail_id'],$task['operation']);
									if(isset($res['html'])) $obj[$key]['new_html']=$res['html'];
									break;
								case 'bclone':
									$res=$this->getBhyveInfo($obj[$key]['jail_id']);
									if(isset($res['html'])) $obj[$key]['new_html']=$res['html'];
									break;
								case 'repo':
									$res=$this->fillRepoTr($obj[$key]['jail_id'],true,false);
									if(isset($res['html'])) $obj[$key]['new_html']=$res['html'];
									break;
								case 'srcup':
									$res=$this->getSrcInfo($obj[$key]['jail_id']);
									if(isset($res['html'])) $obj[$key]['new_html']=$res['html'];
									break;
							}
						}
					}
				}
			}
		}
		
		return $obj;
	}
	
	function jailRename()
	{
		$form=$this->_vars['form_data'];
		
		$host_hostname=$form['host_hostname'];
		$ip4_addr=$form['ip4_addr'];
		$old_name=$form['oldJail'];
		$new_name=$form['jname'];
		$username=$this->_user_info['username'];
		
		$cmd="task owner=${username} mode=new /usr/local/bin/cbsd jrename old=${old_name} new=${new_name} host_hostname=${host_hostname} ip4_addr=${ip4_addr} restart=1";
		$res=$this->cbsd_cmd($cmd);
		
		$err='Jail is not renamed!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Jail was renamed!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$form['jname'],'taskId'=>$taskId,'mode'=>$this->mode);
	}
	function jailClone()
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jclone checkstate=0 old='.$form['oldJail'].' new='.$form['jname'].' host_hostname='.$form['host_hostname'].' ip4_addr='.$form['ip4_addr']);
		
		$err='Jail is not cloned!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Jail was cloned!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}
		
		$html='';
		$hres=$this->getTableChunk('jailslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// исправить на актуальные данные!
				'node'=>'local',				// исправить на актуальные данные!
				'ip4_addr'=>str_replace(',',',<wbr />',$form['ip4_addr']),
				'jname'=>$form['jname'],
				'jstatus'=>$this->translate('Cloning'),
				'icon'=>'spin6 animate-spin',
				'desktop'=>' s-on',
				'maintenance'=>' maintenance',
				'protected'=>'icon-cancel',
				'protitle'=>$this->translate('Delete'),
				'vnc_title'=>$this->translate('Open VNC'),
				'reboot_title'=>$this->translate('Restart jail'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$form['jname'],'taskId'=>$taskId,'mode'=>$this->mode,'html'=>$html);
	}
	function getJailInfo($jname,$op='')
	{
		$stats=array(''=>'','jclone'=>'Cloned','jcreate'=>'Created');
		$html='';
		$db=new Db('base','local');
		if($db!==false)
		{
			$jail=$db->selectAssoc("SELECT jname,ip4_addr,status,protected FROM jails WHERE jname='{$jname}'");
			$hres=$this->getTableChunk('jailslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
//				$status=$jail['status'];
				$vars=array(
					'nth-num'=>'nth0',
					'node'=>'local',
					'ip4_addr'=>str_replace(',',',<wbr />',$jail['ip4_addr']),
					'jname'=>$jail['jname'],
					'jstatus'=>$this->translate($stats[$op]),
					'icon'=>'spin6 animate-spin',
					'desktop'=>' s-on',
					'maintenance'=>' maintenance',
					'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
					'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
					'vnc_title'=>$this->translate('Open VNC'),
					'reboot_title'=>$this->translate('Restart jail'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html.=$html_tpl;
			}
		}
		
		$html=preg_replace('#<tr[^>]*>#','',$html);
		$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);
		
		return array('html'=>$html);
	}
	
	function saveSettingsCBSD()
	{
		$form=$this->form;
		
		$arr=array('error'=>true,'errorMessage'=>'Method is not complete yet! line: 702');
		return $arr;
	}
	
	function saveJailHelperValues()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		
		if(!isset($this->uri_chunks[1]) || !isset($this->url_hash)) return array('error'=>true,'errorMessage'=>'Bad url!');
		$jail_name=$this->uri_chunks[1];
		
		$db=new Db('helper',array('jname'=>$jail_name,'helper'=>$this->url_hash));
		if($db->error) return array('error'=>true,'errorMessage'=>'No helper database!');
		
		foreach($form as $key=>$val)
		{
			if($key!='jname' && $key!='ip4_addr')
			{
				$query="update forms set new='{$val}' where param='{$key}'";
				$db->update($query);
				unset($form[$key]);
			}
		}
		
		//cbsd forms module=<helper> jname=jail1 inter=0
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd forms module='.$this->url_hash.' jname='.$jail_name.' inter=0');

		$err='Helper values is saved!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Helper values was not saved!';
			$taskId=$res['message'];
		}
		
		return array(
			'jail_id'=>$jail_name,
			'taskId'=>$taskId,
			'mode'=>$this->mode,
		);
	}
	
	function jailAdd($redirect='')	//$mode='jailAdd'
	{
		//if(!empty($arr)) $form=$arr; else
		$form=$this->form;
		$helper=preg_replace('/^#/','',$this->_vars['hash']);
		
		$db_path='';
		$with_img_helpers='';
		if($this->mode=='saveHelperValues')
		{
			if($helper=='' && $this->_vars['path']=='/settings/')
			{
				return $this->saveSettingsCBSD();
			}
			
			if(!isset($this->_vars['db_path']))
			{
				$res=$this->cbsd_cmd('make_tmp_helper module='.$helper);
				if($res['retval']==0)
				{
					$db_path=$res['message'];
				}else{
					echo json_encode(array('error'=>true,'errorMessage'=>'Error on open temporary form database!'));
					return;
				}
			}else{
				$db_path=$this->_vars['db_path'];
			}
			
			/*
			$file_name=$this->workdir.'/formfile/'.$helper.'.sqlite';
			if(file_exists($file_name))
			{
				$tmp_name=tempnam("/tmp","HLPR");
				copy($file_name,$tmp_name);
				
				$db=new Db('file',$tmp_name);
				if($db!==false)
				{
					foreach($form as $key=>$val)
					{
						if($key!='jname' && $key!='ip4_addr')
						{
							$query="update forms set new='{$val}' where param='{$key}'";
							$db->update($query);
							unset($form[$key]);
						}
					}
					
					$with_img_helpers=$tmp_name;
					//echo $with_img_helpers;
				}
			}
			*/
			
			$db=new Db('file',$db_path);
			if($db!==false)
			{
				foreach($form as $key=>$val)
				{
					if($key!='jname' && $key!='ip4_addr')
					{
						$query="update forms set new='{$val}' where param='{$key}'";
						$db->update($query);
						unset($form[$key]);
					}
				}
				
				$with_img_helpers=$db_path;
			}
			
			$form['interface']='auto';
			$form['user_pw_root']='';
			$form['astart']=1;
			$form['host_hostname']=$form['jname'].'.my.domain';
		}
	
		$err=array();
		$arr=array(
			'workdir'=>$this->workdir,
			'mount_devfs'=>1,
			'arch'=>'native',
			'mkhostfile'=>1,
			'devfs_ruleset'=>4,
			'ver'=>'native',
			'mount_src'=>0,
			'mount_obj'=>0,
			'mount_kernel'=>0,
			'applytpl'=>1,
			'floatresolv'=>1,
			'allow_mount'=>1,
			'allow_devfs'=>1,
			'allow_nullfs'=>1,
			'mkhostsfile'=>1,
			'pkg_bootstrap'=>0,
			'mdsize'=>0,
			'runasap'=>0,
			'with_img_helpers'=>$with_img_helpers,
		);
		
		$arr_copy=array('jname','host_hostname','ip4_addr','user_pw_root','interface');
		foreach($arr_copy as $a) if(isset($form[$a])) $arr[$a]=$form[$a];
		
		$arr_copy=array('baserw','mount_ports','astart','vnet');
		foreach($arr_copy as $a) if(isset($form[$a]) && $form[$a]=='on') $arr[$a]=1; else $arr[$a]=0;
		
		$sysrc=array();
		if(isset($form['serv-ftpd'])) $sysrc[]=$form['serv-ftpd'];
		if(isset($form['serv-sshd'])) $sysrc[]=$form['serv-sshd'];
		$arr['sysrc_enable']=join($sysrc,' ');
		
		/* create jail */
		$file_name='/tmp/'.$arr['jname'].'.conf';
		
		$file=file_get_contents($this->realpath_public.'templates/jail.tpl');
		if(!empty($file))
		{
			foreach($arr as $var=>$val)
			{
				$file=str_replace('#'.$var.'#',$val,$file);
			}
		}
		file_put_contents($file_name,$file);
		
		$username=$this->_user_info['username'];
		
		$cbsd_queue_name='/clonos/'.trim($this->_vars['path'],'/').'/';
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jcreate inter=0 jconf='.$file_name);
		//.' cbsd_queue_name='.$cbsd_queue_name);

		$err='Jail is not created!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Jail was created!';
			$taskId=$res['message'];
		}
		
		// local - поменять на реальный сервер, на котором создаётся клетка!
		$jid=$arr['jname'];
		
		$table='jailslist';
		$html='';
		$hres=$this->getTableChunk($table,'tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// исправить на актуальные данные!
				'node'=>'local',				// исправить на актуальные данные!
				'ip4_addr'=>str_replace(',',',<wbr />',$form['ip4_addr']),
				'jname'=>$arr['jname'],
				'jstatus'=>$this->translate('Creating'),
				'icon'=>'spin6 animate-spin',
				'desktop'=>' s-off',
				'maintenance'=>' busy maintenance',
				'protected'=>'icon-cancel',
				'protitle'=>$this->translate('Delete'),
				'vnc_title'=>$this->translate('Open VNC'),
				'reboot_title'=>$this->translate('Restart jail'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		/*
		$this->redis_publish($cbsd_queue_name,json_encode(array(
			'jail_id'=>$jid,
			'cmd'=>'jcreate',
			'html'=>$html,
			'mode'=>$this->mode,
			'table'=>$table,
		)));
		*/
		
		return array('errorMessage'=>$err,'jail_id'=>$jid,'taskId'=>$taskId,'mode'=>$this->mode,'redirect'=>$redirect,'db_path'=>$db_path);	//,'html'=>$html
	}
	function jailRenameVars()
	{
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');
		
		$err=false;
		$db=new Db('base','local');
		if($db!==false)
		{
			$query="SELECT jname,host_hostname FROM jails WHERE jname='{$form['jail_id']}';";	//,ip4_addr
			$res['vars']=$db->selectAssoc($query);
		}else{
			$err=true;
		}
		if(empty($res['vars']))
		{
			$err=true;
		}
		if($err)
		{
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.');
			$res['jail_id']=$form['jail_id'];
			$res['reload']=true;
			return $res;
		}
		
		$orig_jname=$res['vars']['jname'];
		//$res['vars']['jname'].='clone';
		$res['vars']['ip4_addr']='DHCP';
		$res['vars']['host_hostname']=preg_replace('/^'.$orig_jname.'/',$res['vars']['jname'],$res['vars']['host_hostname']);
		
		
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		return $res;
	}
	function jailCloneVars()
	{
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');
		
		$err=false;
		$db=new Db('base','local');
		if($db!==false)
		{
			$query="SELECT jname,host_hostname FROM jails WHERE jname='{$form['jail_id']}';";	//,ip4_addr
			$res['vars']=$db->selectAssoc($query);
		}else{
			$err=true;
		}
		if(empty($res['vars']))
		{
			$err=true;
		}
		if($err)
		{
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.');
			$res['jail_id']=$form['jail_id'];
			$res['reload']=true;
			return $res;
		}
		
		$orig_jname=$res['vars']['jname'];
		$res['vars']['jname'].='clone';
		$res['vars']['ip4_addr']='DHCP';
		$res['vars']['host_hostname']=preg_replace('/^'.$orig_jname.'/',$res['vars']['jname'],$res['vars']['host_hostname']);
		
		
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		return $res;
	}
	function jailEditVars()
	{
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');
		
		$err=false;
		$db=new Db('base','local');
		if($db!==false)
		{
			$query="SELECT jname,host_hostname,ip4_addr,allow_mount,interface,mount_ports,astart,vnet FROM jails WHERE jname='{$form['jail_id']}';";
			$res['vars']=$db->selectAssoc($query);
		}else{
			$err=true;
		}
		if(empty($res['vars']))
		{
			$err=true;
		}
		if($err)
		{
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.');
			$res['jail_id']=$form['jail_id'];
			$res['reload']=true;
			return $res;
		}
		
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		return $res;
	}
	function jailEdit()
	{
		$form=$this->_vars['form_data'];
		
		$str=array();
		$jname=$form['jname'];
		$arr=array('host_hostname','ip4_addr','allow_mount','interface','mount_ports','astart','vnet');
		foreach($arr as $a)
		{
			if(isset($form[$a]))
			{
				$val=$form[$a];
				if($val=='on') $val=1;
				$str[]=$a.'='.$val;
			}else{
				$str[]=$a.'=0';
			}
		}
		
		$cmd='jset jname='.$jname.' '.join(' ',$str);
		$res=$this->cbsd_cmd($cmd);
		$res['mode']='jailEdit';
		$res['form']=$form;
		return $res;
	}

	function jailStart()	//$name
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$cbsd_queue_name=trim($this->_vars['path'],'/');
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jstart inter=0 jname='.$name);
		//.' cbsd_queue_name=/clonos/'.$cbsd_queue_name.'/');	// autoflush=2
		return $res;
	}
	function jailStop()	//$name
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$cbsd_queue_name=trim($this->_vars['path'],'/');
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jstop inter=0 jname='.$name);
		//.' cbsd_queue_name=/clonos/'.$cbsd_queue_name.'/');	// autoflush=2
		return $res;
	}
	function jailRestart()	//$name
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jrestart inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function jailRemove()	//$name
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd jremove inter=0 jname='.$name);	// autoflush=2
		return $res;
	}

	function bhyveClone()
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd bclone checkstate=0 old='.$form['oldBhyve'].' new='.$form['vm_name']);
		
		$err='Virtual Machine is not renamed!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Virtual Machine was renamed!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}
		
		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// исправить на актуальные данные!
				'node'=>'local',				// исправить на актуальные данные!
				'jname'=>$form['vm_name'],
				'vm_ram'=>$form['vm_ram'],
				'vm_cpus'=>$form['vm_cpus'],
				'vm_os_type'=>$form['vm_os_type'],
				'jstatus'=>$this->translate('Cloning'),
				'icon'=>'spin6 animate-spin',
				'desktop'=>' s-on',
				'maintenance'=>' maintenance',
				'protected'=>'icon-cancel',
				'protitle'=>$this->translate('Delete'),
				'vnc_title'=>$this->translate('Open VNC'),
				'reboot_title'=>$this->translate('Restart VM'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('errorMessage'=>$err,'vm_name'=>$form['vm_name'],'jail_id'=>$form['vm_name'],'taskId'=>$taskId,'mode'=>$this->mode,'html'=>$html);
	}
	function getBhyveInfo($jname)
	{
		$statuses=array('Not Launched','Launched','unknown-1','Maintenance','unknown-3','unknown-4','unknown-5','unknown-6');
		$html='';
		$db=new Db('base','local');
		if($db!==false)
		{
			$bhyve=$db->selectAssoc("SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve WHERE jname='{$jname}'");
			$hres=$this->getTableChunk('bhyveslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$status=$this->check_vmonline($bhyve['jname']);
				$vars=array(
					'jname'=>$bhyve['jname'],
					'nth-num'=>'nth0',
					'desktop'=>'',
					'maintenance'=>'',
					'node'=>'local',
					'vm_name'=>'',
					'vm_ram'=>$this->fileSizeConvert($bhyve['vm_ram']),
					'vm_cpus'=>$bhyve['vm_cpus'],
					'vm_os_type'=>$bhyve['vm_os_type'],
					'vm_status'=>$this->translate($statuses[$status]),
					'desktop'=>($status==0)?' s-off':' s-on',
					'icon'=>($status==0)?'play':'stop',
					'protected'=>'icon-cancel',
					'protitle'=>' title="'.$this->translate('Delete').'"',
					'vnc_title'=>$this->translate('Open VNC'),
					'reboot_title'=>$this->translate('Restart bhyve'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html.=$html_tpl;
			}
		}
		
		$html=preg_replace('#<tr[^>]*>#','',$html);
		$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);
		
		return array('html'=>$html);
	}
	function bhyveEditVars()
	{
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');
		
		$err=false;
		$db=new Db('base','local');
		if($db!==false)
		{
			$query="SELECT b.jname as vm_name,vm_cpus,vm_ram,vm_vnc_port,bhyve_vnc_tcp_bind,interface FROM bhyve as b inner join jails as j on b.jname=j.jname and b.jname='{$form['jail_id']}';";
			$res['vars']=$db->selectAssoc($query);
			
			$res['vars']['vm_ram']=$this->fileSizeConvert($res['vars']['vm_ram']);
		}else{
			$err=true;
		}
		if(empty($res['vars']))
		{
			$err=true;
		}
		if($err)
		{
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.');
			$res['jail_id']=$form['jail_id'];
			$res['reload']=true;
			return $res;
		}
		
		$res['vars']['vm_vnc_password']='-nochange-';
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		$res['iso_list']=$this->updateBhyveISO($form['jail_id']);
		return $res;
	}
	function bhyveRename()
	{
		$form=$this->_vars['form_data'];
		
		$old_name=$form['oldJail'];
		$new_name=$form['jname'];
		$username=$this->_user_info['username'];
		
	$cmd="task owner=${username} mode=new /usr/local/bin/cbsd brename old=${old_name} new=${new_name} restart=1";
		$res=$this->cbsd_cmd($cmd);
		
		$err='Virtual Machine is not renamed!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Virtual Machine was renamed!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$form['jname'],'taskId'=>$taskId,'mode'=>$this->mode);
	}
	function bhyveRenameVars()
	{
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');
		
		$jname=$form['jail_id'];
		$err=false;
		$db=new Db('base','local');
		if($db!==false)
		{
			$query="SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve WHERE jname='${jname}'";	//,ip4_addr
			$res['vars']=$db->selectAssoc($query);
		}else{
			$err=true;
		}
		if(empty($res['vars']))
		{
			$err=true;
		}
		if($err)
		{
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.');
			$res['jail_id']=$form['jail_id'];
//			$res['reload']=true;
			return $res;
		}
		
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		return $res;
	}
	function bhyveEdit()
	{
		$form=$this->form;
		
		$str=array();
		$jname=$form['jname'];
		
		$ram=$form['vm_ram'];
		$ram_tmp=$ram;
		$ram=str_replace(' ','',$ram);
		$ram=str_ireplace('mb','m',$ram);
		$ram=str_ireplace('gb','g',$ram);
		$form['vm_ram']=$ram;
		
		$arr=array('vm_cpus','vm_ram','bhyve_vnc_tcp_bind','vm_vnc_port','interface');
		if($form['vm_vnc_password']!='-nochange-') $arr[]='vm_vnc_password';
		foreach($arr as $a)
		{
			if(isset($form[$a]))
			{
				$val=$form[$a];
				if($val=='on') $val=1;
				$str[]=$a.'='.$val;
			}else{
				$str[]=$a.'=0';
			}
		}
		
		$form['vm_ram']=$ram_tmp;
		
		/* check mounted ISO */
		$db=new Db('base','storage_media');
		$res=$db->selectAssoc('select * from media where jname="'.$jname.'" and type="iso"');
		if($res!==false && !empty($res))
		{
			$cmd1="cbsd media mode=unregister name=\"${res['name']}\" path=\"${res['path']}\" jname=${jname} type=${res['type']}";
			//echo $cmd1,PHP_EOL,PHP_EOL;
			$this->cbsd_cmd($cmd1);
			$res=$db->selectAssoc('select * from media where idx='.$form['vm_iso_image']);
			if($res!==false && !empty($res) && $form['vm_iso_image']!=-2)
			{
				$cmd2="cbsd media mode=register name=\"${res['name']}\" path=\"${res['path']}\" jname=${jname} type=${res['type']}";
				$this->cbsd_cmd($cmd2);
				//echo $cmd2;
			}
		}
		//exit;

		/* end check */
		
		$cmd='bset jname='.$jname.' '.join(' ',$str);
		$res=$this->cbsd_cmd($cmd);
		$res['mode']='bhyveEdit';
		$res['form']=$form;
		return $res;
	}
	function bhyveAdd()
	{
		$form=$this->form;
		
		
		$os_types=$this->config->os_types;
		$sel_os=$form['vm_os_profile'];
		list($os_num,$item_num)=explode('.',$sel_os);
		if(!isset($os_types[$os_num])) return array('error'=>true,'errorMessage'=>'Error in list of OS types!');
		$os_name=$os_types[$os_num]['os'];
		$os_items=$os_types[$os_num]['items'][$item_num];
		
		$err=array();
		$arr=array(
			'workdir'=>$this->workdir,
			'jname'=>$form['vm_name'],
			'host_hostname'=>'',
			'ip4_addr'=>'',
			'arch'=>'native',
			'ver'=>'native',
			'astart'=>0,
			'interface'=>$form['interface'],
			'vm_size'=>$form['vm_size'],
			'vm_cpus'=>$form['vm_cpus'],
			'vm_ram'=>$form['vm_ram'],
			'vm_os_type'=>$os_items['type'],
			'vm_efi'=>'uefi',
			'vm_os_profile'=>$os_items['profile'],
			'vm_guestfs'=>'',
			'bhyve_vnc_tcp_bind'=>$form['bhyve_vnc_tcp_bind'],
			'vm_vnc_port'=>$form['vm_vnc_port'],
			'vm_vnc_password'=>$form['vm_vnc_password'],
		);
		
		$iso=true;
		$res=array('name'=>'','path'=>'','iso_var_block'=>'');
		$crlf="\r\n";
		$iso_var_block='iso_extract=""'.$crlf.'iso_img_dist=""'.$crlf.'iso_img=""'.$crlf.'iso_site=""';
		$iso_id=$form['vm_iso_image'];
		if(!empty($iso_id))
		{
			if($iso_id>0)
			{
				$db=new Db('base','storage_media');
				$res=$db->selectAssoc('select name,path from media where idx='.$iso_id);
				if($res===false || empty($res)) $iso=false;
			}
			
			if($iso_id==-1)
			{
				$iso=false;
			}
			
			if($iso)
			{
				$arr['register_iso_as']='register_iso_as="'.$res['name'].'"';
				$arr['register_iso_name']='register_iso_name="'.$res['path'].'"';
				if($iso_id!=-2) $arr['iso_var_block']=$iso_var_block;
			}
		}
		
		/* create vm */
		$file_name='/tmp/'.$arr['jname'].'.conf';
		
		$file=file_get_contents($this->realpath_public.'templates/vm.tpl');
		if(!empty($file))
		{
			foreach($arr as $var=>$val)
			{
				$file=str_replace('#'.$var.'#',$val,$file);
			}
		}
		//echo $file;exit;
		file_put_contents($file_name,$file);
		$username=$this->_user_info['username'];
		
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd bcreate inter=0 jconf='.$file_name);

		$err='Virtual Machine is not created!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Virtual Machine was created!';
			$taskId=$res['message'];
		}
		// local - поменять на реальный сервер, на котором создаётся клетка!
		$jid=$arr['jname'];
		
		$vm_ram=str_replace('g',' GB',$form['vm_ram']);
		
		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// исправить на актуальные данные!
				'node'=>'local',				// исправить на актуальные данные!
				'jname'=>$arr['jname'],
				'vm_status'=>$this->translate('Creating'),
				'vm_cpus'=>$form['vm_cpus'],
				'vm_ram'=>$vm_ram,
				'vm_os_type'=>$os_items['type'],	//$os_name,
				'vnc_port'=>'',
				'vnc_port_status'=>'',
				'icon'=>'spin6 animate-spin',
				'desktop'=>' s-off',
				'maintenance'=>' maintenance',
				'protected'=>'icon-cancel',
				'protitle'=>$this->translate('Delete'),
				'vnc_title'=>$this->translate('Open VNC'),
				'reboot_title'=>$this->translate('Restart VM'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$jid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode);
	}
	function bhyveObtain()
	{
		$form=$this->_vars['form_data'];
		$username=$this->_user_info['username'];
		
		$os_types=$this->config->os_types;
		$sel_os=$form['vm_os_profile'];
		list($os_num,$item_num)=explode('.',$sel_os);
		if(!isset($os_types[$os_num])) return array('error'=>true,'errorMessage'=>'Error in list of OS types!');
		//$os_name=$os_types[$os_num]['os'];
		$os_items=$os_types[$os_num]['items'][$item_num];
		$os_type=$os_items['type'];
		
		$key_name='/usr/home/olevole/.ssh/authorized_keys';
		$key_id=$form['vm_authkey'];
		$db=new Db('base','authkey');
		$nres=$db->selectAssoc('select name from authkey where idx='.$key_id);
		if($nres['name']!==false)
		{
			$key_name=$nres['name'];
		}
		$cmd="task owner=${username} mode=new /usr/local/bin/cbsd vm_obtain jname={$form['vm_name']} vm_size={$form['vm_size']} vm_cpus={$form['vm_cpus']} vm_ram={$form['vm_ram']} vm_os_type={$os_type} mask={$form['mask']} ip4_addr={$form['ip4_addr']} gw={$form['gateway']} authkey={$key_name} pw={$form['vm_password']} vnc_password={$form['vnc_password']}";
		
		$res=$this->cbsd_cmd($cmd);
		$err='Virtual Machine is not created!';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Virtual Machine was created!';
			$taskId=$res['message'];
		}
		
		$vm_ram=str_replace('g',' GB',$form['vm_ram']);
		
		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// исправить на актуальные данные!
				'node'=>'local',				// исправить на актуальные данные!
				'jname'=>$form['vm_name'],
				'vm_status'=>$this->translate('Creating'),
				'vm_cpus'=>$form['vm_cpus'],
				'vm_ram'=>$vm_ram,
				'vm_os_type'=>$os_type,
				'icon'=>'spin6 animate-spin',
				'desktop'=>' s-off',
				'maintenance'=>' maintenance',
				'protected'=>'icon-cancel',
				'protitle'=>$this->translate('Delete'),
				'vnc_title'=>$this->translate('Open VNC'),
				'reboot_title'=>$this->translate('Restart VM'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$form['vm_name'],'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode);
	}
	function bhyveStart()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd bstart inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function bhyveStop()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd bstop inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function bhyveRestart()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd brestart inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function bhyveRemove()	//$name
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$name=$form['jname'];
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd bremove inter=0 jname='.$name);	// autoflush=2
		return $res;
	}

	function authkeyAdd()
	{
		$form=$this->form;
		
		$query="insert into authkey (name,authkey) values ('{$form['keyname']}','{$form['keysrc']}')";
		
		$db=new Db('base','authkey');
		//$res=array('error'=>false,'lastId'=>2);
		$res=$db->insert($query);
		if($res['error'])
		{
			return array('error'=>$res);
		}
		
		$html='';
		$hres=$this->getTableChunk('authkeyslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'keyid'=>$res['lastID'],
				'keyname'=>$form['keyname'],
				'keysrc'=>$form['keysrc'],
				'deltitle'=>$this->translate('Delete'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('keyname'=>$form['keyname'],'html'=>$html);
	}
	function authkeyRemove()
	{
		$form=$this->_vars['form_data'];
		
		$db=new Db('base','authkey');
		$res=$db->update('delete from authkey where idx='.$form['auth_id']);
		if($res===false) return array('error'=>true,'res'=>print_r($res,true));
		
		return array('error'=>false,'auth_id'=>$form['auth_id']);
	}
	
	function vpnetAdd()
	{
		$form=$this->_vars['form_data'];
		
		$query="insert into vpnet (name,vpnet) values ('{$form['netname']}','{$form['network']}')";
		
		$db=new Db('base','vpnet');
		
		$res=$db->insert($query);
		if($res['error'])
		{
			return array('error'=>$res);
		}
		
		$html='';
		$hres=$this->getTableChunk('vpnetslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$vars=array(
				'netid'=>$res['lastID'],
				'netname'=>$form['netname'],
				'network'=>$form['network'],
				'deltitle'=>$this->translate('Delete'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		return array('netname'=>$form['netname'],'html'=>$html);
	}
	function vpnetRemove()
	{
		$form=$this->_vars['form_data'];
		
		$db=new Db('base','vpnet');
		$res=$db->update('delete from vpnet where idx='.$form['vpnet_id']);
		if($res===false) return array('error'=>true,'res'=>print_r($res,true));
		
		return array('error'=>false,'vpnet_id'=>$form['vpnet_id']);
	}
	
	
	function mediaRemove()
	{
		$form=$this->form;
		$db=new Db('base','storage_media');
		//$res=$db->update('delete from media where idx='.$form['media_id']);
		$res=$db->selectAssoc('select * from media where idx='.$form['media_id']);
		if($res===false || empty($res)) return array('error'=>true,'res'=>print_r($res,true));
		
		//if($res['jname']=='-')	// если медиа отвязана, то просто удаляем 
		//print_r($res);exit;
		$cmd='media mode=remove name="'.$res['name'].'" path="'.$res['path'].'" jname="'.$res['jname'].'" type="'.$res['type'].'"';	//.$res['name']
		//echo $cmd;exit;
		
		$res=$this->cbsd_cmd($cmd);
		
		if($res['error'])
		{
			$arr['error']=true;
			$arr['error_message']='File image was not deleted! '.$res['error_message'];
		}else{
			$arr['error']=false;
		}
		$arr['media_id']=$form['media_id'];
		$arr['cmd']=$res;
		//echo json_encode($arr);

		
		//return array('error'=>false,'media_id'=>$form['media_id']);
		return $arr;
	}
	
	function srcRemove()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$ver=$form['jname'];
		$ver=str_replace('src','',$ver);
		if(empty($ver)) return array('error'=>true,'errorMessage'=>'Version of sources is emtpy!');
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd removesrc inter=0 ver='.$ver.' jname=#src'.$ver);
		return $res;
	}
	function srcUpdate()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		$ver=$form['jname'];
		//$ver=str_replace('src','',$ver);
		$ver=str_replace('src','',$ver);
		$stable=(preg_match('#\.\d#',$ver))?0:1;
		if(empty($ver)) return array('error'=>true,'errorMessage'=>'Version of sources is emtpy!');
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd srcup stable='.$stable.' inter=0 ver='.$ver.' jname=#src'.$ver);
		return $res;
	}
	function getSrcInfo($id)
	{
		$id=str_replace('src','',$id);
		if(!is_numeric($id)) return array('error'=>true,'errorMessage'=>'Wrong ID of sources!');
		$db=new Db('base','local');
		if($db!==false)
		{
			$res=$db->selectAssoc("SELECT idx,name,platform,ver,rev,date FROM bsdsrc where ver={$id}");
			
			$hres=$this->getTableChunk('srcslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$ver=$res['ver'];
				$vars=array(
					'nth-num'=>'nth0',
					'maintenance'=>' busy',
					'node'=>'local',
					'ver'=>$res['ver'],
					'ver1'=>strlen(intval($res['ver']))<strlen($res['ver'])?'release':'stable',
					'rev'=>$res['rev'],
					'date'=>$res['date'],
					'protitle'=>$this->translate('Update'),
					'protitle'=>$this->translate('Delete'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html=$html_tpl;
			}
		}
		
		$html=preg_replace('#<tr[^>]*>#','',$html);
		$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);
		
		return array('html'=>$html,'arr'=>$res);
	}
	function baseRemove()	//$id
	{
		//$id=str_replace('base','',$id);
		//base10.3-amd64-0
		$form=$this->form;
		$username=$this->_user_info['username'];
		$id=$form['jname'];
		$orig_id=$id;
		preg_match('#base([0-9\.]+)-([^-]+)-(\d+)#',$id,$res);
		$ver=$res[1];
		$arch=$res[2];
		$stable=$res[3];

		$cmd='task owner='.$username.' mode=new /usr/local/bin/cbsd removebase inter=0 stable='.$stable.' ver='.$ver.' arch='.$arch.' jname=#'.$orig_id;
		$res=$this->cbsd_cmd($cmd);
		return $res;
	}
	
	function basesCompile()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		if(!isset($form['sources']) || !is_numeric($form['sources'])) return array('error'=>true,'errorMessage'=>'Wrong OS type selected!');
		$id=$form['sources'];
		
		$db=new Db('base','local');
		if($db!==false)
		{
			$base=$db->selectAssoc("SELECT idx,platform,ver FROM bsdsrc where idx={$id}");
		}else{
			return array('error'=>true,'errorMessage'=>'Database connect error!');
		}
		$ver=$base['ver'];
		$stable_arr=array('release','stable');
		$stable_num=strlen(intval($ver))<strlen($ver)?0:1;
		$stable=$stable_arr[$stable_num];
		$bid=$base['ver'].'-amd64-'.$stable_num;	// !!! КОСТЫЛЬ
		
		$res=$this->fillRepoTr($id);
		$html=$res['html'];
		$res=$res['arr'];

		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd world inter=0 stable='.$res['stable'].' ver='.$ver.' jname=#base'.$bid);
		//$res['retval']=0;$res['message']=3;
		
		$err='';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='World compile start!';
			$taskId=$res['message'];
		}
		
		return array('errorMessage'=>'','jail_id'=>'base'.$bid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode,'txt_status'=>$this->translate('Compiling'));
	}
	function fillRepoTr($id,$only_td=false,$bsdsrc=true)
	{
//		preg_match('#base([0-9\.]+)-#',$id,$res);
//		$id=$res[1];
		
		$html='';
		
		$db=new Db('base','local');
		if($db!==false)
		{
			if($bsdsrc)
			{
				$res=$db->selectAssoc("SELECT idx,platform,ver FROM bsdsrc where idx={$id}");
				$res['name']='—';
				$res['arch']='—';
				$res['targetarch']='—';
				$res['stable']=strlen(intval($res['ver']))<strlen($res['ver'])?0:1;
				$res['elf']='—';
				$res['date']='—';
			}else{
				$res=$db->selectAssoc("SELECT idx,platform,name,arch,targetarch,ver,stable,elf,date FROM bsdbase where ver={$id}");
			}
			$hres=$this->getTableChunk('baseslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$ver=$res['ver'];
				$vars=array(
					'bid'=>$res['idx'],
					'nth-num'=>'nth0',
					'node'=>'local',
					'ver'=>$res['ver'],
					'name'=>'base',
					'platform'=>$res['platform'],
					'arch'=>$res['arch'],
					'targetarch'=>$res['targetarch'],
					'stable'=>$res['stable']==0?'release':'stable',
					'elf'=>$res['elf'],
					'date'=>$res['date'],
					'maintenance'=>' busy',
					'protitle'=>$this->translate('Delete'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html=$html_tpl;
			}
		}
		
		if($only_td)
		{
			$html=preg_replace('#<tr[^>]*>#','',$html);
			$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);
		}
		
		return array('html'=>$html,'arr'=>$res);
	}
	
	function repoCompile()
	{
		$form=$this->form;
		$username=$this->_user_info['username'];
		if(!isset($form['version']) || !is_numeric($form['version'])) return array('error'=>true,'errorMessage'=>'Wrong OS type input!');
		
		$stable_arr=array('release','stable');
		$html='';
		$hres=$this->getTableChunk('baseslist','tbody');
		if($hres!==false)
		{
			$html_tpl=$hres[1];
			$ver=$form['version'];
			$stable_num=strlen(intval($ver))<strlen($ver)?0:1;	//'release':'stable';
			$stable=$stable_arr[$stable_num];
			
			$bid=$ver.'-amd64-'.$stable_num;	// !!! КОСТЫЛЬ
			
			$vars=array(
				'nth-num'=>'nth0',
				'bid'=>$bid,
				'node'=>'local',
				'ver'=>$ver,
				'name'=>'base',
				'platform'=>'—',
				'arch'=>'—',
				'targetarch'=>'—',
				'stable'=>$stable,
				'elf'=>'—',
				'date'=>'—',
				'maintenance'=>' busy',
				'protitle'=>$this->translate('Delete'),
			);
			
			foreach($vars as $var=>$val)
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			
			$html=$html_tpl;
		}
		
		$res=$this->cbsd_cmd('task owner='.$username.' mode=new /usr/local/bin/cbsd repo action=get sources=base inter=0 stable='.$stable_num.' ver='.$ver.' jname=#base'.$bid);
		//$res['retval']=0;$res['message']=3;
		
		$err='';
		$taskId=-1;
		if($res['retval']==0)
		{
			$err='Repo download start!';
			$taskId=$res['message'];
		}
		
		return array('errorMessage'=>'','jail_id'=>'base'.$bid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode,'txt_status'=>$this->translate('Fetching'));
	}
	
	function logLoad()
	{
		$form=$this->_vars['form_data'];
		$log_id=$form['log_id'];
		if(!is_numeric($log_id)) return array('error'=>'Log ID must be a number');
		
		$html='';
		$buf='';
		$log_file='/tmp/taskd.'.$log_id.'.log';
		if(file_exists($log_file))
		{
			$filesize=filesize($log_file);
			if($filesize<=204800)
			{
				$buf=file_get_contents($log_file);
			}else{
				$fp=fopen($log_file,'r');
				if($fp)
				{
					fseek($fp,-1000,SEEK_END);
					$buf=fread($fp,1000);
					$html='<strong>Last 1000 Bytes of big file data:</strong><hr />';
				}
				fclose($fp);
			}
			$buf=htmlentities(trim($buf));
			$arr=preg_split('#\n#iSu',trim($buf));
			if(!empty($arr)) foreach($arr as $txt)
				$html.='<p class="log-p">'.$txt.'</p>';
			
			return array('html'=>'<div style="font-weight:bold;">Log ID: '.$log_id.'</div><br />'.$html);
		}
		
		return array('error'=>'Log file is not exists!');
	}
	function logFlush()
	{
		$res=$this->cbsd_cmd('task mode=flushall');
		return $res;
	}
	
	function getBasesCompileList()
	{
		$db1=new Db('base','local');
		if($db1!==false)
		{
			$bases=$db1->select("SELECT idx,platform,ver FROM bsdsrc order by cast(ver AS int)");
			
			if(!empty($bases)) foreach($bases as $base)
			{
				$val=$base['idx'];
				$stable=strlen(intval($base['ver']))<strlen($base['ver'])?'release':'stable';
				$name=$base['platform'].' '.$base['ver'].' '.$stable;
				echo '					<option value="'.$val.'">'.$name.'</option>',PHP_EOL;
			}
		}
	}
	
/*
	function saveHelperValues()
	{
		$form=$this->_vars['form_data'];
		return $this->jailAdd($form);
	}
*/
	function helpersAdd($mode)
	{
		$form=$this->form;
		if($this->uri_chunks[0]!='jailscontainers' || empty($this->uri_chunks[1])) return array('error'=>true,'errorMessage'=>'Bad url!');
		$jail_id=$this->uri_chunks[1];
		$username=$this->_user_info['username'];
		
		$helpers=array_keys($form);
		if(!empty($helpers)) foreach($helpers as $helper)
		{
		$res=$this->cbsd_cmd('task owner=${username} mode=new /usr/local/bin/cbsd forms inter=0 module='.$helper.' jname='.$jail_id);
		}
		return array('error'=>false);
	}
	function addJailHelperGroup()
	{
//		$form=$this->form;
		if($this->uri_chunks[0]!='jailscontainers' || empty($this->uri_chunks[1]) || empty($this->url_hash)) return array('error'=>true,'errorMessage'=>'Bad url!');
		$jail_id=$this->uri_chunks[1];
		$helper=$this->url_hash;
		
		$db=new Db('helper',array('jname'=>$jail_id,'helper'=>$helper));
		if($db===false) return array('error'=>true,'errorMessage'=>'No database file!');
		
		$db_path=$db->getFileName();
		$res=$this->cbsd_cmd('forms inter=0 module='.$helper.' formfile='.$db_path.' group=add');
		$form=new Forms('',$helper,$db_path);
		$res=$form->generate();
		
		return array('html'=>$res['html']);
	}
	function addHelperGroup($mode)
	{
		$module=$this->url_hash;
		if(isset($this->form)) $form=$this->form; else $form=array();
		if(isset($form['db_path']) && !empty($form['db_path']))
		{
			$db_path=$form['db_path'];
			if(!file_exists($db_path))
			{
				$res=$this->cbsd_cmd('make_tmp_helper module='.$module);
				if($res['retval']==0) $db_path=$res['message']; else return array('error'=>true,'errorMessage'=>'Error on open temporary form file!');
			}
		}else{
			$res=$this->cbsd_cmd('make_tmp_helper module='.$module);
			if($res['retval']==0) $db_path=$res['message'];
		}
		$res=$this->cbsd_cmd('forms inter=0 module='.$module.' formfile='.$db_path.' group=add');
		$form=new Forms('',$module,$db_path);
		$res=$form->generate();
		
		return array('db_path'=>$db_path,'html'=>$res['html']);
	}

	function deleteHelperGroup($mode)
	{
		$module=$this->url_hash;
		if(isset($this->form)) $form=$this->form; else $form=array();
		if(!isset($form['db_path']) || empty($form['db_path'])) return;

		if(!file_exists($form['db_path']))
			return array('error'=>true,'errorMessage'=>'Error on open temporary form file!');
		
		$index=$form['index'];
		$index=str_replace('ind-','',$index);

		$db_path=$form['db_path'];
		$res=$this->cbsd_cmd('forms inter=0 module='.$module.' formfile='.$db_path.' group=del index='.$index);
		$form=new Forms('',$module,$db_path);
		$res=$form->generate();
		
		return array('db_path'=>$db_path,'html'=>$res['html']);
	}
	function deleteJailHelperGroup()
	{
		$form=$this->form;
		if(!isset($this->uri_chunks[1]) || !isset($this->url_hash)) return array('error'=>true,'errorMessage'=>'Bad url!');
		
		$jail_id=$this->uri_chunks[1];
		$helper=$this->url_hash;
		$index=$form['index'];
		$index=str_replace('ind-','',$index);
		
		$db=new Db('helper',array('jname'=>$jail_id,'helper'=>$helper));
		if($db->error) return array('error'=>true,'errorMessage'=>'No helper database!');
		
		$db_path=$db->getFileName();
		$res=$this->cbsd_cmd('forms inter=0 module='.$helper.' formfile='.$db_path.' group=del index='.$index);
		$form=new Forms('',$helper,$db_path);
		$res=$form->generate();
		
		return array('html'=>$res['html']);
	}
	
	function useDialogs($arr=array())
	{
		//print_r($arr);
		$this->_dialogs=$arr;
	}
	function placeDialogs()
	{
		if(empty($this->_dialogs)) return;
		echo PHP_EOL;
		foreach($this->_dialogs as $dialog_name)
		{
			$file_name=$this->realpath_public.'dialogs/'.$dialog_name.'.php';
			if(file_exists($file_name))
			{
				include($file_name);
				echo PHP_EOL,PHP_EOL;
			}
		}
	}
	
	function placeDialogByName($dialog_name=null)
	{
		if(is_null($dialog_name)) return;
		echo PHP_EOL;
		$file_name=$this->realpath_public.'dialogs/'.$dialog_name.'.php';
		if(file_exists($file_name))
		{
			include($file_name);
			echo PHP_EOL,PHP_EOL;
		}
	}
	
	
	
	
	
	
	
	
	function runVNC($jname)
	{
		$query="SELECT vnc_password FROM bhyve WHERE jname='${jname}'";
		$res=$this->_db_local->selectAssoc($query);
		$pass='cbsd';
		if($res!==false)
		{
			$pass=$res['vnc_password'];
		}
		
		$res=$this->cbsd_cmd("vm_vncwss jname={$jname} permit={$this->_client_ip}");
		//$res=$this->_db_local->selectAssoc('select nodeip from local');
		//$nodeip=$res['nodeip'];
		// need for IPv4/IPv6 regex here, instead of strlen
		//if(strlen($nodeip)<7) $nodeip='127.0.0.1';
		//if(strlen($nodeip)<7) $nodeip=$this->server_name;
		$nodeip=$this->server_name;
		
		header('Location: http://'.$nodeip.':6080/vnc_auto.html?host='.$nodeip.'&port=6080?password='.$pass);
		exit;
	}
	
	function getFreeJname($in_helper=false,$type='jail')
	{
		$arr=array();
		$add_cmd=($in_helper)?' default_jailname='.$this->url_hash:'';
		$add_cmd1=' default_jailname='.$type;
		$res=$this->cbsd_cmd("freejname".$add_cmd.$add_cmd1);
		if($res['error'])
		{
			$arr['error']=true;
			$arr['error_message']=$err['error_message'];
		}else{
			$arr['error']=false;
			$arr['freejname']=$res['message'];
		}
		return $arr;
	}
	
	
	
	function GhzConvert($Hz=0)
	{
		$h=1;$l='Mhz';
		if($Hz>1000){$h=1000;$l='Ghz';}
		
		return round($Hz/$h,2).' '.$l;
	}
	
	function fileSizeConvert($bytes,$bytes_in_mb=1024,$round=false)
	{
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "tb",
				"VALUE" => pow($bytes_in_mb, 4)
			),
			1 => array(
				"UNIT" => "gb",
				"VALUE" => pow($bytes_in_mb, 3)
			),
			2 => array(
				"UNIT" => "mb",
				"VALUE" => pow($bytes_in_mb, 2)
			),
			3 => array(
				"UNIT" => "kb",
				"VALUE" => $bytes_in_mb
			),
			4 => array(
				"UNIT" => "b",
				"VALUE" => 1
			),
		);

		$result='0 mb';
		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				if($round) $result=round($result);
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
	
	function colorizeCmd($cmd_string)
	{
		$arr=$this->_cmd_array;
		foreach($arr as $item)
		{
			$cmd_string=str_replace($item,'<span class="cbsd-cmd">'.$item.'</span>',$cmd_string);
		}
		
		$cmd_string=preg_replace('#(\/.+/cbsd)#','<span class="cbsd-lnch">$1</span>',$cmd_string);
		
		return '<span class="cbsd-str">'.$cmd_string.'</span>';
	}
	
	
	function register_media($path,$file,$ext)
	{
		$cmd='cbsd media mode=register name='.$file.' path='.$path.$file.' type='.$ext;
		$res=$this->cbsd_cmd($cmd);
		if($res['error'])
		{
			$arr['error']=true;
			$arr['error_message']='File image not registered!';
		}else{
			$arr['error']=false;
		}
		echo json_encode($arr);
	}
	function media_iso_list_html()
	{
//		$form=$this->form;
		$db=new Db('base','storage_media');
		$res=$db->select('select * from media where type="iso"');
		if($res===false || empty($res)) return;
		
		$html='';
		foreach($res as $r)
		{
			$html.='<option value="'.$r['idx'].'">'.$r['name'].'.'.$r['type'].'</option>';
		}
		return $html;
	}
	function updateBhyveISO($iso='')
	{
		$db=new Db('base','storage_media');
		$res=$db->select('select * from media where type="iso"');
		if($res===false || empty($res)) return array(); //array('error'=>true,'error_message'=>'Profile ISO is not find!');
		
		$sel='';
		//if(empty($iso)) $sel='#sel#';
		$html='<option value="-2"></option><option value="-1"#sel#>Profile default ISO</option>';
		foreach($res as $r)
		{
			$sel1='';
			if(empty($sel) && $iso==$r['jname']) $sel1='#sel1#';
			$html.='<option value="'.$r['idx'].'"'.$sel1.'>'.$r['name'].'.'.$r['type'].'</option>';
		}
		
		if(strpos($html,'#sel1#')!==false)
		{
			$html=str_replace('#sel1#',' selected="selected"',$html);
			$html=str_replace('#sel#','',$html);
		}else{
			$html=str_replace('#sel1#','',$html);
			$html=str_replace('#sel#',' selected="selected"',$html);
		}
		
		return $html;
	}
	
	function get_interfaces_html()
	{
		$if=$this->config->os_interfaces;
		$html='';
		$m=1;
		if(!empty($if)) foreach($if as $i)
		{
			//$html.='<input type="radio" name="interface" value="'.$i['name'].'" id="rint'.$m.'" class="inline"><label for="rint'.$m.'">'.$i['name'].'</label></radio>';
			$html.='<option value="'.$i['name'].'">'.$i['name'].'</option>';
			$m++;
		}
		return $html;
	}
	
	function usersAdd()
	{
		$form=$this->form;
		
		$res=$this->userRegister($form);
		if($res!==false)
		{
			if(isset($res['user_exists']) && $res['user_exists'])
			{
				return array('error'=>true,'errorType'=>'user-exists','errorMessage'=>'User always exists!');
			}
			return $res;
		}
		return array('form'=>$form);
	}
	function usersEdit()
	{
		$form=$this->form;
		
		if(!isset($form['user_id']) || !is_numeric($form['user_id']) || $form['user_id']<1)
			return array('error'=>true,'error_message'=>'incorrect data!');
		
		$db=new Db('clonos');
		if($db!==false)
		{
			$user_id=$form['user_id'];
			$username=$form['username'];
			$first_name=$form['first_name'];
			$last_name=$form['last_name'];
			$is_active=0;
			if(isset($form['actuser']) && $form['actuser']=='on') $is_active=1;
			
			$pwd_sql='';
			if(isset($form['password']))
			{
				$password=$this->getPasswordHash($form['password']);
				$pwd_sql=",password='${password}'";
			}
			
			$query="update auth_user set username='${username}'".$pwd_sql.",first_name='${first_name}',last_name='${last_name}',is_active=${is_active} where id=${user_id}";
			
			//echo $query;
			
			$res=$db->update($query);
			return array('error'=>false,'res'=>$res);
		}else{
			return array('error'=>true,'error_message'=>'db connection lost!');
		}
	}

	
	function getPasswordHash($password)
	{
		return hash('sha256',hash('sha256',$password).$this->getSalt());
	}
	private function getSalt()
	{
		$salt_file='/var/db/clonos/salt';
		if(file_exists($salt_file)) return trim(file_get_contents($salt_file));
		return 'noSalt!';
	}
	function userRegister($user_info=array())
	{
		if(empty($user_info)) return false;
		if(isset($user_info['username']) && isset($user_info['password']))
		{
			$db=new Db('clonos');
			if($db!==false)
			{
				$res=$db->select("select username from auth_user where username='${user_info['username']}'");
				if(!empty($res))
				{
					$res['user_exsts']=true;
					return $res;
				}
				
				$username=$user_info['username'];
				$password=$this->getPasswordHash($user_info['password']);
				$first_name=$user_info['first_name'];
				$last_name=$user_info['last_name'];
				$is_active=0;
				if(isset($user_info['actuser']) && $user_info['actuser']=='on') $is_active=1;
				$query=$db->query_protect("insert into auth_user
				(username,password,first_name,last_name,is_active,date_joined)
				values
				('${username}','${password}','${first_name}','${last_name}',${is_active},datetime('now','localtime'))");
				$res=$db->insert($query);
				return array('error'=>false,'res'=>$res);
			}
		}
	}
	function userRegisterCheck($user_info=array())
	{
		/*
		[0] => Array
		(
			[id] => 1
			[username] => admin
			[password] => 01...87a
			[first_name] => Admin
			[last_name] => Admin
			[last_login] => 
			[is_active] => 1
			[date_joined] => 2017-12-02 00:09:00
			[sess_id] => 
			[secure_sess_id] => 
		)
		*/
		if(empty($user_info)) return false;
		if(isset($user_info['login']) && isset($user_info['password']))
		{
			$db=new Db('clonos');
			if($db!==false)
			{
				$pass=$this->getPasswordHash($user_info['password']);
				$res=$db->selectAssoc(
					"select id,username
					from auth_user 
					where 
						username='${user_info['login']}' and
						password='${pass}' and
						is_active=1");
				if(empty($res))
				{
					sleep(3);
					return array('errorCode'=>1,'message'=>'user not found!');
				}
				$res['errorCode']=0;
				
				$id=$res['id'];
				$ip=$this->_client_ip;
				$memory_hash=md5($id.$res['username'].time());
				$secure_memory_hash=md5($memory_hash.$ip);
				
				/*
				$query="update auth_user set sess_id='${memory_hash}', secure_sess_id='${secure_memory_hash}', last_login=datetime('now','localtime') where id=${id}";
				$db->update($query);
				*/
				
				//$query="update auth_list set secure_sess_id='${secure_memory_hash}',auth_time=datetime('now','localtime') where sess_id='${memory_hash}'";	//sess_id='${memory_hash}',
				$query="update auth_list set sess_id='${memory_hash}',secure_sess_id='${secure_memory_hash}',auth_time=datetime('now','localtime') where user_id=${id} and user_ip='${ip}'";
				$qres=$db->update($query);
				//print_r($qres);
				if(isset($qres['rowCount']))
				{
					if($qres['rowCount']==0)
					{
						$query="insert into auth_list
							(user_id,sess_id,secure_sess_id,user_ip,auth_time) values
							(${id},'${memory_hash}','${secure_memory_hash}','${ip}',datetime('now','localtime'))";
						$qres=$db->insert($query);
					}
				}
				
				setcookie('mhash',$memory_hash,time()+1209600);
				
				return $res;
			}
		}
		return array('message'=>'unregistered user','errorCode'=>1);
	}
	function userAutologin()
	{
		if(isset($_COOKIE['mhash']))
		{
			$memory_hash=$_COOKIE['mhash'];
			$secure_memory_hash=md5($memory_hash.$this->_client_ip);
			$db=new Db('clonos');
			if($db!==false)
			{
				$query="select au.id,au.username
					from auth_user au, auth_list al
					where 
						al.secure_sess_id='${secure_memory_hash}' and
						au.id=al.user_id and
						au.is_active=1";
				//echo $query;
				$res=$db->selectAssoc($query);
				//print_r($res);
				if(!empty($res))
				{
					$res['error']=false;
					return $res;
				}
			}
		}
		return array('error'=>true);
	}
	
	function userRemove()
	{
		$form=$this->form;
		
		$id=$form['user_id'];
		if(is_numeric($id) && $id>0)
		{
			$query="delete from auth_user where id=${id}";
			$db=new Db('clonos');
			if($db!==false)
			{
				$res=$db->select($query);
				return $res;
			}else{
				return array('error'=>true,'error_message'=>'DB connection error!');
			}
		}
	}
	
	function userEditInfo()
	{
		$form=$this->form;
		
		if(!isset($form['user_id']))
		{
			return array('error'=>true,'error_message'=>'incorrect data!');
		}
		
		$user_id=$form['user_id'];
		$db=new Db('clonos');
		if($db!==false)
		{
			$res=$db->selectAssoc("select username,first_name,last_name,is_active as actuser from auth_user where id=${user_id}");
			return array(
				'dialog'=>$form['dialog'],
				'vars'=>$res,
				'error'=>false,
				'tblid'=>$form['tbl_id'],
				'user_id'=>$user_id,
			);
		}else{
			return array('error'=>true,'error_message'=>'DB connection error!');
		}
	}
	
	function userGetInfo()
	{
		$db=new Db('clonos');
		if($db!==false)
		{
			$res=$db->select("select * from auth_user limit 1");
			return $res;
		}else{
			return array('DB connection error!');
		}
	}
	
	function getUserName()
	{
		return $this->_user_info['username'];
	}
	
	function vmTemplateAdd()
	{
		$form=$this->form;
		
		$name=$form['name'];
		$description=$form['description'];
		$pkg_vm_ram=$form['pkg_vm_ram'];
		$pkg_vm_disk=$form['pkg_vm_disk'];
		$pkg_vm_cpus=$form['pkg_vm_cpus'];
		$owner=$this->_user_info['username'];
		$query="insert into vmpackages (name,description,pkg_vm_ram,pkg_vm_disk,pkg_vm_cpus,owner,timestamp)
			values
			('${name}','${description}','${pkg_vm_ram}','${pkg_vm_disk}','${pkg_vm_cpus}','${owner}',datetime('now','localtime'))";
		
		$db=new Db('base','local');
		if($db!==false)
		{
			$res=$db->insert($query);
			if($res!==false)
			{
				if(!$res['error'])
					return $this->messageSuccess($res);
				else
					return $this->messageError('sql error!',$res);
			}
			else
				return $this->messageError('sql error!');
		}else{
			return $this->messageError('data incorrect!'); //array('error'=>true,'error_message'=>'data incorrect!');
		}
	}
	function vmTemplateEditInfo()
	{
		$form=$this->form;
		
		if(!isset($form['template_id']))
		{
			return $this->messageError('incorrect data!');
		}
		
		$tpl_id=$form['template_id'];
		$db=new Db('base','local');
		if($db!==false)
		{
			$res=$db->selectAssoc("select name,description,pkg_vm_ram,pkg_vm_disk,pkg_vm_cpus from vmpackages where id=${tpl_id}");
			return $this->messageSuccess(array('vars'=>$res,'template_id'=>$tpl_id));
		}else{
			return $this->messageError('DB connection error!');
		}
	}
	function vmTemplateEdit()
	{
		$form=$this->form;
		
		$id=$form['template_id'];
		if(!isset($id) || $id<1) $this->messageError('wrong data!');
		$name=$form['name'];
		$description=$form['description'];
		$pkg_vm_ram=$form['pkg_vm_ram'];
		$pkg_vm_disk=$form['pkg_vm_disk'];
		$pkg_vm_cpus=$form['pkg_vm_cpus'];
		$owner=$this->_user_info['username'];
		$query="update vmpackages set
			name='${name}',description='${description}',
			pkg_vm_ram='${pkg_vm_ram}',pkg_vm_disk='${pkg_vm_disk}',
			pkg_vm_cpus='${pkg_vm_cpus}',owner='${owner}',timestamp=datetime('now','localtime') where id=${id}";
		
		$db=new Db('base','local');
		if($db!==false)
		{
			$res=$db->update($query);
			if($res!==false)
			{
				return $this->messageSuccess($res);
			}
			else
				return $this->messageError('sql error!');
		}else{
			return $this->messageError('db connection error!');
		}
	}
	function vmTemplateRemove()
	{
		$form=$this->form;
		
		$id=$form['template_id'];
		if(is_numeric($id) && $id>0)
		{
			$query="delete from vmpackages where id=${id}";
			$db=new Db('base','local');
			if($db!==false)
			{
				$res=$db->select($query);
				return $this->messageSuccess($res);
			}else{
				return $this->messageError('DB connection error!');
			}
		}else{
			return $this->messageError('wrong data!');
		}
	}
	
	
	function messageError($message,$vars=array())
	{
		$rarr=array(
			'error'=>true,
			'error_message'=>$message,
		);
		return array_merge($rarr,$vars);
	}
	function messageSuccess($vars=array())
	{
		$rarr=array(
			'error'=>false,
		);
		return array_merge($rarr,$vars);
	}
	
	
	function getImportedImages()
	{
		$images=array();
		$path=$this->media_import;
		$files=$this->getImagesList($path);
		foreach($files as $key=>$file)
		{
			if(file_exists($file['fullname']))
			{
				$fp=fopen($file['fullname'],'r');
				$buf=fread($fp,300);
				fclose($fp);
				$pat='#emulator="([^\"]*)"#';
				preg_match($pat,$buf,$res);
				if(!empty($res))
				{
					/*
					$type=$res[1];
					$images[$res[1]][]=$file;
					*/
					$files[$key]['type']=$res[1];
				}
			}
		}
		/*
		unset($files);
		return $images;
		*/
		return $files;
	}
	function getImportedImageInfo()
	{
		$form=$this->form;
		$name=$form['id'];
		$info=$this->getImageInfo($name);
		return $info;
	}
	
	function getImagesList($path)
	{
		$files=array();
		foreach (glob($path."*.img") as $filename)
		{
			$info=pathinfo($filename);
			$arr=array(
				'name'=>$info['basename'],
				'fullname'=>$filename,
			);
			$files[]=$arr;
		}
		return $files;
	}
	
	function getImageInfo($imgname)
	{
		if(empty($imgname)) return false;
		
		$file=$this->media_import.$imgname;
		if(!file_exists($file)) return false;
		
		$fp=fopen($file,'r');
		$buf=fread($fp,300);
		fclose($fp);
		
		$type=$this->getImageVar('emulator',$buf);
		$jname=$this->getImageVar('jname',$buf);
		$orig_jname=$jname;
		$ip=$this->getImageVar('ip4_addr',$buf);
		$hostname=$this->getImageVar('host_hostname',$buf);
		
		$name_comment='';
		$db=new Db('base','local');
		if($db!==false)
		{
			$jail=$db->selectAssoc("SELECT jname FROM jails WHERE jname='{$jname}'");
			
			if($jname==$jail['jname'])
			{
				$jres=$this->getFreeJname(false,$type);
				if($jres['error']) return $this->messageError('Something wrong...');
				$jname=$jres['freejname'];
				$name_comment='* '.$this->translate('Since imported name already exist, we are change it');
			}
		}
		
		return array('orig_jname'=>$orig_jname,'jname'=>$jname,'host_hostname'=>$hostname,'ip4_addr'=>$ip,'file_id'=>$imgname,'type'=>$type,'name_comment'=>$name_comment);
	}
	function getImageVar($name,$buf)
	{
		$val=false;
		$pat='#'.$name.'="([^\"]*)"#';
		preg_match($pat,$buf,$res);
		if(!empty($res))
		{
			$val=$res[1];
		}
		return $val;
	}
	
	function imageExport()
	{
		// cbsd jexport jname=XXX dstdir=<path_to_imported_dir>
		$form=$this->form;
		$jname=$form['id'];
		if(empty($jname)) $this->messageError('Jname is incorrect in export command! Is «'.$jname.'».');
		$cmd='cbsd jexport gensize=1 jname='.$jname.' dstdir='.$this->media_import;
		$res=$this->cbsd_cmd('task owner='.$this->_user_info['username'].' mode=new /usr/local/bin/'.$cmd);
		return $res;
	}
	
	function imageImport()
	{
		$form=$this->form;
		
		$file_id=$form['file_id'];
		$res=$this->getImageInfo($file_id);
		if($res===false) return $this->messageError('File not found!');
		
		$jname=$form['jname'];
		
		$attrs=array();
		if($jname!=$res['orig_jname'])
			$attrs[]='new_jname='.$jname;
		
		if($form['ip4_addr']!=$res['ip4_addr'])
			$attrs[]='new_ip4_addr='.$form['ip4_addr'];
		
		if($form['host_hostname']!=$res['host_hostname'])
			$attrs[]='new_host_hostname='.$form['host_hostname'];
		
		$file='jname='.$this->media_import.$file_id;
		$attrs[]=$file;
		$cmd='cbsd jimport '.join($attrs,' ');
		
		$res=$this->cbsd_cmd('task owner='.$this->_user_info['username'].' mode=new /usr/local/bin/'.$cmd);
		
		return $res;
	}
	
	function imageRemove()
	{
		$form=$this->form;
		
		$cmd='cbsd imgremove path='.$this->media_import.' img='.$form['jname'];
		
		$res=$this->cbsd_cmd('task owner='.$this->_user_info['username'].' mode=new /usr/local/bin/'.$cmd);
		return $res;
	}

}