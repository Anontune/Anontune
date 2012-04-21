/*
JS wrapper for netjs.
-- Talks to the netjs Java applet.

Todo: foreign characters aren't being encoded properly
Otherthanthat FUCK YEAH
* if you use the socket functions with callbacks in an asynchronous manner or in a way where you start a second function before the previous has called back then it will fuck up, this is because they will both use the same variables, this is easy to fix, just change the setmember code. this issue prob won't come up in the player for a while
- im not sure this is actually true, the synchronicity of javascript might ensure there are never any problems 
-- confirmed, doesnt matter

* To increase performance of download function use an algorithm that increases the size of the buffers used for recv relative to how fast the function returns. There is a slight overhead for each call so this works well.
* Test whether it's possible to have to calls run concurrently.
* multiple con-current reads will fuq up because they use the same variables to indicate finish, remodel it to use separate ones and they can be used correctly
- confirmed, they do

Todo: Get applet to die on page refresh.

All of this code has the potential to error at any point
once kill_netjs_http_open has been called. This could
potentially lead to invalid behavour. I think it occurs
when netjs.applet has been set to null and it's refered
to in this code. If that's the case there's really nothing
we can do. I mean, when an error is encounted then all the
rest of the code in the cur function doesn't exec so this
behavour is really what we want, right?

Error: When the user interrupts an http open call and another one
is issued the second seems to fail even when the first has been
killed. All alerts and shit have been removed to cover this
up and it's not really such an issue at this point

Error: Interface locks up randomly in Chrome and Firefox.
We don't yet know why. Fixing this may well make the ME stable.
High priority.
Theories:
* Worker using up all the CPU because timeout
was 50.
* Behavour resembles fork bomb so something like that?
* Problem with live connect shit.
* Problem with swapping flash files. If we think about
how we're currently loading music it's pretty retarded. We
should have all the players we're going to use -- YouTube,
JW Player, and an iframe for soundcloud always loaded
and use them to play songs with Javascript. This approach
might have a large memory foot print, howerver, and if flash
crashes there will be no way to fix it . . but you refresh
the page when that happens anyway so it seems logical.

Currently we're just loading a new flash object for every
song. Doing this too fast might have issues and the loading
might be locking up the interface.

-----
Youtube not found error sucks. We're getting "valid" results
from the Youtube API and then when we go to use them they're
for music that doesn't exist. We need a check after x to see
that the song actually plays and if it doesn't we need to
show an error.
-----
Interface hang -- change timeout to 2 seconds.
*/

function html_entity_decode(str)
{
    try
	{
		var  tarea=document.createElement('textarea');
		tarea.innerHTML = str; return tarea.value;
		tarea.parentNode.removeChild(tarea);
	}
	catch(e)
	{
		//for IE add <div id="htmlconverter" style="display:none;"></div> to the page
		document.getElementById("htmlconverter").innerHTML = '<textarea id="innerConverter">' + str + '</textarea>';
		var content = document.getElementById("innerConverter").value;
		document.getElementById("htmlconverter").innerHTML = "";
		return content;
	}
}

// parseUri 1.2.2
// (c) Steven Levithan <stevenlevithan.com>
// MIT License
// This parseUri stuff isn't our work. Thanks Steven Levithan.
// You're a real bro.
function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});

	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};


netjs_cb_recv_ret = null;
netjs_cb_recv_buffer = null;
netjs_cb_send_ret = null;
netjs_ready = 0;
netjs_lock = 0;
function java_socket_bridge_ready()
{
	netjs_ready = 1;
}

netjs = new function() {
this.af_inet = 2;
this.sock_stream = 1;
this.tcp_proto = 6;
this.transaction_name = "netjs_default\0";
this.applet = "";
this.x = "";
this.tran_name = "";

this.obtain_lock = function() {
	return;
	while(netjs_lock) {
		1;
	}
	return;
}

this.create_transaction = function(name) {
/*
Google Chrome and Chromium have memory leaks
when storing Javascript strings into Java objects
via Live Connect. They are not properly null
terminated and this leads to an overflow in
Java. Hence, all strings for chrome must be
null terminated. This is a hack.
*/
	//Blocking.
    if(netjs.applet == null) return 0;
    
	api_call = netjs.applet.get_api_call_obj();
	//alert(api_call);
	api_call.cname = "create_transaction\0";
	api_call.transaction_name = name + "\0";
	netjs.applet.proc_queue_add(api_call);
	//alert(name);
	//alert(netjs.applet.get_transaction_index_by_name(name));
	//alert(netjs.applet.get_transaction_index_by_name);
	//alert(netjs.applet.get_api_call_obj);	
	return netjs.applet.get_transaction_index_by_name(name);
	//Returns -1 on error, otherwise returing transaction index
}

this.destroy_transaction = function(name) {
	//Blocking.
    if(netjs.applet == null) return 0;
    
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "destroy_transaction\0";
	api_call.transaction_name = name + "\0";
	netjs.applet.proc_queue_add(api_call);
	return netjs.applet.get_transaction_index_by_name(name) == -1 ? 0 : -1;
	//Returns -1 on error, otherwise destroying transaction
}

this.socket = function(tran, family, type, proto) {
	//Blocking.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "socket\0";
	api_call.sock_family = family;
	api_call.sock_type = type;
	api_call.sock_proto = proto;
	api_call.tran_index = tran;
	return (netjs.applet.tran_proc_queue_add(api_call)).socket_ret;
	//returns -1 on error, otherwise returning socket descriptor.
}

this.connect = function(tran, sock, addr, port) {
	//Blocking. Timeout of 5 secs.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "connect\0";
	api_call.sock_index = sock;
	api_call.sock_addr = addr + "\0";
	api_call.sock_port = port;
	api_call.tran_index = tran;
	return (netjs.applet.tran_proc_queue_add(api_call)).connect_ret;
	//returns 0 on success, -1 on failure.
}

this.send = function(tran, sock, buf, len, callback) {
	//Blocking or non-blocking.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "send\0";
	api_call.sock_index = sock;
	api_call.send_buffer = buf + "\0";
	api_call.callback = callback != null ? callback + "\0" : callback;
	api_call.tran_index = tran;
	if(callback != null) {
		api_call.use_blocking = 0;
	}
	//api_call.send_buffer_size = len;
	return (netjs.applet.tran_proc_queue_add(api_call)).send_ret;
	//returns -1 on error, or number of bytes written on success (may be 0)
}

this.recv = function(tran, sock, len, callback) {
	//Blocking or non-blocking.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "recv\0";
	api_call.sock_index = sock;
	api_call.recv_buffer_size = len;
	api_call.callback = callback != null ? callback + "\0" : callback;
	api_call.tran_index = tran;
	if(callback != null) {
/*
What if it were to block forever? The tran_proc_queue_add function
would hault the whole application. We cannot allow this for http.open.
That is not production ready, assholes. Fix it.
...
*/
		api_call.use_blocking = 0;
	}
/*
 var getType = {};
	while(netjs.applet.tran_list[tran].proc_queue_add && getType.toString.call(netjs.applet.tran_list[tran].proc_queue_add) != '[object Function]')
	{
		alert(netjs.applet);
		alert(typeof netjs.applet.tran_list[tran].proc_queue_add);
		alert(tran);
		alert(api_call);
		netjs.applet.print_state();
		
		//x = netjs.http.open("http://docstore.mik.ua/orelly/web/jscript/ch19_06.html", null, null, null)[1];
		//alert(netjs.applet.tran_list[tran].);
		//netjs.send(tran, sock, "0", 1, null);
		//ret = [-1, null];
		//return ret;
	}
	netjs.x = typeof netjs.applet.tran_list[tran].proc_queue_add;
*/
/*
	ve = netjs.applet.get_transaction_index_by_name(netjs.tran_name);
	if(ve == -1)
	{
		alert("yes");
	}
	if(ve != tran){
		alert(tran);
		alert(ve);
	}
	netjs.applet.tran_list[tran].test();
*/
	var ret = netjs.applet.tran_proc_queue_add(api_call)
    if(ret.recv_ret >= 0){
        ret.recv_buffer[ret.recv_ret] = "\0";
    }
	ret = [ret.recv_ret, ret.recv_buffer];
	return ret;
	//returns (-1 on error, or number of bytes read on success (may be 0)) and the data (if any)
}

this.close = function(tran, sock) {
	//Blocking.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "close\0";
	api_call.sock_index = sock;
	api_call.tran_index = tran;
	return (netjs.applet.tran_proc_queue_add(api_call)).close_ret;
	//returns 0 on success, -1 on failure
}

this.setsotimeout = function(tran, sock, timeout) {
	//Blocking.
    if(netjs.applet == null) return -1;
    
	netjs.obtain_lock();
	var api_call = netjs.applet.get_api_call_obj();
	api_call.cname = "setsotimeout\0";
	api_call.sock_index = sock;
	api_call.recv_timeout = timeout;
	api_call.tran_index = tran;
	return (netjs.applet.tran_proc_queue_add(api_call)).setsotimeout_ret;
	//returns 0 on success, -1 on failure
}

this.http = new function() {

this.recv_buffer = "";
this.read_done = 0;
this.tran = -1;
this.sock = -1;
this.user_agent = null;
this.lock = 0;

/*
this.read = function(t) {
	alert("read 2");
}
*/

this.read = function() {
    if(netjs.applet == null) return -1;

	//alert("x");
	//alert(netjs.http.read.recv_ret);
	//alert(args);
//netjs.recv(netjs.http.tran, netjs.http.sock, 4080, "netjs.http.read");
	//alert(netjs_cb_recv_ret);
	//alert(netjs_cb_recv_buffer);
	if(netjs_cb_recv_ret != null){
		//alert("h0");
		if(netjs_cb_recv_ret > 0 && netjs_cb_recv_buffer != null) {
			//alert("no");
            netjs_cb_recv_buffer[netjs_cb_recv_ret] = "\0";
			netjs.http.recv_buffer += netjs_cb_recv_buffer;
		}
		//alert(netjs_cb_recv_ret);
		if(netjs_cb_recv_ret == -1) {
			//alert("vv");
			netjs.http.read_done = 1;
			//document.write("1");
			return;
		}
	}
	netjs_cb_recv_ret = null;
	netjs_cb_recv_buffer = null;
	netjs.recv(netjs.http.tran, netjs.http.sock, 100 * 1024, "netjs.http.read");
/*
This really needs a 100ms sleep between calls. Do it in Java.
We can't have this hogging all of the Java thread. Fuck that.
Might even need to make it a second. Who cares if it slows
down read. It's better than making our application lock up.
100 * this ^^

Is it possible this could infinite loop? Yes, if server keeps
sending data forever. Perhaps we should set a theoretical
maximum. Agreed. We only have to worry about this for
malicious servers. Not a priority but we could choose like
10MBs as a limit and overwrite it if they need to D/L more.
*/
}

this.post_encode = function(data) {
	var buf = "";
	if(data[0] instanceof Array) {
		for(var i = 0; i < data.length; i++) {
			buf += encodeURIComponent(data[i][0]) + "=";
			buf += encodeURIComponent(data[i][1]);
			if(i != data.length - 1) {
				buf += "&";
			}
		}
	}
	else {
		buf += encodeURIComponent(data[0]) + "=";
		buf += encodeURIComponent(data[1]);
	}
	return buf;
}

this.open = function(url, data, timeout, callback) {
/*
This function "opens" a URL and downloads the URL's specified resource.
When it is complete callback is called (if defined.) Otherwise the function
blocks until download is complete.

Usage:
data = netjs.http.post_encode([["q", "my query"], ["x", "bleh"]]);
netjs.http.open("http:/www.google.com/search", data, 10000, "fuck_the_police");

This will send a HTTP POST request to google.com/search. The search
is encoded as the POST action. Set data to null if you're making a GET
request. A timeout of 10,000 ms has been set. If you set this to null
the default of 5 seconds will be used. It may be possible to
remove with 0. Finally, a callback has been specified. This means
that the function won't block the application and instead will
call the function defined by callback when all the data has been
received. This value may be null -- no callback, block until all
the data is received, or "symbolic" -- non-blocking but don't make
a call when it's done.
*/

    if(netjs.applet == null) return -1;
	if(netjs.http.lock) return -1;
	
	//Acquire lock.
	netjs.http.lock = 1;
    
	//var url = 'http://www.s.com:8080/sdfds';
	//var data = null;
	//var timeout = null;
	var debug = 0;
	//alert(s.match(/.*/));
	//alert(url);

	//Clear old state.
	netjs_cb_recv_ret = null;
	netjs_cb_recv_buffer = null;
	netjs.http.recv_buffer = null;

	//Build request.
	///([^:\/]+:\/\/)?([^:\/]+){1}(:[0-9]+)?\/([^:]*)/g
	url = parseUri(url);
	//Todo: error checking here.
	/*
	for(var i = 0; i < url.length; i++) {
		if(url[i] == "" || url[i] == undefined) {
			url.splice(i, 1);
			//alert("test");
		}
	}
	*/
	var port = url["port"] == "" ? 80 : url["port"];
	var proto = url["protocol"];
	var host = url["host"];
	var res = url["relative"];
	var req = data == null ? "GET" : "POST";
	req += " " + res + " HTTP/1.1\r\n";
	req += "Host: " + host + "\r\n";
	req += "User-Agent: " + (netjs.http.user_agent == null ? navigator.userAgent : netjs.http.user_agent ) + "\r\n";
	req += "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
	req += "Accept-Language: en-us,en;q=0.5\r\n";
	//req += "Accept-Encoding: gzip,deflate\r\n";
	req += "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
	req += "Connection: close\r\n";
	//req += "Referer: \r\n"; //Buffer this.
	//req += "Cookie: \r\n"; //Buffer this.
	if(data != null) {
		req += "Content-Type: application/x-www-form-urlencoded\r\n";
		req += "Content-Length: " + data.length + "\r\n";
	}
	req += "\r\n";
	if(data != null) {
		req += data;
	}
	//alert(port);
	//alert(req);
	//return;

	//Send request.
	//Create thread to handle socket functions.
	var tran_name = "netjs_http_open" + Math.random() + "\0";
	//alert(tran_name);
	netjs.tran_name = tran_name;
	//alert(tran_name);
	//return;
	var tran = netjs.create_transaction(tran_name);
	if(tran == -1) {
		if(debug) alert("Failed to create transaction.");
		netjs.http.lock = 0;
		return [-1, null];
	}
	//alert(tran);
	//netjs.applet.tran_list[tran].test();
	//return;

	//Create socket.
/*
Todo: A way for netjs users to set connect timeout. Maybe
make that a part of setsotimeout. For now it defaults to 5
seconds in Java.
*/
	var sock = netjs.socket(tran, netjs.af_inet, netjs.sock_stream, netjs.tcp_proto);
	if(sock == -1) {
		if(debug) alert("Failed to create socket.");
		netjs.destroy_transaction(tran_name);
		netjs.http.lock = 0;
		return [-1, null];
	}

	//(Optionally) set timeout on recv
	//if(timeout != null){
	//Not optional, faggots.
	timeout = timeout == null ? 2000 : timeout;
	netjs.setsotimeout(tran, sock, timeout);
	//}

	//Connect the socket.
	var con = netjs.connect(tran, sock, host + "\0", port);
	if(con == -1) {
		if(debug) alert("Failed to connect.");
		netjs.destroy_transaction(tran_name);
		netjs.http.lock = 0;
		return [-1, null];
	}

	//Send a HTTP request.
	var send = netjs.send(tran, sock, req + "\0", req.length, null);
	if(send == -1) {
		if(debug) alert("Failed to send data.");
		netjs.destroy_transaction(tran_name);
		netjs.http.lock = 0;
		return [-1, null];
	}
	//alert("yehhh--");
	//alert(callback);

	//Recv the response.
	if(callback == null) {
		var recv_buf = ""; var temp;
		//alert((temp = netjs.recv(tran, sock, 1024, null))[1]);
		//return;
/*
As mentioned before, we really need a way to put some time
between these calls even though we don't use a blocking version
of this function in our own code. At least 100ms timeout between recvs
*/
		while((temp = netjs.recv(tran, sock, 10000, null))[0] > 0) {
			//hold(50);
			recv_buf = recv_buf + temp[1];
		}
		if(recv_buf == "") {
			if(debug) alert("Failed to recv data.");
			netjs.destroy_transaction(tran_name);
			netjs.http.lock = 0;
			return [-1, null];
		}

		//Cleanup.
		//netjs.close(tran, sock);
		netjs.destroy_transaction(tran_name);
		netjs.http.lock = 0;
		return [recv_buf.length, recv_buf];
	}
	else
	{
		netjs.http.callback = callback;
		netjs.http.tran = tran;
		netjs.http.sock = sock;		
		netjs.http.read();
		if(netjs.http.worker_interval != null){
			clearInterval(netjs.http.worker_interval);
		}
		netjs.http.worker_interval = setInterval(netjs.http.worker_callback, 1000);
	}
	return 1;
}

this.release_lock = function() {
	netjs.http.lock = 0;
}

this.worker_interval = null;

this.worker_callback = function() {
	//alert("yes");
	if(netjs.http.read_done) {
		//alert(netjs.http.recv_buffer);
		netjs.http.read_done = 0;
		//Todo: add the non-blocking hack here
		//s = "setTimeout(function (){ eval(\"" + netjs.http.callback + "();\"); }, 50);";
		//eval(s);
		netjs.destroy_transaction(netjs.tran_name);
		netjs.http.lock = 0;
		clearInterval(netjs.http.worker_interval);
		//If this causes problems set it to null.
		netjs_cb_recv_ret = -1; //Signal end.
		netjs_cb_recv_buffer = null;
		if(netjs.http.callback != null && netjs.http.callback != "symbolic"){
			eval(netjs.http.callback + "();");
		}
		netjs.http.recv_buffer = null;
		netjs.http.worker_interval = null;
		return;
	}
}

}

};

/*
//Init
var netjs_html = '<applet id="netjs_applet" code="netjs.class" name="netjs" archive="';
netjs_html += 'netjs.jar" width="1" height="1"><param name="bgcolor" val';
netjs_html += 'ue="ffffff">Your browser is not Java enabled.</applet>';
document.write(netjs_html);
netjs.applet = document.getElementById("netjs_applet");
*/
//netjs.create_transaction(netjs.transaction_name);
