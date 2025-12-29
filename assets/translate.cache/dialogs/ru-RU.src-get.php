<script type="text/javascript">
err_messages.add({
	'version':'<span id="trlt-323">Can not be empty. Name must be with a numbers and dot symbol</span>',
});
src_table_pattern='<?php $res=$this->getTableChunk('srcslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="srcget" class="window-box">
	<h1><span id="trlt-336">Get Sources FreeBSD</span></h1>
	<h2><span id="trlt-337">Get Version</span></h2>
	<form class="win" method="post" id="srcSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-117">Version number</span>:</span>
				<input type="text" name="version" value="" pattern="[0-9.]{1,5}" required="required" maxlength="5" />
			</p>
<!--
			<p>
				<span class="field-name"><span id="trlt-116">Version</span>:</span>
				<input type="radio" name="version" value="release" id="ver1" checked="checked" class="inline"><label for="ver1">Release</label></radio>
				<input type="radio" name="version" value="stable" id="ver2" class="inline"><label for="ver2">Stable</label></radio>
			</p>
-->
			<p>
				<span class="field-name"><span id="trlt-26">Repository</span>:</span>
				<select name="repository" disabled="disabled">
					<option value="official">Official FreeBSD repository (svn)</option>
					<option value="clonos">Clonos repository</option>
					<option value="own">Your own repository</option>
					<option value="neighbor">Neighbor node</option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Get" class="button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
<?php

/*
сложное правило для пароля: цифры, буквы маленькие и заглавные, плюс спецсимволы
^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,20}$
*/