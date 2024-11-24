<dialog id="helpers-add" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Helpers add');?></span>
	</h1>
	<h2><?php echo $this->translate('Select helpers for install');?></h2>
	<form class="win" method="post" id="helpersAddSettings" onsubmit="return false;">
		<div class="window-content"></div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Add');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
