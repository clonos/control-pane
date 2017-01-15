<?php
$section_name='media';

$clonos->useDialogs(array(
	$section_name,
));
?>
<h1>Список подсетей</h1>

<p>
	<span class="top-button icon-plus id:<?php echo $section_name;?>">Добавить ISO</span>
</p>

<table class="tsimple" id="<?php echo $section_name;?>slist" width="100%">
	<thead>
		<td class="wdt-200 keyname">Имя носителя</td>
		<td class="txtleft">Путь</td>
		<td class="wdt-80">Действия</td>
	</thead>
	<tbody></tbody>
</table>