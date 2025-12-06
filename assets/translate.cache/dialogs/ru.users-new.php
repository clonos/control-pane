<script type="text/javascript">
err_messages.add({
	'username':'<span id="trlt-265">CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
	'first_name':'<span id="trlt-266">TYPE THIS TEXT!!!</span>',
	'last_name':'<span id="trlt-266">TYPE THIS TEXT!!!</span>',
});
</script>
<dialog id="users-new" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-366">Add new user</span></span>
		<span class="edit"><span id="trlt-367">Edit user info</span></span>
	</h1>
	<h2><span id="trlt-368">User Settings</span></h2>
	<form class="win" method="post" id="userSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-369">User name</span>:</span>
				<input type="text" name="username" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p class="new">
				<span class="field-name"><span id="trlt-370">User password</span>:</span>
				<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<p class="new">
				<span class="field-name"><span id="trlt-371">User password (again)</span>:</span>
				<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" class="edit-disable"></input>
			</p>
			<fieldset class="edit full">
				<legend><input type="checkbox" id="letsedit-1" class="letsedit" /><label for="letsedit-1"> <span id="trlt-372">Change the password</span>:</label></legend>
				<p>
					<span class="field-name"><span id="trlt-370">User password</span>:</span>
					<input type="password" name="password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
				<p>
					<span class="field-name"><span id="trlt-371">User password (again)</span>:</span>
					<input type="password" name="password1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20" disabled></input>
				</p>
			</fieldset>
			<p>
				<span class="field-name"><span id="trlt-373">First name</span>:</span>
				<input type="text" name="first_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-374">Last name</span>:</span>
				<input type="text" name="last_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<input type="checkbox" name="actuser" id="actuser" /><label for="actuser"> <span id="trlt-375">Activate user</span></label>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Add" class="new button ok-but" />
		<input type="button" value="Save" class="edit button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>