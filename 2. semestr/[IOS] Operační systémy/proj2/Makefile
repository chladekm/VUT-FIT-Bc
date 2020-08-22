# Projekt:	The Senate Bus Problem
# Predmet:	IOS - Operační systémy
# Autor:	Martin Chládek (xchlad16)
# Kontakt: 	xchlad16@stud.fit.vutbr.cz
# Datum:	27. 4. 2018

CFLAGS=-std=gnu99 -Wall -Wextra -pedantic -Werror

all: proj2

proj2.o: proj2.c
	gcc $(CFLAGS) -c proj2.c -o proj2.o

proj2: proj2.o
	gcc $(CFLAGS) proj2.o -o proj2 -lpthread

clean-o:
	rm -f *.o
