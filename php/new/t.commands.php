<?php
include 't.cmd.jail.php';
include 't.cmd.bhyve.php';
include 't.cmd.helpers.php';

trait tCommands {

	use tcJail, tcBhyve, tcHelpers;
	
	public $table_templates;

	function ccmd_trltGo()
	{
		$dbres=[];
		$form=$this->_vars['form_data'];
		if(isset($form['phraseID']) && is_numeric($form['phraseID']))
		{
			$db=new Db('clonos');
			if(!$db->isConnected())
				return array('error'=>true,'error_message'=>'translate db connection lost!');
			
			$dbres=$db->selectOne(
				'SELECT e.text as eng, o.text as oth FROM "lang_en" as e left join "lang_other" as o on e.id=o.en_id where e.id=?',
				[[$form['phraseID'],PDO::PARAM_INT]]
			);
			
			$dbres['phraseID']=$form['phraseID'];
			$dbres['type']=$form['type'];
			$dbres['dialog']=$form['dialog'];
		}
		return $dbres;
		
	}
	function ccmd_trltUpdate()
	{
		$lang=$this->_locale->get_lang();
		$form=$this->_vars['form_data'];

		switch($form['type'])
		{
			case 'dialog':
				$cache_file_name=$this->realpath_dialogs.self::TRANSLATE_CACHE_DIR.
					DIRECTORY_SEPARATOR.$lang.'.'.$form['dialog'].'.php';
				break;
			case 'pages':
				$cache_file_name=$this->realpath_page.self::TRANSLATE_CACHE_DIR.
					DIRECTORY_SEPARATOR.$lang.'.index.php';
				break;
			default:
				echo $cache_file_name;
				exit;
				break;
		}

		if(file_exists($cache_file_name)) unlink($cache_file_name);
		//echo $cache_file_name;
		//exit;
		//echo $file_name;exit;
		$db=new Db('clonos');
		if(!$db->isConnected())
			return array('error'=>true,'error_message'=>'translate db connection error!');

		$dbres=$db->update("update lang_other set text=?,lang=? where en_id=?",[
			[$form['translText'],PDO::PARAM_STR],
			[$lang,PDO::PARAM_STR],
			[$form['phraseID'],PDO::PARAM_INT]
		]);
		
		//echo '<pre>';print_r($dbres);
		
		if(!isset($dbres['error']))
		{
			if($dbres['rowCount']==0)
			{
				$dbres=$db->insert("insert into lang_other (en_id,text,lang) values (?,?,?)",[
					[$form['phraseID'],PDO::PARAM_INT],
					[$form['translText'],PDO::PARAM_STR],
					[$lang,PDO::PARAM_STR]
				]);
				//print_r($dbres);exit;
				if($dbres['error'])
				{
					return $dbres;
				}
				$dbres['phraseID']=$dbres['lastID'];
			}
		}
		
		
		//$back_file=
		$rowCount=0;
		if(isset($dbres['rowCount'])) $rowCount=$dbres['rowCount'];
		
		return [
			'error'=>false,
			'rowCount'=>$rowCount,
			'phraseID'=>$form['phraseID'],
			'phrase'=>$form['translText']
		];
	}

	function ccmd_getJsonPage(){
		$included_result_array=false;
		if(file_exists(self::$json_name)){
			include(self::$json_name);
			
			if(is_array($included_result_array)){
				$new_array=array_merge($this->sys_vars,$included_result_array);
				echo json_encode($new_array);
				exit;
			}
		}
		echo json_encode($this->sys_vars);
		exit;
	}

	/* example return array('message'=>'unregistered user','errorCode'=>1) */
	function ccmd_login(){
		return $this->userRegisterCheck($this->_vars['form_data']);
	}

	function ccmd_authkeyAdd(){
		$db=new Db('base','authkey');
		if(!$db->isConnected()) return array('error'=>'Database error');

		//$res=array('error'=>false,'lastId'=>2);
		$res=$db->insert("INSERT INTO authkey (name,authkey) VALUES (?, ?)", array([$this->form['keyname']], [$this->form['keysrc']]));
		if($res['error']) return array('error'=>$res);
		
		$html='';
		$hres=$this->getTableChunk('authkeyslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'keyid'=>$res['lastID'],
				'keyname'=>$this->form['keyname'],
				'keysrc'=>$this->form['keysrc'],
				'deltitle'=>$this->translate('Delete'),
			);

			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}

		return array('keyname'=>$this->form['keyname'],'html'=>$html);
	}

	function ccmd_authkeyRemove(){
		$form=$this->_vars['form_data'];

		$db=new Db('base','authkey');
		if(!$db->isConnected()) return array('error'=>true,'res'=>'Database error');

		$res=$db->update('DELETE FROM authkey WHERE idx=?', array([$form['auth_id']]));
		if($res===false) return array('error'=>true,'res'=>print_r($res,true));

		return array('error'=>false,'auth_id'=>$form['auth_id']);
	}

	function ccmd_vpnetAdd(){
		$form=$this->_vars['form_data'];

		$db=new Db('base','vpnet');
		if(!$db->isConnected()) return array('error'=>'Database error');

		$res=$db->insert("INSERT INTO vpnet (name,vpnet) VALUES (?, ?)", array([$form['netname']],[$form['network']]));
		if($res['error']) return array('error'=>$res);

		$html='';
		$hres=$this->getTableChunk('vpnetslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'netid'=>$res['lastID'],
				'netname'=>$form['netname'],
				'network'=>$form['network'],
				'deltitle'=>$this->translate('Delete'),
			);

			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}

		return array('netname'=>$form['netname'],'html'=>$html);
	}

	function ccmd_vpnetRemove(){
		$form=$this->_vars['form_data'];

		$db=new Db('base','vpnet');
		if(!$db->isConnected()) return array('error'=>true,'res'=>'Database error');

		$res=$db->update('DELETE FROM vpnet WHERE idx=?', array([(int)$form['vpnet_id']]));
		if($res===false) return array('error'=>true,'res'=>print_r($res,true));

		return array('error'=>false,'vpnet_id'=>$form['vpnet_id']);
	}

	function ccmd_mediaRemove(){

		$db=new Db('base','storage_media');
		if(!$db->isConnected()) return array('error'=>true,'res'=>'Database error');

		//$res=$db->update('DELETE FROM media WHERE idx=?', array([$this->form['media_id']]));
		$res=$db->selectOne("SELECT * FROM media WHERE idx=?", array([(int)$this->form['media_id'], PDO::PARAM_INT]));
		if($res===false || empty($res)) return array('error'=>true,'res'=>print_r($res,true));

		//if($res['jname']=='-')	// если медиа отвязана, то про

		$res=CBSD::run(
			'media mode=remove name="%s" path="%s" jname="%s" type="%s"', //.$res['name']
			array($res['name'], $res['path'], $res['jname'], $res['type'])
		);

		if($res['error']){
			$arr['error']=true;
			$arr['error_message']='File image was not deleted! '.$res['error_message'];
		} else {
			$arr['error']=false;
		}

		$arr['media_id']=$this->form['media_id'];
		$arr['cmd']=$res;
		//echo json_encode($arr);

		//return array('error'=>false,'media_id'=>$this->form['media_id']);
		return $arr;
	}

	function ccmd_srcRemove(){
		$ver=str_replace('src','',$this->formform['jname']);
		if(empty($ver)) return array('error'=>true,'errorMessage'=>'Version of sources is emtpy!');
//		return CBSD::run(
//			'task owner='.$username.' mode=new {cbsd_loc} removesrc inter=0 ver=%s jname=#src%s',
//			array($this->_user_info['username'], $ver, $ver)
//		);
		return CBSD::run(
			'task owner='.$username.' mode=new {cbsd_loc} removesrc inter=0 ver=%s jname=src%s',
			array($this->_user_info['username'], $ver, $ver)
		);
	}

	function ccmd_srcUpdate(){
		$ver=str_replace('src','',$this->form['jname']);
		$stable=(preg_match('#\.\d#',$ver))?0:1;
		if(empty($ver)) return array('error'=>true,'errorMessage'=>'Version of sources is emtpy!');
//		return CBSD::run(
//			'task owner=%s mode=new {cbsd_loc} srcup stable=%s inter=0 ver=%s jname=#src%s',
//			array($this->_user_info['username'], $stable, $ver, $ver)
//		);
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} srcup stable=%s inter=0 ver=%s jname=src%s',
			array($this->_user_info['username'], $stable, $ver, $ver)
		);
	}

	function ccmd_baseRemove(){	//$id
		//$id=str_replace('base','',$id);
		//base10.3-amd64-0
		$id=$this->form['jname'];
		preg_match('#base([0-9\.]+)-([^-]+)-(\d+)#',$id,$res);
		$ver=$res[1];
		$arch=$res[2];
		$stable=$res[3];

		$username=$this->_user_info['username'];

		$remove_cmd="task owner={$username} mode=new /usr/local/bin/cbsd removebase inter=0 stable={$stable} ver={$ver} arch={$arch} jname=#".$this->form['jname'];

		ClonOS::syslog("cmd.php removeBase cmd:". $remove_cmd);

		$res=CBSD::run('task owner=%s mode=new {cbsd_loc} removebase inter=0 stable=%s ver=%s arch=%s jname=%s',array($this->_user_info['username'], $stable, $ver, $arch, $this->form['jname']));

		return $res;
	}

	function ccmd_basesCompile(){
		$form=$this->form;
		if(!isset($form['sources']) || !is_numeric($form['sources'])) return array('error'=>true,'errorMessage'=>'Wrong OS type selected!');
		$id=(int)$form['sources'];

		$db=new Db('base','local');
		if(!$db->isConnected()) return array('error'=>true,'errorMessage'=>'Database connect error!');

		$base=$db->selectOne("SELECT idx,platform,ver FROM bsdsrc WHERE idx=?", array([$id, PDO::PARAM_INT])); // Casted above as 
		$ver=$base['ver'];
		$stable_arr=array('release','stable');
		$stable_num=strlen(intval($ver))<strlen($ver)?0:1;
		$stable=$stable_arr[$stable_num];
		$bid=$ver.'-amd64-'.$stable_num;	// !!! КОСТЫЛЬ

		$res=$this->fillRepoTr($id);
		$html=$res['html'];
		$res=$res['arr'];

		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} world inter=0 stable=%s ver=%s jname=#base%s',
			array($this->_user_info['username'], $res['stable'], $ver, $bid)
		);
		//$res['retval']=0;$res['message']=3;

		$err='';
		$taskId=-1;
		if($res['retval']==0){
			$err='World compile start!';
			$taskId=$res['message'];
		}

		return array('errorMessage'=>'','jail_id'=>'base'.$bid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode,'txt_status'=>$this->translate('Compiling'));
	}

	function ccmd_repoCompile()
	{
		if(!isset($this->form['version']) || !is_numeric($this->form['version'])) {
			return array('error'=>true,'errorMessage'=>'Wrong OS type input!');
		}

		$ver=$this->form['version'];
		$stable_arr=array('release','stable');
		$html='';
		$hres=$this->getTableChunk('baseslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			# TODO: This next line is weird
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

			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}

		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} repo action=get sources=base inter=0 stable=%s ver=%s jname=#base%s',
			array($this->_user_info['username'], $stable_num, $ver, $bid)
		);

		//$res['retval']=0;$res['message']=3;

		$err='';
		$taskId=-1;
		if($res['retval']==0){
			$err='Repo download start!';
			$taskId=$res['message'];
		}

		return array('errorMessage'=>'','jail_id'=>'base'.$bid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode,'txt_status'=>$this->translate('Fetching'));
	}

	function ccmd_logLoad()
	{
		$log_id=$this->_vars['form_data']['log_id'];
		if(!is_numeric($log_id)) return array('error'=>'Log ID must be a number');

		$html='';
		$buf='';
		$log_file='/tmp/taskd.'.$log_id.'.log';
		if(file_exists($log_file)){
			$filesize=filesize($log_file);
			if($filesize<=204800){
				$buf=file_get_contents($log_file);
			}else{
				$fp=fopen($log_file,'r');
				if($fp)	{
					fseek($fp,-1000,SEEK_END);
					$buf=fread($fp,1000);
					$html='<strong>Last 1000 Bytes of big file data:</strong><hr />';
				}
				fclose($fp);
			}
			$buf=htmlentities(trim($buf));
			$arr=preg_split('#\n#iSu',trim($buf));
			if ($arr != false){
				foreach($arr as $txt){
					$html.='<p class="log-p">'.$txt.'</p>';
				}
			}
			return array('html'=>'<div style="font-weight:bold;">Log ID: '.$log_id.'</div><br />'.$html);
		}

		return array('error'=>'Log file is not exists!');
	}

	function ccmd_logFlush(){
		return CBSD::run('task mode=flushall', array());
	}


	function ccmd_getFreeCname(){
		$arr=array();
		$res=$this->CBSD::run("freejname default_jailname=kube", []);
		if($res['error']){
			$arr['error']=true;
			$arr['error_message']=$err['error_message'];
		}else{
			$arr['error']=false;
			$arr['freejname']=$res['message'];
		}
		return $arr;
	}

	function ccmd_k8sCreate(){
		$form=$this->form;
		$res=array();
		$ass_arr=array(
			'master_nodes'=>'init_masters',
			'worker_nodes'=>'init_workers',
			'master_ram'=>'master_vm_ram',
			'master_cpus'=>'master_vm_cpus',
			'master_img'=>'master_vm_imgsize',
			'worker_ram'=>'worker_vm_ram',
			'worker_cpus'=>'worker_vm_cpus',
			'worker_img'=>'worker_vm_imgsize',
		);

		$add_param=array(
			'master_ram'=>'g',
			'master_img'=>'g',
			'worker_ram'=>'g',
			'worker_img'=>'g',
		);

		foreach($form as $key=>$value){
			if(isset($ass_arr[$key])){
				if(isset($add_param[$key])){
					$value.=$add_param[$key];
				}
				$res[$ass_arr[$key]]=$value;
			}
		}

		$res['pv_enable']="0";
		if(isset($form['pv_enable'])){
			if($form['pv_enable']=='on') $res['pv_enable']="1";
		}

		$res['kubelet_master']="0";
		if(isset($form['kubelet_master'])){
			if($form['kubelet_master']=='on') $res['kubelet_master']="1";
		}

		$cname=$form['cname'];

		$url='http://144.76.225.238/api/v1/create/'.$cname;
		$result=$this->postCurl($url,$res);

		return $result;
	}

	function ccmd_k8sRemove()
	{
		$form=$this->form;
		if(isset($form['k8sname']) && !empty($form['k8sname']))
		{
			$url='http://144.76.225.238/api/v1/destroy/'.$form['k8sname'];
			return ($this->getCurl($url));
		}else{
			return array('error'=>'true','errorMessage'=>'something wrong...');
		}
	}
	
	function ccmd_settingsGetList()
	{
		$res=CBSD::run(
			'/root/bin/web_upgrade listjson',
			array()
		);
		if($res['error']){
			$arr['error']=true;
			$arr['error_message']=$err['error_message'];
		}else{
			$arr['error']=false;
			$arr['update_list']=json_decode($res['message']);
		}
	}
	
	function ccmd_settingsUpdateCheck()
	{
		$res=CBSD::run(
			'/root/bin/web_upgrade check_upgrade',	//listjson
			array()
		);
		if($res['error']){
			$arr['error']=true;
			$arr['error_message']=$res['error_message'];
		}else{
			$arr['error']=false;
			$arr['update_list']=json_decode($res['message']);
		}
		return $arr;
	}
	
	function ccmd_settingsUpdateComponents()
	{
		$res=CBSD::run(
			'/root/bin/web_upgrade upgrade',
			array()
		);
		if($res['error']){
			$arr['error']=true;
			$arr['error_message']=$err['error_message'];
		}else{
			$arr['error']=false;
			$arr['response']=$res['msg'];
			//$arr['update_list']=json_decode($res['message']);
		}
		return $arr;
	}

	function ccmd_vmOsInfo()	//getVMOSListInfo
	{
		return array('form_items'=>$this->getBhyveFormItems($this->form['vmOsProfile'],$this->form['obtain']));
	}

	function ccmd_getObtainFormItems($os_name='')
	{
		$res=array('form_items'=>$this->getBhyveFormItems($os_name,'obtain'));
		return $res;
	}


	function ccmd_vmTemplateAdd(){
		$db=new Db('base','local');
		if(!$db->isConnected()) return $this->messageError('data incorrect!'); //array('error'=>true,'error_message'=>'data incorrect!');
		$owner=$this->_user_info['username'];
		$query="INSERT INTO vmpackages (name,description,pkg_vm_ram,pkg_vm_disk,pkg_vm_cpus,owner,timestamp)
			VALUES (?,?,?,?,?,?,datetime('now','localtime'))";

		$res=$db->insert($query, array(
			[$this->form['name']],
			[$this->form['description']],
			[$this->form['pkg_vm_ram']],
			[$this->form['pkg_vm_disk']],
			[$this->form['pkg_vm_cpus']],
			[$owner]
		));

		if($res['error'] == false){
			return $this->messageSuccess($res); 
		} else {
			return $this->messageError('sql error!',$res);
		}
	}

	function ccmd_vmTemplateEditInfo(){
		if(!isset($this->form['template_id'])) return $this->messageError('incorrect data!');

		$tpl_id=(int)$this->form['template_id'];
		$db=new Db('base','local');
		if(!$db->isConnected()) return $this->messageError('DB connection error!');

		$res=$db->selectOne(
			"select name,description,pkg_vm_ram,pkg_vm_disk,pkg_vm_cpus from vmpackages where id=?",
			array([$tpl_id, PDO::PARAM_INT])
		);
		return $this->messageSuccess(array('vars'=>$res,'template_id'=>$tpl_id));
	}

	function ccmd_vmTemplateEdit(){
		$id=$this->form['template_id'];
		if(!isset($id) || $id<1) $this->messageError('wrong data!');
		$db=new Db('base','local');
		if(!$db->isConnected()) return $this->messageError('db connection error!');

		$query="update vmpackages set
			name=?,description=?, pkg_vm_ram=?,pkg_vm_disk=?, pkg_vm_cpus=?, owner=?, timestamp=datetime('now','localtime')
			where id=?";

		$res=$db->update($query, array(
			[$this->form['name'], PDO::PARAM_STR],
			[$this->form['description'], PDO::PARAM_STR],
			[$this->form['pkg_vm_ram'],  PDO::PARAM_STR],
			[$this->form['pkg_vm_disk'], PDO::PARAM_STR],
			[$this->form['pkg_vm_cpus'], PDO::PARAM_STR],
			[$this->_user_info['username'], PDO::PARAM_STR],
			[(int)$id, PDO::PARAM_INT]
		));
		if($res!==false) return $this->messageSuccess($res);

		return $this->messageError('sql error!');
	}

	function ccmd_vmTemplateRemove(){
		$id=$this->form['template_id'];
		if(!is_numeric($id) || (int)$id <= 0) return $this->messageError('wrong data!');

		$query="DELETE FROM vmpackages WHERE id=?";
		$db=new Db('base','local');
		if(!$db->isConnected()) return $this->messageError('DB connection error!');

		$res=$db->select($query, array([$id, PDO::PARAM_INT]));
		return $this->messageSuccess($res);
	}

	function ccmd_getImportedImageInfo()
	{
		return $this->getImageInfo($this->form['id']);
	}

	function ccmd_imageExport(){
		// cbsd jexport jname=XXX dstdir=<path_to_imported_dir>
		$jname=$this->form['id'];
		if(empty($jname)) $this->messageError('Jname is incorrect in export command! Is «'.$jname.'».');

		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} jexport gensize=1 jname=%s dstdir=%s',
			array($this->_user_info['username'], $jname, self::$media_import)
		);
	}

	function ccmd_imageImport(){

		$file_id=$this->form['file_id'];
		$jname=$this->form['jname'];
		$res=$this->getImageInfo($file_id);
		if($res===false) return $this->messageError('File not found!');

		$cmd = 'task owner=%s mode=new {cbsd_loc} jimport ';
		$attrs=array($this->_user_info['username']);

		if($jname!=$res['orig_jname']) {
			$cmd .= 'new_jname=%s ';
			$attrs[]= $jname;
		}

		if($this->form['ip4_addr']!=$res['ip4_addr']){
			$cmd .= 'new_ip4_addr=%s ';
			$attrs[]=$this->form['ip4_addr'];
		}

		if($this->form['host_hostname']!=$res['host_hostname']) {
			$cmd .= 'new_host_hostname=%s ';
			$attrs[]=$this->form['host_hostname'];
		}

		$cmd .= 'jname=%s';
		$attrs[]=$file;

		return CBSD::run($cmd, $attrs);
	}

	function ccmd_imageRemove(){
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} imgremove path=%s img=$s',
			array($this->_user_info['username'], self::$media_import, $this->form['jname'])
		);
	}

	function ccmd_getSummaryInfo(){

		if(!isset($this->form['mode'])) $this->form['mode']='';
		$jail_name=$this->form['jname'];
		$res=array();

		if(empty($jail_name)) return $res;

		$res['jname']=$jail_name;

		$db=new Db('racct',array('jname'=>$jail_name));
		if($db->isConnected()){
			$query=$db->select("SELECT ? as name,idx as time,memoryuse,pcpu,pmem,maxproc,openfiles,readbps, writebps,readiops,writeiops 
								FROM racct ORDER BY idx DESC LIMIT 25;", array([$jail_name]));	// where idx%5=0
			$res['__all']=$query;
		}

		if($this->form['mode'] == 'bhyveslist'){
			$res['properties']=$this->getSummaryInfoBhyves();
			return $res;
		}

		//$workdir/jails-system/$jname/descr
		$filename=self::$workdir.'/jails-system/'.$jail_name.'/descr';
		if(file_exists($filename)) $res['description']=nl2br(file_get_contents($filename));

		$sql="SELECT host_hostname,ip4_addr,allow_mount,allow_nullfs,allow_fdescfs,interface,baserw,mount_ports,
			  astart,vnet,mount_fdescfs,allow_tmpfs,allow_zfs,protected,allow_reserved_ports,allow_raw_sockets,
			  allow_fusefs,allow_read_msgbuf,allow_vmm,allow_unprivileged_proc_debug
			  FROM jails WHERE jname=?";
		$db=new Db('base','local');
		if($db->isConnected()){
			$query=$db->selectOne($sql, array([$jail_name]));
			$html='<table class="summary_table">';

			foreach($query as $q=>$k){
				if(is_numeric($k) && ($k==0 || $k==1)){
					$k = ($k==0) ? 'no':'yes';
				}
				$html.='<tr><td>'.$this->translate($q).'</td><td>'.$this->translate($k).'</td></tr>';
			}

			$html.='</table>';
			$res['properties']=$html;
		}

		return $res;
	}
	
	function ccmd_diskInfoSmart(){
		if(!isset($this->form['mode'])) $this->form['mode']='';
		$disk=$this->form['disk'];
		
		$file='/var/db/cixnas/dsk/'.$disk.'.smartinfo';
		$html='no data';
		if(file_exists($file))
		{
			//$mtime=filemtime($file);
			//echo 'Time generated: '.date('d.m.Y H:i:s',$mtime)."\n\n";
			$html=file_get_contents($file);
		}
		
		$res['disk']=$disk;
		$res['html']='<pre>'.$html.'</pre>';
		return $res;
	}









	function getTableChunk($table_name, $tag)
	{
		if(isset($this->table_templates[$table_name][$tag])){
			return $this->table_templates[$table_name][$tag];
		}

		//$file_name=self::$realpath_page.$table_name.'.table';
		$file_name=$this->json_path.$table_name.'.table';
		if(!file_exists($file_name)) return false;
		$file=file_get_contents($file_name);
		$pat='#[\s]*?<'.$tag.'[^>]*>(.*)<\/'.$tag.'>#iUs';
 		if(preg_match($pat,$file,$res)){
			$this->table_templates[$table_name][$tag]=$res;
			return $res;
		}
		return ''; # TODO ???
	}

	function check_locktime($nodeip)
	{
		$lockfile=$this->workdir."/ftmp/shmux_{$nodeip}.lock";
		if (file_exists($lockfile)){
			$cur_time = time();
			$difftime=(($cur_time - filemtime($lockfile)) / 60);
			if ($difftime > 1){ 
				return round($difftime);
			}
		}

		return 0; //too fresh or does not exist
	}

	function check_vmonline($vm)
	{
		$vmmdir="/dev/vmm";

		if(file_exists($vmmdir)){
			if($handle=opendir($vmmdir)){
				while(false!==($entry=readdir($handle))){
					if($entry[0]==".") continue;
					if($vm==$entry) {
						closedir($handle);
						return 1;
					}
				}
				closedir($handle);
			}
		}

		return 0;
	}

	function get_node_info($nodename,$value){
		$db = new Db('', '', $this->realpath."/var/db/nodes.sqlite"); 
		if (!$db->isConnected()) return array('error'=>true,'res'=> $db->error_message);

		$result = $db->select("SELECT ? FROM nodelist WHERE nodename=?", array([$value],[$nodename]));

		foreach($result as $res){
			if(isset($res[$value])){
				return $res[$value];
			}
		}
		// TODO: what if not found ?
	}

	function getRunningTasks($ids=array()){
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
		if(!empty($ids)){
			$tid=join("','",$ids);
			$query="SELECT id,cmd,status,jname FROM taskd WHERE status<2 AND jname IN (?)";
			$cmd='';
			$txt_status='';
			$tasks=(new Db('base','cbsdtaskd'))->select($query, array([$tid]));
//print_r($tid);exit;
			if(!empty($tasks)) foreach($tasks as $task){
				$rid=preg_replace('/^#/','',$task['jname']);
				foreach($check_arr as $key=>$val){
					if(strpos($task['cmd'],$key)!==false){
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

	private function doTask($key, $task){
		if($task['status'] != -1) return false;

		switch($task['operation']){
			case 'jstart':		$res=$this->jailStart($key);break;
			case 'jstop':		$res=$this->jailStop($key);break;
			case 'jrestart':	$res=$this->jailRestart($key);break;
			//case 'jedit':		$res=$this->jailEdit('jail'.$key);break;
			case 'jremove':		$res=$this->jailRemove($key);break;
						
			case 'bstart':		$res=$this->bhyveStart($key);break;
			case 'bstop':		$res=$this->bhyveStop($key);break;
			case 'brestart':	$res=$this->bhyveRestart($key);break;
			case 'bremove':		$res=$this->bhyveRemove($key);break;
			case 'removesrc':	$res=$this->srcRemove($key);break;
			case 'srcup':		$res=$this->srcUpdate($key);break;
			case 'removebase':	$res=$this->baseRemove($key);break;
						
			//case 'jexport':	$res=$this->jailExport('jail'.$key,$task['jname'],$key);break;
			//case 'jimport':	$res=$this->jailImport('jail'.$key,$task['jname'],$key);break;
			//case 'jclone':	$res=$this->jailClone('jail'.$key,$key,$obj[$key]);break;
			//case 'madd':		$res=$this->moduleAdd('jail'.$key,$task['jname'],$key);break;
			////case 'mremove':	$res=$this->moduleRemove('jail'.$key,$task['jname'],$key);break;
			//case 'sstart':	$res=$this->serviceStart($task);break;
			//case 'sstop':		$res=$this->serviceStop($task);break;
			////case 'projremove':	$res=$this->projectRemove($key,$task);break;
		}
	}

	function _getTasksStatus($jsonObj){
		//return $jsonObj;
		$tasks=array();
		$obj=json_decode($jsonObj,true);
		# TODO $ids not defined
		#if(empty($ids)) return $obj; // TODO: error? this return NULL..

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

		foreach($obj as $key=>$task){
			if(in_array($task['operation'],$ops_array)){
				if(false !== ($res=$this->runTask($key,$task))){
					if($res['error']) $obj[$key]['retval']=$res['retval'];
					if(!empty($res['error_message'])) $obj[$key]['error_message']=$res['error_message'];

					if(isset($res['message'])){
						$task_id=intval($res['message']);
						if($task_id>0){
							$tasks[]=$task_id;
							$obj[$key]['task_id']=$task_id;
							//$obj[$key]['txt_log']=file_get_contents('/tmp/taskd.'.$task_id.'.log');
						}
					}
				} else {
					$tasks[]=$task['task_id'];
				}
			}

			($task['status']==-1) AND $obj[$key]['status']=0;
		}

		$ids=join(',',$tasks);
		if(empty($ids)) return $obj;

		$statuses=(new Db('base','cbsdtaskd'))->select("SELECT id,status,logfile,errcode FROM taskd WHERE id IN (?)", array([$ids]));

		//print_r($statuses);
		foreach($obj as $key=>$task){
			if(!empty($statuses)) foreach($statuses as $stat){
				if($task['task_id']!=$stat['id']) continue;

				$obj[$key]['status']=$stat['status'];
				$num=($stat['status']<2?0:1);
				$obj[$key]['txt_status']=$stat_array[$obj[$key]['operation']][$num];
				if($stat['errcode']>0){
					$obj[$key]['errmsg']=file_get_contents($stat['logfile']);
					$obj[$key]['txt_status']=$this->translate('Error');
				}

				//Return the IP of the cloned jail if it was assigned by DHCP

				if($stat['status']==2){
					switch($task['operation']){
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

		return $obj;
	}


	function saveSettingsCBSD(){
		return array('error'=>true,'errorMessage'=>'Method is not complete yet! line: '.__LINE__);
	}

	function getSrcInfo($id){
		$id=str_replace('src','',$id);
		$db=new Db('base','local');
		if(!$db->isConnected()) return array('error'=>true,'errorMessage'=>'Database error');
		$res=$db->selectOne("SELECT idx,name,platform,ver,rev,date FROM bsdsrc WHERE ver=?", array([(int)$id, PDO::PARAM_INT]));

		$hres=$this->getTableChunk('srcslist','tbody');
		if($hres!==false){
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

			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}

		$html=preg_replace('#<tr[^>]*>#','',$html);
		$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);

		return array('html'=>$html,'arr'=>$res);
	}

	function fillRepoTr($id,$only_td=false,$bsdsrc=true){
//		preg_match('#base([0-9\.]+)-#',$id,$res);
//		$id=$res[1];

		$html='';

		$db=new Db('base','local');
		if($db->isConnected()){
			if($bsdsrc){
				$res=$db->selectOne("SELECT idx,platform,ver FROM bsdsrc WHERE idx=?", array([(int)$id, PDO::PARAM_INT]));
				$res['name']='—';
				$res['arch']='—';
				$res['targetarch']='—';
				$res['stable']=strlen(intval($res['ver']))<strlen($res['ver'])?0:1;
				$res['elf']='—';
				$res['date']='—';
			}else{
				$res=$db->selectOne("SELECT idx,platform,name,arch,targetarch,ver,stable,elf,date FROM bsdbase WHERE ver=?", array([(int)$id, PDO::PARAM_INT]));
			}
			$hres=$this->getTableChunk('baseslist','tbody');
			if($hres!==false){
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

		if($only_td){
			$html=preg_replace('#<tr[^>]*>#','',$html);
			$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);
		}

		return array('html'=>$html,'arr'=>$res);
	}

	function helpersAdd($mode){
		if($this->uri_chunks[0]!='jailscontainers' || empty($this->uri_chunks[1])) return array('error'=>true,'errorMessage'=>'Bad url!');
		$jail_id=$this->uri_chunks[1];

		$helpers=array_keys($this->form);
		if(!empty($helpers)) foreach($helpers as $helper){
			$res=CBSD::run(
				'task owner=%s mode=new {cbsd_loc} forms inter=0 module=%s jname=%s',
				array($this->_user_info['username'], $helper, $jail_id)
			);
		}
		return array('error'=>false);
	}

	function useDialogs($arr=array()){
		//print_r($arr);
		$this->_dialogs=$arr;
	}

	function placeDialogs(){
		if(empty($this->_dialogs)) return;
		echo PHP_EOL;
		foreach($this->_dialogs as $dialog_name){
			/*
			$file_name=$this->realpath_public.'dialogs/'.$dialog_name.'.php';
			if(file_exists($file_name)){
				include($file_name);
				echo PHP_EOL,PHP_EOL;
			}
			*/
			
			$trres=$this->translateF('dialogs','dialogs/',$dialog_name.'.php');
			
			$incfile=$this->get_translated_filename();
			//echo $incfile;exit;
			if(file_exists($incfile))
			{
				include($incfile);
			}
			//if(isset($trres['message'])) echo $trres['message'],"<br>";
		}
	}

	function placeDialogByName($dialog_name=null){
		if(is_null($dialog_name)) return;
		echo PHP_EOL;
		//$file_name=$this->realpath_public.'dialogs/'.$dialog_name.'.php';
		
		$trres=$this->translateF('dialogs','dialogs/',$dialog_name.'.php');
		$file_name=$this->get_translated_filename();

		if(file_exists($file_name)){
			include($file_name);
			echo PHP_EOL,PHP_EOL;
		}
	}

	function postCurl($url,$vars=false)
	{
		if($vars===false) return array('error'=>true,'errorMessage'=>'something wrong...');

		$txt_vars=json_encode($vars);
		//$txt_vars=http_build_query($vars);


		$ch = curl_init($url);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $txt_vars);
		$result = curl_exec($ch);
		curl_close($ch);
		//echo print_r($result,true);exit;
		return $result;
	}

	function getCurl($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	function GhzConvert($Hz=0){
		$h=1;
		$l='Mhz';

		if($Hz>1000){
			$h=1000;
			$l='Ghz';
		}

		return round($Hz/$h,2).' '.$l;
	}

	function fileSizeConvert(int $bytes,$bytes_in_mb=1024,$round=false,$small=false){
		//$bytes = intval($bytes);
		//var_dump($bytes);exit;

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

		$result='0 MB';
		foreach($arBytes as $arItem){
			if($bytes >= $arItem["VALUE"]){
				$result = $bytes / $arItem["VALUE"];
				if($round) $result=round($result);
				$result = str_replace(".", "," , strval(round($result, 2))).($small?strtolower(substr($arItem['UNIT'],0,1)):" ".strtoupper($arItem["UNIT"]));
				break;
			}
		}
		return $result;
	}

	function colorizeCmd($cmd_string){
		$arr=$this->_cmd_array;
		foreach($arr as $item){
			$cmd_string=str_replace($item,'<span class="cbsd-cmd">'.$item.'</span>',$cmd_string);
		}

		$cmd_string=preg_replace('#(\/.+/cbsd)#','<span class="cbsd-lnch">$1</span>',$cmd_string);

		return '<span class="cbsd-str">'.$cmd_string.'</span>';
	}

	function media_iso_list_html(){
//		$form=$this->form;
		$db=new Db('base','storage_media');
		$res=$db->select("select * from media where type='iso'", array());
		
		//var_dump($res);exit;
		if($res===false || empty($res)) return;

		$html='';
		if(is_array($res)){
			foreach($res as $r){
				$html.='<option value="'.$r['idx'].'">'.$r['name'].'.'.$r['type'].'</option>';
			}
		}
		return $html;
	}

	function get_interfaces_html(){
		$if=$this->config->os_interfaces;
		$html='';
		//$m=1;
		foreach($if as $i){
			//$html.='<input type="radio" name="interface" value="'.$i['name'].'" id="rint'.$m.'" class="inline"><label for="rint'.$m.'">'.$i['name'].'</label></radio>';
			$html.='<option value="'.$i['name'].'">'.$i['name'].'</option>';
			//$m++;
		}
		return $html;
	}

	function messageError($message,$vars=[])
	{
		return array_merge(['error'=>true, 'error_message'=>$message], $vars);
	}

	function messageSuccess($vars=[])
	{
		return array_merge(['error'=>false], $vars);
	}

	function getImportedImages(){
		$images=array();
		$path=self::$media_import;
		$files=$this->getImagesList($path);
		foreach($files as $key=>$file){
			if(file_exists($file['fullname'])){
				$fp=fopen($file['fullname'],'r');
				$buf=fread($fp,300);
				fclose($fp);

				$res=$this->getImageVar('emulator',$buf);
				$res1=$this->getImageVar('jname',$buf);
				if(isset($res)) $files[$key]['type']=$res;
				if(isset($res1)) $files[$key]['jname']=$res1;
			}
		}
		return $files;
	}

	function getImagesList($path){
		$files=[];
		foreach (glob($path."*.img") as $filename){
			$files[] = [
				'name'=>pathinfo($filename)['basename'],
				'fullname'=>$filename
			];
		}
		return $files;
	}

	function getImageInfo($imgname){
		if(empty($imgname)) return false;

		$file=self::$media_import.$imgname;
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
		if($db->isConnected()){
			$jail=$db->selectOne("SELECT jname FROM jails WHERE jname=?", array([$jname]));

			if($jname==$jail['jname']){
				$jres=$this->ccmd_getFreeJname(false,$type);
				if($jres['error']) return $this->messageError('Something wrong...');
				$jname=$jres['freejname'];
				$name_comment='* '.$this->translate('Since imported name already exist, we are change it');
			}
		}

		return [
			'orig_jname'=>$orig_jname,
			'jname'=>$jname,
			'host_hostname'=>$hostname,
			'ip4_addr'=>$ip,
			'file_id'=>$imgname,
			'type'=>$type,
			'name_comment'=>$name_comment
		];
	}

	function getImageVar($name,$buf){
		$val=false;
		$pat='#'.$name.'="([^\"]*)"#';
		preg_match($pat,$buf,$res);
		if(!empty($res)) $val=$res[1];
		return $val;
	}


}