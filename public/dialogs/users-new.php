<script type="text/javascript">
err_messages.add({
	'username':'<translate id="265">CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
	'first_name':'<translate id="266">TYPE THIS TEXT!!!</translate>',
	'last_name':'<translate id="266">TYPE THIS TEXT!!!</translate>',
});
</script>
<dialog id="users-new" class="window-box new">
	<h1>
		<span class="new"><translate id="366">Add new user</translate></span>
		<span class="edit"><translate id="367">Edit user info</translate></span>
	</h1>
	<h2><translate id="368">User Settings</translate></h2>
	<form class="win" method="post" id="userSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="369">User name</translate>:</span>
				<input type="text" name="username" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p class="new">
				<span class="field-name"><translate id="370">User password</translate>:</span>
				<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<p class="new">
				<span class="field-name"><translate id="371">User password (again)</translate>:</span>
				<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<fieldset class="edit full">
				<legend><input type="checkbox" id="letsedit-1" class="letsedit" /><label for="letsedit-1"> <translate id="372">Change the password</translate>:</label></legend>
				<p>
					<span class="field-name"><translate id="370">User password</translate>:</span>
					<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
				<p>
					<span class="field-name"><translate id="371">User password (again)</translate>:</span>
					<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
			</fieldset>
			<p>
				<span class="field-name"><translate id="373">First name</translate>:</span>
				<input type="text" name="first_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><translate id="374">Last name</translate>:</span>
				<input type="text" name="last_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<input type="checkbox" name="actuser" id="actuser" /><label for="actuser"> <translate id="375">Activate user</translate></label>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="114">Add</translate>" class="new button ok-but" />
		<input type="button" value="<translate id="74">Save</translate>" class="edit button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>