<?php
$section_name='media';

$clonos->useDialogs(array(
	//$section_name,
	'media-upload',
));
?>
<h1><translate>Virtual media list</translate></h1>

<p>
<!--	<span class="top-button icon-plus id:<?php echo $section_name;?>">Info</span> -->
	<span class="top-button icon-plus id:media-upload"><translate>Upload ISO</translate></span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname"><translate>Name of the disk</translate></td>
		<td class="txtleft"><translate>Path</translate></td>
		<td class="txtleft wdt-80"><translate>VM</translate></td>
		<td class="wdt-80"><translate>Action</translate></td>
	</thead>
	<tbody></tbody>
</table>