<dialog id="vnc">
	<div class="panel" style="text-align:right;">
		<span onclick="clonos.dialogFullscreen(this);" style="font-size:130%;cursor:pointer;"
			<span class="dialog-fullscreen"></span>
		</span>
		<span onclick="clonos.dialogClose();" style="font-size:150%;font-weight:bold;cursor:pointer;">
			<span class="dialog-close"></span>
		</span>
	</div>
	<div class="vnc-wait">
		<div class="outer">
			<div class="inner">
				<span class="icon-spin5 animate-spin"></span><span id="trlt-129">Please, wait for initialize Virtual Machine</span>
				<br /><small style="display: block;font-size: small;margin-top: 20px;"><span id="trlt-130">You can click here, or wait</span>: <span id="vnc-countdown"><span id="trlt-131">some time</span></span></small>
			</div>
		</div>
	</div>
	<iframe src="about:blank" id="vnc-iframe" border="0" width="1026" height="802"></iframe>
</dialog>