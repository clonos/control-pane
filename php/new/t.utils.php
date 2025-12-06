<?php
trait tUtils {
	
	public static function syslog($msg)
	{
		file_put_contents('/tmp/clonos.log', date("j.n.Y").":".$msg . "\n", FILE_APPEND);
		return 0;
	}

}