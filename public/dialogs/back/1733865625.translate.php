<dialog id="translates" class="window-box new">
	<h1>
		<translate>Translate</translate>
	</h1>
	<h2><translate>Translate phrase</translate></h2>
	<form class="win" method="post" id="translate" onsubmit="return false;">
		<div class="window-content">
			<p>
				Вы можете поучаствовать в переводе интерфейса на свой язык.
			</p>
			<p>
				<span class="field-name"><translate>Original phrase</translate>:</span>
				<textarea id="origPhrase" disabled="disabled" name="origText"></textarea>
			</p>
			<p class="new">
				<span class="field-name"><translate>Translated phrase</translate>:</span>
				<textarea id="translPhrase" name="translText"></textarea>
			</p>
		</div>
		<input type="hidden" name="phraseID" id="trlt-phID" />
		<input type="hidden" name="type" id="trlt-type" />
		<input type="hidden" name="dialog" id="trlt-type" />
	</form>
	<div class="buttons">
		<input type="button" value="Save" class="new button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>