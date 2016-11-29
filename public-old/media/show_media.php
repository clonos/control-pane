<?php
function fetch_media($dbfilepath)
{
	$stat = file_exists($dbfilepath);
	$str = "";
	$idx = 0;

	if (!$stat) {
		echo "$dbfilepath not found";
		die();
	}

	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	$mediares = $db->query('SELECT idx,name,path FROM media;');

	if (!($mediares instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch media";
	} else {
		while ($row = $mediares->fetchArray()) {
			list( $idx , $name, $path ) = $row;
			$butt_str=etranslate('Remove');
			$remove_td="<td><form id=\"name\" Onsubmit=\"return false;\"> <input type=\"hidden\" id=\"name\" name=\"name\" value=\"$idx\"/> <input type=\"submit\" name=\"$idx+remove\" id=\"$idx+remove\" value=\"$butt_str\" class=\"inp\" OnClick=\"if(confirm(translate('You want to remove name. Are you sure?'))){document.getElementById(id).disabled = 'disabled'; xajax_mediaremove(name.value);}\"> </form></td>";
$str .= <<<EOF
			<tr>
			<td>$name</td>
			<td>$path</td>
			${remove_td}
			</tr>
EOF;
		}
	}

	$idx++;

// 	// add empty string
//  	$str .= <<<EOF
//  	<tr>
//  	<form name="nameadd" id="nameadd" class="navbar-form" role="create">
//  	<td><div class="text"><input type="text" class="form-control" placeholder="namename" name="namename" value="" id="namename"></div></td>
//  	<td><div class="text"><input type="text" class="form-control" placeholder="key" name="key" value="" id="key"></div></td>
//  	<td>
//  	<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_nameadd(xajax.getFormValues('nameadd')); return false;" value="Add" />
//  	</td>
//  
//  	</form>
//  	</tr>
// EOF;

	echo $str;

	$db->close();
}

function show_media()
{
	global $workdir;

	fetch_media($workdir."/var/db/storage_media.sqlite");
}
?>
