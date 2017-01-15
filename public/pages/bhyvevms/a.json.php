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
if(!empty($nodes))foreach($nodes as $node)
{
	$db1=new Db('base',$node);
	if($db1!==false)
	{
		$bhyves=$db1->select("SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve where hidden!=1 order by jname asc;");
		//$allnodes[$node]=$bhyves;
		
		$num=$nth & 1;
		if(!empty($bhyves)) foreach($bhyves as $bhyve)
		{
			
			$hres=$this->getTableChunk('bhyveslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$status=$this->check_vmonline($bhyve['jname']);
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
					'protected'=>'icon-cancel',
					'protitle'=>' title="'.$this->translate('Delete').'"',
//					'maintenance'=>($status==3)?' maintenance':'',
//					'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
//					'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
					'vnc_title'=>$this->translate('Open VNC'),
					'reboot_title'=>$this->translate('Restart bhyve'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
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

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'bhyveslist',
	'tasks'=>$tasks,
));
