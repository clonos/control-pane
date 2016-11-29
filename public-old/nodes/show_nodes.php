<?php
function fetch_node_inv($dbfilepath)
{
	global $allncpu, $allcpufreq, $allphysmem, $allnodes, $nodetable, $alljails, $knownfreq, $workdir;

	$stat = file_exists($dbfilepath);

	if (!$stat) {
		$nodetable .= "<tr><td bgcolor=\"#CCFFCC\">$allnodes</td><td colspan=10><center>$dbfilepath not found</center></td></tr>";
		return 0;
	}

	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);

	$results = $db->query('SELECT COUNT(*) FROM jails;');

	if (!($results instanceof Sqlite3Result)) {
		$numjails=0;
	} else {
		while ($row = $results->fetchArray()) {
		$numjails=$row[0];
		}
	}

	$gwinfo="";

	$netres = $db->query('SELECT ip4,ip6,mask4,mask6 FROM net;');

	if (!($netres instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch net info";
	} else {
		while ($row = $netres->fetchArray()) {
			for ($i=0;$i<4;$i++)
				if (isset($row[$i])) $gwinfo.= $row[$i]." ";
		}
	}

	$netres = $db->query('SELECT * FROM gw;');

	if (!($netres instanceof Sqlite3Result)) {
	} else {
		while ($row = $netres->fetchArray()) {
			for ($i=0;$i<4;$i++)
				if (isset($row[$i])) $gwinfo.= $row[$i]." ";
		}
	}

	$results = $db->query('SELECT nodename,nodeip,fs,ncpu,physmem,cpufreq,osrelease FROM local;');
	if (!($results instanceof Sqlite3Result)) {
		$nodetable .=<<<EOF
		<tr>
			<td bgcolor="#CCFFCC">$allnodes</td><td colspan=10></td>
		</tr>
EOF;
	} else {
		while ($row = $results->fetchArray()) {
			list($nodename, $nodeip, $fs, $ncpu, $physmem, $cpufreq, $osrelease) = $row;
			$descrfile=$workdir."/var/db/nodedescr/".$nodename.".descr";
			$desc="";
			if (file_exists($descrfile)) {
				$fp = fopen($descrfile, "r");
				$size = filesize($descrfile);
				if ($size>0)
					$desc = fread($fp, filesize($descrfile));
					fclose($fp);
			}

			$locfile=$workdir."/var/db/nodedescr/".$nodename.".location";
			$loc="";

			if (file_exists($locfile)) {
				$fp = fopen($locfile, "r");
				$size = filesize($locfile);
				if ($size>0) 
					$loc = fread($fp, filesize($locfile));
				fclose($fp);
			}

			$notesfile=$workdir."/var/db/nodedescr/".$nodename.".notes";
			$notes="";

			if (file_exists($notesfile)) {
				$fp = fopen($notesfile, "r");
				$size = filesize($notesfile);
				if ($size>0)
					$notes = fread($fp, filesize($notesfile));
				fclose($fp);
			}

			$idle=check_locktime($nodeip);
			
			if ($idle == 0 ) {
				$hdr = '<tr rel="${nodename}" style="background-color:#D6D2D0">';
			} else {
				$hdr = '<tr rel="${nodename}">';
			}
			
			$butt_str=etranslate('Remove');
			$remove_td="<td><form id=\"noderemove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"nodename\" name=\"nodename\" value=\"$nodename\"/> <input type=\"submit\" name=\"$nodename+remove\" id=\"$nodename+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to detach node. Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_noderemove(nodename.value);}\"> </form></td>";
			
			$nodetable .=<<<EOF
			$hdr
				<td bgcolor="#CCFFCC" class="node-name" data-file="descr" data-type="text">$nodename</td>
				<td data-togle="toolkip" title="$gwinfo">$nodeip</td>
				<td class="edited" data-file="descr" data-type="textarea">$desc</td>
				<td class="edited" data-file="location" data-type="text">$loc</td>
				<td>$osrelease</td>
				<td>$fs</td>
				<td>$physmem</td>
				<td>$ncpu</td>
				<td>$cpufreq</td>
				<td>$numjails</td>
				<td class="edited" data-file="notes" data-type="textarea">$notes</td>
				${remove_td}
			</tr>
EOF;
		$allncpu+=$ncpu;
		$allphysmem+=$physmem;
		$allcpufreq+=$cpufreq;
		$alljails+=$numjails;
		if ($cpufreq>1) $knownfreq++;
	}
    }
$db->close();
}


function show_nodes()
{
	global $allncpu, $allcpufreq, $allphysmem, $allnodes, $nodetable, $alljails, $knownfreq, $workdir;

$allncpu=0;
$allcpufreq=0;
$allphysmem=0;
$allnodes=0;
$nodetable="";
$alljails=0;
$knownfreq=0;

$db = new SQLite3("$workdir/var/db/nodes.sqlite"); $db->busyTimeout(5000);
$sql = "SELECT nodename FROM nodelist";
$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
$row = array();
$i = 0;

while($res = $result->fetchArray(SQLITE3_ASSOC)){
	if(!isset($res['nodename'])) continue;
	$nodename = $res['nodename'];
	++$allnodes;
	$path=$workdir."/var/db/";
	$postfix=".sqlite";
	$dbpath=$path.chop($nodename).$postfix;
	fetch_node_inv($dbpath);
}

if ( $knownfreq > 0 ) {
	$avgfreq=round($allcpufreq / $knownfreq);
} else {
	$avgfreq=0;
}

	echo $nodetable;
}
?>

