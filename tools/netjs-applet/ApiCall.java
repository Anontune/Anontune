import java.applet.*;
import javax.swing.*;
import java.net.*;
import java.io.*;

/*
    This structure is used to:
    * Make an API call from Javascript
    * Return the results from said call to Javascript

    As you can imagine, having one object that stores all the
    parameters used by the Javascript socket bridge and all
    the information returned to the bridge is ugly as fuck.
    Nevertheless, fuck your mother.
*/
public class ApiCall
{
	public ApiCall api_pack;

    //API call name.
    public String cname; public String name;

    //All API functions are associated with a transaction
    //the transaction is a thread to process any API calls
    //sent to it, and here we identify it.
    public String transaction_name;
	//Used to find a particular transaction in tran_list.
	public int tran_index;
    //Used to find a particular sock object in the sock_list.
    public int sock_index;
    //UNSPEC = 0, INET = 2, IPX = 6, APPLETALK = 16, NETBIOS = 17
    //INET6 = 23, IRDA = 26, BTH = 32
    public int sock_family = 2;
    //STREAM = 1, DGRAM = 2, RAW = 3, RDM = 4, SEQPACKET = 5
    public int sock_type = 1;
    //ICMP = 1, IGMP = 2, RFCOMM = 3, TCP = 6, UDP = 17, ICMPV6 = 58
    //RM = 113
    public int sock_proto = 6;
    //Address to connect/bind to.
    public String sock_addr;
    //Port to connect/bind to.
    public short sock_port;
    //Info to send down a sock.
    public String send_buffer;
    //Amount of info to send down a sock.
    public int send_buffer_size;
    //Dynamic buffer to recv info from a sock.
    public String recv_buffer = null;
    //Size of info we are expecting to recv from sock.
    public int recv_buffer_size;
    //whether sock is client or server 1 = client, 2 = server
    public int use_server = 0;
    //Shall we use SSL?
    public int use_ssl = 0;
    //Is it multicast?
    public int use_multicast = 0;
    //Used by servers. How many can they hold in their little queues
    //before they tell the client they can't hold any more?
    public long listen_backlog = 100;
    //? I Probably don't use this.
    public int in_use;
	// Todo: Comment these lines. What are they for?
	public int proc_queue_add_ret = 0;
	public int socket_ret = -1;
	public int close_ret = -1;
	public int connect_ret = -1;
	public int send_ret = -1;
	public int recv_ret = -1;
	public byte[] recv_buffer_temp;
	public int recv_timeout = -1;
	public int use_blocking = 1;
	public String callback = null;
	public int setsotimeout_ret = -1;
}

