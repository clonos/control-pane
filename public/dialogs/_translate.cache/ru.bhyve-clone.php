<script type="text/javascript">
err_messages.add({
	'vm_name':'<span id="trlt-83">Не может быть пустым. Имя должно начинаться и содержать латинские буквы / a-z / и не должно иметь спец. символы: -,.=% и тд.</span>',
});
</script>
<dialog id="bhyve-clone" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-255">Клонирование виртуальной машины</span></span>
	</h1>
	<h2><span id="trlt-256">Настройки клонирования ВМ</span></h2>
	<form class="win" method="post" id="bhyveCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-98">Имя виртуальной машины</span>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Clone" class="new button ok-but" />
		<input type="button" value="Отменить" class="button red cancel-but" />
	</div>
</dialog>
