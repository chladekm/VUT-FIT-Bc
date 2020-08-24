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

#include <stdlib.h>
#include <stdbool.h>
#include "stack.h"
#include "scanner.h"

/******************************************************************************/
/*                            DEFINICE FUNKCI                                 */
/******************************************************************************/

/****************************** stack_init *********************************/
bool stack_init (tStack *stack)
{
	if(stack != NULL) {

		stack->top = NULL;

		tStack_item item = stack->top;
		
		item = malloc(sizeof (struct StackItem));

		if(item == NULL)
			return false;

		/* Konstanta - musi  */
		item->type = DOLLAR_ENUM_VALUE;
		item->dollar = true;
		item->ptr_next = NULL;

		stack->top = item;

		if (stack->top == NULL) { 
			return false;
		}
		else {
			return true;
		}
	}
	else {
		return false;
	}
}

/****************************** stack_empty ********************************/
bool stack_empty (const tStack *stack)
{
	if (stack->top == NULL) {
		return true;
	}
	else {
		return false;
	}
}

/****************************** stack_top **********************************/
struct StackItem* stack_top (tStack *stack)
{
	if (stack_empty(stack) == true) {
		return false;
	}
	else {
		tStack_item *item = &stack->top;

		return *item;
	}
}

/****************************** stack_pop **********************************/
bool stack_pop (tStack *stack)
{
	if (stack_empty(stack) == true) { 
		return false;
	}
	else {
		tStack_item item = stack->top;
		stack->top = item->ptr_next;
		free(item);
		return true;
	}
}

/****************************** stack_pop **********************************/
void stack_free (tStack *stack)
{
	while(!stack_empty(stack))
	{ 
		stack_pop (stack);
	}
}

/****************************** stack_push *********************************/
int stack_push (tStack *stack, tToken *token)
{
	tStack_item item;

	item = malloc(sizeof (struct StackItem));

	if (item == NULL) {
		return false; 
	}

	if(
		(token->type != T_ID) &&
		(token->type != T_INT) &&
		(token->type != T_FLOAT) &&
		(token->type != T_STRING) && 
		(token->type != T_NIL) && 
		(token->type != T_PLUS) &&
		(token->type != T_MINUS) &&
		(token->type != T_MUL) &&
		(token->type != T_DIV) &&
		(token->type != T_LESS) &&
		(token->type != T_MORE) &&
		(token->type != T_LE) &&
		(token->type != T_ME) &&
		(token->type != T_EQUAL) &&
		(token->type != T_NEQUAL) &&
		(token->type != T_LBRAC) &&
		(token->type != T_RBRAC)
	)
	{
		item->dollar = true;
		item->type = token->type;
	}
	else
	{
		item->dollar = false;
		item->type = token->type;
	}

	item->token_atribut = token->token_atribut;

	/** Vkladam prvni polozku **/
	if(stack_empty(stack) == true) {
		item->ptr_next = NULL;
		stack->top = item;
	}
	/** V zasobniku uz jsou nejake polozky **/
	else {
		item->ptr_next = stack->top;
		stack->top = item;
	}

	return true;
}



