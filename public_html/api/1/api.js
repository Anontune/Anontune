var api_endpoint = "http://api.anontune.local";
var api_version = 1;

var api_helper_queue = [];
var api_helper_list = [];
var api_task_queue = [];
var api_callbacks = new Array();

var api_last_request = 0;

function PostHelper()
{
	this.busy = false;
	this.element = $('<iframe src="' + api_endpoint + '/' + api_version + '/crossdomain/" width=1 height=1 border=0 frameborder=0></iframe>').appendTo('body')[0].contentWindow;
	
	api_helper_list.push(this);
	
	this.runTask = function(request, callback, reference)
	{
		this.busy = true;
		this.callback = callback;
		this.reference = reference;
		
		var object = {
			'url': "/" + api_version + request.uri,
			'data': request.data
		};
		
		api_last_request += 1;
		
		if(api_last_request > 9999)
		{
			api_last_request = 0;
		}
		
		this.request_id = api_last_request;
		
		this.element.postMessage(pad(this.request_id, 4) + JSON.stringify(object), api_endpoint);
	}
}

function ApiRequest(method, uri, data)
{
	this.uri = uri;
	this.data = data;
	this.method = method;
}

function Task(request, callback, reference)
{
	this.request = request;
	this.callback = callback;
	this.reference = reference;
}

function create_helper()
{
	new PostHelper();
}

function queue_task(request, reference, callback)
{
	if(request.method == "post")
	{
		api_task_queue.push(new Task(request, callback, reference));
		pop_queue();
	}
	else
	{
		api_callbacks[reference] = callback;
		$('<script type="text/javascript" src="' + api_endpoint + '/' + api_version + request.uri + '?format=jsonp&reference=' + reference + '"></script>').appendTo('body');
	}
}

function run_callback(reference, data)
{
	api_callbacks[reference](reference, data);
}

function pop_queue()
{
	if(api_task_queue.length > 0)
	{
		for(i in api_helper_queue)
		{
			var target = api_helper_queue[i];
			if(target.busy === false)
			{
				var new_task = api_task_queue.shift();
				target.runTask(new_task.request, new_task.callback, new_task.reference)
				
				if(api_task_queue.length == 0)
				{
					return;
				}
			}
		}
	}
}

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
		var response = event.originalEvent.data;
		
		if(response == "READY")
		{
			helper = event.originalEvent.source;
			
			for(i in api_helper_list)
			{
				if(api_helper_list[i].element == helper)
				{
					api_helper_queue.push(api_helper_list[i]);
					pop_queue();
				}
			}
		}
		else
		{
			var request_id = response.substring(0, 4);
			var response = response.substring(4);
			
			for(i in api_helper_queue)
			{
				if(api_helper_queue[i].request_id == request_id)
				{
					target = api_helper_queue[i];
					target.busy = false;
					var jsonobj = JSON.parse(response);
					alert(target.reference);
					target.callback(target.reference, jsonobj);
					pop_queue();
				}
			}
		}
	});
});
