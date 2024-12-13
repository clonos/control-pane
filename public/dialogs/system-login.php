<dialog id="login" class="window-box">
	<div class="login-wait hide"><div class="loadersmall"></div></div>
	<div class="login-error-nouser hide"><span class="icon-attention" style="font-size:large;"></span> <translate id="380">Error! User not found!</translate></div>
	<div class="login-header"><span class="icon-expeditedssl"></span><translate id="381">Login</translate></div>
	<form class="win" method="post" id="loginData" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="381">Login</translate>:</span>
				<input type="text" name="login" value="" autofocus />
			</p>
			<p>
				<span class="field-name"><translate id="382">Password</translate>:</span>
				<input type="password" name="password" value="" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="383">Go to the system</translate>" class="button ok-but" />
	</div>
</dialog>
