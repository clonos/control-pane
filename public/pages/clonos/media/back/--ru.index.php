<?php
$section_name='media';

$clonos->useDialogs(array(
	//$section_name,
	'media-upload',
));
?>
<h1>Список виртуальных дисков</h1>

<p>
<!--	<span class="top-button icon-plus id:<?php echo $section_name;?>">Info</span> -->
	<span class="top-button icon-plus id:media-upload">Добавить ISO</span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname">Имя носителя</td>
		<td class="txtleft">Путь</td>
		<td class="txtleft wdt-80">Клетка/VM</td>
		<td class="wdt-80">Действия</td>
	</thead>
	<tbody></tbody>
</table>