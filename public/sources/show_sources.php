<?php
function show_sources($nodelist="local")
{
	global $workdir;
	$pieces = explode(" ", $nodelist);

	foreach ($pieces as $nodename) {
		if (!$nodename) {
			$nodename=$nodelist;
		}
		$db = new SQLite3("$workdir/var/db/$nodename.sqlite"); $db->busyTimeout(5000);
		if (!$db) return;
		$sql = "SELECT idx,platform,name,ver,rev,date FROM bsdsrc;";
		$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
		$row = array();
		$i = 0;

		if ( $nodename != "local" ) {
			$nodeip=get_node_info($nodename,"ip");
			$idle=check_locktime($nodeip);
		} else {
			$idle=1;
		}

		if ($idle == 0 ) {
			$hdr = '<tr style="background-color:#D6D2D0">';
		} else {
			$hdr = '<tr>';
		}
		while($res = $result->fetchArray(SQLITE3_ASSOC)){
			if(!isset($res['idx'])) continue;
			$idx = $res['idx'];
			$platform = $res['platform'];
			$name = $res['name'];
			$ver = $res['ver'];
			$rev = $res['rev'];
			$date = $res['date'];
			$i++;

			if ( $idle != 0 ) {
				//off
				$statuscolor="#EDECEA";
				$butt_str=etranslate('Update');
				$action="<form id=\"srcupdate\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"idx\" name=\"idx\" value=\"$idx\"/> <input type=\"submit\" name=\"$idx+start\" id=\"$idx+start\" value=\"$butt_str\" class=\"inp\" OnClick=\"document.getElementById(id).disabled = 'disabled'; xajax_srcupdate(idx.value);\"> </form>";
				//$status_str=etranslate('Stopped');

			} else {
				$statuscolor="#D6D2D0";
				$action="offline";
			}

			if ( $idle != 0 ) {
				$status_td="<td>";
				$status_td.="$name";
				$status_td.="</td>";
				$butt_str=etranslate('Remove');
				$remove_td="<td><form id=\"srcremove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"idx\" name=\"idx\" value=\"$idx\"/> <input type=\"submit\" name=\"$idx+remove\" id=\"$idx+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to delete jail! Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_srcremove(idx.value);}\"> </form></td>";
			} else {
				$status_td="<td>$name</td>";
				$remove_td="<td>Remove</td>";
			}

			$str = <<<EOF
				${hdr}
				<td><strong>$nodename</strong></td>
				${status_td}
				<td>$platform</td>
				<td>$ver</td>
				<td>$rev</td>
				<td>$date</td>
				<td>$action</td>
				${remove_td}
EOF;

			echo $str;
		}
	}
//	echo "</tbody></table>";
}
?>
