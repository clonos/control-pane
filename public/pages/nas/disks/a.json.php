<?php

/* круговая диаграмма для информации о диске
https://htmlacademy.ru/demos/251#1
*/

$error=false;
$error_message='';

if(file_exists(self::filenameNASDisksList))
{
	$txt=file_get_contents(self::filenameNASDisksList);
	try{
		$disks=json_decode($txt,true);
		foreach($disks as $key=>$val)
		{
			if($disks[$key]=='type')
			{
				$disks[$key]['typeUC']=strtoupper($disks[$key]);
			}
		}
	}catch(Exception $e){
		$error=true;
		$error_message=$e->getMessage();
	}
}

$res_array = [
	'disks'=>$disks,
	'error'=>$error,
	'error_message'=>$error_message,
	'func'=>'nas.makeDiskInfoList',
];



$included_result_array=$res_array;