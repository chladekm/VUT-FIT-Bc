#------------------------------------------------------------------------------#
# File:     Makefile                                                           #
# Project:  2BIT IFJ, compiler                                                 #
#           Faculty of Information Technolgy                                   #
#           Brno University of Technology                                      #
# Authors:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>                        #
#           Peter Kruty <xkruty00@stud.fit.vutbr.cz>                           #
#           Michal Krůl <xkrulm00@stud.fit.vutbr.cz>                           #
#           Bořek Reich <xreich06@stud.fit.vutbr.cz>                           #
# Date:     06.12.2018                                                         #
#------------------------------------------------------------------------------#

CC=gcc
CFLAGS=-std=c99 -pedantic -Wall -Wextra
OF= errors.o symtable.o str.o scanner.o generator.o parser.o main.o expression.o stack.o
COMPILER=test-prekladac
#------------------------------------------------------------------------------#
compiler: $(OF)
	$(CC) $(CFLAGS) $(OF) -o $(COMPILER)
	rm -f $(OF)
#------------------------------------------------------------------------------#
main.o: main.c
	$(CC) $(CFLAGS) -c main.c -o main.o
#------------------------------------------------------------------------------#
errors.o: errors.c errors.h
	$(CC) $(CFLAGS) -c errors.c -o errors.o
#------------------------------------------------------------------------------#
symtable.o: symtable.c symtable.h
	$(CC) $(CFLAGS) -c symtable.c -o symtable.o
#------------------------------------------------------------------------------#
str.o: str.c str.h
	$(CC) $(CFLAGS) -c str.c -o str.o
#------------------------------------------------------------------------------#
scanner.o: scanner.c scanner.h
	$(CC) $(CFLAGS) -c scanner.c -o scanner.o
#------------------------------------------------------------------------------#
generator.o: generator.c generator.h
	$(CC) $(CFLAGS) -c generator.c -o generator.o
#------------------------------------------------------------------------------#
parser.o: parser.c parser.h
	$(CC) $(CFLAGS) -c parser.c -o parser.o
#------------------------------------------------------------------------------#
expression.o: expression.c expression.h
	$(CC) $(CFLAGS) -c expression.c -o expression.o
#------------------------------------------------------------------------------#
stack.o: stack.c stack.h
	$(CC) $(CFLAGS) -c stack.c -o stack.o
#------------------------------------------------------------------------------#
clear-of:
	rm -f $(OF)
#-------------------------------- END OF FILE ---------------------------------#
