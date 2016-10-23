
<?php
function show_bhyvevm($nodelist="local")
{
	global $workdir;

	$pieces = explode(" ", $nodelist);

	$db = new SQLite3("$workdir/var/db/local.sqlite"); $db->busyTimeout(5000);
	$sql = "SELECT nodeip FROM local;";
	$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
	$row = $result->fetchArray();
	list($nodeip)=$row;
	if (strlen($nodeip)<10) $nodeip="127.0.0.1";
	$db->close();

	foreach ($pieces as $nodename) {
		if (!$nodename) {
			$nodename=$nodelist;
		}
		$db = new SQLite3("$workdir/var/db/$nodename.sqlite"); $db->busyTimeout(5000);
		$sql = "SELECT jname,vm_ram,vm_cpus,vm_os_type,hidden FROM bhyve;";
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

			$vm_ram = $res['vm_ram'] / 1024 / 1024 ;
			$vm_cpus = $res['vm_cpus'];
			$vm_os_type = $res['vm_os_type'];
			$status=check_vmonline($jname);
			$i++;

			if ( $idle != 0 ) {
				switch ($status) {
				case 0:
					//off
					$statuscolor="#EDECEA";
					$butt_str=etranslate('Start');
					$action="<form id=\"bstart\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+start\" id=\"$jname+start\" value=\"$butt_str\" class=\"inp\" OnClick=\"document.getElementById(id).disabled = 'disabled'; xajax_bstart(jname.value);\"> </form>";
					$status_str=etranslate('Stopped');
					break;
				case 1:
					//running
					$statuscolor="#51FF5F";
					$butt_str=etranslate('Stop');
					$action="<form id=\"bstop\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+stop\" id=\"$jname+stop\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to stop vm! Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_bstop(jname.value);}\"> </form>";
					$status_str=etranslate('Launched');
					break;
				default:
					$action="maintenance";
					//$action=$status;
					break;
				}
			} else {
				$statuscolor="#D6D2D0";
				$action="offline";
			}

			if ( $idle != 0 ) {
				$status_td="<td><a href=\"bstatus.php?jname=$jname\">$jname</a></td>";
				//$remove_td="<td><a href=\"bremove.php?jname=$jname\">Remove</a></td>";

//				$status_td="<td><a href=\"javascript:window.open('document.aspx','mywindowtitle','width=500,height=150')">open window</a>

				$butt_str=etranslate('Remove');
				$remove_td="<td><form id=\"bremove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"jname\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"$jname+remove\" id=\"$jname+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to delete vm! Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_bremove(jname.value);}\"> </form></td>";
			} else {
				$status_td="<td>$jname</td>";
				$remove_td="<td>Remove</td>";
			}

			$str = <<<EOF
			${hdr}
			<td><strong>$nodename</strong></td>
			${status_td}
			<td>$vm_ram</td><td>$vm_cpus</td>
			<td>$vm_os_type</td>
			<td style="background-color:$statuscolor">$status</td>
			<td>$action</td>
			${remove_td}
EOF;

		if ($status == 1) {
		$butt_str=etranslate('Launch VNC');
		$console="<input type=\"button\" value=\"Launch VNC\" class=\"inp\" onclick=\"window.open('/fun.server.php?launchvnc=".$jname."','_blank','toolbar=no,width=808,height=640,location=no');\">";

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
}
