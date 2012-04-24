<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */
 
// Please don't touch this file unless you REALLY need to change it.
// It provides crossdomain POST functionality for the API.
?>
<!doctype html>
<html>
	<head>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript">
			$(function(){
				$(window).bind("message", function(event){
					response = event.originalEvent.data;
					var object = JSON.parse(response);
					
					$.post(object.url + "?type=json", object.data, function(data){
						parent.postMessage(data, "*");
					});
				})
				
				parent.postMessage("READY", "*");
			})
		</script>
	</head>
</html>
<?php die(); /* Kill off all subsequent API-related stuff. */ ?>
