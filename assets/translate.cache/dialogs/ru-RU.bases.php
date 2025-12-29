<script type="text/javascript">
err_messages.add({
	'version':'<span id="trlt-323">Can not be empty. Name must be with a numbers and dot symbol</span>',
});
src_table_pattern='<?php $res=$this->getTableChunk('baseslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="basescompile" class="window-box">
	<h1><span id="trlt-324">Compile FreeBSD</span></h1>
	<h2><span id="trlt-325">Compile from sources</span></h2>
	<form class="win" method="post" id="basesSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-326">Sources version</span>:</span>
				<select name="sources">
<?php $this->getBasesCompileList(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><span id="trlt-118">Source</span>:</span>
				<select name="repository" disabled="disabled">
					<option value="clonos">Clonos repository</option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Get" class="button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
