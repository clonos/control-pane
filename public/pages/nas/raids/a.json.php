<?php

$error=false;
$error_message='';

if(file_exists(self::filenameNASDisksList))
{
	$traids=file_get_contents(self::filenameNASRaidsList);
	$tdisks=file_get_contents(self::filenameNASDisksList);
	try{
		$disks=json_decode($tdisks,true);
		$raids=json_decode($traids,true);
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
	'raids'=>$raids,
	'disks'=>$disks,
	'error'=>$error,
	'error_message'=>$error_message,
	'func'=>'nas.makeRAIDsList',
];



$included_result_array=$res_array;