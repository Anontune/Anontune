<?php
/*
 *  This file is part of Anontune.
 *
 *  Anontune is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Anontune is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Anontune.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */

// Please don't touch this file unless you REALLY need to change it.
// It provides crossdomain POST functionality for the API.
?>
<!doctype html>
<html>
	<head>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript">
			function pad(number, length) 
			{
				var str = '' + number;
				while (str.length < length) 
				{
					str = '0' + str;
				}

				return str;
			}

			$(function(){
				$(window).bind("message", function(event){
					response = event.originalEvent.data;
					
					var request_id = response.substring(0, 4);
					var response = response.substring(4);
					
					var object = JSON.parse(response);
					
					$.post(object.url + "?format=json", object.data, function(data){
						parent.postMessage(pad(request_id, 4) + data, "*");
					});
				})
				
				parent.postMessage("READY", "*");
			})
		</script>
	</head>
</html>
<?php die(); /* Kill off all subsequent API-related stuff. */ ?>
