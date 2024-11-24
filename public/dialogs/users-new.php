<script type="text/javascript">
err_messages.add({
	'username':'<?php echo $this->translate("CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'first_name':'<?php echo $this->translate("TYPE THIS TEXT!!!");?>',
	'last_name':'<?php echo $this->translate("TYPE THIS TEXT!!!");?>',
});
</script>
<dialog id="users-new" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Add new user');?></span>
		<span class="edit"><?php echo $this->translate('Edit user info');?></span>
	</h1>
	<h2><?php echo $this->translate('User Settings');?></h2>
	<form class="win" method="post" id="userSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('User name');?>:</span>
				<input type="text" name="username" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p class="new">
				<span class="field-name"><?php echo $this->translate('User password');?>:</span>
				<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<p class="new">
				<span class="field-name"><?php echo $this->translate('User password (again)');?>:</span>
				<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<fieldset class="edit full">
				<legend><input type="checkbox" id="letsedit-1" class="letsedit" /><label for="letsedit-1"> <?php echo $this->translate('Change the password');?>:</label></legend>
				<p>
					<span class="field-name"><?php echo $this->translate('User password');?>:</span>
					<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
				<p>
					<span class="field-name"><?php echo $this->translate('User password (again)');?>:</span>
					<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
			</fieldset>
			<p>
				<span class="field-name"><?php echo $this->translate('First name');?>:</span>
				<input type="text" name="first_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Last name');?>:</span>
				<input type="text" name="last_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<input type="checkbox" name="actuser" id="actuser" /><label for="actuser"> <?php echo $this->translate('Activate user');?></label>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Add');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Save');?>" class="edit button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>