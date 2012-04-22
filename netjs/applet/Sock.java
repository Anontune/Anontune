import java.applet.*;
import javax.swing.*;
import java.net.*;
import java.io.*;


public class Sock
{
    /*
		The socket and all important information about it.
		The socket functions in Tran use this information.
    */
    public int in_use = 0;
    
    //More info in ApiCall.java.
    public int sock_family;
    public int sock_type;
    public int sock_proto;
    

    //All the possible different sockets. Only one will be used per obj.
    public ServerSocket tcp_serv_sock; //TCP server socket.
    //public SSLServerSocket tcp_serv_ssl_sock; //TCP server SSL socket.
    public DatagramSocket udp_sock; //UDP server/client socket.
    public Socket tcp_client_sock; //TCP client socket.
    //public SSLClient tcp_client_ssl_sock; //TCP client SSL socket.
    public MulticastSocket udp_mcast_sock;  //UDP server/client multicast socket.

    //Helps tell us which of the above is used.
    public int use_ssl;
    public int use_multicast;
    public int use_server;

    //Which socket are we using?
    public String type;

    //Socket input and output.
    public DataOutputStream out = null;
    public DataInputStream in = null;

    public void set_type()
    {
	if(use_server == 1 && use_ssl == 0) //ServerSocket . . .
	{
	    type = "ServerSocket";
	}
	//else if(use_server && use_ssl) //ServerSocket with SSL . . .
	//{
	//    type = "SSLServerSocket";
	//}
	else if(sock_type == 17) //DatagramSocket
	{
	    type = "DatagramSocket";
	}
	else if(use_multicast == 1) //MulticastSocket . . .
	{
	    type = "MulticastSocket";
	}
	//else if(use_ssl) //SSLCocket . . .
	//{
	//    type = "SSLClient";
	//}
	else //Socket . . .
	{
	    type = "Socket";
	}
    }
    
    public void cleanup()
    {
		try
		{
			if(type.equals("ServerSocket")) //ServerSocket . . .
			{
				tcp_serv_sock.close();
			}
			//else if(type == "SSLServerSocket") //ServerSocket with SSL . . .
			//{
			//	tcp_serv_ssl_sock.close();
			//}
			else if(type.equals("DatagramSocket")) //DatagramSocket
			{
				udp_sock.close();
			}
			else if(type.equals("MulticastSocket")) //MulticastSocket . . .
			{
				udp_mcast_sock.close();
			}
			//else if(type == "SSLClient") //SSLClient . . .
			//{
			//	tcp_client_ssl_sock.close();
			//}
			else //Socket . . .
			{
				tcp_client_sock.close();
			}
		}
		catch(Exception e) //IOException
		{
			int i; //Haters gonna hate.
		}
		sock_family = 0;
		sock_type = 0;
		sock_proto = 0;
		in_use = 0;
		try
		{
			out.close(); out = null;
			in.close(); in = null;
		}
		catch(Exception e)
		{
			int ii;
		}
    }

}
