<?php
trait tUtils {
	
	public static function syslog($msg)
	{
		file_put_contents('/tmp/clonos.log', date("j.n.Y").":".$msg . "\n", FILE_APPEND);
		return 0;
	}
	
	function getBasesCompileList(){
		$db1=new Db('base','local');
		if($db1!==false){
			$bases=$db1->select("SELECT idx,platform,ver FROM bsdsrc order by cast(ver AS int)", array());

			if(!empty($bases)) foreach($bases as $base){
				$val=$base['idx'];
				$stable=strlen(intval($base['ver']))<strlen($base['ver'])?'release':'stable';
				$name=$base['platform'].' '.$base['ver'].' '.$stable;
				echo '					<option value="'.$val.'">'.$name.'</option>',PHP_EOL;
			}
		}
	}


}