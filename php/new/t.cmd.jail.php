<?php

trait tcJail {
	
	function ccmd_jailRename() {
		$form=$this->_vars['form_data'];
		$cmd = "task owner=%s mode=new {cbsd_loc} jrename old=%s new=%s host_hostname=%s ip4_addr=%s restart=1";
		$args = array(
			$this->_user_info['username'], 
			$form['oldJail'], 
			$form['jname'], 
			$form['host_hostname'], 
			$form['ip4_addr']
		);
		$res=CBSD::run($cmd, $args);

		$err='Jail is not renamed!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Jail was renamed!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}

		return array('errorMessage'=>$err,'jail_id'=>$form['jname'],'taskId'=>$taskId,'mode'=>$this->mode);
	}

	function ccmd_jailClone() {
		$form=$this->_vars['form_data'];
		$cmd = 'task owner=%s mode=new {cbsd_loc} jclone checkstate=0 old=%s new=%s host_hostname=%s ip4_addr=%s';
		$args = array(
			$this->_user_info['username'], 
			$form['oldJail'], 
			$form['jname'],
			$form['host_hostname'], 
			$form['ip4_addr']
		);
		$res=CBSD::run($cmd, $args);

		$err='Jail is not cloned!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Jail was cloned!';
			$taskId=$res['message'];
		}else{
			$err=$res['error'];
		}

		$html='';
		$hres=$this->getTableChunk('jailslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// TODO: actual data
				'node'=>'local',				// TODO: actual data
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
	
	function ccmd_saveJailHelperValues(){
		$form=$this->form;
		$username=$this->_user_info['username'];

		if(!isset($this->uri_chunks[1]) || !isset($this->url_hash)) return array('error'=>true,'errorMessage'=>'Bad url!');
		$jail_name=$this->uri_chunks[1];

		$db=new Db('helper',array('jname'=>$jail_name,'helper'=>$this->url_hash));
		if(!$db->isConnected()) return array('error'=>true,'errorMessage'=>'No helper database!');
	
		foreach($form as $key=>$val) {
			if($key!='jname' && $key!='ip4_addr') {
				$query="update forms set new=? where param=?";
				$db->update($query, array([$val], [$key]));
				unset($form[$key]);
			}
		}

		//cbsd forms module=<helper> jname=jail1 inter=0
		$cmd = 'task owner=%s mode=new {cbsd_loc} forms module=%s jname=%s inter=0';
		$args = array($username, $this->url_hash, $jail_name);
		$res=CBSD::run($cmd, $args);

		$err='Helper values is saved!';
		$taskId=-1;
		if($res['retval']==0) {
			$err='Helper values was not saved!';
			$taskId=$res['message'];
		}

		return array(
			'jail_id'=>$jail_name,
			'taskId'=>$taskId,
			'mode'=>$this->mode,
		);
	}

	function ccmd_jailRenameVars(){
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');

		$err=false;
		$db=new Db('base','local');
		if($db->isConnected()){
			$query="SELECT jname,host_hostname FROM jails WHERE jname=?;"; //ip4_addr
			$res['vars']=$db->selectOne($query, array([$form['jail_id']]));
		} else {
			$err=true;
		}

		if(empty($res['vars'])) $err=true;

		if($err){
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

	function ccmd_jailCloneVars(){
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');

		$err=false;
		$db=new Db('base','local');
		if($db->isConnected()){
			$query="SELECT jname,host_hostname FROM jails WHERE jname=?;";	//ip4_addr
			$res['vars']=$db->selectOne($query, array([$form['jail_id']]));
		} else {
			$err=true;
		}

		(empty($res['vars'])) AND $err=true;
		if($err){
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

	function ccmd_jailEditVars(){
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');

		$err=false;
		$db=new Db('base','local');
		if($db->isConnected()){
			$query="SELECT jname,host_hostname,ip4_addr,allow_mount,interface,mount_ports,astart,vnet FROM jails WHERE jname=?;";
			$res['vars']=$db->selectOne($query, array([$form['jail_id']]));
		} else {
			$err=true;
		}
		(empty($res['vars'])) AND $err=true;

		if($err){
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

	function ccmd_jailEdit(){
		$form=$this->_vars['form_data'];

		$str=array();
		$jname=$form['jname'];
		$arr=array('host_hostname','ip4_addr','allow_mount','interface','mount_ports','astart','vnet');
		foreach($arr as $a){
			if(isset($form[$a])){
				$val=$form[$a];
				($val=='on') AND $val=1;
				$str[]=$a.'='.$val;
			} else {
				$str[]=$a.'=0';
			}
		}

		$cmd='jset jname=%s %s';
		$res=CBSD::run($cmd, array($jname, join(' ',$str)));
		$res['mode']='jailEdit';
		$res['form']=$form;
		return $res;
	}

	function ccmd_jailStart(){
		//$cbsd_queue_name=trim($this->_vars['path'],'/');
		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} jstart inter=0 jname=%s',
			array($this->_user_info['username'], $this->_vars['form_data']['jname'])
		);
		//.' cbsd_queue_name=/clonos/'.$cbsd_queue_name.'/');	// autoflush=2
		return $res;
	}

	function ccmd_jailStop(){
		//$cbsd_queue_name=trim($this->_vars['path'],'/');
		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} jstop inter=0 jname=%s',
			array($this->_user_info['username'], $this->_vars['form_data']['jname'])
		);
		//.' cbsd_queue_name=/clonos/'.$cbsd_queue_name.'/');	// autoflush=2
		return $res;
	}

	function ccmd_jailRestart(){
		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} jrestart inter=0 jname=%s',
			array($this->_user_info['username'], $this->_vars['form_data']['jname'])
		);	// autoflush=2
		return $res;
	}

	function ccmd_jailRemove(){
		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} jremove inter=0 jname=%s',
			array($this->_user_info['username'], $this->_vars['form_data']['jname'])
		);	// autoflush=2
		return $res;
	}

	function ccmd_addJailHelperGroup()
	{
		if($this->uri_chunks[0]!='jailscontainers' || empty($this->uri_chunks[1]) || empty($this->url_hash)){
			return array('error'=>true,'errorMessage'=>'Bad url!');
		}
		$jail_id=$this->uri_chunks[1];
		$helper=$this->url_hash;

		$db=new Db('helper',array('jname'=>$jail_id,'helper'=>$helper));
		if(!$db->isConnected()) return array('error'=>true,'errorMessage'=>'No database file!');

		$db_path=$db->getFileName();

		$res=CBSD::run(
			'forms inter=0 module=%s formfile=%s group=add',
			array($helper, $db_path)
		);

		$html=(new Forms('', $helper, $db_path))->generate();

		return array('html'=>$html);
	}

	function ccmd_deleteJailHelperGroup()
	{
		if(!isset($this->uri_chunks[1]) || !isset($this->url_hash)){
			return array('error'=>true,'errorMessage'=>'Bad url!');
		}
		$jail_id=$this->uri_chunks[1];
		$helper=$this->url_hash;
		$index=str_replace('ind-','',$this->form['index']);

		$db=new Db('helper',array('jname'=>$jail_id,'helper'=>$helper));
		if($db->error) return array('error'=>true,'errorMessage'=>'No helper database!');

		$db_path=$db->getFileName();
		$res=CBSD::run(
			'forms inter=0 module=%s formfile=%s group=del index=%s',
			array($helper, $db_path, $index)
		);
		$html=(new Forms('',$helper,$db_path))->generate();
		return array('html'=>$html);
	}

	function ccmd_getFreeJname($in_helper=false,$type='jail'){
		$arr=array();

		/* TODO: CHECK THE ORIGINAL CODE
			$add_cmd=($in_helper)?' default_jailname='.$this->url_hash:'';
			$add_cmd1=' default_jailname='.$type;
			$res=$this->cbsd_cmd("freejname".$add_cmd.$add_cmd1);
		*/
		if ($in_helper) {
			$res = CBSD::run('freejname default_jailname=%s default_jailname=%s', array($this->url_hash, $type));
		} else {
			$res = CBSD::run('freejname default_jailname=%s', array($type));
		}

		if($res['error']){
			$arr['error']=true;
			$arr['error_message']=$res['error_message'];
		}else{
			$arr['error']=false;
			$arr['freejname']=$res['message'];
		}
		return $arr;
	}

	function getJailInfo($jname,$op=''){
		$stats=array(''=>'','jclone'=>'Cloned','jcreate'=>'Created');
		$html='';
		$db=new Db('base','local');
		if($db->isConnected()){
			$jail=$db->selectOne("SELECT jname,ip4_addr,status,protected FROM jails WHERE jname=?", array([$jname]));
			$hres=$this->getTableChunk('jailslist','tbody');
			if($hres!==false){
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

				foreach($vars as $var=>$val){
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				}
				$html.=$html_tpl;
			}
		}

		$html=preg_replace('#<tr[^>]*>#','',$html);
		$html=str_replace(array('</tr>',"\n","\r","\t"),'',$html);

		return array('html'=>$html);
	}

	function jailAdd($redirect=''){	//$mode='jailAdd'
		//if(!empty($arr)) $form=$arr; else
		$form=$this->form;
		$helper=preg_replace('/^#/','',$this->_vars['hash']);

		$db_path='';
		$with_img_helpers='';
		if($this->mode=='saveHelperValues'){
			if($helper=='' && $this->_vars['path']=='/settings/') return $this->saveSettingsCBSD();

			if(!isset($this->_vars['db_path'])){
				$res=CBSD::run('make_tmp_helper module=%s', array($helper));
				if($res['retval']==0){
					$db_path=$res['message'];
				} else {
					echo json_encode(array('error'=>true,'errorMessage'=>'Error opening temporary form database!'));
					return;
				}
			} else { 
				$db_path=$this->_vars['db_path'];
			}

			
			/*
			$file_name=$this->workdir.'/formfile/'.$helper.'.sqlite';
			if(file_exists($file_name)){
				$tmp_name=tempnam("/tmp","HLPR");
				copy($file_name,$tmp_name);
				
				$db=new Db('file',$tmp_name);
				if($db->isConnected()){
					foreach($form as $key=>$val){
						if($key!='jname' && $key!='ip4_addr'){
							$query="update forms set new=? where param=?";
							$db->update($query, array([$val],[$key]));
							unset($form[$key]);
						}
					}
					
					$with_img_helpers=$tmp_name;
					//echo $with_img_helpers;
				}
			}
			*/

			$db=new Db('file',$db_path);
			if($db->isConnected()){
				foreach($form as $key=>$val){
					if($key!='jname' && $key!='ip4_addr'){
						$db->update("update forms set new=? where param=?", array([$val],[$key]));
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
		foreach($arr_copy as $a){
			(isset($form[$a])) AND $arr[$a]=$form[$a];
		}

		$arr_copy=array('baserw','mount_ports','astart','vnet');
		foreach($arr_copy as $a){
			if(isset($form[$a]) && $form[$a]=='on'){
				$arr[$a]=1;
			} else {
				$arr[$a]=0;
			}
		}

		$sysrc=array();
		(isset($form['serv-ftpd'])) AND $sysrc[]=$form['serv-ftpd'];
		(isset($form['serv-sshd'])) AND $sysrc[]=$form['serv-sshd'];
		$arr['sysrc_enable']=implode(' ',$sysrc);

		/* create jail */
		$file_name='/tmp/'.$arr['jname'].'.conf';

		$file=file_get_contents($this->realpath_public.'templates/jail.tpl');
		if(!empty($file)) {
			foreach($arr as $var=>$val){
				$file=str_replace('#'.$var.'#',$val,$file);
			}
		}
		file_put_contents($file_name,$file);

		$username=$this->_user_info['username'];

		//$cbsd_queue_name='/clonos/'.trim($this->_vars['path'],'/').'/';
		$res=CBSD::run('task owner=%s mode=new {cbsd_loc} jcreate inter=0 jconf=%s', array($username, $file_name));
		//.' cbsd_queue_name='.$cbsd_queue_name);

		$err='Jail is not created!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Jail was created!';
			$taskId=$res['message'];
		}

		// local - change to the real server on which the jail was created!
		$jid=$arr['jname'];

		$table='jailslist';
		#$html='';
		$hres=$this->getTableChunk($table,'tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',		// TODO: fix actual data!
				'node'=>'local',		// TODO: fix actual data!
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

			/* foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
			*/
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

		return array(
			'errorMessage'=>$err,
			'jail_id'=>$jid,
			'taskId'=>$taskId,
			'mode'=>$this->mode,
			'redirect'=>$redirect,
			'db_path'=>$db_path
		);	//,'html'=>$html
	}

}