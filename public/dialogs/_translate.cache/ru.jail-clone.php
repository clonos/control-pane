<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Не может быть пустым. Имя должно начинаться и содержать латинские буквы / a-z / и не должно иметь спец. символы: -,.=% и тд.</span>',
	'host_hostname':'<span id="trlt-84">Это поле не может быть пустым</span>',
	'ip':'<span id="trlt-85">Укажите корректный IP адрес, например: 10.0.0.2</span>',
});
</script>
<dialog id="jail-clone" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-211">Клонирование контейнера</span></span>
	</h1>
	<h2><span id="trlt-212">Настройки клонирования</span></h2>
	<form class="win" method="post" id="jailCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<span id="trlt-119"><strong>ВНИМАНИЕ!</strong> Клонирование запущенного контейнера может привести к неконсистентным данным клона (например окружение с активной базой данных). Остановите контейнер для безопасного клонирования или продолжайте, если уверены в безопасности операции!</span>
			</p>
			<p>
				<span class="field-name"><span id="trlt-58">имя контейнера</span>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-59">имя хоста</span> (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-61">IP-адрес</span>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Clone" class="new button ok-but" />
		<input type="button" value="Отменить" class="button red cancel-but" />
	</div>
</dialog>
