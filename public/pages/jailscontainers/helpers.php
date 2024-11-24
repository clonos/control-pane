<?php
$clonos->useDialogs(['helpers-add']);
?>

<h1><?php echo $clonos->translate('Helpers list for jail'), ': ', $clonos->uri_chunks[1]; ?></h1>

<div id="tab1">
	<p><span class="top-button icon-plus id:helpers-add"><?php echo $clonos->translate('Add helper'); ?></span></p>

	<table class="tsimple" id="helperslist" width="100%">
		<thead>
			<tr>
				<th class="txtleft wdt-150">Логотип</th>
				<th class="txtleft wdt-100">Название</th>
				<th>Описание</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

</div>
<div id="tab2"></div>