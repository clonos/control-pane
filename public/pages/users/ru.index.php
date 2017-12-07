<?php
$clonos->useDialogs(array(
	'users-new',
));
?>

<h1>Пользователи CBSD</h1>
<p><span class="top-button icon-plus id:users-new">Добавить пользователя</span></p>

<table class="tsimple" id="userslist" width="100%">
	<thead>
		<tr>
			<th class="txtleft">Логин</th>
			<th class="txtleft">Имя</th>
			<th class="txtleft">Фамилия</th>
			<th class="txtleft">Дата регистрации</th>
			<th class="txtleft">Последний вход</th>
			<th class="txtcenter">Активный пользователь</th>
			<th class="txtcenter wd-100">Действия</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>