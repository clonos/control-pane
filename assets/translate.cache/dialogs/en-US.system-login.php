<dialog id="login" class="window-box">
	<div class="login-wait hide"><div class="loadersmall"></div></div>
	<div class="login-error-nouser hide"><span class="icon-attention" style="font-size:large;"></span> <span id="trlt-380">Error! User not found!</span></div>
	<div class="login-header"><span class="icon-expeditedssl"></span><span id="trlt-381">Login</span></div>
	<form class="win" method="post" id="loginData" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-381">Login</span>:</span>
				<input type="text" name="login" value="" autofocus />
			</p>
			<p>
				<span class="field-name"><span id="trlt-382">Password</span>:</span>
				<input type="password" name="password" value="" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Go to the system" class="button ok-but" />
	</div>
</dialog>
