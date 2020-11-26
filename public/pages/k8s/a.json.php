<?php

$res=json_decode('{
  "clusters": [
    {
    "name": "ole",
    "cluster": "k8s.bhyve.io",
    "masters": 3,
    "workers": 3,
    "bhyves": [
        "master1",
        "master2",
        "master3",
        "worker1",
        "worker2", 
        "worker3" 
        ]
    },
    {
    "name": "mon",
    "cluster": "mon.bhyve.io",
    "masters": 1,
    "workers": 1,
    "bhyves": [
        "master1",
        "worker1"
        ]
    }
  ]
}',true);

$nth=0;
$hres=$this->getTableChunk('k8slist','tbody');

if(!empty($res) && isset($res['clusters']))foreach($res['clusters'] as $cluster)
{
	$num=$nth & 1;
	$html_tpl=$hres[1];
	$vars=array(
		'nth-num'=>'nth'.$num,
		'name'=>$cluster['name'],
		'cluster'=>$cluster['cluster'],
		'masters'=>$cluster['masters'],
		'workers'=>$cluster['workers'],
		'bhyves'=>join('; ',$cluster['bhyves']),
		//'jstatus'=>$this->translate($statuses[$status]),
		//'icon'=>($status==0)?'play':'stop',
		//'desktop'=>($status==0)?' s-off':' s-on',
		//'maintenance'=>($status==3)?' maintenance':'',
		//'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
		//'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
		//'vnc_title'=>$this->translate('Open VNC'),
		//'reboot_title'=>$this->translate('Restart jail'),
	);
	
	foreach($vars as $var=>$val)
		$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
	
//	if($node!='local') $html_tpl=str_replace('<span class="icon-cog"></span>','',$html_tpl);
	
	$html.=$html_tpl;
	
	$nth++;
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

$included_result_array=array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'k8slist',
	//'tasks'=>$tasks,
	'template'=>$html_tpl_1,
	//'protected'=>$protected,
);