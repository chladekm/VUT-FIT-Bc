/********************* 2. PROJEKT DO IPK 2018/2019 *********************/
/*																	   */
/*		Autor: 	Martin Chládek										   */
/*		Login:	xchlad16											   */
/*		Kontakt: xchlad16@stud.fit.vutbr.cz							   */
/*																	   */
/***********************************************************************/

/****************************** KNIHOVNY *******************************/

#include <stdio.h>
#include <stdlib.h>
#include <getopt.h>
#include <stdbool.h>
#include <string.h>
#include <ctype.h>
#include <pcap.h>
#include <arpa/inet.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netinet/ip.h>
#include <netinet/ip6.h>
#include <netinet/tcp.h>
#include <netinet/udp.h>
#include <netinet/ip_icmp.h>
#include <netinet/if_ether.h>
#include <netdb.h>
#include <unistd.h>
#include <errno.h>
#include <sys/wait.h>

/***************************** KONSTANTY *******************************/

#define MAX_ARGUMENTS 8

#define SRC_PORT 16145 // zdrojovy port (vybran nahodne)

#define PCKT_LEN 8192 // delka paketu
#define PCKT_CNT 1 // pocet zpracovavanych paketu
#define NETMASK 0 //netmask

#define IPv4 4
#define IPv6 6
#define UNSPEC_IP 0
#define IPv4_FIRST_OCTET 127

#define TCP_TYPE 6
#define UDP_TYPE 17

#define TCP_TIMEOUT 400 // timeout pro cekani na packet
#define UDP_TIMEOUT 1000 // timeout pro cekani na packet


/************************** DEFINICE STRUKTUR ****************************/

typedef struct Args
{
	bool UDP_flag;
	bool TCP_flag;
	bool Interface_flag;

	char *UDP_ports;
	char *TCP_ports;

	char *interface;

	struct sockaddr_in *src_address;
	struct sockaddr_in *dst_address;

	struct sockaddr_in6 *src6_address;
	struct sockaddr_in6 *dst6_address;

	char *DN_or_IP;
	int IP_version;

	bool UDP_interval_set;
	bool TCP_interval_set;
}TArgs;

typedef	struct Indexes
{
	uint UDP_actual_index;
	uint TCP_actual_index;
}TIndexes;

typedef struct pseudo_TCP_IPv4_header
{
	u_int32_t TCP_src;
	u_int32_t TCP_dst;
	u_int8_t TCP_reserved;
	u_int8_t TCP_protocol;
	u_int16_t TCP_tcplen;
}T_pseudo_TCP_IPv4_header;

typedef struct pseudo_TCP_IPv6_header
{
	struct in_addr6 *TCP_src;
	struct in_addr6 *TCP_dst;
	u_int8_t TCP_reserved [3];
	u_int8_t TCP_protocol;
	u_int32_t TCP_tcplen;
}T_pseudo_TCP_IPv6_header;

typedef struct pseudo_UDP_header
{
	 u_int32_t UDP_src;
	 u_int32_t UDP_dst;
	 u_int8_t UDP_reserved;
	 u_int8_t UDP_protocol;
	 u_int16_t UDP_udplen;
}T_pseudo_UDP_header;

typedef struct pseudo_UDP6_header
{
	struct in_addr6 *UDP_src;
	struct in_addr6 *UDP_dst;
	u_int32_t UDP_udplen;
	u_int32_t UDP_protocol;
}T_pseudo_UDP6_header;

pcap_t *handle_device;

/************************ GLOBALNI POMOCNE FUNKCE ************************/

// Funkce inicializuje strukturu
void init_args_struct(TArgs *Arguments)
{
	Arguments->UDP_flag = false;
	Arguments->TCP_flag = false;
	Arguments->Interface_flag = false;
	Arguments->UDP_ports = "";
	Arguments->TCP_ports = "";
	Arguments->interface = "";
	Arguments->DN_or_IP = "";
	Arguments->IP_version = UNSPEC_IP;
	Arguments->UDP_interval_set = false;
	Arguments->TCP_interval_set = false;
}

void init_indexes(TIndexes *Indexes)
{
	Indexes->UDP_actual_index = 0;
	Indexes->TCP_actual_index = 0;
}

void alarm_handler() 
{
	pcap_breakloop(handle_device);
}

// Funkce vypíše error
void throw_error(char * reason)
{
	fprintf(stderr, "[ERROR] %s.\n", reason);
	exit(-1);
}

/************************ ZPRACOVAVANI ARGUMENTU ************************/

// Funkce vrati domenove jmeno nebo IP adresu
char * get_DN_or_IP(int argc, char *argv[])
{
	char *DN_or_IP = "";

	for(int i = 1; i < argc; i++)
	{
		if((strcmp(argv[i], "-pu") == 0)||(strcmp(argv[i], "-pt") == 0)||(strcmp(argv[i], "-i") == 0))
		{
			i++;
		}
		else
		if(strcmp(DN_or_IP, "") == 0)
			DN_or_IP = argv[i];
		else
			throw_error("Invalid arguments");
	}
	
	if(strcmp(DN_or_IP,"") == 0)
	{
		throw_error("Cannot find domain name or IP adress in arguments");
		return 0;
	}
	else
		return DN_or_IP;
}

// Funkce zjisti zda byl zadan interval, nebo cislice a zkontroluje format vstupu
bool is_interval_and_check_format(char * string_to_check)
{
	if(isdigit(string_to_check[0]) == false)
		throw_error("Incorrect format of argument");

	bool dash_already_found = false;
	bool comma_already_found = false;

	for(uint i=0; i<strlen(string_to_check); i++)
	{
		char vchar = string_to_check[i];

		if(!isdigit(vchar))
		{
			// Nacten nekorektni znak
			if(vchar != '-' && vchar != ',')
			{
				throw_error("Incrolorrect format of argument");						
			}
			// Byla nactena pomlcka, ale uz nekdy predtim byla nactena taky -> error
			if(vchar == '-' && dash_already_found)
			{
				throw_error("Incorrect format of argument");
			}
			// Byla nactena pomlcka poprve a pred i za ni je cislo
			else if (vchar == '-' && isdigit(string_to_check[i+1]) && !comma_already_found)
			{
				dash_already_found = true;
			}
			// Byla nactena carka
			else if (vchar == ',' && !dash_already_found)
			{
				// Pred carkou nebo za carkou neni cislo
				if(!isdigit(string_to_check[i-1]) || !isdigit(string_to_check[i+1]))
					throw_error("Incorrect format of argument");
				else
					{/* korektni stav */}
			}
			else
			{
				throw_error("Incorrect format of argument");	
			}
		}
	}

	if(dash_already_found)
		return true; // Zadana cisla jsou interval
	else
		return false; // Zadana cisla nejsou interval
}

// Funkce kontroluje, zda byly zadany potrebne parametry
void check_loaded_parameters(TArgs *Arguments)
{
	if((!Arguments->UDP_flag && !Arguments->TCP_flag) || strcmp(Arguments->DN_or_IP,"") == 0)
		throw_error("Wrong parameters set");

	if(Arguments->UDP_flag) Arguments->UDP_interval_set = is_interval_and_check_format(Arguments->UDP_ports);
	if(Arguments->TCP_flag) Arguments->TCP_interval_set = is_interval_and_check_format(Arguments->TCP_ports);
}

// Funkce nacita parametry zadane pri volani skriptu
void parse_arguments(int argc, char *argv[], TArgs *Arguments)
{
	static int UDP_flag;
	static int TCP_flag;

	if(argc > MAX_ARGUMENTS)
		throw_error("Too many arguments");

	while(true)
	{
		static struct option long_options[] = 
		{
			{"pu", required_argument, &UDP_flag, 1},
			{"pt", required_argument, &TCP_flag, 1},
			{0,0,0,0}		
		};

		int option_index = 0;

		int option = getopt_long_only(argc, argv, "pu:pt:i:", long_options, &option_index);		

		Arguments->UDP_flag = UDP_flag;
		Arguments->TCP_flag = TCP_flag;
		
		// Ukonceni nacitani argumentu
		if(option == -1)
			break;

		switch(option)
		{
			case 0:
				// Tato moznost nastavila flag
				if (long_options[option_index].flag != 0)
				{
	            	if(strcmp(long_options[option_index].name, "pu") == 0)
	        			Arguments->UDP_ports = optarg;
					else if (strcmp(long_options[option_index].name, "pt") == 0)
            			Arguments->TCP_ports = optarg;
            	}
	          	break;
	        case 'i':
	        	Arguments->Interface_flag = true;
	        	Arguments->interface = optarg;
	        	break;
			default:
				throw_error("Uknown parameter or requires argument");
				break;
		}
	}

	Arguments->DN_or_IP = get_DN_or_IP(argc, argv);

	check_loaded_parameters(Arguments);
}

/************************** ZPRACOVAVANI PORTU **************************/

// Funkce kontroluje zda je zadany interval v rozmezi <0,65535>
void check_interval_borders(int number)
{
	if(number == -1)
		return;
	if(number >= 0 && number <= 65535)
		return;
	else
		throw_error("Port must be in interval <0, 65335>");
}

// Funkce vrati nasledujici zadany port. V pripade ze uz byly vsechny vycerpany, vrati -1
int get_port_number(uint * index, char *number_string, bool interval)
{
	int number = -1;

	if(interval)
	{	
		//Odrizne pocatecni cislo intervalu
		number = atoi(number_string);

		// Najiti horni hranice intervalu
		char * max = strchr(number_string, '-');
		max++;
		int interval_max = atoi(max);		

		// Overi spravnost intervalu
		if(interval_max < number)
			throw_error("Incorrect format of interval");
	
		check_interval_borders(interval_max);
		// Nove cislo
		number = number + *index;
		
		// Overeni aby nebylo cislo mimo interval
		if(number > interval_max)
			number = -1;

		*index = *index + 1;
	}
	else
	{
		if(*index >= strlen(number_string))
		{
			number = -1;
			return number;
		}
		
		char *actual_string = number_string + *index;
		number = atoi(actual_string);

		int length = 0;
		if(number >= 0 && number < 10) length = 1;
		else if(number >= 10 && number < 100) length = 2;
		else if(number >= 100 && number < 1000) length = 3;
		else if(number >= 1000 && number < 10000) length = 4;
		else if(number >= 10000 && number < 100000) length = 5;

		// Aktualni index + delka cisla + oddelujici carka
		*index = *index + length + 1;
	} 

	check_interval_borders(number);
	return number;
}

// Otestovani vsech zadanych portu pred tim nez budou pouzity
void test_ports(TArgs *Arguments, TIndexes *Indexes)	
{
	int port = 0;

	if(Arguments->TCP_interval_set == false)
	{
		while((port = get_port_number(&Indexes->TCP_actual_index, Arguments->TCP_ports, Arguments->TCP_interval_set)) != -1)
		{
			check_interval_borders(port);
		}
	}

	if(Arguments->UDP_interval_set == false)
	{
		while((port = get_port_number(&Indexes->UDP_actual_index, Arguments->UDP_ports, Arguments->UDP_interval_set)) != -1)
		{
			check_interval_borders(port);
		}
	}
	init_indexes(Indexes);
}

/***************************** PRACE S IP *****************************/

// Nalezne IP adresu zdrojoveho pocitace podle zadaneho rozhrani
int find_IP_of_interface(TArgs *Arguments, int specified_IP)
{
	static char errbuf[PCAP_ERRBUF_SIZE];
	pcap_if_t *device;

	int state = pcap_findalldevs(&device, errbuf);

	if(state != 0)
		throw_error("Cannot find any of network devices");

	pcap_addr_t *address;

	while(true)
	{
		// Nalezli jsme pozadovane rozhrani
		if(strcmp(device->name, Arguments->interface) == 0)
		{
			address = device->addresses;
			break;
		}
		// Konec seznamu rozhrani, pozadovane rozhrani nebylo nalezeno
		else if(device->next == NULL)
			throw_error("Cannot find this interface");

		device = device->next;
	}

	printf("Source IP: \n");

	// Prochazeni seznamu adres daneho zarizeni
	while(address != NULL)   
   	{
    	if(specified_IP == IPv4)
    	{
    		// Nalezena IPv4
	    	if(address->addr->sa_family == AF_INET) 
	    	{
   				Arguments->src_address = (struct sockaddr_in *)(address->addr);
	    		return 0;  
	    	}
    	}
    	else if(specified_IP == IPv6)
		{
			// Nalezena IPv6
	    	if(address->addr->sa_family == AF_INET6) 
	    	{
   				Arguments->src6_address = (struct sockaddr_in6 *)(address->addr);
	    		return 0;  
	    	}
		}
		address = address->next;
	}
	
	if(address == NULL)
		return -1;

	return 0;
}

// Funkce najde rozhrani v pripade, ze nebylo zadano
void find_interface(TArgs *Arguments, int specified_IP, bool want_loopback)
{
	static char errbuf[PCAP_ERRBUF_SIZE];

	pcap_if_t *device;
	pcap_addr_t *address;

	int state = pcap_findalldevs(&device, errbuf);

	if(state != 0)
		throw_error("Cannot find any of network devices");

	int type = 0;

	if(specified_IP == IPv4)
		type = AF_INET;
	else
		type = AF_INET6;

	// studijni zdroj: https://www.winpcap.org/docs/docs_40_2/html/structpcap__if.html
	while(device != NULL)
	{
		address = device->addresses;

		while(address != NULL)
		{
			if(want_loopback)
			{
				if(((device->flags == PCAP_IF_LOOPBACK) || device->name[0] == 'l') && (address->addr->sa_family == type))
				{
					Arguments->interface = device->name;
					return;
				}	
			}
			else
			{
				if((device->flags != PCAP_IF_LOOPBACK) && (address->addr->sa_family == type))
				{
					Arguments->interface = device->name;
					return;
				}	
			}
			
			address = address->next;
		}
		device = device->next;
	}
	throw_error("Error while searching for interface device");
}

bool is_dst_address_loopback(TArgs* Arguments, int specified_IP)
{
	if(specified_IP == IPv4)
	{
		char *ip = inet_ntoa(Arguments->dst_address->sin_addr);
		
		if(atoi(ip) == IPv4_FIRST_OCTET)
			return true;
		else
			return false;
	}
	else
	{
    	char dst[INET6_ADDRSTRLEN];
        inet_ntop(AF_INET6, &Arguments->dst6_address->sin6_addr, dst, INET6_ADDRSTRLEN);	
        for(uint i=0; i < strlen(dst); i++)
        {
        	char character = dst[i];

        	if(character != '0' && character != '\0' && character != ':')
        	{
   				if(character == '1' && (i+1 == strlen(dst)))
   					return true;
   				else
   					return false; 
        	}

        }
	}

	return false;
}

// Najde IP adresu pro dane domenove jmeno
void convert_domain_name_to_IP(TArgs *Arguments, int specified_IP)
{
	struct addrinfo hints, *result;
	memset (&hints, 0, sizeof (hints));
	
	if(specified_IP == IPv4)
	{
		hints.ai_family = AF_INET;
	}
	else if(specified_IP == IPv6)
	{
		hints.ai_family = AF_INET6;
	}
	else
	{
		hints.ai_family = AF_UNSPEC;
	}

	hints.ai_socktype = SOCK_DGRAM;
	hints.ai_flags = 0;
	hints.ai_protocol = 0;

	int ip_result = (getaddrinfo(Arguments->DN_or_IP, NULL, &hints, &result));

	if (ip_result != 0) {
           throw_error("Cannot get destination IP address");
	}

	printf("Destination IP: \n");
	
	if(result->ai_family == AF_INET)
	{
		Arguments->dst_address = (struct sockaddr_in *)result->ai_addr;
		Arguments->IP_version = IPv4;
	}
	else
	{
		Arguments->dst6_address = (struct sockaddr_in6 *)result->ai_addr;
		Arguments->IP_version = IPv6;
	}
}

/************************* FUNKCE PRO SCANNING *************************/

// Funkce vytvari socket
void open_socket(int *e_socket, char type, int IP_type)
{
	// socket pro TCP
	if(type == TCP_TYPE)
	{
		if(IP_type == IPv4)
			*e_socket = socket(AF_INET, SOCK_RAW, IPPROTO_TCP);
		else
			*e_socket = socket(AF_INET6, SOCK_RAW, IPPROTO_TCP);
	}
	// socket pro UDP
	else if(type == UDP_TYPE)
	{
		if(IP_type == IPv4)
			*e_socket = socket(AF_INET, SOCK_RAW, IPPROTO_UDP);
		else
			*e_socket = socket(AF_INET6, SOCK_RAW, IPPROTO_UDP);
	}

	if(*e_socket == -1)
		throw_error("Cannot create socket");

	int ret = 1;
		
	if(IP_type == IPv4)
	{
		if(setsockopt(*e_socket, IPPROTO_IP, IP_HDRINCL, (void *)&ret, sizeof(ret)) == -1)
			throw_error("Error while initializing socket");
	}
	else
	{
		if(setsockopt(*e_socket, IPPROTO_IPV6, IP_HDRINCL, (void *)&ret, sizeof(ret)) == -1)
			throw_error("Error while initializing socket");
	}
}

// Funkce pocita checksum
// Studijni zdroj: https://www.tenouk.com/Module43a.html
unsigned short checkSum(unsigned short *buf, int nwords)
{
	unsigned long sum;

	for(sum = 0; nwords > 0; nwords--)
	{
		sum += *buf++;
	}
	sum = (sum >> 16) + (sum & 0xffff);
	sum += (sum >> 16);

	return (unsigned short)(~sum);
}

void set_and_compile_filter(pcap_t *handle_device, int port, char Protocol_type)
{
	// Pomocne "stringy"
	char str_src_port[8];
	char str_dst_port[8];

	// Konverze portu z intu na string, aby mohla byt vyhotovena konkatenace			
	sprintf(str_src_port, "%d", SRC_PORT);
	sprintf(str_dst_port, "%d", port);

	char filter[80] = "";

	// Vyhotoveni filtru
	if(Protocol_type == TCP_TYPE)
		strcat(filter, "tcp[2:2]=");
	else
		strcat(filter, "icmp[28:2]=");

	strcat(filter, str_src_port);
	
	if(Protocol_type == TCP_TYPE)
		strcat(filter, " and tcp[0:2]=");
	else
		strcat(filter, " and icmp[30:2]=");

	strcat(filter, str_dst_port);
	
	// Zkompilovany filtr
	struct bpf_program compiled_filter; 
	
	// Kompilace filtru
	if(pcap_compile(handle_device, &compiled_filter, filter, 0, NETMASK) == -1)
		throw_error("Could not parse filter.");
	
	// Nastaveni filtru
	if(pcap_setfilter(handle_device, &compiled_filter) == -1)
		throw_error("Cloud not install filter.");
}

/*************************** PLNENI HLAVICEK ***************************/

// Naplneni struktury IPv4 hlavicky
void fill_IPv4_header(struct ip *IP_header, TArgs *Arguments, char Protocol)
{
	IP_header->ip_hl = 5;  // velikost hlavicky (typicky 20 bytu)
	IP_header->ip_v = IPv4;   // verze IP protokolu

	if(Protocol == TCP_TYPE)
		IP_header->ip_tos = 0;
	else if(Protocol == UDP_TYPE)
		IP_header->ip_tos = 16;
	
	IP_header->ip_len = sizeof(struct ip) + sizeof(struct tcphdr);
	IP_header->ip_id = htons(55220);
	IP_header->ip_off= 0;
	IP_header->ip_ttl = 64;

	IP_header->ip_p = Protocol; // TCP || UDP

	IP_header->ip_sum = 0; // checksum - bude dopocitano pozdeji

	IP_header->ip_src.s_addr = Arguments->src_address->sin_addr.s_addr; // zdrojova IP adresa
	IP_header->ip_dst.s_addr = Arguments->dst_address->sin_addr.s_addr; // cilova IP adresa
}

// Naplneni struktury IPv6 hlavicky
void fill_IPv6_header(struct ip6_hdr *IP6_header, TArgs *Arguments, char Protocol_type)
{	
	// Studijni zdroj pro ip6_flow: https://labs.apnic.net/?p=1057
	IP6_header->ip6_flow = htonl(((6<<28))|(0 << 20) | 0); // 20 bits of flow ID
	IP6_header->ip6_plen = htons(20);	// Payload lengt
	IP6_header->ip6_hops = 255; // Hop limit

	if(Protocol_type == TCP_TYPE)
		IP6_header->ip6_nxt = IPPROTO_TCP; // TCP
	else if(Protocol_type == UDP_TYPE)
		IP6_header->ip6_nxt = IPPROTO_UDP; // UDP

	IP6_header->ip6_src = Arguments->src6_address->sin6_addr;
	IP6_header->ip6_dst = Arguments->dst6_address->sin6_addr;
}

// Naplneni struktury TCP hlavicky
void fill_TCP_header(struct tcphdr *TCP_header, int dst_port) 
 {	
	TCP_header->th_sport = htons(SRC_PORT);   // zdrojovy port (source port)
	TCP_header->th_dport = htons(dst_port);   // cilovy port (destination port)
	TCP_header->th_seq = htonl(1); // poradove cislo (sequence number)
	TCP_header->th_ack = htons(0);		 // potrvzovaci cislo (acknowledgement number)
	TCP_header->th_x2 = 0; // rezervovano
	TCP_header->th_off = 5;	//data offset
	TCP_header->th_flags = TH_SYN; // priznaky (flags/control bits)
	TCP_header->th_win = htons(65535); // počet akceptovatelných bytů(oktetů)
	TCP_header->th_sum = 0; // checksum - bude dopocitano pozdeji
	TCP_header->th_urp = 0; // Urgent data pointer
}

// Naplneni pseudo TCP hlavicky pro checksum
void fill_TCP_pseudo_header(T_pseudo_TCP_IPv4_header *pseudo_tcp, TArgs *Arguments)
{
	pseudo_tcp->TCP_src = Arguments->src_address->sin_addr.s_addr;
	pseudo_tcp->TCP_dst = Arguments->dst_address->sin_addr.s_addr;
	pseudo_tcp->TCP_protocol = IPPROTO_TCP;
	pseudo_tcp->TCP_reserved = 0;
	pseudo_tcp->TCP_tcplen = htons(sizeof(struct tcphdr));
}

// Naplneni pseudo TCP hlavicky pro IPv6 checksum
void fill_TCP6_pseudo_header(T_pseudo_TCP_IPv6_header *pseudo_tcp/*, TArgs *Arguments*/)
{
	// pseudo_tcp->TCP_src = Arguments->src6_address->sin6_addr.s6_addr;
	// pseudo_tcp->TCP_dst = Arguments->dst6_address->sin6_addr.s6_addr;
	pseudo_tcp->TCP_tcplen = htons(sizeof(struct tcphdr));
	pseudo_tcp->TCP_protocol = IPPROTO_TCP;
}

void fill_UDP_header(struct udphdr *UDP_header, int dst_port)
{
	UDP_header->uh_sport = htons(SRC_PORT);	  // zdrojovy port (source port)
	UDP_header->uh_dport = htons(dst_port);   // cilovy port (destination port)
	UDP_header->uh_ulen = htons(sizeof(struct udphdr));   // velikost hlavicky
	UDP_header->uh_sum = 0; // checksum - bude dopocitano pozdeji
}

void fill_UDP_pseudo_header(T_pseudo_UDP_header *pseudo_udp, TArgs *Arguments)
{
	pseudo_udp->UDP_src = Arguments->src_address->sin_addr.s_addr;
	pseudo_udp->UDP_dst = Arguments->dst_address->sin_addr.s_addr;
	pseudo_udp->UDP_protocol = IPPROTO_UDP;
	pseudo_udp->UDP_reserved = 0;
	pseudo_udp->UDP_udplen = htons(sizeof(struct udphdr));
}

void fill_UDP6_pseudo_header(T_pseudo_UDP6_header *pseudo_udp/*, TArgs *Arguments*/)
{
	// pseudo_udp->UDP_src = Arguments->src6_address->sin6_addr.s6_addr;
	// pseudo_udp->UDP_dst = Arguments->dst6_address->sin6_addr.s6_addr;
	pseudo_udp->UDP_udplen = htons(sizeof(struct udphdr));
	pseudo_udp->UDP_protocol = 0 + IPPROTO_UDP;
}

/************************* ZPRACOVAVANI PACKETU *************************/

// [TCP] Funkce zpracovava zachyceny packet
void packet_handler_TCP(u_char *user, const struct pcap_pkthdr *header, const u_char *pkt_data)
{
	// Pomocne struktury
	struct ip *IP_header;
	struct tcphdr *TCP_header;
	(void) header;
	(void) user;

	// Preskocim v packetu Ethernet hlavicku
	IP_header = (struct ip *)(pkt_data + sizeof(struct ether_header));
	
	// Preskocim v packetu IP hlavicku
	TCP_header = (struct tcphdr *)(pkt_data + sizeof(struct ether_header) + (IP_header->ip_hl*4));

	// 0x14 - Flags A, R -> closed
	if(TCP_header->th_flags == 0x14)
		printf("closed\n");
	else
		printf("open\n");
}

// [UDP] Funkce zpracovava zachyceny packet
void packet_handler_UDP( u_char *user, const struct pcap_pkthdr *header, const u_char *pkt_data)
{
	// Pomocne struktury
	struct ip *IP_header;
	struct icmphdr *ICMP_header;
	(void) header;
	(void) user;
	
	// Preskocim v packetu Ethernet hlavicku
	IP_header = (struct ip *)(pkt_data + sizeof(struct ether_header));

	// Preskocim v packetu IP hlavicku
	ICMP_header = (struct icmphdr *)(pkt_data + sizeof(struct ether_header) + (IP_header->ip_hl*4));

	// ICMP paket typu 3 - destination unreachable
	if(ICMP_header->type == 3)
		printf("closed\n");
	else
		printf("open\n");
}

///////////////////////////////////////////////////////////////////////////
// 									TCP 								 //
///////////////////////////////////////////////////////////////////////////
void TCP_scan_IPv4(TArgs *Arguments, TIndexes *Indexes)
{
	// Inicializace datagramu
	char datagram[PCKT_LEN];

	// Inicializace IP hlavicky
	struct ip *IP_header = (struct ip *) datagram;

	// Inicializace TCP hlavicky
	struct tcphdr *TCP_header = (struct tcphdr *) (datagram + sizeof(struct ip));
		
	int e_socket;
	
	open_socket(&e_socket, TCP_TYPE, IPv4);

	int port;

	// Cyklus pro jednotlive porty
	while((port = get_port_number(&Indexes->TCP_actual_index, Arguments->TCP_ports, Arguments->TCP_interval_set)) != -1)
	{
		// Vynulovani datagramu
		memset(datagram, 0, PCKT_LEN);

		Arguments->src_address->sin_port = htons(SRC_PORT);
		Arguments->dst_address->sin_port = htons(port);

		// Naplneni hlavicky TCP
		fill_TCP_header(TCP_header, port);

			// Vytvoreni a naplneni TCP pseudohlavicky pro checksum
		struct pseudo_TCP_IPv4_header *pseudo_tcp = (struct pseudo_TCP_IPv4_header *)((char*)TCP_header - sizeof(struct pseudo_TCP_IPv4_header));
		fill_TCP_pseudo_header(pseudo_tcp, Arguments);

		TCP_header->th_sum = checkSum((unsigned short*) pseudo_tcp, sizeof(struct pseudo_TCP_IPv4_header) + sizeof(struct tcphdr));

		// Naplneni hlavicky IP
		fill_IPv4_header(IP_header, Arguments, TCP_TYPE);
		IP_header->ip_sum = checkSum((unsigned short *)datagram, sizeof(struct ip) + sizeof(struct tcphdr));
	
			
		char errbuf[PCAP_ERRBUF_SIZE];
		
		handle_device = pcap_open_live(Arguments->interface, BUFSIZ, 1, TCP_TIMEOUT, errbuf);
		
		if(handle_device == NULL)
			throw_error("Could not open device");

		set_and_compile_filter(handle_device, port, TCP_TYPE);
			
		///////////////// Odeslani packetu /////////////////
		int send_return = sendto(e_socket, datagram, IP_header->ip_len, 0, (struct sockaddr *) Arguments->dst_address, sizeof(struct sockaddr));

		if(send_return < 0)
		{
			pcap_close(handle_device);
			close(e_socket);
			throw_error("Error while sending packet");
		}
		///////////////// Zachycovani packetu /////////////////
		else
		{
			/// Kvuli odsazeni
			if(port > 999)
				printf("%d/tcp\t", port);
			else
				printf("%d/tcp\t\t", port);

			signal(SIGALRM, alarm_handler);
			alarm(1);	

			// Cte a zpracovava prijate pakety
			int packet_to_process = pcap_dispatch(handle_device, PCKT_CNT, packet_handler_TCP, NULL);
			
			if(packet_to_process == 0 || packet_to_process == PCAP_ERROR_BREAK)
			{
				// Pokud neprijde zadna odpoved, posleme packet jeste jednou a potom je oznacen za filtrovany
				send_return = sendto(e_socket, datagram, IP_header->ip_len, 0, (struct sockaddr *) Arguments->dst_address, sizeof(struct sockaddr));
				
				if(send_return < 0)
				{
					pcap_close(handle_device);
					close(e_socket);
					throw_error("Error while sending packet");
				}
				
				signal(SIGALRM, alarm_handler);
				alarm(1);
				
				packet_to_process = pcap_dispatch(handle_device, PCKT_CNT, packet_handler_TCP, NULL);

				if(packet_to_process != 1)
				{
					printf("filtered\n");
				}
			}
		
			pcap_close(handle_device);
		}
	}	
	close(e_socket);
}

void TCP_scan_IPv6(TArgs *Arguments, TIndexes *Indexes)
{
	// Inicializace datagramu
	char datagram[PCKT_LEN];

	// Inicializace IP hlavicky
	struct ip6_hdr *IP6_header = (struct ip6_hdr *) datagram;

	// Inicializace TCP hlavicky
	struct tcphdr *TCP_header = (struct tcphdr *) (datagram + sizeof(struct ip));
		
	int e_socket;
	
	open_socket(&e_socket, TCP_TYPE, IPv6);

	int port;

	// Cyklus pro jednotlive porty
	while((port = get_port_number(&Indexes->TCP_actual_index, Arguments->TCP_ports, Arguments->TCP_interval_set)) != -1)
	{
		// Vynulovani datagramu
		memset(datagram, 0, PCKT_LEN);

		Arguments->src6_address->sin6_port = htons(SRC_PORT);
		Arguments->dst6_address->sin6_port = htons(port);

		// Naplneni hlavicky TCP
		fill_TCP_header(TCP_header, port);
	
		// Vytvoreni a naplneni TCP pseudohlavicky pro checksum
		struct pseudo_TCP_IPv6_header *pseudo_tcp = (struct pseudo_TCP_IPv6_header *)((char*)TCP_header - sizeof(struct pseudo_TCP_IPv6_header));
		fill_TCP6_pseudo_header(pseudo_tcp/*, Arguments*/);

		TCP_header->th_sum = checkSum((unsigned short*) pseudo_tcp, sizeof(struct pseudo_TCP_IPv6_header) + sizeof(struct tcphdr));

		// Naplneni hlavicky IP
		fill_IPv6_header(IP6_header, Arguments, TCP_TYPE);
	
		struct sockaddr_in6 dstPC;

		memset(&dstPC, 0, sizeof(struct sockaddr_in6));
		dstPC.sin6_family = AF_INET6;
		dstPC.sin6_addr = Arguments->dst6_address->sin6_addr;
			
		char errbuf[PCAP_ERRBUF_SIZE];
		
		handle_device = pcap_open_live(Arguments->interface, BUFSIZ, 1, TCP_TIMEOUT, errbuf);
	
		if(handle_device == NULL)
			throw_error("Could not open device");

		set_and_compile_filter(handle_device, port, TCP_TYPE);

		///////////////// Odeslani packetu /////////////////
		int send_return = sendto(e_socket, datagram, sizeof(struct ip6_hdr) + sizeof(struct tcphdr) , 0, (struct sockaddr*) &dstPC, sizeof(struct sockaddr_in6));

		if(send_return < 0)
		{
			pcap_close(handle_device);
			close(e_socket);
			throw_error("Error while sending packet");
		}
		///////////////// Zachycovani packetu /////////////////
		else
		{
			/// Kvuli odsazeni
			if(port > 999)
				printf("%d/tcp\t", port);
			else
				printf("%d/tcp\t\t", port);

			signal(SIGALRM, alarm_handler);
			alarm(1);	

			// Cte a zpracovava prijate pakety
			int packet_to_process = pcap_dispatch(handle_device, PCKT_CNT, packet_handler_TCP, NULL);
			
			if(packet_to_process == 0 || packet_to_process == PCAP_ERROR_BREAK)
			{
				// Pokud neprijde zadna odpoved, posleme packet jeste jednou a potom je oznacen za filtrovany
				send_return = sendto(e_socket, datagram, sizeof(struct ip6_hdr) + sizeof(struct tcphdr) , 0, (struct sockaddr*) &dstPC, sizeof(struct sockaddr_in6));
				
				if(send_return < 0)
				{
					pcap_close(handle_device);
					close(e_socket);
					throw_error("Error while sending packet");
				}
				
				signal(SIGALRM, alarm_handler);
				alarm(1);
				
				packet_to_process = pcap_dispatch(handle_device, PCKT_CNT, packet_handler_TCP, NULL);

				if(packet_to_process != 1)
				{
					printf("filtered\n");
				}
			}
			pcap_close(handle_device);
		}
	}	
	close(e_socket);
}


///////////////////////////////////////////////////////////////////////////
// 									UDP 								 //
///////////////////////////////////////////////////////////////////////////
void UDP_scan_IPv4(TArgs *Arguments, TIndexes *Indexes)
{
	// Inicializace datagramu
	char datagram[PCKT_LEN];

	// Inicializace IP hlavicky
	struct ip *IP_header = (struct ip *) datagram;

	// Inicializace TCP hlavicky
	struct udphdr *UDP_header = (struct udphdr *) (datagram + sizeof(struct ip));

	int e_socket;

	if(Arguments->dst_address->sin_family == AF_INET)
		open_socket(&e_socket, TCP_TYPE, IPv4);
	else
		open_socket(&e_socket, TCP_TYPE, IPv6);
	
	int port;

	// Cyklus pro jednotlive porty
	while((port = get_port_number(&Indexes->UDP_actual_index, Arguments->UDP_ports, Arguments->UDP_interval_set)) != -1)
	{
		// Vynulovani datagramu
		memset(datagram, 0, PCKT_LEN);

		// Naplneni struktury aktualnimi porty
		Arguments->src_address->sin_port = htons(SRC_PORT);
		Arguments->dst_address->sin_port = htons(port);

		fill_UDP_header(UDP_header, port);

		struct pseudo_UDP_header *pseudo_udp = (struct pseudo_UDP_header *)((char*)UDP_header - sizeof(struct pseudo_UDP_header));

		fill_UDP_pseudo_header(pseudo_udp, Arguments);
		UDP_header->uh_sum = checkSum((unsigned short*) pseudo_udp, sizeof(struct pseudo_UDP_header) + sizeof(struct udphdr)); 

		// Naplneni hlavicky IP
		fill_IPv4_header(IP_header, Arguments, UDP_TYPE);
		IP_header->ip_sum = checkSum((unsigned short *)datagram, sizeof(struct ip) + sizeof(struct tcphdr));

		char errbuf[PCAP_ERRBUF_SIZE];
			
		handle_device = pcap_open_live("lo", BUFSIZ, PCKT_CNT, UDP_TIMEOUT, errbuf);
			
		if(handle_device == NULL)
			throw_error("Could not open device.");

		set_and_compile_filter(handle_device, port, UDP_TYPE);

		///////////////// Odeslani packetu /////////////////
		if(sendto(e_socket, datagram, IP_header->ip_len, 0, (struct sockaddr *) Arguments->dst_address, sizeof(struct sockaddr)) < 0)
		{
			close(e_socket);
			throw_error("Error while sending packet");
		}
		///////////////// Zachycovani packetu /////////////////
		else
		{
			/// Kvuli odsazeni
			if(port > 999)
				printf("%d/udp\t", port);
			else
				printf("%d/udp\t\t", port);

			signal(SIGALRM, alarm_handler);
			alarm(2);

			// Cte a zpracovava prijate pakety
			int packet_to_process = pcap_dispatch(handle_device, PCKT_CNT, packet_handler_UDP, NULL);
		
			// Pokud zadny packet neprisel, muzeme port oznacit za otevreny
			if(packet_to_process != 1)
				printf("open\n");

			pcap_close(handle_device);
		}
	}	

	close(e_socket);
}

void scan(TArgs *Arguments, TIndexes *Indexes)
{
	test_ports(Arguments, Indexes);

	convert_domain_name_to_IP(Arguments, UNSPEC_IP);
		
	if(!Arguments->Interface_flag)
	{
		find_interface(Arguments, Arguments->IP_version, is_dst_address_loopback(Arguments, Arguments->IP_version));
	}

	int already_tried = 0;

	// Hledani IPv4 zdrojove adresy rozhrani 
	if(Arguments->IP_version == IPv4)
	{
		// Nebyla IPv4 nalezena (ale možná existuje IPv6)
		if(find_IP_of_interface(Arguments, IPv4) == -1)
		{
			already_tried = IPv4;
		}
	}
	// Hledani IPv6 zdrojove adresy rozhrani 
	else
	{
		// Nebyla IPv6 nalezena (ale možná existuje IPv4)
		if(find_IP_of_interface(Arguments, IPv6) == -1)
		{
			already_tried = IPv6;
		}
	}

	// Nebyla nalezena spolecna rec (vzdaleny klient/server vratil format adresy, ktery my nemame)
	if(already_tried != 0)
	{
		// Budeme se snažit najít společnou řeč skrze IPv4
		if(already_tried == IPv6)
		{
			convert_domain_name_to_IP(Arguments, IPv4);
			if(find_IP_of_interface(Arguments, IPv4) == -1)
			{
				throw_error("Cannot find source IP adress");
			}
		}
		// Budeme se snažit najít společnou řeč skrze IPv6
		else if(already_tried == IPv4)
		{
			convert_domain_name_to_IP(Arguments, IPv6);
			if(find_IP_of_interface(Arguments, IPv6) == -1)
			{
				throw_error("Cannot find source IP adress");
			}
		}
	}

	printf("PORT\t\tSTATE\n");

	if(Arguments->IP_version == IPv4)
	{
		if(Arguments->TCP_flag)
		{
			TCP_scan_IPv4(Arguments, Indexes);
		}
		if(Arguments->UDP_flag)
		{
			UDP_scan_IPv4(Arguments, Indexes);
		}
	}
	else
	{
		if(Arguments->TCP_flag)
		{
			TCP_scan_IPv6(Arguments, Indexes);
		}
		if(Arguments->UDP_flag)
		{
			int port;
			while((port = get_port_number(&Indexes->UDP_actual_index, Arguments->UDP_ports, Arguments->UDP_interval_set)) != -1)
			{
				/// Kvuli odsazeni
				if(port > 999)
					printf("%d/udp\t---\n", port);
				else
					printf("%d/udp\t\t---\n", port);
			}
		}
	}
}


/******************************** MAIN *******************************/

int main (int argc, char **argv)
{
	TArgs *Arguments = malloc(sizeof(struct Args));
	TIndexes *Indexes = malloc(sizeof(struct Indexes));

	init_args_struct(Arguments);
	init_indexes(Indexes);

	parse_arguments(argc,argv, Arguments);

	scan(Arguments, Indexes);

	free(Arguments);
	free(Indexes);
}