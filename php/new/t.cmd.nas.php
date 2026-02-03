<?php

trait tNAS {
	
	function ccmd_getDisksList()
	{
		
	}
	
	function ccmd_getRAIDsEngine()
	{
		$error=false;
		$error_message='';

		if(file_exists(self::filenameNASEnginesList))
		{
			$tengines=file_get_contents(self::filenameNASEnginesList);
			$tdisks=file_get_contents(self::filenameNASDisksList);
			try{
				$engines=json_decode($tengines,true);
				$disks=json_decode($tdisks,true);
			}catch(Exception $e){
				$error=true;
				$error_message=$e->getMessage();
			}
		}

		$res_array = [
			'engines'=>$engines['raid_engines'],
			'disks'=>$disks,
			'error'=>$error,
			'error_message'=>$error_message,
			//'func'=>'nas.makeRAIDsEngines',
		];
		return $res_array;


		//$included_result_array=$res_array;
	}
	
}