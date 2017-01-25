<script type="text/javascript">
err_messages={
	'version':'<?php echo $this->translate("Can not be empty. Name must be with a numbers and dot symbol");?>',
};
src_table_pattern='<?php $res=$this->getTableChunk('baseslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="getrepo" class="window-box">
	<h1><?php echo $this->translate('Compile FreeBSD');?></h1>
	<h2><?php echo $this->translate('Compile from bases');?></h2>
	<form class="win" method="post" id="repoSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Version number');?>:</span>
				<input type="text" name="version" value="" pattern="[0-9.]{1,5}" required="required" maxlength="5" />
			</p>
<!--
			<p>
				<span class="field-name"><?php echo $this->translate('Version');?>:</span>
				<input type="radio" name="version" value="release" id="ver1" checked="checked" class="inline"><label for="ver1">Release</label></radio>
				<input type="radio" name="version" value="stable" id="ver2" class="inline"><label for="ver2">Stable</label></radio>
			</p>
-->
			<p>
				<span class="field-name"><?php echo $this->translate('Repository');?>:</span>
				<select name="repository" disabled="disabled">
					<option value="official"><?php echo $this->translate('Official FreeBSD repository (svn)');?></option>
					<option value="clonos" selected="selected"><?php echo $this->translate('Clonos repository');?></option>
					<option value="own"><?php echo $this->translate('Your own repository');?></option>
					<option value="neighbor"><?php echo $this->translate('Neighbor node');?></option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Get');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
