<?php
$html='';

$db=new Db('base','nodes');
$nodes=$db->select('select nodename,ip from nodelist order by nodename desc');
$nodes[]=array('nodename'=>'local');
$nodes=array_reverse($nodes);

$nth=0;
if(!empty($nodes))foreach($nodes as $node)
{
	$db1=new Db('base',$node['nodename']);
	if($db1!==false)
	{
		$bases=$db1->select("SELECT idx,platform,name,arch,targetarch,ver,stable,elf,date FROM bsdbase");
		
		$num=$nth & 1;
		if(!empty($bases)) foreach($bases as $base)
		{
			$idle=1;
			//print_r($node);exit;
			if($node['nodename']!='local')
			{
				$idle=$this->check_locktime($node['ip']);
			}
			
			$hres=$this->getTableChunk('baseslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$vars=array(
					'nth-num'=>'nth'.$num,
					'node'=>$node['nodename'],
					'name'=>$base['name'],
					'platform'=>$base['platform'],
					'arch'=>$base['arch'],
					'targetarch'=>$base['targetarch'],
					'ver'=>$base['ver'],
					'stable'=>$base['stable'],
					'elf'=>$base['elf'],
					'date'=>$base['date'],
					'maintenance'=>($idle==0)?' maintenance':'',
					'deltitle'=>$this->translate('Delete'),
					'updtitle'=>$this->translate('Update'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html.=$html_tpl;
			}
			
			//$jail_ids[]=$jail['jname'];
		}
		
		$nth++;
	}
}

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'baseslist',
));