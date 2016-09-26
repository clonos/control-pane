<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h2 class="sub-header">TaskLog</h2>
	<div class="table-responsive">

	<table class="table table-striped">
		<thead>
		<tr>
		<th>Job Id</th>
		<th>CMD</th>
		<th>Start Time</th>
		<th>End Time</th>
		<th>Status</th>
		<th>ErrCode</th>
		<th>LogFile</th>
		<th>LogSize</th>
		</tr>
		</thead>
		<tbody>

		<?php
			require_once('show_tasklog.php');
			show_tasklog();
		?>

	</div>
</div>
