<div class="modal fade" id="vpnetadd" tabindex="-1" role="dialog" aria-labelledby="vpnetadd" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
				<h4 class="modal-title" id="vpnetadd"><span class="glyphicon"></span>Add Network</h4>
			</div>

			<form name="vpnetadd" id="vpnetadd" class="navbar-form" role="create">

			<div class="row">
				<div class="col-md-6">
					<label for="netname">Name:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="network1" name="netname" id="netname" value="network1"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="net">Network:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="10.0.0.0/24" name="net" id="net" value="10.0.0.0/24"/>
					</div>
				</div>
			</div>

			</form>

			<div class="modal-footer">
				<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_vpnetadd(xajax.getFormValues('vpnetadd')); return false;" value="Add" />
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
