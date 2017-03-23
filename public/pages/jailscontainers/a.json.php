<?php
$jail_name='';
if(isset($this->uri_chunks[1])) $jail_name=$this->uri_chunks[1];
if(!empty($jail_name))
{
	include('helpers.json.php');
	return;
}


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
		$jails=$db1->select("SELECT jname,ip4_addr,status,protected FROM jails WHERE emulator!='bhyve' and hidden!=1 order by jname asc;");
		$allnodes[$node]=$jails;
		
		$num=$nth & 1;
		if(!empty($jails)) foreach($jails as $jail)
		{
			
			$hres=$this->getTableChunk('jailslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$status=$jail['status'];
				$vars=array(
					'nth-num'=>'nth'.$num,
					'node'=>$node,
					'ip4_addr'=>str_replace(',',',<wbr />',$jail['ip4_addr']),
					'jname'=>$jail['jname'],
					'status'=>$status,
					'jstatus'=>$this->translate($statuses[$status]),
					'icon'=>($status==0)?'play':'stop',
					'desktop'=>($status==0)?' s-off':' s-on',
					'maintenance'=>($status==3)?' maintenance':'',
					'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
					'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
					'vnc_title'=>$this->translate('Open VNC'),
					'reboot_title'=>$this->translate('Restart jail'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				if($node!='local') $html_tpl=str_replace('<span class="icon-cog"></span>','',$html_tpl);
				
				$html.=$html_tpl;
			}
			
			$jail_ids[]=$jail['jname'];
			
/*
			$jname=$jail['jname'];
			$jail_ids[]=$jname;
			$status=$jail['status'];
			$jstatus=$this->translate($statuses[$status]);
			$icon=($status==0)?'play':'stop';
			$desktop=($status==0)?' s-off':' s-on';
			$maintenance=($status==3)?' maintenance':'';
			//$protected=($jail['protected']==1)?' protected':'';
			$protected=($jail['protected']==1)?'icon-lock':'icon-cancel';
			$protitle=($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"';
			$vnc_title=$this->translate('Open VNC');
			$reboot_title=$this->translate('Restart jail');
			$html.=
<<<EOT
	<tr class="nth{$num}{$desktop}{$maintenance}" id="{$jname}">
		<td>{$node}</td>
		<td class="txtleft">{$jail['jname']}</td>
		<td class="txtleft jname">{$jail['ip4_addr']}</td>
		<td class="jstatus">{$jstatus}</td>
		<td class="ops" width="5"><span class="icon-cnt"><span class="icon-{$icon}"></span></span></td>
		<td width="5" class="op-settings"><span class="icon-cog"></span></td>
		<td width="5" class="op-reboot" title="{$reboot_title}"><span class="icon-arrows-cw"></span></td>
		<td width="5" class="op-del"{$protitle}><span class="{$protected}"></span></td>
		<td width="5" class="op-vnc"><span class="icon-desktop" title="{$vnc_title}"></span></td>
	</tr>
EOT;
*/
		}
		
		$nth++;
	}
}

$html=str_replace(array("\n","\r","\t"),'',$html);

$tasks='';
if(!empty($jail_ids))
{
	$tasks=$this->getRunningTasks($jail_ids);
}

$html_tpl_1=str_replace(array("\n","\r","\t"),'',$hres[1]);
if($hres!==false)
{
	$vars=array(
		'nth-num'=>'nth0',
		'status'=>'',
		'jstatus'=>$this->translate('Creating'),
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
		'title'=>$this->translate('Protected jail')
	)
);

echo json_encode(array(
//	'thead'=>$thead,
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'jailslist',
	'tasks'=>$tasks,
	'template'=>$html_tpl_1,
	'protected'=>$protected,
));
