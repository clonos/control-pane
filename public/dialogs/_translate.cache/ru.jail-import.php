<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Не может быть пустым. Имя должно начинаться и содержать латинские буквы / a-z / и не должно иметь спец. символы: -,.=% и тд.</span>',
});
</script>
<dialog id="jail-import" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-205">Импорт контейнера</span></span>
		<span class="edit"><span id="trlt-56">редактирование параметров контейнера</span></span>
	</h1>
	<h2><span id="trlt-206">Импорт контейнера</span></h2>
	<form class="win" method="post" id="jailImport" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div><span id="trlt-207">Перетащите файлы сюда</span></div>
				  <div class="or"><span id="trlt-208">-или-</span></div>
				  <div class="browser">
				   <label>
					 <span><span id="trlt-209">Кликните для открытия списка файлов</span></span>
					 <input type="file" name="jimp_files[]" multiple="multiple" Click to add Files>
				   </label>
				  </div>
				</div>
				<div class="uploader-progress"></div>
				<!-- /D&D Markup -->
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Отменить" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>