<dialog id="login" class="window-box">
	<div class="login-wait hide"><div class="loadersmall"></div></div>
	<div class="login-error-nouser hide"><?php echo $this->translate('<span class="icon-attention" style="font-size:large;"></span> Error! User not found!'); ?></div>
	<div class="login-header"><span class="icon-expeditedssl"></span><?php echo $this->translate('Login');?></div>
	<form class="win" method="post" id="loginData" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Login');?>:</span>
				<input type="text" name="login" value="" autofocus />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Password');?>:</span>
				<input type="password" name="password" value="" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Go to the system');?>" class="button ok-but" />
	</div>
</dialog>
