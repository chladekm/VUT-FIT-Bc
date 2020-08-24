/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Deklaracia opreacii na pracu s nekonecnym stringom
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#ifndef STRING_H_INCLUDED
#define STRING_H_INCLUDED

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#define STR_ALLOC_BLOCK 32

#define STR_OK 0
#define STR_ERR 1

typedef struct{
	char *string;
	unsigned int strSize;
	unsigned int strAllocatedSize;
}inf_string;

/**
 * @brief Funkce pro vytvoreni noveho retezce
 *
 * @param s Vytvareny retezec
 *
 * @return Uspesnot operace
 */
int strInit (inf_string *s);

/**
 * @brief Funkce pro uvolneni pameti retezce
 *
 * @param s Uvolnovany retezec
 */
void strFree (inf_string *s);

/**
 * @brief Funkce pro vymazani retezce
 *
 * @param s Mazany retezec
 */
void strClear (inf_string *s);

/**
 * @brief Funkce pro pridani znaku do retezce
 *
 * @param s Aktualizovany retezec
 * @param c Pridavany znak
 *
 * @return Uspesnost operace
 */
int strAddChar (inf_string *s, char c);

/**
 * @brief Funkce pro pridani retezce do bufferu
 *
 * @param s Aktualizovany retezec
 * @param add_s pridavany retezec
 *
 * @return Uspesnost operace
 */
int strAddString (inf_string *s, char *add_s);


/**
 * @brief Funkce co vymaze posledni znak retezce
 *
 * @param s Aktualizovany retezec
 *
 * @return Smazany znak
 */
char strDelChar (inf_string *s);

#endif
