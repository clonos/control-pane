<?php
function show_jails($nodelist="local")
{
	global $workdir;

	$pieces = explode(" ", $nodelist);

	foreach ($pieces as $nodename) {
		if (!$nodename) {
			$nodename=$nodelist;
		}
		$db = new SQLite3("$workdir/var/db/$nodename.sqlite"); $db->busyTimeout(5000);
		if (!$db) return;
		$sql = "SELECT jname,ip4_addr,status,hidden FROM jails WHERE emulator != \"bhyve\";";
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
			if(!isset($res['jname'])) continue;
			$jname = $res['jname'];
			$hidden = $res['hidden'];

			//skip blacklisted jail
			if ($hidden == "1") continue;

			$ip4_addr = $res['ip4_addr'];
			$status = $res['status'];
			$status_str = "";
			$i++;

			if ( $idle != 0 ) {
				switch ($status) {
				case 0:
					//off
					$statuscolor="#EDECEA";
					$butt_str=etranslate('Start');
//					$action="<form id=\"jstart\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"startb\" id=\"startb\" value=\"$butt_str\" class=\"inp\" OnClick=\"xajax.request({xjxfun:'jstart'}, { parameters:[jname.value, \"startb\"] });\"> </form>";
					$action="<form id=\"jstart\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+start\" id=\"$jname+start\" value=\"$butt_str\" class=\"inp\" OnClick=\"document.getElementById(id).disabled = 'disabled'; xajax_jstart(jname.value);\"> </form>";
					$status_str=etranslate('Stopped');
					break;
				case 1:
					//running
					$statuscolor="#51FF5F";
					$butt_str=etranslate('Stop');
//					$action="<form id=\"jstop\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"stopb\" id=\"stopb\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to stop jail! Are you sure?'))){xajax.request({xjxfun:'jstop'}, { parameters:[jname.value, \"stopb\"] });}\"> </form>";
					$action="<form id=\"jstop\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+stop\" id=\"$jname+stop\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to stop jail! Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_jstop(jname.value);}\"> </form>";
					$status_str=etranslate('Launched');
					break;
				default:
					$statuscolor="#D6D2D0";
					$action="maintenance";
					break;
				}
			} else {
				$statuscolor="#D6D2D0";
				$action="offline";
			}

			if ( $idle != 0 ) {
				$status_td="<td>";
				if(isset($_GET['jname'])) {
				    if (( $jname == $_GET['jname'] )&&( $nodename == $_GET['jnode'] )) {
					$status_td.="<a href=\"#jconfig\" data-toggle=\"modal\" data-target=\"#jconfig\"><div class=\"btn btn-success btn-xs\"><span class=\"glyphicon glyphicon-pencil\"></span></div></a>";
				    }
				}
				$status_td.="<a href=\"?mod=jailscontainers&jname=$jname&jnode=${nodename}\"> $jname</a>";
				$status_td.="</td>";
				$butt_str=etranslate('Remove');
				$remove_td="<td><form id=\"jremove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+remove\" id=\"$jname+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to delete jail! Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_jremove(jname.value);}\"> </form></td>";
			} else {
				$status_td="<td>$jname</td>";
				$remove_td="<td>Remove</td>";
			}

			$str = <<<EOF
				${hdr}
				<td><strong>$nodename</strong></td>
				${status_td}
				<td>$ip4_addr</td>
				<td style="background-color:$statuscolor" align='center'><div id='status_str'>$status_str</div></td>
				<td>$action</td>
				${remove_td}
EOF;

if ($status == 1) {
	$butt_str=etranslate('Launch VNC');
	$console="<input type=\"button\" value=\"Launch VNC\" class=\"inp\" onclick=\"window.open('/fun.server.php?launchvnc=".$jname."','_blank','toolbar=no,width=750,height=436,location=no');\">";
} else {
	$console=etranslate('Not running');
}

$str .=<<<EOF
				<td>$console</td>
				</tr>
EOF;
			echo $str;
		}
	}
//	echo "</tbody></table>";
}
?>
