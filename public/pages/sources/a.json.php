<?php
$html='';

$db=new Db('base','nodes');
$nodes=$db->select('select nodename,ip from nodelist order by nodename desc');
$nodes[]=array('nodename'=>'local');
$nodes=array_reverse($nodes);

$ids=array();
$nth=0;
if(!empty($nodes))foreach($nodes as $node)
{
	$db1=new Db('base',$node['nodename']);
	if($db1!==false)
	{
		$bases=$db1->select("SELECT idx,name,platform,ver,rev,date FROM bsdsrc ORDER BY CAST(ver AS int)");
		
		$num=$nth & 1;
		if(!empty($bases)) foreach($bases as $base)
		{
			$idle=1;
			//print_r($node);exit;
			if($node['nodename']!='local')
			{
				$idle=$this->check_locktime($node['ip']);
			}
			
			$hres=$this->getTableChunk('srcslist','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$vars=array(
					'nth-num'=>'nth'.$num,
					'node'=>$node['nodename'],
					'name'=>$base['name'],
					'platform'=>$base['platform'],
					'ver'=>$base['ver'],
					'rev'=>$base['rev'],
					'date'=>$base['date'],
					'maintenance'=>($idle==0)?' maintenance':'',
					'deltitle'=>$this->translate('Delete'),
					'updtitle'=>$this->translate('Update'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html.=$html_tpl;
			}
			$ids[]='#src'.$base['ver'];
			
		}
		
		$nth++;
	}
}

$tasks='';
if(!empty($ids))
{
	$tasks=$this->getRunningTasks($ids);
}

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'srcslist',
	'tasks'=>$tasks,
));