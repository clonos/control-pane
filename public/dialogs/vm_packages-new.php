<script type="text/javascript">
err_messages.add({
	'name':'<translate id="265">CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
	'first_name':'<translate id="266">TYPE THIS TEXT!!!</translate>',
	'last_name':'<translate id="266">TYPE THIS TEXT!!!</translate>',
});
</script>
<dialog id="vm_packages-new" class="window-box new">
	<h1>
		<span class="new"><translate id="267">Add new template</translate></span>
		<span class="edit"><translate id="268">Edit template</translate></span>
	</h1>
	<h2><translate id="269">Template Settings</translate></h2>
	<form class="win" method="post" id="templateSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="270">Name</translate>:</span>
				<input type="text" name="name" value="" pattern=".{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><translate id="39">Description</translate>:</span>
				<textarea name="description" rows="3"></textarea>
			</p>
			<p>
				<span class="field-name"><translate id="271">RAM Size</translate>:</span>
				<input type="text" name="pkg_vm_ram" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="1g" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="272">HDD Size</translate>:</span>
				<input type="text" name="pkg_vm_disk" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="10g" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="273">CPUs Count</translate>:</span>
				<span class="range">
					<input type="range" name="pkg_vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus" oninput="rngCpusShow.value=rngCpus.value" />
					<input type="text" disabled="disabled" id="rngCpusShow" value="1" />
				</span>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="114">Add</translate>" class="new button ok-but" />
		<input type="button" value="<translate id="74">Save</translate>" class="edit button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>