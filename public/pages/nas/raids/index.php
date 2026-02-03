<?php
$this->useDialogs([
	'raid-add'
]);
?>
<h1>RAID Array</h1>
<p><span class="top-button icon-plus" data-btn="new-raid" data-dlg="raid-add">Add RAID</span></p>
<p><br /></p>

<div data-view="raids"></div>
<div class="vblock" data-view="unraid"><h2>Disks list without RAID:</h2>
	<table class="tsimple" width="100%" id="raids-disks">
		<thead>
			<tr>
				<th class="txtcenter wdt-30">№</th>
				<th class="txtleft elastic" data-sort="yes">Disk name</th>
				<th class="txtleft wdt-50" data-sort="yes">Type</th>
				<th class="txtcenter wdt-80" data-sort="yes">Size</th>
				<th class="txtleft elastic">Model</th>
				<th class="txtleft elastic">Serial</th>
				<th class="txtleft">Businfo</th>
			</tr>
		</thead>
	<tbody></tbody>
	</table>
</div>


<template id="tplRBlock">
	<div class="dblock" data-raid="#raid#"><div class="dheader">RAID: <strong>#raid#</strong></div><div class="container">
	<div class="raid">
		<span class="inlblk wdt-100">RAID Name:</span> <strong class="inlblk wdt-200">#raid#</strong>
		<span class="inlblk wdt-100">Capacity:</span> <strong class="inlblk wdt-200">#capacity_human#</strong><br />
		<span class="inlblk wdt-100">RAID Type:</span> <strong class="inlblk wdt-200">RAID #level# (#engine#)</strong>
		<span class="inlblk wdt-100" data-val="dcount">Disk Count:</span> <strong data-val="dcount" class="inlblk wdt-200">0</strong>
	</div>
	<details>
		<summary>Disks list:</summary>
		<table class="tsimple" width="100%">
			<thead>
				<tr>
					<th class="txtcenter wdt-30">№</th>
					<th class="txtleft elastic">Disk name</th>
					<th class="txtleft wdt-50">Type</th>
					<th class="txtcenter wdt-80">Size</th>
					<th class="txtleft elastic">Model</th>
					<th class="txtleft elastic">Serial</th>
					<th class="txtleft">Businfo</th>
				</tr>
			</thead>
		<tbody></tbody>
		</table>
	</details>
</div></div>
</template>
<template id="tplRTableTr">
	<tr>
		<td class="txtleft autonum"></td>
		<td class="txtleft">#disk#</td>
		<td class="txtleft">#type#</td>
		<td class="txtcenter">#capacity_human#</td>
		<td class="txtleft">#model#</td>
		<td class="txtleft">#ident#</td>
		<td class="txtleft">#businfo#</td>
	</tr>
</template>


<?php


/*

$db = new Db('file','/zmirror/jails/formfile/dsk.sqlite');
if($db->error){
	echo 'DB not found';
	return;
}

$db_path = $db->getFileName();
$res_html = (new DialogsGen($db_path))->generate();
//$res_html = '<h1>'.$this->translate('Helper settings: '.$hash).'</h1>'.$res_html;
echo $res_html;

*/