/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Implementacia hash table.
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#include <stdlib.h>
#include <stdbool.h>
#include <string.h>
#include "symtable.h"
#include "parser.h"

/******************************************************************************/
/*                            FUNCTION DEFINITIONS                            */
/******************************************************************************/

/****************************** hash_function *********************************/
unsigned int DJBHash(char *str) {
  unsigned int hash = 5381;
  int c;
  while ((c = *str++)) {
    hash = ((hash << 5) + hash) + c; /* hash * 33 + c */
  }
  return (hash % HT_SIZE);
}

/****************************** symtable_init *********************************/
void symtable_init(tSymtab symtab, bool *internal_err) {
  /*------------------ Chyba: NULL argumenty funkcie -------------------------*/
  if (symtab == NULL) {
    *internal_err = true;
    return;
  }

  /*------------- Inicializacia ukazatelov v tabulke na NULL -----------------*/
  for (int i = 0; i < HT_SIZE; i++) {
    symtab[i] = NULL;
  }
}

/***************************** symtable_insert ********************************/
tTabItem *symtable_insert(tSymtab symtab, tToken *token, bool *internal_err) {
  /*------------------ Chyba: NULL argumenty funkcie -------------------------*/
  if ((symtab == NULL) || (token == NULL)) {
    *internal_err = true;
    return NULL;
  }

  int index = DJBHash(token->token_atribut.str_value);
  tTabItem *item_ptr = symtab[index];

  /*---------- Zistenie , ci sa identifikator uz nachadza v tabulke ----------*/
  while ((item_ptr != NULL)) {
    /*------------------ Ak sa kluce rovnaju vraciam NULL --------------------*/
    if (strcmp(token->token_atribut.str_value, item_ptr->key) == 0) {
      return NULL;
    }
    else {
      item_ptr = item_ptr->next_item;
    }
  }
  /*------------ Identifikator sa nenachadza v tabulke symbolov --------------*/

  /*-------------------- Alokacia pamati pre novy item -----------------------*/
  tTabItem *newItem = (tTabItem*) malloc(sizeof(tTabItem));
  if (newItem == NULL) {
    *internal_err = true;
    return NULL;
  }
  /*--------------------- Alokacia pamati pre kluc (ID) ----------------------*/
  newItem->key = (char*) malloc(sizeof(char) * (strlen(token->token_atribut.str_value)+1));
  if (newItem->key == NULL) {
    free(newItem);
    *internal_err = true;
    return NULL;
  }
  newItem->symbol_type = S_TYPE_UNDEF;

  /*------------------------ Inicializacia kluca (ID) ------------------------*/
  strcpy(newItem->key, token->token_atribut.str_value);

  newItem->next_item = symtab[index];
  symtab[index] = newItem;
  return newItem;
}

/***************************** symtable_search ********************************/
tTabItem *symtable_search(tSymtab symtab, tToken *token, bool *internal_err) {
  /*------------------ Chyba: NULL argumenty funkcie -------------------------*/
  if (symtab == NULL || token == NULL) {
    *internal_err = true;
    return NULL;
  }

	unsigned index = DJBHash(token->token_atribut.str_value);
	tTabItem *item_ptr = symtab[index];

  /*----------------- Vyhladavanie identifikatora v tabulke ------------------*/
	while (item_ptr != NULL) {
    /*------------------------ Identifikator najdeny -------------------------*/
		if (strcmp(token->token_atribut.str_value, item_ptr->key) == 0) {
        return item_ptr;
    }

		item_ptr = item_ptr->next_item;
	}
  /*------------------------- Identifikator nenajdeny ------------------------*/
	return NULL;
}

bool symtable_foreach(tSymtab symtab, bool *internal_err) {
  if (symtab == NULL) {
    *internal_err = true;
    return false;
  }

  /*--- Prechod cez celu tabulku a overenie ze je kazda funkcia definovana ---*/
  for (int i=0; i < HT_SIZE; i++) {
    tTabItem *item_ptr = symtab[i];

    while (item_ptr != NULL) {
      if (item_ptr->func_data.defined == false) {
        return false;
      }

      item_ptr = item_ptr->next_item;
    }
  }
  return true;
}

/****************************** symtable_free *********************************/
void symtable_free(tSymtab symtab, bool *internal_err) {
  /*-------------------- Chyba: NULL argumenty funkcie -----------------------*/
  if (symtab == NULL) {
    *internal_err = true;
    return;
  }

	/*---------- Prechod cez celu tabulku a vymazanie kazdej polozky -----------*/
	for (int i=0; i < HT_SIZE; i++) {
		tTabItem *item_ptr = symtab[i];

		while (item_ptr != NULL) {
			tTabItem *prev_item_ptr = item_ptr;
			item_ptr = item_ptr->next_item;

			free(prev_item_ptr);
		}
    symtab[i] = NULL;
	}
}
