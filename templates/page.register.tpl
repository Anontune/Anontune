<h2>Register for AnonTune.</h2>

<%?error>

<form method="post" action="/register/">
	<label class="col_2" for="username">Username</label>
	<input class="col_4 tooltip-right" name="username" id="username" type="text" placeholder="a-z, A-Z, 0-9, max. 30 characters" value="<%?value-username>" title="a-z, A-Z, 0-9, max. 30 characters">
	<div class="clear"></div>

	<label class="col_2" for="password">Password</label>
	<input class="col_4 tooltip-right" name="password" id="password" type="password" placeholder="min. 8 characters" title="min. 8 characters">
	<div class="clear"></div>

	<label class="col_2" for="verify">Verify</label>
	<input class="col_4 tooltip-right" name="verify" id="verify" type="password" placeholder="type the same password again" title="type the same password again">
	<div class="clear"></div>
	
	<div class="col_2 fake-label">Captcha</div>
	<div class="col_4 fake-input"><%?recaptcha></div>
	<div class="clear"></div>

	<div class="col_4"></div>
	<button class="col_2" type="submit" name="submit" value="submit">Register</button>
	<div class="clear"></div>
</form>
