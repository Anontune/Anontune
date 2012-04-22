<h2><%!login-header></h2>

<%?error>

<form method="post" action="/login/">
	<label class="col_2" for="username"><%!login-label-username></label>
	<input class="col_4" name="username" id="username" type="text" value="<%?value-username>">
	<div class="clear"></div>

	<label class="col_2" for="password"><%!login-label-password></label>
	<input class="col_4" name="password" id="password" type="password">
	<div class="clear"></div>

	<div class="col_4"></div>
	<button class="col_2" type="submit" name="submit" value="submit"><%!login-button></button>
	<div class="clear"></div>
</form>
