/**
 * Projekt: Implementace p�eklada�e imperativn�ho jazyka IFJ18
 *
 * @brief Implementacia operacii na pracu s nekonecnym stringom
 *
 * @author Martin Chl�dek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krut� <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Kr�l <xkrulm00@stud.fit.vutbr.cz>
 * @author Bo�ek Reich <xreich06@stud.fit.vutbr.cz>
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "str.h"

/*
* Funkce inicializujici nekonecny retezec
*/
int strInit (inf_string *s)
{
	if ((s->string = (char *) malloc(STR_ALLOC_BLOCK)) == NULL)
			return STR_ERR;
	s->string[0] = '\0';
	s->strSize = 0;
	s->strAllocatedSize = STR_ALLOC_BLOCK;

	return STR_OK;
}

/*
* Funkce co uvolni pamet alokovanou pro nekonecny retezec
*/
void strFree (inf_string *s)
{
	free(s->string);
}

/*
* Funkce, ktera vzmaze obsah retezce v nekonecnem retezci
*/
void strClear (inf_string *s)
{
	strcpy(s->string, "");
	s->strSize = 0;
}

/*
* Funkce, ktera prida znak do retezce v nekonecnem retezci
*/
int strAddChar (inf_string *s, char c)
{
	if (s->strSize + 1 >= s->strAllocatedSize)
	{
		if((s->string = (char *) realloc(s->string, s->strAllocatedSize + STR_ALLOC_BLOCK)) == NULL)
			return STR_ERR;
		s->strAllocatedSize += STR_ALLOC_BLOCK;
	}
	s->string[s->strSize] = c;
	s->string[s->strSize + 1] = '\0';
	s->strSize++;
	return STR_OK;
}

/*
* Funkce co pripoji predavany retezec do retezce v nekonecnem retezci
*/
int strAddString(inf_string *s, char *add_s)
{
	if (s->strSize + strlen(add_s) + 1 >= s->strAllocatedSize)
	{
		if((s->string = (char *) realloc(s->string, s->strSize + strlen(add_s) + STR_ALLOC_BLOCK)) == NULL)
			return STR_ERR;
		s->strAllocatedSize = s->strSize + strlen(add_s) + STR_ALLOC_BLOCK;
	}

	s->strSize += strlen(add_s);
	strcat(s->string, add_s);

	return STR_OK;
}

/*
* Funkce, ktera vymaze posledni znak retezce v nekonecnem retezci
*/
char strDelChar (inf_string *s)
{
	if (s->strSize == 0)
		return STR_ERR;

	char c = s->string[s->strSize];
	s->string[s->strSize] = '\0';
	s->strSize--;

	return c;
}
