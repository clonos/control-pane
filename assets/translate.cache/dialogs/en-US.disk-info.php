<dialog id="disk-info" class="window-box info-box" style="width:80%;height:80%;">
	<div class="tabs">
		<h1>
			<span><span id="trlt-70">Disk info</span>: <span id="di-disk-name"></span></span>
		</h1>
		<!-- <h2><span id="trlt-71">Virtual Machine Settings</span></h2> -->
		<div class="tabs_group">
			<span class="tab sel" id="tab-info">Information</span>
			<span class="tab" id="tab-smart">S.M.A.R.T.</span>
		</div>
	</div>
	<div class="window-content smpad">
		<div id="tab-info-cnt" class="tab-cnt">Information of disk</div>
		<div id="tab-smart-cnt" class="tab-cnt hide pre"><pre>
			no data
<?php
/*
$file="/var/db/cixnas/dsk/ada0.smartinfo";
if(file_exists($file))
{
	//$mtime=filemtime($file);
	//echo 'Time generated: '.date('d.m.Y H:i:s',$mtime)."\n\n";
	echo file_get_contents($file);
}
*/?>
		</pre></div>
	</div>
	<div class="buttons">
		<input type="button" value="Cancel" class="button red cancel-but">
	</div>
</dialog>