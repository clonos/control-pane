<div class="modal fade" id="nodeadd" tabindex="-1" role="dialog" aria-labelledby="nodeadd" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
				<h4 class="modal-title" id="nodeadd"><span class="glyphicon"></span>Add Node</h4>
			</div>

			<form name="nodeadd" id="nodeadd" class="navbar-form" role="create">

			<div class="row">
				<div class="col-md-6">
					<label for="address">Node IP or Hostname:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="Node IP" name="address" id="address"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="password">CBSD User Password:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="password" class="form-control" placeholder="Node Passord" name="password" id="password"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="sshport">SSH Port:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="SSH Port" name="sshport" id="sshport"/>
					</div>
				</div>
			</div>

			</form>

			<div class="modal-footer">
				<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_nodeadd(xajax.getFormValues('nodeadd')); return false;" value="Add Node" />
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
