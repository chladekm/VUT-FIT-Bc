/********************* 1. PROJEKT DO ISA 2019/2020 *********************/
/*																	   */
/*		Autor: 	Martin Chládek										   */
/*		Login:	xchlad16											   */
/*		Kontakt: xchlad16@stud.fit.vutbr.cz							   */
/*																	   */
/***********************************************************************/

/****************************** KNIHOVNY *******************************/

// ******** Standardni knihovny ********
#include <stdio.h> 
#include <stdlib.h> 
#include <string.h> 
#include <stdbool.h>
#include <errno.h>
#include <err.h>
#include <getopt.h>
#include <limits.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/prctl.h>
#include <signal.h>
#include <syslog.h> 

// ******** Sitove knihovny ********
#include <pcap.h>
#include <arpa/inet.h>
#include <netinet/ether.h>
#include <sys/socket.h> 
#include <netinet/in.h>
#include <netinet/ip6.h>
#include <netinet/udp.h>
#include <netinet/if_ether.h>
#include <ifaddrs.h>
#include <netpacket/packet.h>
#include <netdb.h>
#include <sys/ioctl.h>
#include <net/if.h>


/***************************** KONSTANTY *******************************/

#define MAX_ARGUMENTS 7 // argc - max 6 argumentu + 1 nazev souboru

#define PCKT_CNT 1 // pocet zpracovavanych paketu
#define UDP_TIMEOUT 1000 // timeout pro cekani na packet

#define DHCP_CLIENT_PORT 546
#define DHCP_SERVER_PORT 547

#define PCKT_LEN 512 // delka paketu

#define SOLICIT_MSG 1
#define ADVERTISE_MSG 2
#define REQUEST_MSG 3
#define REPLY_MSG 7
#define RENEW_MSG 5
#define REBIND_MSG 6
#define RELAY_FORWARD_MSG 12
#define RELAY_REPLY_MSG 13

#define HOP_COUNT_LIMIT 8
#define HOP_COUNT_NEW 0

#define OPTION_RELAY_MESSAGE 9
#define OPTION_CLIENT_LINK_ADDRESS 79
#define OPTION_INTERFACE_ID 18

#define CLIENT_IP_OFFSET 24 //pocet bytu od zacatku bufferu po IPv6 adresu
#define LINKTYPE_ETHERNET 1

#define RELAY_MSG_SIZE_ALIGMENT 2 // size od zarovnava 34 bitu na 36, v packetu maji ale 34 -> potreba odecist
#define OPT_RELAY_MESSAGE_CODE_LENGTH_SIZE 4 // sizeof parametru code a length ve strukture Opt_relay_message

#define LOCAL_IP_CMP "fe80"
#define GLOBAL_IP_CMP "200"
#define LOCAL_IP_CHAR 'L'
#define GLOBAL_IP_CHAR 'G'

#define MULTICAST_GROUP_ADDR "ff02::1:2"

#define EMPTY_CHAR '\0'

#define IF_ARR_COUNT 20
#define IF_ARR_LENGTH 20

/************************** DEFINICE STRUKTUR ****************************/

typedef struct Args
{
	bool syslog_flag;
	bool debug_flag;
	bool interface_flag;
	bool server_flag;	

	char *server;
	char *interface;

	char type_of_message;
}TArgs;

typedef struct Addresses
{
	struct in6_addr client_addr;
	u_char src_mac[6];
	struct in6_addr dst_addr;
	u_char dst_mac[6];
	
	char *interface_name;
	struct in6_addr interface_addr;

}T_Addresses;

typedef struct icmpv6
{
	char type;
	char code;
	u_short checksum;
	u_short max_response_delay;
	u_short reserved;
	struct in6_addr multicast_address;
}T_icmpv6;


typedef struct relay_msg
{
	char hop_count;
	char msg_type;
	struct in6_addr link_address;
	struct in6_addr peer_address;
}T_relay_msg;

typedef struct Opt_relay_message
{
	u_short option_code;
	u_short option_len;
	u_char *option_data;
}T_Opt_relay_msg;

typedef struct Opt_client_addr
{
	u_short option_code;
	u_short option_len;
	u_short link_layer_type;
	u_char	link_layer_addr[6];
}T_Opt_client_addr;

typedef struct Opt_interface_id
{
	u_short option_code;
	u_short option_len;
	u_int interface_id;
}T_Opt_interface_id;

/************************ GLOBALNI POMOCNE FUNKCE ************************/

// Funkce inicializuje strukturu
void init_args_struct(TArgs *Arguments)
{
	Arguments->syslog_flag = false;
	Arguments->debug_flag = false;
	Arguments->interface_flag = false;
	Arguments->server_flag = false;

	Arguments->server = "";
	Arguments->interface = "";

	Arguments->type_of_message = 0;
}

// Funkce vypíše error
void throw_error(char * reason)
{
	fprintf(stderr, "[ERROR] %s.\n", reason);
	exit(-1);
}

/************************ ZPRACOVAVANI ARGUMENTU ************************/

// Funkce nacita parametry zadane pri volani skriptu
void parse_arguments(int argc, char *argv[], TArgs *Arguments)
{
	if(argc > MAX_ARGUMENTS)
		throw_error("Too many arguments");

	// Tento cyklus je kvuli nezadoucim argumentum a variantam getopt jako "-i eth0" bez mezery
	for (int i = 1; i < argc; ++i)
	{
		if((strcmp(argv[i], "-s") == 0) || (strcmp(argv[i], "-i") == 0))
			{i++;}
		else if(!((strcmp(argv[i], "-l") == 0) || (strcmp(argv[i], "-d")) == 0))
			{throw_error("Invalid argument");} 
	}

	static struct option long_options[] = 
	{
		{"l", no_argument, NULL, 'l'},
		{"d", no_argument, NULL, 'd'},
		{"s", required_argument, NULL, 's'},
		{"i", required_argument, NULL, 'i'},
		{0,0,0,0} // ukoncovaci prvek	
	};

	int option_index = 0;
	char option;

	// Nacitani argumentu
	while((option = getopt_long_only(argc, argv, "lds:i:", long_options, &option_index)) != -1)
	{	
		switch(option)
		{
	        case 'l':
	        	Arguments->syslog_flag = true;
	        	break;
	        case 'd':
	        	Arguments->debug_flag = true;
	        	break;
			case 's':
				Arguments->server_flag = true;
	        	Arguments->server = optarg;
	          	break;
	        case 'i':
	        	Arguments->interface_flag = true;
	        	Arguments->interface = optarg;
	        	break;
			default:
				throw_error("Invalid argument");
				break;
		}
	}

	if(Arguments->server_flag == false)
		throw_error("Necessary -s argument was not set");
}

/************************** POMOCNE FUNKCE NA KOPIROVANI **************************/

void copy_relay_header_to_buffer(int *offset, u_char *buffer, T_relay_msg* relay_message)
{
	// Nakopirovani DHCP Relay hlavicky do bufferu
	memcpy(buffer, &relay_message->msg_type, sizeof(relay_message->msg_type));
	*offset += sizeof(relay_message->msg_type);

	memcpy(buffer + *offset, &relay_message->hop_count, sizeof(relay_message->hop_count));
	*offset += sizeof(relay_message->hop_count);
	
	memcpy(buffer + *offset, &relay_message->link_address, sizeof(relay_message->link_address));
	*offset += sizeof(relay_message->link_address);
	
	memcpy(buffer + *offset, &relay_message->peer_address, sizeof(relay_message->peer_address));
	*offset += sizeof(relay_message->peer_address);
}

void copy_option_relay_msg_to_buffer(int *offset, u_char *buffer, T_Opt_relay_msg* Relay_msg_opt)
{
	// Nakopirovani puvodniho obsahu DHCP zpravy do bufferu
	memcpy(buffer + *offset, &(Relay_msg_opt->option_code), sizeof(Relay_msg_opt->option_code));
	*offset += sizeof(Relay_msg_opt->option_code);

	memcpy(buffer + *offset, &(Relay_msg_opt->option_len), sizeof(Relay_msg_opt->option_len));
	*offset += sizeof(Relay_msg_opt->option_len);

	memcpy(buffer + *offset, Relay_msg_opt->option_data, ntohs(Relay_msg_opt->option_len));
	*offset += ntohs(Relay_msg_opt->option_len);
}

void copy_option_client_link_address_to_buffer(int *offset, u_char *buffer, T_Opt_client_addr* Relay_client_addr_opt)
{
	// Nakopirovani optionu Client link layer address
	memcpy(buffer + *offset, &(Relay_client_addr_opt->option_code), sizeof(Relay_client_addr_opt->option_code));
	*offset += sizeof(Relay_client_addr_opt->option_code);

	memcpy(buffer + *offset, &(Relay_client_addr_opt->option_len), sizeof(Relay_client_addr_opt->option_len));
	*offset += sizeof(Relay_client_addr_opt->option_len);

	memcpy(buffer + *offset, &(Relay_client_addr_opt->link_layer_type), sizeof(Relay_client_addr_opt->link_layer_type));	
	*offset += sizeof(Relay_client_addr_opt->link_layer_type);

	memcpy(buffer + *offset, &(Relay_client_addr_opt->link_layer_addr), sizeof(Relay_client_addr_opt->link_layer_addr));
	*offset += sizeof(Relay_client_addr_opt->link_layer_addr);
}

void copy_option_interface_id_to_buffer(int *offset, u_char *buffer, T_Opt_interface_id* Relay_interface_opt)
{
	// Nakopirovani optionu Client link layer address
	memcpy(buffer + *offset, &(Relay_interface_opt->option_code), sizeof(Relay_interface_opt->option_code));
	*offset += sizeof(Relay_interface_opt->option_code);

	memcpy(buffer + *offset, &(Relay_interface_opt->option_len), sizeof(Relay_interface_opt->option_len));
	*offset += sizeof(Relay_interface_opt->option_len);

	memcpy(buffer + *offset, &(Relay_interface_opt->interface_id), sizeof(Relay_interface_opt->interface_id));
	*offset += sizeof(Relay_interface_opt->interface_id);
}

/************************** SITOVY PROVOZ **************************/

// Funkce nastavuje a kompiluje filtr
void set_and_compile_filter(pcap_t *interface_device)
{
	char filter[80] = "";

	strcat(filter, "src port ");

	// Pomocne "stringy"
	char str_src_port[8];
	char str_dst_port[8];

	sprintf(str_src_port, "%d", DHCP_CLIENT_PORT);
	sprintf(str_dst_port, "%d", DHCP_SERVER_PORT);
	
	strcat(filter, str_src_port);
	strcat(filter, " and dst port ");
	strcat(filter, str_dst_port);

	// Zkompilovany filtr
	struct bpf_program compiled_filter; 

	// Kompilace filtru
	if(pcap_compile(interface_device, &compiled_filter, filter, 0, PCAP_NETMASK_UNKNOWN) == -1)
		throw_error("Could not parse filter.");

	// Nastaveni filtru
	if(pcap_setfilter(interface_device, &compiled_filter) == -1)
		throw_error("Cloud not install filter.");
}

// Funkce nahraje do struktury addresses lokalni/globalni adresu rozhrani
int get_IP_from_interface(T_Addresses* Addresses, char address_type)
{
 	char *string_adress = malloc(INET6_ADDRSTRLEN);
	struct ifaddrs *interface;
  
    if (getifaddrs(&interface) == -1)
        throw_error("Cannot get interfaces of device");

    while(interface)
    {
        if ((interface->ifa_addr) && (interface->ifa_addr->sa_family == AF_INET6))
        {
        	if(!strcmp(Addresses->interface_name, interface->ifa_name))
        	{
				inet_ntop(AF_INET6, &(((struct sockaddr_in6*) interface->ifa_addr)->sin6_addr), string_adress, INET6_ADDRSTRLEN);

				// Nasel jsem validni lokalni adresu pro zadany interface
				if(address_type == LOCAL_IP_CHAR)
				{
					if(strncmp(string_adress, LOCAL_IP_CMP, 4) == 0)
					{
						Addresses->interface_addr = ((struct sockaddr_in6*) interface->ifa_addr)->sin6_addr;
						free(string_adress);
		        		return 0;
					}
				}
				else if(address_type == GLOBAL_IP_CHAR)
				{
					if(strncmp(string_adress, GLOBAL_IP_CMP, 3) == 0)
					{
						Addresses->interface_addr = ((struct sockaddr_in6*) interface->ifa_addr)->sin6_addr;
						free(string_adress);
		        		return 0;
					}
				}
        	}
       }
       interface = interface->ifa_next;
    }

    return 1;
}


// Sitovy provoz z relay na klienta
void Relay_to_Client(TArgs *Arguments, T_Addresses* Addresses, u_char *buffer, int length_of_packet)
{
	// Zdrojova & Cilova adresa
	int fd;
	if((fd = socket(AF_INET6, SOCK_DGRAM, 0)) == -1)
		throw_error("Cannot create socket");

	struct sockaddr_in6 src_addr, dst_addr;

	// Nastaveni zdrojove adresy
	memset(&src_addr, 0, sizeof(src_addr));
	src_addr.sin6_family = AF_INET6;
	src_addr.sin6_port = htons(DHCP_SERVER_PORT);
	src_addr.sin6_scope_id = if_nametoindex(Addresses->interface_name);
	src_addr.sin6_addr = Addresses->interface_addr;

	// Nastaveni cilove adresy
	memset(&dst_addr, 0, sizeof(dst_addr));
	dst_addr.sin6_family = AF_INET6;
	dst_addr.sin6_port = htons(DHCP_CLIENT_PORT);
	dst_addr.sin6_addr = Addresses->client_addr;

	// Binding zdrojove adresy
	if (bind(fd, (struct sockaddr *) &src_addr, sizeof(src_addr)) < 0)
	    throw_error(strerror(errno));

	u_char * buffer_pointer;

	// Urceni polohy v bufferu, odkud se budou data posilat
	buffer_pointer = (u_char *) (buffer + sizeof(struct relay_msg) - RELAY_MSG_SIZE_ALIGMENT + sizeof(struct Opt_interface_id) + OPT_RELAY_MESSAGE_CODE_LENGTH_SIZE);

	// Velikost posilanych dat
	length_of_packet = length_of_packet -(sizeof(struct relay_msg) - RELAY_MSG_SIZE_ALIGMENT + sizeof(struct Opt_interface_id) + OPT_RELAY_MESSAGE_CODE_LENGTH_SIZE);	

	// Odesilani packetu
	if(sendto(fd, buffer_pointer, length_of_packet, 0, (struct sockaddr *)&dst_addr, sizeof(dst_addr))  == -1)
		throw_error(strerror(errno));

	// Vypis pridelene IP adresy klientovi, kdyz se podarilo odeslat zpravu
	if((Arguments->debug_flag || Arguments->syslog_flag) && (*buffer_pointer == REPLY_MSG))
	{
		char *string_adress = NULL;
		string_adress = malloc(INET6_ADDRSTRLEN);
		
		struct in6_addr *IP_for_client;
		IP_for_client = (struct in6_addr*) (buffer_pointer + CLIENT_IP_OFFSET);
		inet_ntop(AF_INET6, IP_for_client, string_adress, INET6_ADDRSTRLEN);

		if(Arguments->debug_flag)
			printf("%s, %s\n", string_adress, ether_ntoa((const struct ether_addr *) &Addresses->src_mac));

		if(Arguments->syslog_flag)
			syslog(LOG_INFO, "%s, %s\n", string_adress, ether_ntoa((const struct ether_addr *) &Addresses->src_mac));

		free(string_adress);
	}

	close(fd);
}

// Preposilani zachycenehe packetu serveru
void Relay_to_Server(TArgs* Arguments, T_Addresses* Addresses, T_Opt_relay_msg* Relay_msg_opt, T_Opt_client_addr* Relay_client_addr_opt, T_Opt_interface_id* Relay_interface_opt)
{
	T_relay_msg* relay_message = malloc(sizeof(struct relay_msg));

	memset(relay_message, 0, sizeof(struct relay_msg));
	
	relay_message->msg_type = Arguments->type_of_message;
	relay_message->hop_count = HOP_COUNT_NEW;

	if(get_IP_from_interface(Addresses, GLOBAL_IP_CHAR) == 1)
		throw_error("Error while searching for interface with valid local adress");

	relay_message->peer_address = Addresses->client_addr;
	relay_message->link_address = Addresses->interface_addr;

	u_char buffer[PCKT_LEN];
	memset(buffer, 0, sizeof(buffer));

	int offset = 0;

	// Kopirovani vsech dat do bufferu
	copy_relay_header_to_buffer(&offset, buffer, relay_message);
	copy_option_relay_msg_to_buffer(&offset, buffer, Relay_msg_opt);
	copy_option_client_link_address_to_buffer(&offset, buffer, Relay_client_addr_opt);
	copy_option_interface_id_to_buffer(&offset, buffer, Relay_interface_opt);

	int e_socket;
	
	if((e_socket = socket(AF_INET6, SOCK_DGRAM, 0)) == -1)
		throw_error("Cannot create socket");

	// Zdrojova & Cilova adresa
	struct sockaddr_in6 src_addr, dst_addr;

	// Nastaveni zdrojove adresy
	memset(&src_addr, 0, sizeof(src_addr));
	src_addr.sin6_family = AF_INET6;
	src_addr.sin6_port = htons(DHCP_SERVER_PORT);
	src_addr.sin6_addr = in6addr_any;

	// Binding zdrojove adresy
	if (bind(e_socket, (struct sockaddr *) &src_addr, sizeof(src_addr)) < 0)
	    throw_error(strerror(errno));

	// Nastaveni cilove adresy
	memset(&dst_addr, 0, sizeof(dst_addr));
	dst_addr.sin6_family = AF_INET6;
	dst_addr.sin6_port = htons(DHCP_SERVER_PORT);
	inet_pton(AF_INET6, Arguments->server, &(dst_addr.sin6_addr));

	// Odesilani packetu
	if(sendto(e_socket, buffer, offset, 0, (struct sockaddr *)&dst_addr, sizeof(dst_addr))  == -1)
		throw_error(strerror(errno));

	memset(buffer, 0, sizeof(buffer));

	int length_of_buffer;
	socklen_t addrlen;

	if((length_of_buffer = recvfrom(e_socket, buffer, PCKT_LEN, 0, (struct sockaddr *)&dst_addr, &addrlen)) == -1)
	{
		close(e_socket);
		throw_error(strerror(errno));
	}
	
	if(buffer[0] != RELAY_REPLY_MSG)
	{
		close(e_socket);
		throw_error("Uknown message received");
	}

	close(e_socket);

	if(get_IP_from_interface(Addresses, LOCAL_IP_CHAR) == 1)
		throw_error("Error while searching for interface with valid local adress");

	Relay_to_Client(Arguments, Addresses, buffer, length_of_buffer);
	free(relay_message);
}

// Funkce pro zpracovani packetu
void prepare_packet(TArgs* Arguments, const u_char *packet_to_process, int header_len, char *interface_name)
{	
	// Ziskam typ prijateho packetu
	char DHCP_message_type = *((u_char *)(packet_to_process + sizeof(struct ether_header) + sizeof(struct ip6_hdr) + sizeof(struct udphdr)));

	// Pokud packet neni ani SOLICIT,REQUEST,RENEW nebo REBIND, nepotrebuji ho zpracovavat a muzu proces zabit
	if((DHCP_message_type != SOLICIT_MSG) && (DHCP_message_type != REQUEST_MSG) && (DHCP_message_type != REBIND_MSG) && (DHCP_message_type != RENEW_MSG))
		exit(0);

	// Potrebne struktury
	struct ether_header *ethernet_header;
	struct ip6_hdr *IP_header;

	T_Addresses *Addresses = malloc(sizeof(struct Addresses));

	// ******** Zpracovavani packetu ********

	// Preskocim v packetu za ramec
	ethernet_header = (struct ether_header *)(packet_to_process);
	// Preskocim v packetu za Ethernet hlavicku
	IP_header = (struct ip6_hdr *)(packet_to_process + sizeof(struct ether_header));

	// Ulozeni prislusne zdrojove a cilove IP adresy
	Addresses->client_addr = IP_header->ip6_src;
	Addresses->dst_addr = IP_header->ip6_dst;

	// Ulozeni prislusne zdrojove a cilove MAC adresy
	memcpy(&Addresses->src_mac, &ethernet_header->ether_shost, sizeof(Addresses->src_mac));
	memcpy(&Addresses->dst_addr, &ethernet_header->ether_dhost, sizeof(Addresses->dst_mac));
	
	// Ulozeni nazvu interface na kterem jsem packet prijal
	Addresses->interface_name = interface_name;

	Arguments->type_of_message = RELAY_FORWARD_MSG;

	// Option Relay Message
	T_Opt_relay_msg *Relay_msg_opt = malloc(sizeof(struct Opt_relay_message));
	// Option Client Link-Layer Address 
	T_Opt_client_addr *Relay_client_addr_opt = malloc(sizeof(struct Opt_client_addr));
	// Option Interface ID
	T_Opt_interface_id *Relay_interface_opt = malloc(sizeof(struct Opt_interface_id));

	// Relay message option - obsah puvodni zpravy
	Relay_msg_opt->option_code = htons(OPTION_RELAY_MESSAGE);
	Relay_msg_opt->option_len = htons(header_len - sizeof(struct ether_header) - sizeof(struct ip6_hdr) - sizeof(struct udphdr));
	Relay_msg_opt->option_data = (u_char *)(packet_to_process + sizeof(struct ether_header) + sizeof(struct ip6_hdr) + sizeof(struct udphdr));
	
	// Option pro vlozeni MAC adresy
	Relay_client_addr_opt->option_code = htons(OPTION_CLIENT_LINK_ADDRESS);
	Relay_client_addr_opt->option_len = htons(sizeof(Relay_client_addr_opt->link_layer_type) + sizeof(Relay_client_addr_opt->link_layer_addr));
	Relay_client_addr_opt->link_layer_type = htons(LINKTYPE_ETHERNET);
	memcpy(Relay_client_addr_opt->link_layer_addr, &ethernet_header->ether_shost, sizeof(Relay_client_addr_opt->link_layer_addr));

	// Interface option
	Relay_interface_opt->option_code = htons(OPTION_INTERFACE_ID);
	Relay_interface_opt->option_len = htons(sizeof(Relay_interface_opt->interface_id));
	Relay_interface_opt->interface_id = htons(if_nametoindex(interface_name));

	Relay_to_Server(Arguments, Addresses, Relay_msg_opt, Relay_client_addr_opt, Relay_interface_opt);	

	free(Relay_msg_opt);
	free(Relay_client_addr_opt);
	free(Relay_interface_opt);
	free(Addresses);
}


void capture_packet(TArgs* Arguments, char *interface_name)
{
	const u_char *packet_to_process;
	struct pcap_pkthdr header;

	char errbuf[PCAP_ERRBUF_SIZE];
	pcap_t *interface_device;

	interface_device = pcap_open_live(interface_name, BUFSIZ, PCKT_CNT, UDP_TIMEOUT, errbuf);

	if(interface_device == NULL)
		throw_error(errbuf);

	// ********  Pripojovani do multicast skupiny ******** 
	int fd = pcap_get_selectable_fd(interface_device);

	struct sockaddr_in6 addr;
	addr.sin6_family = AF_INET6;
	addr.sin6_port = htons(DHCP_SERVER_PORT);

	bind(fd,(struct sockaddr*)&addr, sizeof(addr));

	// Studijni zdroj https://www.tenouk.com/Module41c.html

	struct ipv6_mreq multicast_group;
	multicast_group.ipv6mr_interface = if_nametoindex(interface_name);
	inet_pton(AF_INET6, MULTICAST_GROUP_ADDR, &multicast_group.ipv6mr_multiaddr);

	setsockopt(fd,IPPROTO_IPV6, IPV6_ADD_MEMBERSHIP, &multicast_group, sizeof(multicast_group));

	// Nastaveni filtru
    set_and_compile_filter(interface_device);

	pid_t parent_pid = getpid();

	// ******** Prijimani packetu na zpracovani ********
	while((packet_to_process = pcap_next(interface_device, &header)) != NULL)
	{
		
		pid_t child_pid = fork();
    	if(child_pid == 0)
    	{
    		// Studijni zdroj https://stackoverflow.com/Questions/284325/how-to-make-child-process-die-after-parent-exits
    		int r = prctl(PR_SET_PDEATHSIG, SIGTERM);
		    if (r == -1)
	    	{
	    		throw_error("Error while setting child process");
	    	}

    		if (getppid() != parent_pid)
    		{
		        exit(1);
    		}

			prepare_packet(Arguments, packet_to_process, header.len, interface_name);
			exit(0);
    	}

	}

	pcap_close(interface_device);
}

void fork_to_listen_everywhere(TArgs* Arguments, char interfaces_arr[20][20], int number_of_interfaces)
{

    if(Arguments->interface_flag)
    {
		capture_packet(Arguments, interfaces_arr[number_of_interfaces]);
    }
	else
	{
		pid_t parent_pid = getpid();

		for(int j = 0; j < number_of_interfaces; j++)
	    {
	    	if(j == number_of_interfaces-1)
	    	{
	    		capture_packet(Arguments, interfaces_arr[j]);
	    	}
	    	else
	    	{
		    	pid_t child_pid = fork();

		    	if(child_pid == 0)
		    	{
		    		// Studijni zdroj https://stackoverflow.com/Questions/284325/how-to-make-child-process-die-after-parent-exits
		    		int r = prctl(PR_SET_PDEATHSIG, SIGTERM);
				    if (r == -1)
			    	{
			    		throw_error("Error while setting child process");
			    	}

		    		if (getppid() != parent_pid)
				        exit(1);

		    		capture_packet(Arguments, interfaces_arr[j]);
		    	}

	    	}
	    }
	}
}

void find_interface(TArgs *Arguments)
{
	struct ifaddrs *interface;
    bool found = false;
	
	char interfaces_arr[IF_ARR_COUNT][IF_ARR_LENGTH]={{0}};
	int number_of_interfaces = 0;

	char *previous_correct_interface = "";
	char previous_correct_addrtype = EMPTY_CHAR;

    if (getifaddrs(&interface) == -1)
        throw_error("Cannot get interfaces of device");
 
    if(Arguments->interface_flag)
    {
	    while (interface)
	    {
	        if ((interface->ifa_addr) && (interface->ifa_addr->sa_family == AF_INET6) && !strcmp(Arguments->interface, interface->ifa_name))
	        {
				strcpy(interfaces_arr[number_of_interfaces], interface->ifa_name);
	        	fork_to_listen_everywhere(Arguments, interfaces_arr, number_of_interfaces);
	        	return;
        	}
	        interface = interface->ifa_next;
        }
    }
    else
    {
    	char *string_adress = malloc(INET6_ADDRSTRLEN);

    	while(interface)
	    {
	        if ((interface->ifa_addr) && (interface->ifa_addr->sa_family == AF_INET6))
	        {
	        	
				inet_ntop(AF_INET6, &(((struct sockaddr_in6*) interface->ifa_addr)->sin6_addr), string_adress, INET6_ADDRSTRLEN);

				// Nasel jsem validni lokalni adresu pro zadany interface
				if(strncmp(string_adress, LOCAL_IP_CMP, 4) == 0)
				{	
					if((strcmp(interface->ifa_name, previous_correct_interface) == 0) && (previous_correct_addrtype == GLOBAL_IP_CHAR))
					{
						strcpy(interfaces_arr[number_of_interfaces], interface->ifa_name);
						number_of_interfaces++;
						previous_correct_addrtype = EMPTY_CHAR;
						found = true;
					}
					else
					{
						previous_correct_interface = interface->ifa_name;
						previous_correct_addrtype = LOCAL_IP_CHAR;
					}
				}
				if(strncmp(string_adress, GLOBAL_IP_CMP, 3) == 0)
				{
					if((strcmp(interface->ifa_name, previous_correct_interface) == 0) && (previous_correct_addrtype == LOCAL_IP_CHAR))
					{
						strcpy(interfaces_arr[number_of_interfaces], interface->ifa_name);
						number_of_interfaces++;
						previous_correct_addrtype = EMPTY_CHAR;
						found = true;
						if(number_of_interfaces == IF_ARR_COUNT)
							break;
					}
					else
					{
						previous_correct_interface = interface->ifa_name;
						previous_correct_addrtype = GLOBAL_IP_CHAR;
					}
				}
	       }
	       interface = interface->ifa_next;
	    }

	    free(string_adress);
    }

    if(!found)
    	throw_error("Cannot find this interface");
    else
    	fork_to_listen_everywhere(Arguments, interfaces_arr, number_of_interfaces);
}

/******************************** MAIN *******************************/

int main (int argc, char **argv)
{
	openlog (NULL, LOG_CONS | LOG_PID | LOG_NDELAY | LOG_DEBUG, LOG_USER);

	TArgs *Arguments = malloc(sizeof(struct Args));

	init_args_struct(Arguments);
	parse_arguments(argc,argv, Arguments);
	find_interface(Arguments);

	free(Arguments);
	closelog();

	return 0;
}
