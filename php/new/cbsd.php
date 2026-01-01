<?php

//require_once __DIR__ . '../../vendor/autoload.php';
//require_once $clonos_path.'vendor/autoload.php';	//../../clonos/

//\Sentry\init([
//  'dsn' => 'http://2d223b424e41448123a5f79e2e5d7611@sentry.convectix.com/2',
//  // Add request headers, cookies and IP address,
//  // see https://docs.sentry.io/platforms/php/data-management/data-collected/ for more info
//  'send_default_pii' => true,
//]);


class CBSD extends ClonOS {

	static function run($cmd, $args){

		$prepend='env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ';
		$defines = array(
			'{cbsd_loc}' => "/usr/local/bin/cbsd"
		);

		$specs = array(
			0 => array('pipe','r'),
			1 => array('pipe','w'),
			2 => array('pipe','r')
		);

		$cmd = vsprintf($cmd, $args); # make sure we deal with a string
		$cmd = strtr($cmd, $defines);
		// olevole: add trim tabs + \r\n
		$cmd = trim(preg_replace('/\t+|\r|\n/', '', $cmd));
		$full_cmd = $prepend.trim($cmd);

		if ($cmd != escapeshellcmd($cmd)){
			ClonOS::clonos_syslog("cmd.php SHELL ESCAPE:". $cmd);
			die("Shell escape attempt");
		}

//		ClonOS::syslog("cmd.php:". $full_cmd);
		$process = proc_open($full_cmd,$specs,$pipes,null,null);

		$error=false;
		$error_message='';
		$message='';
		if (is_resource($process)){
			$buf=stream_get_contents($pipes[1]);
			$buf0=stream_get_contents($pipes[0]);
			$buf1=stream_get_contents($pipes[2]);
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);

			$task_id=-1;
			$return_value = proc_close($process);
			if($return_value==0) $message=trim($buf); else {
				$error=true;
				$error_message=$buf;
			}

//			ClonOS::clonos_syslog("cmd.php:"."ret:".$return_value." msg:[".$message."] "."error:[".$error."] "."error_message:[".$error_message." ]");

			return array(
				'cmd'=>$cmd,
				'full_cmd'=>$full_cmd,
				'retval'=>$return_value,
				'message'=>$message,
				'error'=>$error,
				'error_message'=>$error_message
			);
		} else {
			ClonOS::clonos_syslog("cmd.php: empty process resource");
		}
	}

	static function register_media($path,$file,$ext)
	{
		$cmd='cbsd media mode=register name=%s path=%s type=%s';
		$res=self::run($cmd, array($file, $path.$file, $ext));
		echo json_encode($arr);
	}
}

