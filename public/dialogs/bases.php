<script type="text/javascript">
err_messages.add({
	'version':'<translate id="323">Can not be empty. Name must be with a numbers and dot symbol</translate>',
});
src_table_pattern='<?php $res=$this->getTableChunk('baseslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="basescompile" class="window-box">
	<h1><translate id="324">Compile FreeBSD</translate></h1>
	<h2><translate id="325">Compile from sources</translate></h2>
	<form class="win" method="post" id="basesSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="326">Sources version</translate>:</span>
				<select name="sources">
<?php $this->getBasesCompileList(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><translate id="118">Source</translate>:</span>
				<select name="repository" disabled="disabled">
					<option value="clonos"><translate id="327">Clonos repository</translate></option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="112">Get</translate>" class="button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
