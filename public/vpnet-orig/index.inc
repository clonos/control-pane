<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h2 class="sub-header">Virtual Personal Network</h2>
	<div class="table-responsive">

		<table class="table table-striped">
		<thead>
		<tr>
		<th>Name</th>
		<th>Key</th>
		</tr>
		</thead>
		<tbody>
		<?php
			require_once('vpnet.php');
			show_vpnet();
		?>
	</div>
</div>
