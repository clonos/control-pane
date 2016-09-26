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
	$authkeyres = $db->query('SELECT idx,name,authkey FROM authkey;');

	if (!($authkeyres instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch authkey";
	} else {
		while ($row = $authkeyres->fetchArray()) {
			list( $idx , $name, $authkey ) = $row;
			$butt_str=etranslate('Remove');
			$remove_td="<td><form id=\"authkeyremove\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"authkey\" name=\"authkey\" value=\"$idx\"/> <input type=\"submit\" name=\"$idx+remove\" id=\"$idx+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to remove authkey. Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_authkeyremove(authkey.value);}\"> </form></td>";
$str .= <<<EOF
			<tr>
			<td><div class="text" class="form-control"><input type="text" name="name" size="20" value="$name"></div></td>
			<td><div class="text" class="form-control"><input type="text" name="key" size="60" value="$authkey"></div></td>
			${remove_td}


			</tr>
EOF;
		}
	}

	$idx++;

// 	// add empty string
//  	$str .= <<<EOF
//  	<tr>
//  	<form name="authkeyadd" id="authkeyadd" class="navbar-form" role="create">
//  	<td><div class="text"><input type="text" class="form-control" placeholder="namename" name="namename" value="" id="namename"></div></td>
//  	<td><div class="text"><input type="text" class="form-control" placeholder="key" name="key" value="" id="key"></div></td>
//  	<td>
//  	<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_authkeyadd(xajax.getFormValues('authkeyadd')); return false;" value="Add" />
//  	</td>
//  
//  	</form>
//  	</tr>
// EOF;

	echo $str;

	$db->close();
}

function show_authkey()
{
	global $workdir;

	fetch_key($workdir."/var/db/authkey.sqlite");
}
?>
