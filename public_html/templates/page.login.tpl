<h2><%!login-header></h2>

<%?error>

<form method="post" action="/login/">
	<label class="col_2" for="username"><%!login-label-username></label>
	<input class="col_4" name="username" id="username" type="text" value="<%?value-username>">
	<div class="clear"></div>

	<label class="col_2" for="password"><%!login-label-password></label>
	<!--<div id="password_div" style="position: absolute; margin-top: 6.5px; overflow: hidden;"></div> style="padding-right: 1px; color: white;"-->
	<input class="col_4" name="password" id="password" type="password">
	<div class="clear"></div>

	<div class="col_4"></div>
	<button class="col_2" type="submit" name="submit" value="submit"><%!login-button></button>
	<div class="clear"></div>
</form>
<!--
<script>
$(document).ready(function() {
    var imgSrc = "/images/password_heart.png";
       $("#password_div").width($("#password").outerWidth(false));
    $("#password_div").height($("#password").outerHeight(false));
	$("#password_div").css($("#password").offset());
	$("#password_div").css("width", parseInt($("#password_div").css("width"), 10) - 5);
	$("#password_div").css("height", parseInt($("#password_div").css("height"), 10) - 10);
	$("#password_div").css("left", parseInt($("#password_div").css("left"), 10) + 6);
    
    $("#password").keypress(function(event) {
		if(event.keyCode == 8){
			$('#password_div img:last-child').remove();
			return;
		}
		if(event.keyCode == 9 || event.keyCode == 13 || (event.keyCode >= 16 && event.keyCode <= 20) || event.keyCode == 27 || (event.keyCode >= 33 && event.keyCode <= 40) || event.keyCode == 45 || event.keyCode == 46 || event.keyCode == 91 || event.keyCode == 92 || event.keyCode == 93 || (event.keyCode >= 112 && event.keyCode <= 123) || event.keyCode == 144 || event.keyCode == 145){
			return;
		}
        $("#password_div").append("<img src=\"" + imgSrc + "\">");
    });
    
    $("#password_div").click(function() {
		$("#password_div").width($("#password").outerWidth(false));
		$("#password_div").height($("#password").outerHeight(false));
		$("#password_div").css($("#password").offset());
		$("#password_div").css("width", parseInt($("#password_div").css("width"), 10) - 5);
		$("#password_div").css("height", parseInt($("#password_div").css("height"), 10) - 10);
		$("#password_div").css("left", parseInt($("#password_div").css("left"), 10) + 6);
       $("#password").focus(); 
    });
});
</script>
-->
