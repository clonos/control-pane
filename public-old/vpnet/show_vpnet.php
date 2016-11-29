<?php
function fetch_key($dbfilepath)
{
	$stat = file_exists($dbfilepath);
	$str = "";
	$idx = 0;

	if (!$stat) {
		echo "$dbfilepath not found";
		die();
	}

	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	$vpnetres = $db->query('SELECT idx,name,vpnet FROM vpnet;');

	if (!($vpnetres instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch vpnet";
	} else {
		while ($row = $vpnetres->fetchArray()) {
			list( $idx , $name, $vpnet ) = $row;
			$butt_str=etranslate('Remove');
			$remove_td="<td><form id=\"vpnetremove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"vpnet\" name=\"vpnet\" value=\"$idx\"/> <input type=\"submit\" name=\"$idx+remove\" id=\"$idx+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to remove vpnet. Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_vpnetremove(vpnet.value);}\"> </form></td>";
$str .= <<<EOF
			<tr>
			<td><div class="text" class="form-control"><input type="text" name="name" size="20" value="$name"></div></td>
			<td><div class="text" class="form-control"><input type="text" name="key" size="60" value="$vpnet"></div></td>
			${remove_td}


			</tr>
EOF;
		}
	}

	$idx++;

// 	// add empty string
//  	$str .= <<<EOF
//  	<tr>
//  	<form name="vpnetadd" id="vpnetadd" class="navbar-form" role="create">
//  	<td><div class="text"><input type="text" class="form-control" placeholder="namename" name="namename" value="" id="namename"></div></td>
//  	<td><div class="text"><input type="text" class="form-control" placeholder="key" name="key" value="" id="key"></div></td>
//  	<td>
//  	<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_vpnetadd(xajax.getFormValues('vpnetadd')); return false;" value="Add" />
//  	</td>
//  
//  	</form>
//  	</tr>
// EOF;

	echo $str;

	$db->close();
}

function show_vpnet()
{
	global $workdir;

	fetch_key($workdir."/var/db/vpnet.sqlite");
}
?>
