import netscape.javascript.*;
import java.applet.*;
import javax.swing.*;
import java.net.*;
import java.io.*;

public class netjs extends Applet
{

    public JSObject browser = null;
	JSObject window = null;
    public boolean running = false;
    //API call backlog size.
    static final int proc_queue_size = 10;
    //Max threads processing a socket session.
    static final int tran_list_size = 1000;
    
    //Create queues.
    //Create/destroy socket sessions.
    public ApiCall proc_queue[] = new ApiCall[proc_queue_size];
    //Array of objects that represent a socket session.
    public Tran tran_list[] = new Tran[tran_list_size];

    public int proc_queue_p = 0;
    public int tran_list_p = 0;
	public int le_done = 0;
	//public String tete = "51";

    public void init()
    {
		/*
			Get a reference to the browser.
		*/
		try
		{
			browser = JSObject.getWindow(this);
			window = JSObject.getWindow(this);
		}
		catch(Exception e)
		{
			int vsdf = 0;
		}
    }

    public void start()
    {
		/*
			This function handles a small queue of calls to create new worker threads
			for a socket API session. The calls it encounters are create_transaction
			and destroy_transaction.
		*/

		//Alloc objects.
		int i;
		for(i = 0; i < tran_list_size; i++) tran_list[i] = new Tran(this);
		for(i = 0; i < proc_queue_size; i++) proc_queue[i] = new ApiCall();
		//System.out.println("Hello World!");

		browser.call("java_socket_bridge_ready", null); 
		running = true;
		while(running)
		{
			try
			{
				Thread.sleep(30);
			}
			catch(Exception e)
			{
				//Problem was here . . . continue debugging
				running = true;
				//return;
			}

			//Items to process.
			if(proc_queue_p != 0)
			{
				//System.out.println("create_transaction1");
				//System.out.println(proc_queue[0].cname + "2");
				if(proc_queue[0].cname.equals("create_transaction"))
				{
					//Todo: error checking/logging here
					create_transaction(proc_queue[0].transaction_name);
					proc_queue_remove();
					//System.out.println("yes");
					le_done = 1;
				}

				if(proc_queue[0].cname.equals("destroy_transaction"))
				{
					//Test this then this module is done.
					//Todo: error checking/logging here
					destroy_transaction(proc_queue[0].transaction_name);
					proc_queue_remove();
					le_done = 1;
					System.out.println("destroy..");
				}
				if(proc_queue[0].cname.equals("tran_proc_queue_add"))
				{
					tran_list[proc_queue[0].tran_index].proc_queue_add(proc_queue[0].api_pack);
					proc_queue_remove();
					le_done = 1;
				}
			}
		}
		System.out.println("exit main");
    }

	public void print_state()
	{

		System.out.println("main proc_queue_p " + proc_queue_p);
	System.out.println("main tran_list_p " + tran_list_p);
	System.out.println("main le_done " + le_done);
	System.out.println("tran 0 proc_queue_p " + tran_list[0].proc_queue_p);

	System.out.println("tran 0 in_use " + tran_list[0].in_use);

	System.out.println("tran 0 running " + tran_list[0].running);

	System.out.println("tran 0 le_done " + tran_list[0].le_done);
	System.out.println("tran 0 proc queue add test" + tran_list[0].proc_queue_add(proc_queue[0]));


		
	}

    public int get_transaction_index_by_name(String name)
    {
		/*
			This function returns the index of a tran object in the tran_list
			given the name of the object (as stored in the object.)
		*/

		int i;
		for(i = 0; i < tran_list_size; i++)
		{
			if(tran_list[i].name != null)
			{
				if(tran_list[i].name.equals(name))
				{
					return i;
				}
			}
		}
		return -1;
    }

    public ApiCall get_api_call_obj()
    {
		/*
			The queue is of ApiCall objects so in order to pass such an object
			to the function responsible for adding to this queue we need the
			ApiCall obj first as a model.
		*/
		return new ApiCall();
    }

    public synchronized int create_transaction(String name){
		/*
			This function wakes up a worker thread and prepares it for processing
			a socket API session.
		*/
		int i = 0;
		for(i = 0; i < tran_list_size; i++)
		{
			if(tran_list[i].in_use == false)
			{
				tran_list[i].in_use = true;
				tran_list[i].name = name;
				/*
					It's possible this just calls the start code in
					this thread rather than starting a new thread.
					Test this.
				*/
				tran_list[i].start();
				return 0;
			}			
		}
		return -1;
    }

    public synchronized int destroy_transaction(String name)
    {
		/*
			This function reaps a worker thread so it may be used again for
			a new slate to create a new worker thread.
		*/
		int index = get_transaction_index_by_name(name);
		if(index < 0) return -1;
		tran_list[index].le_stop();
		tran_list[index] = new Tran(this); //Create a new clean object.
		return 0;
    }

    public void stop()
    {
		running = false;
		cleanup();
    }

    public void destroy()
    {
		running = false;
		cleanup();
    }

    public ApiCall tran_proc_queue_add(ApiCall call)
    {
		return tran_list[call.tran_index].proc_queue_add(call);
	}

    public int proc_queue_add(ApiCall call)
    {
		/*
			Add a new call to the process queue. The calls are for
			creating new workers or destroing them.

			Todo: Do we know if this copies the object or stores a reference
			what is it? I don't know how Java handles stuff like this.
		*/
		le_done = 0;
		
		if(proc_queue_p < proc_queue_size)
		{
			proc_queue[proc_queue_p] = call;
			//proc_queue[proc_queue_p].transaction_name = "test";
			proc_queue_p++;

			//Wait until work is done.
			
			while(le_done == 0)
			{
				try
				{
					Thread.sleep(30);
				}
				catch(Exception e) //java.lang.InterruptedException;
				{
					proc_queue_p = proc_queue_p;
				}
			}

			return 1;
		}
		return 0;
    }

    public int proc_queue_remove()
    {
		/*
			Remove a new call from the process queue. The calls are for
			creating new workers or destroing them.
		*/

		
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
		//System.out.println("h0h0");
		//proc_queue_p = 0;
		//return 1;
		return 0;
    }

    public void cleanup()
    {
		running = false;
		return;
		//Place holder.
    }

}
