<script type="text/javascript">
err_messages.add({
	'name':'<span id="trlt-265">CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
	'first_name':'<span id="trlt-266">TYPE THIS TEXT!!!</span>',
	'last_name':'<span id="trlt-266">TYPE THIS TEXT!!!</span>',
});
</script>
<dialog id="vm_packages-new" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-267">Add new template</span></span>
		<span class="edit"><span id="trlt-268">Edit template</span></span>
	</h1>
	<h2><span id="trlt-269">Template Settings</span></h2>
	<form class="win" method="post" id="templateSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-270">Name</span>:</span>
				<input type="text" name="name" value="" pattern=".{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-39">Description</span>:</span>
				<textarea name="description" rows="3"></textarea>
			</p>
			<p>
				<span class="field-name"><span id="trlt-271">RAM Size</span>:</span>
				<input type="text" name="pkg_vm_ram" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="1g" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-272">HDD Size</span>:</span>
				<input type="text" name="pkg_vm_disk" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="10g" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-273">CPUs Count</span>:</span>
				<span class="range">
					<input type="range" name="pkg_vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus" oninput="rngCpusShow.value=rngCpus.value" />
					<input type="text" disabled="disabled" id="rngCpusShow" value="1" />
				</span>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Add" class="new button ok-but" />
		<input type="button" value="Save" class="edit button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>