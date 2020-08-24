# Autor: Martin Chladek
# Login: xchlad16
# Kontakt: xchlad16@stud.fit.vutbr.cz

# CFLAGS= -g  -Wall -Werror -pedantic  -Wextra 

CC=gcc
CFLAGS= -pedantic -g -Wall -Werror -Wextra
OF= d6r.o
FILE= d6r

#all: 
#	clear

d6r: d6r.c
	$(CC) $(CFLAGS) $(FILE).c -o $(FILE) -lpcap

run: 
	./$(FILE) -s  2001:db8::1111 -d 

clean:
	rm d6r;

test: 
	
