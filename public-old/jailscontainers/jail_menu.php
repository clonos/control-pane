<div class="col-xs-6 col-sm-2 placeholder">
	<a href="#jclone" data-toggle="modal" data-target="#jclone"><div class="btn btn-success"><span style="font-size: 30px" class="glyphicon glyphicon-duplicate gi-5x"></span></div></a>
	<a href="#jclone" data-toggle="modal" data-target="#jclone"><h4>Clone <?php echo $jname; ?></h4></a>
	<?php
		require_once("jclone.inc");
	?>
</div>

<div class="col-xs-6 col-sm-2 placeholder">
	<a href="#jrename" data-toggle="modal" data-target="#jrename"><div class="btn btn-success"><span style="font-size: 30px" class="glyphicon glyphicon-duplicate gi-5x"></span></div></a>
	<a href="#jrename" data-toggle="modal" data-target="#jrename"><h4>Rename <?php echo $jname; ?></h4></a>
	<?php
		require_once("jrename.inc");
	?>
</div>

<div class="col-xs-6 col-sm-2 placeholder">
	<a href="#jexport" data-toggle="modal" data-target="#jexport"><div class="btn btn-success"><span style="font-size: 30px" class="glyphicon glyphicon-share gi-5x"></span></div></a>
	<a href="#jexport" data-toggle="modal" data-target="#jexport"><h4>Export <?php echo $jname; ?></h4></a>
</div>

<div class="col-xs-6 col-sm-2 placeholder">
	<a href="#imghelper" data-toggle="modal" data-target="#imghelper"><div class="btn btn-success"><span style="font-size: 30px" class="glyphicon glyphicon-question-sign gi-5x"></span></div></a>
	<a href="#imghelper" data-toggle="modal" data-target="#imghelper"><h4>Helpers</h4></a>
</div>

