<?php
function fetch_net($dbfilepath)
{
	$stat = file_exists($dbfilepath);
	$str = "";
	$idx = 0;

	if (!$stat) {
		$nodetable .= "<tr><td bgcolor=\"#CCFFCC\">$allnodes</td><td colspan=10><center>$dbfilepath not found</center></td></tr>";
		return 0;
	}

	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	$vpnetres = $db->query('SELECT idx,name,vpnet FROM vpnet;');

	if (!($vpnetres instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch vpnet";
	} else {
		while ($row = $vpnetres->fetchArray()) {
			list( $idx , $name, $vpnet ) = $row;
$str .= <<<EOF
 <tr>
  <td><div class="field"><input type="text" name="vpnet_name" size="20" value="$name"></div></td>
  <td><div class="field"><input type="text" name="vpnet" size="60" value="$vpnet"></div></td>
  <td><a href="vpnet_remove.php?idx=$idx">remove</a></td></a>
 </tr>
EOF;

		}
	}

	$idx++;

// add empty string
$str .= <<<EOF
 <tr>
  <td><div class="field"><input type="text" name="vpnet_name" size="20" value=""></div></td>
  <td><div class="field"><input type="text" name="vpnet" size="60" value=""></div></td>
  <td><input type="submit" name="create" value="Add" >
 </tr>
EOF;


	echo $str;

	$db->close();
}


function show_vpnet()
{
	fetch_net("/var/db/webdev/vpnet.sqlite");
}
?>
