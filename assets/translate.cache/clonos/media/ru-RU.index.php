<?php
$section_name='media';

$this->useDialogs(array(
	//$section_name,
	'media-upload',
));
?>
<h1><span id="trlt-302">Virtual media list</span></h1>

<p>
<!--	<span class="top-button icon-plus id:<?php echo $section_name;?>">Info</span> -->
	<span class="top-button icon-plus id:media-upload"><span id="trlt-303">Upload ISO</span></span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname"><span id="trlt-304">Name of the disk</span></td>
		<td class="txtleft"><span id="trlt-305">Path</span></td>
		<td class="txtleft wdt-80"><span id="trlt-236">VM</span></td>
		<td class="wdt-80"><span id="trlt-223">Action</span></td>
	</thead>
	<tbody></tbody>
</table>