/**
 *
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Implementace syntaktické a sémantické analýzy výrazů jazyka IFJ18.
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#ifndef EXPRESSION_H_INCLUDED
#define EXPRESSION_H_INCLUDED

#include <stdlib.h>
#include <stdio.h>

#include "parser.h"

#define PREC_TAB_SIZE 7
#define NUM_ID_OPERAND 1
#define NUM_EXP_OPERANDS 3
#define DOLLAR_ENUM_VALUE 999

#define EXP_ERROR(value)							\
        do{                                                                     \
                stack_free(exp_stack);                                          \
                return value;                                                   \
        } while(0);

/** PT_ - Precedencni tabulka **/
/** int hodnoty enum budou pouzity jako index v prec_table poli **/
typedef enum {
        PT_PLUS_MIN = 0,        // +- int value 0 
        PT_MUL_DIV = 1,         // */ int value 1 
        PT_ID = 2,              // i  int value 2
        PT_REL_OP = 3,          // r  int value 3
        PT_LBRAC = 4,           // (  int value 4
        PT_RBRAC = 5,           // )  int value 5
        PT_DOLLAR = 6           // $  int value 6
} PT_symbol_index;


/** NT - Nonterminal **/
/** Vyctovy typ pravidel **/
typedef enum {
        OPERAND,                // E -> i
        LBRAC_NT_RBRAC,	        // E -> (E)
        NT_PLUS_NT,             // E -> E + E
        NT_MINUS_NT,            // E -> E - E
        NT_MUL_NT,              // E -> E * E
        NT_DIV_NT,              // E -> E / E
        NT_EQ_NT,               // E -> E == E
        NT_NEQ_NT,              // E -> E != E
        NT_LESS_NT,             // E -> E < E
        NT_MORE_NT,             // E -> E > E
        NT_LE_NT,               // E -> E <= E
        NT_ME_NT,               // E -> E >= E
        NOT_A_RULE,             // Neni pravidlo
} PT_rules;

/**
 * Function funkce provadi syntaktickou kontrolu vyrazu
 *
 * @param par_data Ukazatel na strukturu obsahujici data z SA konstrukci
 * @return vraci COMP_SUCC v pripade uspechu, jinak prislusny error kod
 */
int expression(tPData *par_data);

#endif //EXPRESSION_H_INCLUDED
