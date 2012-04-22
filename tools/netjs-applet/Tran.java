import netscape.javascript.*;
import java.applet.*;
import javax.swing.*;
import java.net.*;
import java.io.*;

//To do: Fix obj new so it actually allocates the obj . . .
public class Tran extends Thread
{
	public JSObject browser = null;
    public boolean in_use = false;
    public String name;
    static final int proc_queue_size = 10; //API call backlog size.
    public ApiCall proc_queue[] = new ApiCall[proc_queue_size];
    public int proc_queue_p = 0;
    public boolean running = false;
    //Store reply from API call here.
    //This will be returned by the proc_queue_add function.
    public ApiCall reply = null;
    //1k sockets per transaction . . .
    static final int sock_list_size = 1024;
    public Sock sock_list[] = new Sock[sock_list_size];
	public int le_done = 0;
	public netjs parent = null;

    Tran(netjs par)
    {
		parent = par;
		in_use = false;
    }

	public void test()
	{
		System.out.println("------start");
		System.out.println(proc_queue_p);
		System.out.println(le_done);
		System.out.println(running);
		System.out.println(in_use);
		System.out.println("------stop");
	}
    //Thread entry point.
    public void run()
    {

		
		//Alloc objects.
		int i;
		for(i = 0; i < sock_list_size; i++) sock_list[i] = new Sock();
		for(i = 0; i < proc_queue_size; i++) proc_queue[i] = new ApiCall();

		running = true;
		while(running)
		{
			try
			{
				Thread.sleep(30);
			}
			catch(Exception e)
			{
				int xxxx = 4;
				//running = true;
			}
			System.out.println("still running");
			//Items to process.
			if(proc_queue_p != 0)
			{
				System.out.println("searching.");
				System.out.println(proc_queue[0].cname);

				//Fuck the police.
				parent.window.setMember("netjs_lock", 1);
				if(proc_queue[0].cname.equals("setsotimeout"))
				{
					//System.out.println("Socket done.");
					reply = setsotimeout(proc_queue[0]);	
					proc_queue_remove();
					le_done = 1;
					//System.out.println(sock_list[0].type);
				}
				if(proc_queue[0].cname.equals("socket"))
				{
					//System.out.println("Socket done.");
					reply = socket(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
					//System.out.println(sock_list[0].type);
				}
				if(proc_queue[0].cname.equals("connect"))
				{
					//System.out.println("Connect done.");
					reply = connect(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
				}
				if(proc_queue[0].cname.equals("send")) //yes
				{
					reply = send(proc_queue[0]);	
					proc_queue_remove();
					le_done = 1;
					if(proc_queue[0].callback != null)
					{
						parent.window.setMember("netjs_cb_send_ret", reply.send_ret);
						String s = "setTimeout(function (){ eval(\"" +
proc_queue[0].callback + "();\"); }, 50);";
						parent.window.eval(s);
					}
				}
				if(proc_queue[0].cname.equals("recv")) //yess
				{
					//Todo: check browser still exists before trying
					//to talk to it
					//System.out.println("start recv.");
					reply = recv(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
System.out.println(proc_queue[0].callback);
//System.out.println(proc_queue_p);
					if(proc_queue[0].callback != null)
					{
						parent.window.setMember("netjs_cb_recv_buffer", reply.recv_buffer);
						parent.window.setMember("netjs_cb_recv_ret", reply.recv_ret);
						//System.out.println(reply.recv_buffer);
						//Hack to make eval non-blocking.
						String s = "setTimeout(function (){ eval(\"" +
proc_queue[0].callback + "();\"); }, 50);";
						System.out.println("Now calling " + s);
						parent.window.eval(s);
						System.out.println("Finished calling " + s);
						//System.out.println(s);
						//System.out.println(parent.window.eval(s));
					}	
				}
				if(proc_queue[0].cname.equals("accept")) //yes
				{
					reply = accept(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
				}
				if(proc_queue[0].cname.equals("listen")) //yes
				{
					reply = listen(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
				}
				if(proc_queue[0].cname.equals("bind"))
				{
					reply = bind(proc_queue[0]);	
					proc_queue_remove();
					le_done = 1;
				}
				if(proc_queue[0].cname.equals("close"))
				{
					reply = close(proc_queue[0]);
					proc_queue_remove();
					le_done = 1;
				}
				parent.window.setMember("netjs_lock", 0);
			}
		}
		System.out.println("transaction " + name + " finished.");
		return;
    }

    public void le_stop()
    {
		running = false;
		cleanup();
		//Todo: close all open sockets on this...
    }

    public void destroy()
		{
		running = false;
		cleanup();
    }

    public void cleanup()
    {
		//Placeholder.
		//Todo: Clean internal state.
		in_use = false;
		System.out.println("cleanup thread.");
    }

    public ApiCall proc_queue_add(ApiCall call)
    {
		//Add work to queue.
		ApiCall er_reply = new ApiCall();
		//System.out.println(proc_queue_p);
		if(proc_queue_p < proc_queue_size)
		{
			le_done = 0;
			//Todo: How is this copied?
			proc_queue[proc_queue_p] = call;
			proc_queue_p++;
			System.out.println("Added new call to Tran.");

			//Wait until work is done.
			while(le_done == 0 && call.use_blocking == 1)
			{
				try
				{
					Thread.sleep(30);
					System.out.println("Waiting for work.");
				}
				catch(Exception e) //java.lang.InterruptedException;
				{
					int v = 0; //umad
				}

			}
			reply.proc_queue_add_ret = 1;
			return reply;
		}
		return er_reply;
    }

    public int proc_queue_remove()
    {
		int i;
		//Remove the thing at the start of the queue so the next
		//can be processed.
		if(proc_queue_p <= proc_queue_size && proc_queue_p > 0)
		{
			for(i = 0; i < proc_queue_p - 1; i++)
			{
				proc_queue[i] = proc_queue[i + 1];
			}
			proc_queue_p--;
			return 1;
		}
		return 0;
    }

    //. . . Wrapper.
    public ApiCall setsotimeout(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		if(sock_list[call.sock_index].type.equals("Socket"))
		{
			try
			{
				sock_list[call.sock_index].tcp_client_sock.setSoTimeout(call.recv_timeout);
			}
			catch(Exception e)
			{
				ret.setsotimeout_ret = -1;
				return ret;
			}
			ret.setsotimeout_ret = 0;
		}
		return ret;
    }
    public ApiCall socket(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		int i;
		for(i = 0; i < sock_list_size; i++)
		{
			if(sock_list[i].in_use == 0)
			{
				sock_list[i].sock_family = call.sock_family;
				sock_list[i].sock_type = call.sock_type;
				sock_list[i].sock_proto = call.sock_proto;
				sock_list[i].use_ssl = call.use_ssl;
				sock_list[i].use_multicast = call.use_multicast;
				sock_list[i].use_server = call.use_server;
				sock_list[i].set_type();
				sock_list[i].in_use = 1;
				ret.socket_ret = i;
				return ret;
			}
		}
		return ret;
    }

    public ApiCall close(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		if(call.sock_index >= 0 && call.sock_index < sock_list_size)
		{
			if(sock_list[call.sock_index].in_use == 1)
			{
				sock_list[call.sock_index].cleanup();
				ret.close_ret = 0;
				return ret;
			}
		}
		return ret;
    }


    public ApiCall connect(ApiCall call)
    {
		/*
			Note: Maybe an empty socket* obj should be created
			in the socket function and then we just call connect on it
			but it seems you can do it all in one step so we I'm
			doing that for now.
		*/
		ApiCall ret = new ApiCall();
		if(sock_list[call.sock_index].type.equals("Socket"))
		{
				
			//System.out.println("in socket");
			//Create socket and connect it.
			try
			{
				sock_list[call.sock_index].tcp_client_sock = new Socket(call.sock_addr, call.sock_port);
			}
			catch(Exception e) //java.net.UnknownHostException
			{
				//System.out.println("a");
				ret.connect_ret = -1;
				return ret;
			}	
			//Setup send() stream.
			try
			{
				sock_list[call.sock_index].out = new DataOutputStream(sock_list[call.sock_index].tcp_client_sock.getOutputStream());
			}
			catch(Exception e) //java.io.IOException
			{
//System.out.println("b");
				ret.connect_ret = -1;
				return ret;
			}
			//Setup recv() stream.
			try
			{
				sock_list[call.sock_index].in = new DataInputStream(sock_list[call.sock_index].tcp_client_sock.getInputStream());
			}
			catch(Exception e) //java.io.IOException
			{
//System.out.println("c");
				ret.connect_ret = -1;
				return ret;
			}
			ret.connect_ret = 0;
			return ret;
		}
		return ret;
    }
	//Todo: Do send, recv, ... for all different socket types.
    public ApiCall send(ApiCall call)
    {
		//Should block until all data is sent?
		//If it can't send all the data it will error right?
		//^ that would mean the con has been closed I guess
		//or the socket is invalid
		ApiCall ret = new ApiCall();
		if(sock_list[call.sock_index].type.equals("Socket"))
		{
			try
			{
				sock_list[call.sock_index].out.writeBytes(call.send_buffer);
				sock_list[call.sock_index].out.flush();
			}
			catch(Exception e) //IOException
			{
				ret.send_ret = -1;
				return ret;
			}
			ret.send_ret = sock_list[call.sock_index].out.size(); //Protected, fucking pussy.
			return ret;
		}
		return ret;
    }
    public ApiCall recv(ApiCall call)
    {
		//What about blocking for this? It needs to be blocking
		//
		ApiCall ret = new ApiCall();
		if(sock_list[call.sock_index].type.equals("Socket"))
		{
			try
			{
				call.recv_buffer_temp = new byte[call.recv_buffer_size];
				ret.recv_ret = sock_list[call.sock_index].in.read(call.recv_buffer_temp, 0, call.recv_buffer_size);
				ret.recv_buffer = new String(call.recv_buffer_temp);
			}			
			catch(Exception e) //java.io.IOException
			{
				ret.recv_ret = -1;
				return ret; //-1
			}
			return ret;
		}
		return ret;
    }
    public ApiCall accept(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		return ret;
    }
    public ApiCall listen(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		return ret;
    }
    public ApiCall bind(ApiCall call)
    {
		ApiCall ret = new ApiCall();
		return ret;
    }
}
