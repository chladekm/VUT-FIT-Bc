# Soubor: Makefile
# Autori: Borek Reich, Martin Chladek
# Datum: 9.12.2019
# Kurz: IMS - Uhlikova stopa v doprave

CLAGS=-g -O2 
PFLAGS=-lsimlib -lm
 
CC = g++
AUTHOR = xreich06
FILES = project.cpp Makefile dokumentace.pdf

all: project

project: project.cpp
	$(CC) $(CFLAGS) -o $@ project.cpp $(PFLAGS)

run:
	./project cs 3