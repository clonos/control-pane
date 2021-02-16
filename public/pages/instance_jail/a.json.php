<?php
if(!empty($this->_vars['hash']))
{
	include('helpers.php');
	return;
}

$sys_helpers=array();
//	'network','cbsd','bhyvenet','general','zfsinstall','userspw','natcfg','jconstruct',
//);

$db=new Db('clonos');
if($db!==false)
{
	$query="select module from sys_helpers_list";
	if(!$db->error)
	{
		$res=$db->select($query);
		if(!empty($res))
		{
			foreach($res as $r)	$sys_helpers[]=$r['module'];
			
		}
	}
}

$html='';
$arr=array();
$res=$this->cbsd_cmd('forms header=0');
if($res['retval']==0)
{
	$empty_logo='/images/logo/empty.png';
	
	$lst=explode("\n",$res['message']);
	$n=0;
	if(!empty($lst)) foreach($lst as $item)
	{
		if(in_array($item,$sys_helpers))
		{
			$description='';
			$db=new Db('helper',$item);
			if($db!==false)
			{
				if($db->error)
				{
					$file_name=$db->getFileName();
					if(!file_exists($file_name))
					{
						$res=$this->cbsd_cmd('forms module='.$item.' inter=0');
						if($res['retval']==0)
						{
							$db=new Db('helper',$item);
						}
					}
				}
				if($db!==false && !$db->error) $res=$db->selectOne("select longdesc from system");
				
				if(isset($res['longdesc'])) $description=$res['longdesc']; else $description=$this->translate('no data').'&hellip; ('.$file_name.')';
			}else{
				$description='helper connection error!';
			}

			$hres=$this->getTableChunk('instances','tbody');
			if($hres!==false)
			{
				$html_tpl=$hres[1];
				$logo_file='images/logo/'.$item.'.png';
				$logo=file_exists($this->realpath_public.$logo_file)?'/'.$logo_file:$empty_logo;
				$vars=array(
					'nth-num'=>'nth0',
					'logo'=>$logo,
					'name'=>$item,
					'description'=>$description,
					'opentitle'=>$this->translate('Open'),
				);
				
				foreach($vars as $var=>$val)
					$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
				
				$html.=$html_tpl;
			}
		}
	}
}

$html=str_replace(array("\n","\r","\t"),'',$html);

/*
echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'instanceslist',
));
*/

$included_result_array=array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'instanceslist',
);