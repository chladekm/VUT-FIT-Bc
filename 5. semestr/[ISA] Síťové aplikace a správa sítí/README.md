## [ISA] Projekt – DHCPv6 relay s podporou vložení MAC adresy

### Zadání

Napište program d6r, který bude umět vložit do DHCPv6 zpráv MAC adresu klienta, jak definuje RFC 6939, a logovat tyto informace pomocí protokolu syslog. 

#### Spuštění aplikace

**Použití**: 
```d6r -s server [-l] [-d] [-i interface]```

**Popis parametrů** (pořadí parametrů je libovolné):
- `-s`: DHCPv6 server, na který je zaslán upravený DHCPv6 paket.
- `-l`: Zapnutí logování pomocí syslog zpráv
- `-i`: Rozhraní, na kterém relay naslouchá, všechna síťová rozhraní, pokud parametr není definován.
- `-d`: Zapnutí debug výpisu na standardní výstup

**Výstup aplikace**:

Na standardní výstup vypište informaci pouze pokud je zapnutý přepínač -d, ve formátu IPv6 adresa (prefix), MAC adresa klienta.

**Ukázka možného výstupu**:

```
$ d6r -s  2001:db8::1111 -d
2001:db8:a::/56,aa:bb:cc:dd:ee:ff
2001:db8:b::1,aa:bb:cc:dd:ff:ff
```

**Upřesnění zadání**

- Pro logování pomocí protokolu syslog využijte standardní volání knihovny syslog. Informaci logujte ve stejném formátu jako na standardní výstup, tj. prefix, MAC.
- Při vytváření programu je povoleno použít hlavičkové soubory pro práci se sokety a další obvyklé funkce používané v síťovém prostředí (jako je netinet/*, sys/*, arpa/* apod.), knihovy pro práci s vlákny (pthread), pakety (pcap), syslog, signály, časem, stejně jako standardní knihovnu jazyka C (varianty ISO/ANSI i POSIX), C++ a STL. 
- Relay nemusí podporovat multicast komunikaci s DHCPv6 serverem.

### Hodnocení 

Získáno bodů: 17 / 20
