<?php
$section_name='media';

$clonos->useDialogs(array(
	//$section_name,
	'media-upload',
));
?>
<h1>Virtual media list</h1>

<p>
<!--	<span class="top-button icon-plus id:<?php echo $section_name;?>">Info</span> -->
	<span class="top-button icon-plus id:media-upload">Upload ISO</span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname">Name of the disk</td>
		<td class="txtleft">Path</td>
		<td class="txtleft wdt-80">VM</td>
		<td class="wdt-80">Action</td>
	</thead>
	<tbody></tbody>
</table>