<?php
$hash=$this->url_hash;
if(empty($hash))
{
#	Узнаём список хелперов
	$jails_helpers=array();
	$db=new Db('clonos');
	if($db!==false)
	{
		$query="select module from jails_helpers_list";
		if(!$db->error)
		{
			$res=$db->select($query);
			if(!empty($res))
			{
				foreach($res as $r)	$jails_helpers[]=$r['module'];
			}
		}
	}

	$lst=array();
	if(!empty($jails_helpers)) foreach($jails_helpers as $helper)
	{
		$db=new Db('helper',array('jname'=>$jail_name,'helper'=>$helper));
		if(!$db->error)	// !error — значит хелпер установлен
		{
			$res=$db->selectAssoc("select longdesc from system limit 1");
			if(isset($res['longdesc'])) $description=$res['longdesc']; else $description=$this->translate('no data').'&hellip; ('.$file_name.')';
			$lst[]=array('helper'=>$helper,'description'=>$description);
		}else{
			$hlst[]=$helper;
		}
	}
	
	$html='';
	$html_tpl='';
	$empty_logo='/images/logo/empty.png';
	$hres=$this->getTableChunk('helpers','tbody');
	if($hres!==false) $html_tpl=$hres[1];
	
	if(!empty($lst) && !empty($html_tpl))
	{
		foreach($lst as $item)
		{
			$tpl=$html_tpl;
			$logo_file='images/logo/'.$item['helper'].'.png';
			$logo=file_exists($this->realpath_public.$logo_file)?'/'.$logo_file:$empty_logo;
			$vars=array(
				'nth-num'=>'nth0',
				'logo'=>$logo,
				'name'=>$item['helper'],
				'description'=>$item['description'],
				'opentitle'=>$this->translate('Open'),
			);
			
			foreach($vars as $var=>$val)
				$tpl=str_replace('#'.$var.'#',$val,$tpl);
			
			$html.=$tpl;
		}
	}else{
		$html='<tr><td colspan="3">'.$this->translate('No installed helpers').'</td></tr>';
	}
	
	// Определяем список хелперов, доступных для установки в клетку
	$helpers_list_html='<ul class="helpers-list">';
	if(!empty($hlst)) foreach($hlst as $item)
	{
		$logo_file='images/logo/'.$item.'.png';
		$logo=file_exists($this->realpath_public.$logo_file)?'/'.$logo_file:$empty_logo;
		$helpers_list_html.='<li><input type="checkbox" name="'.$item.'" id="'.$item.'"><label for="'.$item.'"><img src="'.$logo.'" />&nbsp; '.$item.'</label></li>';
	}
	$helpers_list_html.='</ul>';
	
	$html=str_replace(array("\n","\r","\t"),'',$html);

	/*
	echo json_encode(array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'helperslist',
		'helpers_list'=>$helpers_list_html,
	));
	*/
	$included_result_array=array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'helperslist',
		'helpers_list'=>$helpers_list_html,
	);
	return;
}else{
#	Открываем настройки хелпера
	
	$db=new Db('helper',array('jname'=>$jail_name,'helper'=>$hash));
	if($db->error)
	{
		//echo json_encode(array('error'=>true,'errorMessage'=>'No helper database!'));
		$included_result_array=array('error'=>true,'errorMessage'=>'No helper database!');
		return;
	}

	$db_path=$db->getFileName();
	$form=new Forms($jail_name,$hash,$db_path);
	$res=$form->generate();
	$res['html']='<h1>'.$this->translate('Helper settings: '.$hash).'</h1>'.$res['html'];
}


//echo json_encode(array('html'=>$res['html'],'func'=>'fillTab'));	//,'currents'=>$res['currents']
$included_result_array=array(
	'html'=>$res['html'],
	'func'=>'fillTab'
);