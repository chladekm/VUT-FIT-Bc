# Autor: Martin Chladek
# Login: xchlad16
# Kontakt: xchlad16@stud.fit.vutbr.cz

# CFLAGS= -g  -Wall -Werror -pedantic  -Wextra 

CC=gcc
CFLAGS= -pedantic -g -Wall -Werror -Wextra
OF= ipk-scan.o
FILE= ipk-scan

ipk-scan: ipk-scan.c
	$(CC) $(CFLAGS) ipk-scan.c -o $(FILE) -lpcap

run: 
	./$(FILE) -pt 22,420 -pu 22,420 localhost