<script type="text/javascript">
err_messages.add({
	'version':'<translate id="323">Can not be empty. Name must be with a numbers and dot symbol</translate>',
});
src_table_pattern='<?php $res=$this->getTableChunk('baseslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="getrepo" class="window-box">
	<h1><translate id="324">Compile FreeBSD</translate></h1>
	<h2><translate id="328">Compile from bases</translate></h2>
	<form class="win" method="post" id="repoSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="117">Version number</translate>:</span>
				<input type="text" name="version" value="" pattern="[0-9.]{1,5}" required="required" maxlength="5" />
			</p>
<!--
			<p>
				<span class="field-name"><translate id="116">Version</translate>:</span>
				<input type="radio" name="version" value="release" id="ver1" checked="checked" class="inline"><label for="ver1">Release</label></radio>
				<input type="radio" name="version" value="stable" id="ver2" class="inline"><label for="ver2">Stable</label></radio>
			</p>
-->
			<p>
				<span class="field-name"><translate id="26">Repository</translate>:</span>
				<select name="repository" disabled="disabled">
					<option value="official"><translate id="329">Official FreeBSD repository (svn)</translate></option>
					<option value="clonos" selected="selected"><translate id="327">Clonos repository</translate></option>
					<option value="own"><translate id="330">Your own repository</translate></option>
					<option value="neighbor"><translate id="331">Neighbor node</translate></option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="112">Get</translate>" class="button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
