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
				<span class="icon-spin5 animate-spin"></span><?php echo $this->translate('Please, wait for initialize Virtual Machine');?>
				<br /><small style="display: block;font-size: small;margin-top: 20px;"><?php echo $this->translate('You can click here, or wait');?>: <span id="vnc-countdown"><?php echo $this->translate('some time');?></span></small>
			</div>
		</div>
	</div>
	<iframe src="about:blank" id="vnc-iframe" border="0" width="1026" height="802"></iframe>
</dialog>