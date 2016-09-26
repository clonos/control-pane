<?php
function flush_log()
{
	$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task mode=flushall", "r");
	$read = fgets($handle, 4096);
	pclose($handle);
//	header( 'Location: /tasklog' ) ;
}

// just show all log where owner is cbsdweb
function show_logs()
{
	global $workdir;

	$butt_str=etranslate('FlushLog');
	echo "<form id=\"flushtasklog\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"flushtasklog\" name=\"flushtasklog\" value=\"1\"/> <input type=\"submit\" name=\"1+remove\" id=\"1+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to flush log. Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_flushtasklog(flushtasklog.value);}\"> </form>";
	
	$db = new SQLite3("$workdir/var/db/cbsdtaskd.sqlite"); $db->busyTimeout(5000);
	$sql = "SELECT id,st_time,end_time,cmd,status,errcode,logfile FROM taskd WHERE owner='cbsdweb' ORDER BY id DESC;";
	$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
	$row = array();
	$i = 0;

	while($res = $result->fetchArray(SQLITE3_ASSOC)){
		if(!isset($res['id'])) continue;
		$id = $res['id'];
		$cmd = $res['cmd'];
		$start_time = $res['st_time'];
		$end_time = $res['end_time'];
		$status = $res['status'];
		$errcode = $res['errcode'];
		$logfile = $res['logfile'];

		if (file_exists($logfile)) {
			$tmplogsize=filesize($logfile);
			$logsize=human_filesize($tmplogsize,0);
		} else {
			$logsize=0;
		}
		$i++;

		switch ($status) {
		case 0:
			//pending
			$hdr = '<tr style="background-color:#51FF5F">';
			break;
		case 1:
			//in progress
			$hdr = '<tr style="background-color:#F3FF05">';
			break;
		case 2:
			//complete
			switch ($errcode) {
			case 0:
				$hdr = '<tr style="background-color:#EDECEA">';
				break;
			default:
				//errcode not 0
				$hdr = '<tr style="background-color:#FFA7A1">';
				break;
			}
			break;
		}

		$s_time=date("Y-M-d H:i", strtotime($start_time));
		$e_time=date("Y-M-d H:i", strtotime($end_time));

		if ( $logsize!= 0 ) {
			$logfiletd="<td><a href=\"tasklog/showtasklog.php?log=$logfile\" target=\"_blank\">$logfile</a></td>";
		} else {
			$logfiletd="<td>$logfile</td>";
		}

		$str = <<<EOF
			<td>$id</td>
			<td>$cmd</td>
			<td>$s_time</td>
			<td>$e_time</td>
			<td>$status</td>
			<td>$errcode</td>
			$logfiletd
			<td>$logsize</td>
			</tr>
EOF;
		echo $hdr.$str;
	}
}

function show_tasklog()
{
	if (isset($_GET['flushlog'])) {
		flush_log();
	}

	show_logs();
}
