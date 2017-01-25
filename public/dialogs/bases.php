<script type="text/javascript">
err_messages={
	'version':'<?php echo $this->translate("Can not be empty. Name must be with a numbers and dot symbol");?>',
};
src_table_pattern='<?php $res=$this->getTableChunk('baseslist','tbody'); echo str_replace(array("\n","\r","\t"),'',$res[1]);?>';
</script>
<dialog id="basescompile" class="window-box">
	<h1><?php echo $this->translate('Compile FreeBSD');?></h1>
	<h2><?php echo $this->translate('Compile from sources');?></h2>
	<form class="win" method="post" id="basesSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Sources version');?>:</span>
				<select name="sources">
<?php $this->getBasesCompileList(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Source');?>:</span>
				<select name="repository" disabled="disabled">
					<option value="clonos"><?php echo $this->translate('Clonos repository');?></option>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Get');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
