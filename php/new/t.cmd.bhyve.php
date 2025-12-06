<?php

trait tcBhyve {
	
	function ccmd_bhyveClone(){
		$form=$this->_vars['form_data'];

		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} bclone checkstate=0 old=%s new=%s',
			array($this->_user_info['username'], $form['oldBhyve'], $form['vm_name'])
		);

		$err='Virtual Machine is not renamed!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Virtual Machine was renamed!';
			$taskId=$res['message'];
		} else {
			$err=$res['error'];
		}

		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// TODO: actual data
				'node'=>'local',				// TODO: actual data
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

	function ccmd_bhyveEditVars(){
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');

		$err=false;
		$db=new Db('base','local');
		if($db->isConnected())	{
			$query="SELECT b.jname as vm_name,vm_cpus,vm_ram,vm_vnc_port,bhyve_vnc_tcp_bind,interface FROM bhyve AS b INNER JOIN jails AS j ON b.jname=j.jname AND b.jname=?;";
			$res['vars']=$db->selectOne($query, array([$form['jail_id']]));
			$res['vars']['vm_ram']=$this->fileSizeConvert($res['vars']['vm_ram'],1024,false,true);
		}else{
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

		$res['vars']['vm_vnc_password']='-nochange-';
		$res['error']=false;
		$res['dialog']=$form['dialog'];
		$res['jail_id']=$form['jail_id'];
		$res['iso_list']=$this->ccmd_updateBhyveISO($form['jail_id']);
		return $res;
	}

	function ccmd_bhyveRename(){
		$form=$this->_vars['form_data'];

		$res=CBSD::run(
			"task owner=%s mode=new /usr/local/bin/cbsd brename old=%s new=%s restart=1",
			array($this->_user_info['username'], $form['oldJail'], $form['jname'])
		);

		$err='Virtual Machine is not renamed!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Virtual Machine was renamed!';
			$taskId=$res['message'];
		} else {
			$err=$res['error'];
		}

		return array('errorMessage'=>$err,'jail_id'=>$form['jname'],'taskId'=>$taskId,'mode'=>$this->mode);
	}

	function ccmd_bhyveRenameVars(){
		$form=$this->_vars['form_data'];
		if(!isset($form['jail_id'])) return array('error'=>true,'error_message'=>'Bad jail id!');

		$jname=$form['jail_id'];
		$err=false;
		$db=new Db('base','local');
		if($db->isConnected()){
			$query="SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve WHERE jname=?"; //ip4_addr
			$res['vars']=$db->selectOne($query, array([$jname]));
		} else {
			$err=true;
		}

		(empty($res['vars'])) AND $err=true;

		if($err){
			$res['error']=true;
			$res['error_message']=$this->translate('Jail '.$form['jail_id'].' is not present.'); // XSS 
			$res['jail_id']=$form['jail_id']; // Possible XSS
//			$res['reload']=true;
			return $res;
		}

		$res['error']=false;
		$res['dialog']=$form['dialog']; // Possible XSS
		$res['jail_id']=$form['jail_id'];
		return $res;
	}

	function ccmd_bhyveEdit(){
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
		foreach($arr as $a){
			if(isset($form[$a])){
				$val=$form[$a];
				if($val=='on') $val=1;
				$str[]=$a.'='.$val;
			} else {
				$str[]=$a.'=0';
			}
		}

		$form['vm_ram']=$ram_tmp;

		/* check mounted ISO */
		$db=new Db('base','storage_media');
		if(!$db->isConnected()) return(false); // TODO: Fix return

		$res=$db->selectOne("SELECT * FROM media WHERE jname=? AND type='iso'", array([$jname]));
		if($res!==false && !empty($res)){
			CBSD::run(
				'cbsd media mode=unregister name="%s" path="%s" jname=%s type=%s',
				array($res['name'], $res['path'], $jname, $res['type'])
			);
			$res=$db->selectOne(
				"SELECT * FROM media WHERE idx=?",
				array([(int)$form['vm_iso_image']])
			); 
			if($res!==false && !empty($res) && $form['vm_iso_image']!=-2){
				CBSD::run(
					'cbsd media mode=register name="%s" path="%s" jname=%s type=%s',
					array($res['name'], $res['path'], $jname, $res['type'])
				);
			}
		}
		//exit;

		/* end check */

		$cmd='bset jname=%s %s';
		$res=CBSD::run($cmd, array($jname, join(' ',$str)));
		$res['mode']='bhyveEdit';
		$res['form']=$form;
		return $res;
	}

	function ccmd_bhyveAdd(){
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
			'vm_size'=>$form['vm_imgsize']*1024*1024*1024,
			'vm_cpus'=>$form['vm_cpus'],
			'vm_ram'=>$form['vm_ram']*1024*1024*1024,
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
		if(!empty($iso_id)){
			$iso_id=(int)$iso_id;
			if($iso_id>0){
				$db=new Db('base','storage_media');
				if(!$db->isConnected()) return(false); // TODO: return error
				$res=$db->selectOne("SELECT name,path FROM media WHERE idx= ?", array([$iso_id])); // OK, $iso_id is casted as int above.
				if($res===false || empty($res)) $iso=false;
			}
			
			if($iso_id==-1) $iso=false;
			
			if($iso){
				$arr['register_iso_as']='register_iso_as="'.$res['name'].'"';
				$arr['register_iso_name']='register_iso_name="'.$res['path'].'"';
				if($iso_id!=-2) $arr['iso_var_block']=$iso_var_block;
			}
		}

		/* create vm */
		$file_name='/tmp/'.$arr['jname'].'.conf';

		$file=file_get_contents($this->realpath_public.'templates/vm.tpl');
		if(!empty($file)){
			foreach($arr as $var=>$val) $file=str_replace('#'.$var.'#',$val,$file);
		}
		//echo $file;exit;
		file_put_contents($file_name,$file);

		$res=CBSD::run(
			'task owner=%s mode=new {cbsd_loc} bcreate inter=0 jconf=%s',
			array($this->_user_info['username'], $file_name)
		);

		$err='Virtual Machine is not created!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Virtual Machine was created!';
			$taskId=$res['message'];
		}
		// local - change to the real server on which the jail is created!
		$jid=$arr['jname'];

		$vm_ram=str_replace('g',' GB',$form['vm_ram']);

		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// TODO: actual data
				'node'=>'local',				// TODO: actual data
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
			
			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}
		
		return array('errorMessage'=>$err,'jail_id'=>$jid,'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode);
	}

	function ccmd_bhyveObtain(){
		$form=$this->_vars['form_data'];
		$os_types=$this->config->os_types;
		$os_types_obtain=$this->config->os_types_obtain;
		$sel_os=$form['vm_os_profile'];
		list($os_num,$item_num)=explode('.',$sel_os);
		if(!isset($os_types[$os_num])) return array('error'=>true,'errorMessage'=>'Error in list of OS types!');
		//$os_name=$os_types[$os_num]['os'];
		$os_items=$os_types[$os_num]['items'][$item_num];
		$os_type=$os_items['type'];

		//echo '<pre>';print_r($os_types_obtain);exit;

		// os select
		list($one,$two)=explode('.',$sel_os,2);

		if(isset($os_types_obtain[$one])){
			if(isset($os_types_obtain[$one]['items'][$two])){
				$os_profile=$os_types_obtain[$one]['items'][$two]['profile'];
				$os_type=$os_types_obtain[$one]['items'][$two]['type'];
			}
		}

		//$key_name='/usr/home/olevole/.ssh/authorized_keys';
		if(!isset($form['vm_authkey'])) $form['vm_authkey']=0;
		$key_id=(int)$form['vm_authkey'];
		ClonOS::syslog("clonos.php: key_id: [".$key_id."]");

		if($key_id>0) {
			$db=new Db('base','authkey');
			if(!$db->isConnected())  return array('error'=>true,'errorMessage'=>'Database error!');
			if($nres['name']!==false) $key_name=$nres['name'];
			ClonOS::syslog("clonos.php:". "SELECT authkey FROM authkey WHERE idx=?". array([$key_id, PDO::PARAM_INT]));
			$nres=$db->selectOne("SELECT authkey FROM authkey WHERE idx=?", array([$key_id, PDO::PARAM_INT]));
			//var_dump($nres);exit;

	//		[22-Jul-2022 13:15:19 UTC] PHP Warning:  Trying to access array offset on value of type bool in /usr/local/www/clonos/php/clonos.php on line 1416
			if($nres['authkey']!==false) $authkey=$nres['authkey']; else $authkey='';
		} else {
			$authkey='';
		}

		$user_pw=(!empty($form['user_password']))?' ci_user_pw_user='.$form['user_password'].' ':'';

		// olevole: SHELL ESCAPE here - tabs + \r\n
		$res=CBSD::run( // TODO: THIS SEEMS WRONG pw_user={$form['vm_password']} {$user_pw}vnc_password={$form['vnc_password']}";
			'task owner=%s mode=new {cbsd_loc} bcreate jname=%s 
			vm_os_profile="%s" imgsize=%s vm_cpus=%s vm_ram=%s vm_os_type=%s mask=%s 
			ip4_addr=%s ci_ip4_addr=%s ci_gw4=%s ci_user_pubkey="%s" ci_user_pw_user=%s %svnc_password=%s',
			array(
				$this->_user_info['username'],
				$form['vm_name'],
				$os_profile,
				$form['vm_size']*1024*1024*1024,
				$form['vm_cpus'],
				$form['vm_ram']*1024*1024*1024,
				$os_type,
				$form['mask'],
				$form['ip4_addr'],
				$form['ip4_addr'],
				$form['gateway'],
				$authkey,
				$form['vm_password'],
				$user_pw,
				$form['vnc_password']
			)
		);

		$err='Virtual Machine is not created!';
		$taskId=-1;
		if($res['retval']==0){
			$err='Virtual Machine was created!';
			$taskId=$res['message'];
		}

		$vm_ram=str_replace('g',' GB',$form['vm_ram']);

		$html='';
		$hres=$this->getTableChunk('bhyveslist','tbody');
		if($hres!==false){
			$html_tpl=$hres[1];
			$vars=array(
				'nth-num'=>'nth0',				// TODO: actual data
				'node'=>'local',				// TODO: actual data
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

			foreach($vars as $var=>$val){
				$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
			}
			$html=$html_tpl;
		}

		return array('errorMessage'=>$err,'jail_id'=>$form['vm_name'],'taskId'=>$taskId,'html'=>$html,'mode'=>$this->mode);
	}

	function ccmd_bhyveStart(){
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} bstart inter=0 jname=%s',
			array($this->_user_info['username'], $this->form['jname'])
		);	// autoflush=2
	}

	function ccmd_bhyveStop(){
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} bstop inter=0 jname=%s',
			array($this->_user_info['username'], $this->form['jname'])
		);	// autoflush=2
	}

	function ccmd_bhyveRestart(){
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} brestart inter=0 jname=%s',
			array($this->_user_info['username'], $this->form['jname'])
		);	// autoflush=2
	}

	function ccmd_bhyveRemove(){
		return CBSD::run(
			'task owner=%s mode=new {cbsd_loc} bremove inter=0 jname=%s',
			array($this->_user_info['username'], $this->form['jname'])
		);	// autoflush=2
	}

	function ccmd_updateBhyveISO($iso=''){
//echo $this->config->os_types_getOne('first');exit;
		$db=new Db('base','storage_media');
		$res=$db->select("SELECT * FROM media WHERE type='iso'", array());
		
		if($res===false || empty($res)) return array(); //array('error'=>true,'error_message'=>'Profile ISO is not find!');

		$sel='';
		//if(empty($iso)) $sel='#sel#';
		$html='<option value="-2"></option><option value="-1"#sel#>Profile default ISO</option>';
		foreach($res as $r){
			$sel1='';
			if(empty($sel) && $iso==$r['jname']) $sel1='#sel1#';
			$html.='<option value="'.$r['idx'].'"'.$sel1.'>'.$r['name'].'.'.$r['type'].'</option>';
		}

		if(strpos($html,'#sel1#')!==false){
			$html=str_replace('#sel1#',' selected="selected"',$html);
			$html=str_replace('#sel#','',$html);
		}else{
			$html=str_replace('#sel1#','',$html);
			$html=str_replace('#sel#',' selected="selected"',$html);
		}
		
		$form_items=$this->getBhyveFormItems();

		return array('iso_list'=>$html,'form_items'=>$form_items);
	}

	function getBhyveInfo($jname){
		$statuses=array('Not Launched','Launched','unknown-1','Maintenance','unknown-3','unknown-4','unknown-5','unknown-6');
		$html='';
		$db=new Db('base','local');
		if($db->isConnected())	{
			$bhyve=$db->selectOne("SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve WHERE jname=?", array([$jname]));
			$hres=$this->getTableChunk('bhyveslist','tbody');
			if($hres!==false){
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

	function getBhyveFormItems($os_name='',$obtain='')
	{
		$jname='undefined';
		if($os_name!='')
		{
			$arr=$this->config->os_types_getOne($os_name,$obtain);
		}else{
			$arr=$this->config->os_types_getOne('first',$obtain);
		}

		$jres=$this->ccmd_getFreeJname(false,$arr['default_jname']);
		if(!$jres['error'])
		{
			$jname=$jres['freejname'];
		}

		$res=array(
			'jname'=>$jname,	//$arr['jname'],
			'imgsize'=>array(
				'min'=>intval($arr['imgsize_min']),
				'max'=>intval($arr['imgsize_max']),
				'cur'=>intval($arr['imgsize'])
			),
			'vm_cpus'=>array(
				'min'=>intval($arr['vm_cpus_min']),
				'max'=>intval($arr['vm_cpus_max']),
				'cur'=>intval($arr['vm_cpus'])
			),
			'vm_ram'=>array(
				'min'=>intval($arr['vm_ram_min']),
				'max'=>intval($arr['vm_ram_max']),
				'cur'=>intval($arr['vm_ram'])
			),
			'obtain'=>$obtain,
		);

		return $res;
	}

	function getSummaryInfoBhyves(){

		$html='';

		/*
		$bool=array(
			'created','astart','vm_cpus','vm_os_type','vm_boot','vm_os_profile','bhyve_flags',
			'vm_vnc_port','bhyve_vnc_tcp_bind','bhyve_vnc_resolution','ip4_addr','state_time',
			'cd_vnc_wait','protected','hidden','maintenance','media_auto_eject','jailed'
		);
		*/
		$bool=array('astart','hidden','jailed','cd_vnc_wait','protected','media_auto_eject');
		$chck=array(
			'bhyve_generate_acpi','bhyve_wire_memory','bhyve_rts_keeps_utc','bhyve_force_msi_irq',
			'bhyve_x2apic_mode','bhyve_mptable_gen','bhyve_ignore_msr_acc','xhci'
		);

		$db=new Db('bhyve',array('jname'=>$this->form['jname']));
		if($db->isConnected()) {
			$sql="SELECT created, astart, vm_cpus, vm_ram, vm_os_type, vm_boot, vm_os_profile, bhyve_flags,
				vm_vnc_port, virtio_type, bhyve_vnc_tcp_bind, bhyve_vnc_resolution, cd_vnc_wait,
				protected, hidden, maintenance, ip4_addr, vnc_password, state_time,
				vm_hostbridge, vm_iso_path, vm_console, vm_efi, vm_rd_port, bhyve_generate_acpi,
				bhyve_wire_memory, bhyve_rts_keeps_utc, bhyve_force_msi_irq, bhyve_x2apic_mode,
				bhyve_mptable_gen, bhyve_ignore_msr_acc, bhyve_vnc_vgaconf text, media_auto_eject,
				vm_cpu_topology, debug_engine, xhci, cd_boot_firmware, jailed FROM settings";
			$query=$db->selectOne($sql, array());
			$html='<table class="summary_table">';

			foreach($query as $q=>$k){
				if(in_array($q,$bool)){
					$k=($k==0)?'no':'yes';
				}
				if(in_array($q,$chck)){
					$k=($k==0)?'no':'yes';
				}

				if($q=='vm_ram') $k=$this->fileSizeConvert($k);
				if($q=='state_time') $k=date('d.m.Y H:i:s',$k);

				$html.='<tr><td>'.$this->translate($q).'</td><td>'.$this->translate($k).'</td></tr>';
			}

			$html.='</table>';
		}

		return $html;
	}

}