<h2><%!register-header></h2>

<%?error>

<form method="post" action="/register/">
	<label class="col_2" for="username"><%!register-label-username></label>
	<input class="col_4 tooltip-right" name="username" id="username" type="text" placeholder="<%!register-hint-username>" value="<%?value-username>" title="<%!register-hint-username>">
	<div class="clear"></div>

	<label class="col_2" for="password"><%!register-label-password></label>
	<input class="col_4 tooltip-right" name="password" id="password" type="password" placeholder="<%!register-hint-password>" title="<%!register-hint-password>">
	<div class="clear"></div>

	<label class="col_2" for="verify"><%!register-label-verify></label>
	<input class="col_4 tooltip-right" name="verify" id="verify" type="password" placeholder="<%!register-hint-verify>" title="<%!register-hint-verify>">
	<div class="clear"></div>
	
	<div class="col_2 fake-label"><%!register-label-captcha></div>
	<div class="col_4 fake-input"><%?recaptcha></div>
	<div class="clear"></div>

	<div class="col_4"></div>
	<button class="col_2" type="submit" name="submit" value="submit"><%!register-button></button>
	<div class="clear"></div>
</form>
