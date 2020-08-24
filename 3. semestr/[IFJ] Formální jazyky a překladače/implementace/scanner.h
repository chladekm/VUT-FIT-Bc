/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Deklaracia operaci a struktur scanneru
 *
 * @author Martin Chládek <xchald16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#ifndef SCANNER_H_INCLUDED
#define SCANNER_H_INCLUDED

#include <stdlib.h>
#include <stdio.h>

#include "str.h"

#define KWORDS_COUNT 9
#define OP_COUNT 13
#define CHAR_OP_COUNT 8
#define SEP_COUNT 6
#define DEF_FUNC_COUNT 8

#define LEX_OK 0

typedef enum{
	T_ERR,	//0
	T_EOF,
	T_EOL,
	T_ID,
	T_INT,
	T_FLOAT,	//5
	T_STRING,
	//keywords
	T_DEF,
	T_DO,
	T_ELSE,
	T_END,	//10
	T_IF,
	T_NOT,
	T_NIL,
	T_THEN,
	T_WHILE,	//15
	//operators
	T_PLUS,
	T_MINUS,
	T_MUL,
	T_DIV,
	T_EQSIGN, //equal sign	20
	T_LESS,
	T_MORE,
	T_LE,	//less or equal
	T_ME,	//more or equal
	T_EQUAL,	//25
	T_NEQUAL,
	//separators
	T_SLASH,
	T_COMMA,
	T_SEMICOL,
	T_LBRAC,	//30
	T_RBRAC,
	T_OPENBRAC,
	T_CLOSEBRAC,
	// default functions
	T_INPUTI,
	T_INPUTS,	//35
	T_INPUTF,
	T_PRINT,
	T_LENGTH,
	T_SUBSTR,
	T_ORD,		//40
	T_CHR
}Token_type;

typedef union {
	int int_value;
	double float_value;
	char *str_value;
} tAtribut;

typedef struct{
	Token_type type;
	tAtribut token_atribut;
}tToken;

/* Definice pomocnyh promenych */
inf_string *buffer;
inf_string buffer_struct;
FILE *source_file;

/* Inicializuje a nastavi soubor */
void set_file(FILE *fr);

/* Inicializuje a nastavi buffer */
void set_buffer(inf_string *string);

/**
 * @brief Funkce ktera rozhodne zda se jedna o klicovy identifikator jazyka
 *
 * @param term zkoumany string
 *
 * @return 1 pokud ano
 * @return 0 pokud ne
 */
int is_keyword (inf_string *term);

/**
 * @brief Funkce ktera rozhodne zda se jedna o defualt funkci jazyka
 *
 * @param term zkoumany string
 *
 * @return 1 pokud ano
 * @return 0 pokud ne
 */
int is_def_func(inf_string *term);

/**
* @brief Funkce ktera rozhodne zda muze byt nacteny znak soucasti operatoru
*
* @param c Zkoumany znak
*
* @return 1 pokud muze byt soucasti operatoru
* @return 0 pokud ne
*/
int is_operator (char c);

/**
 * @brief Funkce ktera rozhodne zda muze byt nacteny znak soucasti separatoru
 *
 * @param c Zkoumany znak
 *
 * @return 1 pokud muze byt soucasti separatoru
 * @return 0 pokud ne
 */
int is_separator (char c);

/**
 * @brief Funkce, ktera smaze obsah string a vrati prislusnou navratovou hodnotu.
 *
 * @param buffer dynamicky retezec k uvolneni
 *
 * @return navratova hodnota
 */
int free_res (inf_string *buffer, int exit_code);

/**
 * @brief Funkce pro prevod retezce na int.
 *
 * @param buffer obsahuje dynamicky retezec pro prevod
 *
 * @return 0 neuspech
 * @return 1 uspech
 */
int process_integer (inf_string *buffer, tToken *token);

/**
 * @brief Funkce pro prevod retezce na float.
 *
 * @param buffer obsahuje dynamicky retezec pro prevod
 *
 * @return 0 neuspech
 * @return 1 uspech
 */
int process_float (inf_string *buffer, tToken *token);

/**
 * @brief Funkce ktera uvolni zdroje scanneru
 *
 * @return LEX_OK Kdyz je vsechno v poradku
 */
int clear_scanner ();

/**
 * @brief Hlavni funkce scanneru, pomoci promene token se vraci lexem
 *
 * @param token Struktura predavana odkazem, pomoci ktere se vraci lexem
 * @param buffer Dynamicky alokovany retezec do ktereho se nacitaji lexemy
 *
 * @return LEX_OK pokud v poradku
 * @return LEX_ERROR pokud nastane chyba
 * @return EOF pokud dojde na konec souboru
 *
 */
int get_token (tToken *token);

#endif //SCANNER_H_INCLUDED
