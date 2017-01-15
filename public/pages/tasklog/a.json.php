<?php

$db=new Db('base','cbsdtaskd');
$res=$db->select("SELECT id,st_time,end_time,cmd,status,errcode,logfile FROM taskd WHERE owner='cbsdwebsys' ORDER BY id DESC");

$html='';
if($res!==false)
{
	$nth=0;
	$num=$nth & 1;

	if(!empty($res)) foreach($res as $item)
	{
		$hres=$this->getTableChunk('tasklog','tbody');
		if($hres!==false)
		{
			$html_tmp=$hres[1];
			$vars=array(
				'nth-num'=>'nth'.$num,
				'logid'=>$item['id'],
				'logcmd'=>$this->colorizeCmd($item['cmd']),
				'logstarttime'=>date("d.m.Y H:i",strtotime($item['st_time'])),
				'logendtime'=>date("d.m.Y H:i",strtotime($item['end_time'])),
				'logstatus'=>$item['status'],
				'logerrcode'=>$item['errcode'],
				'logsize'=>'0 B',
			);
			
			$logsize=0;
			$logfile=$item['logfile'];
			if(file_exists($logfile))
			{
				$logsize=filesize($logfile);
				$vars['logsize']=$this->fileSizeConvert($logsize,1024,true);
			}
			
			//if($logsize>0) $vars['logfile']='<span class="link openlog" title="'.$this->translate('Open log').'">'.$vars['logfile'].'</span>';
			
			$vars['buttvalue']=$this->translate('Open');
			
			$disabled='disabled';
			if($logsize>0 && $logsize<204800)
			{
				$disabled='';
			}
			$vars['disabled']=$disabled;
			
			$status='';
			if($item['status']==1) $status=' progress';
			if($item['status']==2 && $item['errcode']==0) $status=' ok';
			if($item['status']==2 && $item['errcode']!=0) $status=' error';
			$vars['status']=$status;
			
			foreach($vars as $var=>$val)
				$html_tmp=str_replace('#'.$var.'#',$val,$html_tmp);
			
			$html.=$html_tmp;
		}
	}

	echo json_encode(array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'taskloglist',
	));
}