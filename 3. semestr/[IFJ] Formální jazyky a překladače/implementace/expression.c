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
 
 #include <stdio.h>
 #include <stdlib.h>
 
 #include "expression.h"
 #include "stack.h"
 #include "scanner.h"
 #include "errors.h"
 #include "parser.h"
 #include "generator.h"

/**
 * Symboly precedencni tabulky
 */
 typedef enum {
	S,	// < push
	E,	// = equal
	L,	// > pull
	R	// # error
 } prec_tab_type;
 
/**
 * Precedencni tabulka
 */
 int prec_table [PREC_TAB_SIZE][PREC_TAB_SIZE] = {
//	 +- |*/ |i |r |( |) |$
	{ L,  S, S, L, S, L, L },	// +-
	{ L,  L, S, L, S, L, L },	// */
	{ L,  L, R, L, R, L, L },	// i
	{ S,  S, S, R, S, L, L }, 	// r
	{ S,  S, S, S, S, E, R },   // (
	{ L,  L, R, L, R, L, L },   // )
	{ S,  S, S, S, S, R, R }	// $
 };
	
/**
 * @brief Lokalni funkce, vraci index symbolu v zavisloti na danem tokenu
 */
static PT_symbol_index get_PT_symbol_index(Token_type type)
{
	switch(type)
	{
		case T_ID:
			return PT_ID;
		case T_INT:
			return PT_ID;
		case T_FLOAT:
			return PT_ID;
		case T_STRING:
			return PT_ID;
		case T_NIL:
			return PT_ID;
		case T_PLUS:
			return PT_PLUS_MIN;
		case T_MINUS:
			return PT_PLUS_MIN;
		case T_MUL:
			return PT_MUL_DIV;
		case T_DIV:
			return PT_MUL_DIV;
		case T_LESS:
			return PT_REL_OP;
		case T_MORE:
			return PT_REL_OP;
		case T_LE:
			return PT_REL_OP;
		case T_ME:
			return PT_REL_OP;
		case T_EQUAL:
			return PT_REL_OP;
		case T_NEQUAL:
			return PT_REL_OP;
		case T_LBRAC:
			return PT_LBRAC;
		case T_RBRAC:
			return PT_RBRAC;
		default:
			return PT_DOLLAR;
	}
}

/**
 * @brief Lokalni funkce, ktera vraci typ pravidla, ktere ma byt pouzito
 */
static PT_rules get_PT_rule (int number_of_operands, tStack_item item1, tStack_item item2, tStack_item item3, unsigned int TERMINAL)
{
	switch(number_of_operands)
	{
		case NUM_ID_OPERAND: // 1
			
			// E -> i
			if((item1->type == T_ID) || (item1->type == T_INT) || (item1->type == T_FLOAT) || (item1->type == T_STRING) || (item1->type == T_NIL)) //Pokud je to id, int, float, string nebo nil
				return OPERAND;
			else
				return NOT_A_RULE;

		case NUM_EXP_OPERANDS: // 3
			
			// E -> (E)
			if(item1->type == T_LBRAC &&  item2->type != TERMINAL && item3->type == T_RBRAC)
				return LBRAC_NT_RBRAC;
			
			if(item1->type != TERMINAL && item3->type != TERMINAL)
			{
				switch(item2->type)
				{
					// E -> E + E
					case T_PLUS:
						return NT_PLUS_NT;
					
					// E -> E - E
					case T_MINUS:
						return NT_MINUS_NT;

					// E -> E * E
					case T_MUL:
						return NT_MUL_NT;
					
					// E -> E / E	
					case T_DIV:
						return NT_DIV_NT;
					
					// E -> E == E
					case T_EQUAL:
						return NT_EQ_NT;
					
					// E -> E != E 
					case T_NEQUAL:
						return NT_NEQ_NT;

					// E -> E < E
					case T_LESS:
						return NT_LESS_NT;

					// E -> E > E
					case T_MORE:
						return NT_MORE_NT;

					// E -> E <= E
					case T_LE:
						return NT_LE_NT;

					// E -> E >= E
					case T_ME:
						return NT_ME_NT;

					// Nekorektní relacni operator
					default:
						return NOT_A_RULE;	
				}

			return NOT_A_RULE;
			}
	}
	return NOT_A_RULE;
	
}

/**
 * @brief Funkce co mi vyresi podvyraz v ramci vyrazu, vysledek predava skrz final, resi pretypovani
 */
static int resolve_subexp (int rule, tStack_item item1, tStack_item item2, tStack_item item3, tToken *final)
{
	if (rule == NOT_A_RULE || item1 == NULL || item2 == NULL || item3 == NULL)
	{
		final = NULL;
		return SYNTAX_ERROR; 
	}

	// ID op ID
	if(item1->type == T_ID && item3->type == T_ID) {
		final->type = T_ID;
		return COMP_SUCC;
	}
	// ID op COKOLIV
	else if(item1->type == T_ID && item3->type != T_ID) {
		final->type = item3->type;
		return COMP_SUCC;
	}
	// COKOLIV op ID
	else if(item1->type != T_ID && item3->type == T_ID) {
		final->type = item1->type;
		return COMP_SUCC;
	}

	switch (rule)
	{
		// ( E )
		case LBRAC_NT_RBRAC:
			
			final->type = item2->type;
			break;

		// E + E || E - E || E * E
		case NT_PLUS_NT:
		case NT_MINUS_NT:
		case NT_MUL_NT:
			//  STRING + STRING
			if (rule == NT_PLUS_NT && item1->type == T_STRING && item3->type == T_STRING) {
				final->type = T_STRING;
			}
			// STRING - STRING || STRING * STRING
			else if (item1->type == T_STRING && item3->type == T_STRING) { 
				return SEM_ERROR_COMPAT;
			}
			// STRING + INT || STRING + FLOAT || INT * STRING || FLOAT * STRING || ...
			else if (item1->type == T_STRING || item3->type == T_STRING) { 
				return SEM_ERROR_COMPAT;
			}
			// INT + INT ||  INT - INT || INT * INT
			else if(item1->type == T_INT && item3->type == T_INT) {
				final->type = T_INT;
			}
			// NIL + NIL || NIL - NIL || NIL * NIL
			else if(item1->type == T_NIL && item3->type == T_NIL) {
				return SEM_ERROR_COMPAT;
			}
			// NIL + INT || INT + NIL || FLOAT - NIL || NIL * INT || NIL * FLOAT || ...
			else if(item1->type == T_NIL || item3->type == T_NIL) {
				return SEM_ERROR_COMPAT;
			}
			// INT + FLOAT || INT - FLOAT || INT * FLOAT
			else if (item1->type == T_INT && item3->type == T_FLOAT) {
				/** Pretypovani prvniho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			} 
			// FLOAT + INT || FLOAT - INT || FLOAT * INT
			else if (item1->type == T_FLOAT && item3->type == T_INT) {
				/** Pretypovani druheho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}
			// FLOAT + FLOAT || FLOAT - FLOAT || FLOAT * FLOAT
			else {
				final->type = T_FLOAT;
			}
			
			break;

		// E / E
		case NT_DIV_NT:
			// STRING / STRING
			if (item1->type == T_STRING || item3->type == T_STRING) {
				return SEM_ERROR_COMPAT;
			}
			// INT / 0 || FLOAT / 0
			else if ((item3->type == T_INT && item3->token_atribut.int_value == 0) || (item3->type == T_FLOAT && item3->token_atribut.float_value == 0)) { 
				// Deleni nulou
				return ZERO_DIV_ERROR;
			}
			// INT / INT
			else if (item1->type == T_INT && item3->type == T_INT) {
				final->type = T_INT;
			}
			// NIL / NIL
			else if(item1->type == T_NIL && item3->type == T_NIL) {
				return SEM_ERROR_COMPAT;
			}
			// NIL / INT || INT / NIL || NIL / STRING || FLOAT / NIL || ...
			else if(item1->type == T_NIL || item3->type == T_NIL) {
				return SEM_ERROR_COMPAT;
			}
			// INT / FLOAT
			else if (item1->type == T_INT && item3->type == T_FLOAT) {
				/** Pretypovani prvniho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}
			// FLOAT / INT
			else if (item1->type == T_FLOAT && item3->type == T_INT) {
				/** Pretypovani druheho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}
			// FLOAT / FLOAT
			else {
				final->type = T_FLOAT;
			}

			break;

		// E == E || E != E
		case NT_EQ_NT:
		case NT_NEQ_NT:
			// INT == FLOAT || INT != FLOAT
			if (item1->type == T_INT && item3->type == T_FLOAT) {
				/** Pretypovani prvniho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}
			// FLOAT == INT || FLOAT != INT 
			else if (item1->type == T_FLOAT && item3->type == T_INT) {
				/** Pretypovani druheho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}

			break;

		// E < E || E > E || E <= E || E >= E
		case NT_LESS_NT:
		case NT_MORE_NT:
		case NT_LE_NT:
		case NT_ME_NT:
			// INT rel FLOAT
			if (item1->type == T_INT && item3->type == T_FLOAT) {
				/** Pretypovani prvniho operandu INT2FLOAT **/
				final->type = T_FLOAT;		
			}
			// FLOAT rel INT 
			else if (item1->type == T_FLOAT && item3->type == T_INT) {
				/** Pretypovani druheho operandu INT2FLOAT **/
				final->type = T_FLOAT;
			}
			// INT rel INT
			else if (item1->type == T_INT && item3->type == T_INT) {
				final->type = T_INT;
			}
			// FLOAT rel FLOAT
			else if (item1->type == T_FLOAT && item3->type == T_FLOAT) {
				final->type = T_FLOAT;
			}
			// STRING rel STRING
			else if (item1->type == T_STRING && item3->type == T_STRING) {
				final->type = T_STRING;
			}

			// STRING rel INT || INT rel STRING || STRING rel FLOAT || FLOAT rel STRING || STRING rel NIL || NIL rel STRING
			// NIL rel INT || INT rel NIL || NIL rel FLOAT || FLOAT rel NIL || NIL rel NIL || NIL rel NIL
			else {
				return SEM_ERROR_COMPAT;
			}
				
			break;
	}

	return 0;
}

/**
 * @brief Funkce volá generování kódu podle pravidla
 */
void exp_generate_code (int rule, tPData *par_data) {

	switch(rule)
	{	
		// E -> (E)
		case LBRAC_NT_RBRAC:
			break;

		// E -> E + E
		case NT_PLUS_NT:
			stack_operations(T_PLUS, par_data->in_while);
			break;

		// E -> E - E
		case NT_MINUS_NT:
			stack_operations(T_MINUS, par_data->in_while);
			break;

		// E -> E * E
		case NT_MUL_NT:
			stack_operations(T_MUL, par_data->in_while);
			break;
		
		// E -> E / E
		case NT_DIV_NT:
			stack_operations(T_DIV, par_data->in_while);
			break;
		
		// E -> E == E
		case NT_EQ_NT:
			par_data->rel_op = true;
			if_func_start(T_EQUAL, par_data->in_while);
			break;
		
		// E -> E != E 
		case NT_NEQ_NT:
			par_data->rel_op = true;
			if_func_start(T_NEQUAL, par_data->in_while);
			break;
		
		// E -> E < E
		case NT_LESS_NT:
			par_data->rel_op = true;
			if_func_start(T_LESS, par_data->in_while);
			break;

		// E -> E > E
		case NT_MORE_NT:
			par_data->rel_op = true;
			if_func_start(T_MORE, par_data->in_while);
			break;
		
		// E -> E <= E
		case NT_LE_NT:
			par_data->rel_op = true;
			if_func_start(T_LE, par_data->in_while);
			break;
		
		// E -> E >= E
		case NT_ME_NT:
			if_func_start(T_ME, par_data->in_while);
			break;
	}
}

/**
 * @brief Hlavni funkce syntakticke analyzy vyrazu
 */
int expression (tPData *par_data) {
	
	tStack e_stack;
	tStack *exp_stack = &e_stack;

	if (!stack_init (exp_stack))
		EXP_ERROR(INTERNAL_ERROR);

	unsigned int TERMINAL;

	/** Po inicializaci je na stacku jen implicitni $ **/
	tStack_item top_stack_item;
	
	/** Nacteni prvku z vrcholu stacku ($) **/
	top_stack_item = stack_top(exp_stack);

	/** Pomocny item pri case L pri vyhledavani itemu co muze byt TERMINAL **/
	tStack_item terminal_item;

	/** Pomocna booleovska hodnota pri prohledavani stacku **/
	bool terminal_found;

	/** Dolar by mel byt na zasobniku za kazdou cenu, ale pro jistotu test **/
	if(top_stack_item->dollar != true)
		EXP_ERROR(INTERNAL_ERROR);

	/** Muj implicitni terminal na porovnavani bude $, ktery jsem nacetl ze zasobniku **/
	TERMINAL = top_stack_item->type;
	
	/** Promenna, ktera urcuje, zda je zpracovani vyrazu dokonceno **/
	bool expression_done = false;
	
	/** Udava, jestli je vyraz pravidlo, nebo ne **/ 
	int rule_result = 0;

	/** Udava, jestli vse problehlo v poradku, nebo bude vracen error **/
	int error_returning = 0;

	/** Token, ktery bude uchovavat vysledny datovy typ vyrazu a bude pushnut **/
	tToken final;

	/** Kdybych mel na vstupu jen jeden token **/
	final.type = 0;

	/** Aktualni token, ktery byl nacten, prvni token je predavam z SA konstrukci **/
	tToken actual_token = par_data->token;
	
	/** Itemy, ktere budou slouzit pro zpracovani vyrazu **/
	tStack_item item1 = NULL, item2 = NULL, item3 = NULL;

	/** Pomocna promenna, ktera mi pomuze zajistit, ze jsem nenacetl 2 relacni operatory **/
	bool one_rel_operator_already_readed = false;

	/** Pomocna promenna, kontroluje uzavreni vsech zavorek **/
	int brackets_not_closed = 0;

	/** Pomocny token, poslouzi kdyz mam na vstupu jen 1 cislo **/
	tToken previous_token;

	while(!expression_done)
	{

		/** Nacetl jsem $ -> posledni switch -> zpracovany vyraz **/
		if(get_PT_symbol_index(actual_token.type) == PT_DOLLAR){	
			expression_done = true;

			/** Kontrola, zda mam u konce vyrazu uzavrene vsechny zavorky **/
			if(brackets_not_closed != 0)
				EXP_ERROR(SYNTAX_ERROR);

			/** Do SA konstrukci predam posledni token **/
			par_data->token = actual_token; 
			par_data->extra_t_used = false;

			actual_token.type = DOLLAR_ENUM_VALUE;
		}

		/** Kontrola, zda jsem nenacetl 2 relacni operatory **/
		if(get_PT_symbol_index(actual_token.type) == PT_REL_OP) {
			if(one_rel_operator_already_readed == false)
			{
				/** Pokud jsem nacetl relacni operator, ale zpracovavam prirazeni (nemuze byt rel. operator) **/
				if(par_data->in_var_init == true)
					EXP_ERROR(SYNTAX_ERROR);

				/** Prvni nacteny relacni operator **/
				one_rel_operator_already_readed = true;
			}
			else
			{
				/** Nacetl jsem uz druhy relacni operator -> chyba **/
				EXP_ERROR(SYNTAX_ERROR);
			}
		}

		/** Kontrola, zda mam na vstupu vsechny zavorky uzavrene **/
		if(actual_token.type == T_LBRAC) {
			/** Pokazde kdyz nactu "(" prictu 1 **/
			brackets_not_closed = brackets_not_closed + 1;
		}
		else if(actual_token.type == T_RBRAC) {
			/** Pokazde kdyz nactu ")" odectu 1 **/
			brackets_not_closed = brackets_not_closed - 1;
		}

		/** Test, zda promenna byla deklarovana **/
		if(actual_token.type == T_ID)
		{
			bool internal_error = false;
			tTabItem *tab_item = symtable_search(CHOOSE_SYMTAB(par_data->main_table, par_data->loc_table), &actual_token, &internal_error);

			/** Identifikator nebyl deklarovany **/
			if(tab_item == NULL)
			{
				if(internal_error) {
					EXP_ERROR(INTERNAL_ERROR);
				}
				else {
					EXP_ERROR(SEM_ERROR_UNDEF);
				}
			}
			
		}
       	
		switch (prec_table[get_PT_symbol_index(TERMINAL)][get_PT_symbol_index(actual_token.type)]) {
		
			/********************************** PUSH **********************************/
			case S:

					/** Nacetl jsem i -> rovnou provedu pravidlo E -> i **/
					if(get_PT_symbol_index(actual_token.type) == PT_ID) 
					{

						if(!stack_push(exp_stack, &actual_token))
							EXP_ERROR(INTERNAL_ERROR);

						top_stack_item = stack_top(exp_stack);
						
						if(top_stack_item == NULL)
							EXP_ERROR(INTERNAL_ERROR);
						
						/** Kontrola pravidla  E -> i **/
						rule_result = get_PT_rule(NUM_ID_OPERAND, top_stack_item, NULL, NULL, TERMINAL);
						
						/** Generovani kodu push **/
						generate_stack_push(actual_token.type, actual_token.token_atribut, par_data->in_while);
						
						if(rule_result != OPERAND){
							EXP_ERROR(SYNTAX_ERROR);	
						}

						/** TERMINAL bude porad puvodni token, nemuzu mit E jako terminal **/
					}
					else
					{
					/** Pushuju jakykoliv jiny validni token krome i **/
						if (!stack_push(exp_stack, &actual_token))
							EXP_ERROR(INTERNAL_ERROR);
						
						top_stack_item = stack_top(exp_stack);

						/** Novy terminal je z vrcholu zasobniku **/
						TERMINAL = top_stack_item->type;						
					}

					break;

			/********************************** EQUAL **********************************/
			case E:
					/** Podle algoritmu pouze PUSH actual_token a precist dalsi **/
					if (!stack_push(exp_stack, &actual_token))
						EXP_ERROR(INTERNAL_ERROR);

					top_stack_item = stack_top(exp_stack);

					TERMINAL = top_stack_item->type;

					break;
					
			/********************************** PULL **********************************/
			case L:
					/** Pulluju 3 posledni itemy, prostredni by mel byt operator(item2), prvni a treti jsou operandy; neplati u (E) **/
					
					do
					{
						if((get_PT_symbol_index(TERMINAL) == PT_DOLLAR))
						{
							break;
						}

						item1 = top_stack_item;
						item2 = item1->ptr_next;
						item3 = item2->ptr_next;

						/** item1 byl pushnuty jako posledni -> budu ho pushovat jako posledni **/
						rule_result = get_PT_rule(NUM_EXP_OPERANDS, item3, item2, item1, TERMINAL); 
						
						if(rule_result == NOT_A_RULE){
							EXP_ERROR(SYNTAX_ERROR);
						}
						
						error_returning = resolve_subexp(rule_result, item3, item2, item1, &final);

						if(error_returning != 0) 
							EXP_ERROR(error_returning);

						/** Generovani kodu podle pravidla **/
						exp_generate_code(rule_result, par_data);
						
						/** Popnu vsechny tri operanty **/
						for(int i=0; i<3;i++)
							stack_pop(exp_stack);
						
						/** Pushnu vysledny token (ma spravny datovy typ) **/
						if (!stack_push(exp_stack, &final)) 
							EXP_ERROR(INTERNAL_ERROR);

						top_stack_item = stack_top(exp_stack);
						
						/** Do terminal_item nacteme vrsek stacku **/
						terminal_item = top_stack_item;
						

						while (!terminal_found)
						{
							/** Opakuje dokud nenarazi na prvek co muze byt terminal (vse krome PT_ID) **/
							if (get_PT_symbol_index(terminal_item->type) != PT_ID)
							{
								terminal_found = true;
								
								TERMINAL = terminal_item->type;
							}
							else
							{
								terminal_item = terminal_item->ptr_next;
							}
						}

						terminal_found = false;

					}while(prec_table[get_PT_symbol_index(TERMINAL)][get_PT_symbol_index(actual_token.type)] == L);
					

					/** Pushnuti konecne toho tokenu ktery prisel na vstup **/
					if (!stack_push(exp_stack, &actual_token))
								EXP_ERROR(INTERNAL_ERROR);

					top_stack_item = stack_top(exp_stack);
					TERMINAL = top_stack_item->type;

					break;	

			/********************************** ERROR **********************************/
			case R:

					/** Pokud je načten $ -> konec vyrazu **/
					if((get_PT_symbol_index(actual_token.type) == PT_DOLLAR) &&  (get_PT_symbol_index(TERMINAL) == PT_DOLLAR)) 
					{

						top_stack_item = stack_top(exp_stack);

						/** Kdyz mi prijde na vstup "string" 3 5.9 1, bez teto podminky by to proslo (ani jednou neprobehl pull) **/
						if(get_PT_symbol_index(top_stack_item->ptr_next->type) != PT_DOLLAR)
							EXP_ERROR(SYNTAX_ERROR);

						expression_done = true;

						if(final.type == 0) 
		 					final.type = previous_token.type;

		 				break;
					}
					else
						EXP_ERROR(SYNTAX_ERROR);
		
		}

		/** Pokud v SA konstrukci uz byly nacteny 2 tokeny misto standartniho jednoho **/

		if(!expression_done){

			if(par_data->extra_t_used) { 
			
				actual_token = par_data->extra_token;
				par_data->extra_t_used = false;
			}
			else
			{
				/** Uchovavam predchozi token - pouzit kdyz je na vstupu jen jedno cislo **/
				previous_token = actual_token;
					
				get_token(&actual_token);
			}	
		}
		
		/** Vola se az na konci, protoze pro prvni chod je v zasobniku implicitne $ **/
		top_stack_item = stack_top(exp_stack);
	}

	stack_free(exp_stack);

	generate_stack_pop (EXP_RETURN_VAL_ID, 'G', par_data->in_while);

	if(error_returning != 0)
	 	return error_returning;
	 else
		return COMP_SUCC;		
}




