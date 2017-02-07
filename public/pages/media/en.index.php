<?php
$section_name='media';

$clonos->useDialogs(array(
	$section_name,
));
?>
<h1>Subnet list</h1>

<p>
	<span class="top-button icon-plus id:<?php echo $section_name;?>">Add ISO</span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname">Storage name</td>
		<td class="txtleft">Path</td>
		<td class="wdt-80">Action</td>
	</thead>
	<tbody></tbody>
</table>