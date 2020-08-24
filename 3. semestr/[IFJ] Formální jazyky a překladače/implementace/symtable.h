/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Deklaracia hash table.
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

/*
  POZNAMKY
  1. Doporucenie vytvorit 2 instancie tabulky symbolov, globalnu a lokalnu.
  2. Struktura datovych typov sa bude este menit, teraz mi to pride prilis zlozite.
  3. Datove typy v rozhranii funkcii sa budu menit podla potreby. Zatial nastrel.
  4. Funkcie sa budu doplnat podla potreby. Zatial neviem odhadnut.
*/

#ifndef SYMTABLE_H_INCLUDED
#define SYMTABLE_H_INCLUDED

#include <stdbool.h>
#include "scanner.h"

#define HT_SIZE 5381

/**
 * @enum Typ symbolu
 */
typedef enum {
  S_TYPE_UNDEF,
  S_TYPE_VARIABLE,
  S_TYPE_FUNCTION,
} tSymbolType;

/**
 * @enum Datovy typ
 */
typedef enum {
  D_TYPE_NIL,
  D_TYPE_INT,
  D_TYPE_FLOAT,
  D_TYPE_STRING,
} tDataType;

/**
 * @struct Data funkcie
 */
typedef struct {
  int arg_num;   /* Pocet parametrov funkcie */
  bool defined;  /* Je funkcia definovana? (nutne nemusi byt pri volani) */
} tFuncData;

/**
 * @struct Polozka tabulky symbolov
 */
typedef struct tab_item {
  char *key;                   /* Nazov polozky (vyhladavaci kluc) */
  tSymbolType symbol_type;     /* Urcuje ci je polozka funkcia/premenna */
  tFuncData func_data;         /* Data funkcie */
  struct tab_item *next_item;  /* Ukazatel na dalsiu polozku */
} tTabItem;

 /* Typ: Pole ukazatelov na item tabulky (Tabulka symbolov) */
typedef struct tab_item *(tSymtab[HT_SIZE]);

/**
 * Hashovacia funkcia - Daniel J. Bernstein
 *
 * @param str Retazec, ktory sa bude hashovat
 * @return Vysledny index hashovacie funkcie do hashovacej tabulky
 */
unsigned int DJBHash(char *str);

/**
 * Funkcia inicializuje tabulku symbolov
 *
 * @param symtab Tabulka symbolov
 * @param internal_err Ukazatel na signalizaciu internej chyby
 */
void symtable_init(tSymtab symtab, bool *internal_err);

/**
 *  Funkcia prida polozku do tabulky symbolov podla vstupneho tokenu
 *
 * @param symtab Tabulka symbolov
 * @param token Ukazatel na vstupny token
 * @param internal_err Ukazatel na signalizaciu internej chyby
 * @return Ukazatel na pridanu polozku, NULL v pripade neuspechu
 */
tTabItem *symtable_insert(tSymtab symtab, tToken *token, bool *internal_err);

/**
 *  Funkcia vyhlada polozku v tabulke symbolov podla vstupneho tokenu
 *
 * @param symtab Tabulka symbolov
 * @param token Ukazatel na vstupny token
 * @param internal_err Ukazatel na signalizaciu internej chyby
 * @return Ukazatel na najdenu polozku, NULL v pripade neuspechu
 */
tTabItem *symtable_search(tSymtab symtab, tToken *token, bool *internal_err);

/**
 * Funkcia skontroluje ci su vsetky funkcie v tabulke symbolov definovane
 *
 * @param symtab Tabulka symbolov
 * @param internal_err Ukazatel na signalizaciu internej chyby
 */
bool symtable_foreach(tSymtab symtab, bool *internal_err);

/**
 * Funkcia uvolni vsetky polozky v tabulke symbolov
 *
 * @param symtab Tabulka symbolov
 * @param internal_err Ukazatel na signalizaciu internej chyby
 */
void symtable_free(tSymtab symtab, bool *internal_err);

#endif //SYMTABLE_H_INCLUDED
