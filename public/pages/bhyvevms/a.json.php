<?php
$html='';
//$hres=$this->getTableChunk('jailslist','thead');
//if($hres!==false) $thead=$hres[1];

$db=new Db('base','nodes');
$res=$db->select('select nodename from nodelist');
$nodes=array('local');
if(!empty($res))foreach($res as $val) $nodes[]=$val['nodename'];

$statuses=array('Not Launched','Launched','unknown-1','Maintenance','unknown-3','unknown-4','unknown-5','unknown-6');
$allnodes=array();

$jail_ids=array();
$nth=0;
$hres=$this->getTableChunk('bhyveslist','tbody');
if(!empty($nodes))foreach($nodes as $node)
{
	$db1=new Db('base',$node);
	if($db1!==false)
	{
		$bhyves=$db1->select("SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden,protected,bhyve_vnc_tcp_bind FROM bhyve where hidden!=1 order by jname asc;");
		//$allnodes[$node]=$bhyves;
		
		$num=$nth & 1;
		if(!empty($bhyves)) foreach($bhyves as $bhyve)
		{
			
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$status=$this->check_vmonline($bhyve['jname']);

				$jname=$bhyve['jname'];
				$vnc_port_status='grey';
				$vnc_ip=$bhyve['bhyve_vnc_tcp_bind'];
				if($status==1)
				{
					$vnc_port_file=$this->workdir.'/jails-system/'.$jname.'/vnc_port';
					if(file_exists($vnc_port_file))
					{
						$vnc_port=trim(file_get_contents($vnc_port_file));
					}
				}else{
					$vnc_port='';
				}
				if($vnc_ip!='127.0.0.1') $vnc_port_status='black';
				
				$vars=array(
					'jname'=>$bhyve['jname'],
					'nth-num'=>'nth'.$num,
					'desktop'=>'',
					'maintenance'=>'',
					'node'=>$node,
					'vm_name'=>'',
					'vm_ram'=>$this->fileSizeConvert($bhyve['vm_ram']),
					'vm_cpus'=>$bhyve['vm_cpus'],
					'vm_os_type'=>$bhyve['vm_os_type'],
					'vm_status'=>$this->translate($statuses[$status]),
					'desktop'=>($status==0)?' s-off':' s-on',
					'icon'=>($status==0)?'play':'stop',
					'protected'=>($bhyve['protected']==1)?'icon-lock':'icon-cancel',
					'protitle'=>' title="'.$this->translate('Delete').'"',
//					'maintenance'=>($status==3)?' maintenance':'',
//					'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
//					'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
					'vnc_title'=>$this->translate('Open VNC'),
					'reboot_title'=>$this->translate('Restart bhyve'),
					'vnc_port'=>$vnc_port,
					'vnc_port_status'=>$vnc_port_status,
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				if($node!='local') $html_tpl=str_replace('<span class="icon-cog"></span>','',$html_tpl);
				
				$html.=$html_tpl;
			}
			
			$bhyve_ids[]=$bhyve['jname'];
		}
		
		$nth++;
	}
}

$html=str_replace(array("\n","\r","\t"),'',$html);

$tasks='';
if(!empty($bhyve_ids))
{
	$tasks=$this->getRunningTasks($bhyve_ids);
}

$html_tpl_1=str_replace(array("\n","\r","\t"),'',$hres[1]);
if($hres!==false)
{
	$vars=array(
		'nth-num'=>'nth0',
		'vm_status'=>$this->translate('Creating'),
		'icon'=>'spin6 animate-spin',
		'desktop'=>' s-off',
		'maintenance'=>' maintenance busy',
		'protected'=>'icon-cancel',
		'protitle'=>'',
		'vnc_title'=>$this->translate('Open VNC'),
		'reboot_title'=>$this->translate('Restart jail'),
	);
	
	foreach($vars as $var=>$val)
		$html_tpl_1=str_replace('#'.$var.'#',$val,$html_tpl_1);
}

$protected=array(
	0=>array(
		'icon'=>'icon-cancel',
		'title'=>$this->translate('Delete')
	),
	1=>array(
		'icon'=>'icon-lock',
		'title'=>$this->translate('Protected bhyve')
	)
);

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'bhyveslist',
	'tasks'=>$tasks,
	'template'=>$html_tpl_1,
	'protected'=>$protected,
));
