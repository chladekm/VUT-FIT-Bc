
/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Implementace zásobníku.
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

/*
  POZNAMKY
  1. Implementace zasobniku pro syntaktickou analyzu vyrazu
*/

#ifndef STACK_H_INCLUDED
#define STACK_H_INCLUDED

#include <stdbool.h>
#include "scanner.h"
#include "expression.h"

/**
 * @struct Polozka zasobniku
 */
typedef struct StackItem {
	Token_type type;
	bool dollar;
	tAtribut token_atribut;
	struct StackItem *ptr_next;
} *tStack_item;

/**
 * @struct Struktura zasobniku
 */
typedef struct {
	tStack_item top;
} tStack;


/**
 * Funkce inicializuje zasobnik
 *
 * @param symtab Ukazatel na zasobnik
 * @return true pokud inicializace byla uspesna
 */
bool stack_init (tStack *stack);

/**
 * Function returns if stack is empty
 *
 * @param stack Ukazatel na zasobnik
 * @return true pokud je zasobnik prazdny, jinak true
 */
bool stack_empty (const tStack *stack);

/**
 * Funkce vraci položku z vrcholu zásobníku
 *
 * @param stack Ukazatel na zasobnik
 * @return vraci ukazatel na polozku z vrcholu zasobniku
 */
struct StackItem* stack_top (tStack *stack);

/**
 * Funkce vyjme vrchní polozku ze zasobniku
 *
 * @param stack Ukazatel na zasobnik
 * @return false pokud chci vyjmout z prazdneho zasobniku, true pokud jsem uspesne vyjmul
 */
bool stack_pop (tStack *stack);

/**
 * Funkce uvolni cely zasobik
 *
 * @param stack Ukazatel na zasobnik
 */
void stack_free (tStack *stack);

/**
 * Funkce vlozi token na zasobnik
 *
 * @param stack Ukazatel na zasobnik
 * @return false pokud se snazim vlozit prazdny item, jinak true
 */
int stack_push (tStack *stack, tToken *token);

#endif //STACK_H_INCLUDED
