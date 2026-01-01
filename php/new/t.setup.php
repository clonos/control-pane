<?php
trait tSetup {
	
	public function setup()
	{
		$arr=($_SERVER['argv']);
		if($arr[0]=='_setup.php')
		{
			//print_r($arr);
		}
		//echo PHP_EOL.PHP_EOL;
		
		$db=new Db('clonos');
		if(!$db->isConnected())	return array('error'=>true,'error_message'=>'db connection lost!');
		
		$tableName='menu';
		$query="SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'";
		$res=$db->selectOne($query,[]);
		if(empty($res))
		{
			$filename=self::$realpath_php.'clonos_menu.dump.sql';
			if(file_exists($filename))
			{
				$sql=file_get_contents($filename);
				print_r($db->exec($sql));
			}else{
				echo 'File: "'.$filename.'" no found.';
			}
		}
		echo PHP_EOL;	//.PHP_EOL
	}
}