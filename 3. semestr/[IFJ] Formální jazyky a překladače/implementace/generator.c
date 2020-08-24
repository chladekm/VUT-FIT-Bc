/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Generator IFJcode18
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>

#include "str.h"
#include "scanner.h"
#include "generator.h"
#include "expression.h"
#include "errors.h"

/**
 * Vestavene funkce pro generovani IFJcode18
 */
#define DEFAULT_FUNC_LENGTH			\
	"JUMP $end$func$length\n" 		\
	"LABEL length\n"\
	"PUSHFRAME\n" 	\
	"DEFVAR LF@%retval\n"			\
	"DEFVAR LF@str_type\n"			\
	"TYPE LF@str_type LF@%1\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@string\n"	\
	"STRLEN LF@%retval LF@%1\n"		\
	"POPFRAME\n"	\
	"RETURN\n"		\
	"LABEL $end$func$length\n\n"

#define DEFAULT_FUNC_SUBSTR			\
	"JUMP $end$func$substr\n" 		\
	"LABEL substr\n"\
	"PUSHFRAME\n" 	\
	"DEFVAR LF@%retval\n"			\
	"MOVE LF@%retval string@\n"		\
	"DEFVAR LF@str_type\n"			\
	"TYPE LF@str_type LF@%1\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@string\n"	\
	"TYPE LF@str_type LF@%2\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@int\n"	\
	"TYPE LF@str_type LF@%3\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@int\n"	\
	"DEFVAR LF@%length\n"			\
	"STRLEN LF@%length LF@%1\n"		\
	"SUB LF@%length LF@%length int@1\n"		\
	"DEFVAR LF@%var\n"		\
	"DEFVAR LF@%str_len\n"	\
	"DEFVAR LF@%iterator\n"	\
	"LT LF@%var LF@%3 int@0\n"\
	"JUMPIFEQ end LF@%var bool@true\n"		\
	"GT LF@%var LF@%2 LF@%length\n"			\
	"JUMPIFEQ end LF@%var bool@true\n"		\
	"LT LF@%var LF@%2 int@0\n"\
	"JUMPIFEQ end LF@%var bool@true\n"		\
	"MOVE LF@%iterator LF@%2\n"\
	"ADD LF@%str_len LF@%2 LF@%3\n"			\
	"SUB LF@%str_len LF@%str_len int@1\n"	\
	"LABEL while\n"			\
	"LT LF@%var LF@%length LF@%iterator\n"	\
	"JUMPIFEQ end LF@%var bool@true\n"		\
	"LT LF@%var LF@%str_len LF@%iterator\n"	\
	"JUMPIFEQ end LF@%var bool@true\n"		\
	"GETCHAR LF@%var LF@%1 LF@%iterator\n"	\
	"CONCAT LF@%retval LF@%retval LF@%var\n"\
	"ADD LF@%iterator LF@%iterator int@1\n"	\
	"JUMP while\n"	\
	"LABEL end\n"	\
	"POPFRAME\n"	\
	"RETURN\n"		\
	"LABEL $end$func$substr\n\n"

#define DEFAULT_FUNC_CHR		\
	"JUMP $end$func$chr\n"	 	\
	"LABEL chr\n"\
	"PUSHFRAME\n" \
	"DEFVAR LF@%retval\n"		\
	"MOVE LF@%retval nil@nil\n"	\
	"DEFVAR LF@str_type\n"			\
	"TYPE LF@str_type LF@%1\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@int\n"	\
	"DEFVAR LF@%var\n"			\
	"LT LF@%var LF@%1 int@0\n"\
	"JUMPIFEQ end_chr LF@%var bool@true\n"	\
	"GT LF@%var LF@%1 int@255\n"			\
	"JUMPIFEQ end_chr LF@%var bool@true\n"	\
	"INT2CHAR LF@%retval LF@%1\n"			\
	"LABEL end_chr\n"	\
	"POPFRAME\n"		\
	"RETURN\n"			\
	"LABEL $end$func$chr\n\n"

#define DEFAULT_FUNC_ORD		\
	"JUMP $end$func$ord\n"		\
	"LABEL ord\n"\
	"PUSHFRAME\n" \
	"DEFVAR LF@%retval\n"		\
	"MOVE LF@%retval nil@nil\n"	\
	"DEFVAR LF@str_type\n"			\
	"TYPE LF@str_type LF@%1\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@string\n"	\
	"TYPE LF@str_type LF@%2\n"		\
	"JUMPIFNEQ $err$end LF@str_type string@int\n"	\
	"DEFVAR LF@%var\n"			\
	"DEFVAR LF@%length\n"		\
	"STRLEN LF@%length LF@%1\n"	\
	"LT LF@%var LF@%2 int@0\n"	\
	"JUMPIFEQ end_chr LF@%var bool@true\n"	\
	"SUB LF@%length LF@%length int@1\n"		\
	"GT LF@%var LF@%2 LF@%length\n"			\
	"JUMPIFEQ end_chr LF@%var bool@true\n"	\
	"STRI2INT LF@%retval LF@%1 LF@%2\n"		\
	"LABEL end_ord\n"	\
	"POPFRAME\n"		\
	"RETURN\n"			\
	"LABEL $end$func$ord\n\n"


/**
 * @brief Funkce, ktera alokuje pamet a inicializuje dynamicke retezce pro ukladani IFJcode18.
 */
int init_generator () {

	output_buffer = &output_buffer_struct;
	tmp_buffer = &tmp_buffer_struct;
	if (strInit (output_buffer))
		print_error (INTERNAL_ERROR);
	strClear (output_buffer);

	if (strInit (tmp_buffer))
		print_error (INTERNAL_ERROR);
	strClear (tmp_buffer);

	return COMP_SUCC;
}

/**
 * @brief Funkce, ktera uvolni pamet - dynamicke retezce.
 */
void clear_generator () {

	strFree (output_buffer);
	strFree (tmp_buffer);
}

/**
 * @brief Lokalni funkce, ktera vypise na vystup definice vestavenych funkci IFJcode18.
 */
void init_default_func () {

	inf_string *code_buffer;
	code_buffer = output_buffer;

	strAddString (code_buffer, DEFAULT_FUNC_LENGTH);
	strAddString (code_buffer, DEFAULT_FUNC_SUBSTR);
	strAddString (code_buffer, DEFAULT_FUNC_CHR);
	strAddString (code_buffer, DEFAULT_FUNC_ORD);
}

/**
 * @brief Pomocna funkce pro nastaveni vystupniho dynamickeho retezce (potreba u while).
 */
inf_string *set_out_buffer (bool in_while) {

	if (!in_while)
		return output_buffer;
	else
		return tmp_buffer;
}

/**
 * @brief Pomocna funkce pro vypsani identifikatoru a ramce promenne.
 */
void generate_var (char frame_type, char *id, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	strAddChar (code_buffer, frame_type);
	strAddString (code_buffer, "F@");
	strAddString (code_buffer, id);
}

/**
 * @brief Funkce pro generovani porovnani dvou retezcu.
 */
bool jump_if_string_comp (char *id1, char *id2, char frame_type1, char frame_type2, char *label, bool in_while, bool oper_enable) {

	inf_string *code_buffer = set_out_buffer (in_while);

	strAddString (code_buffer, "JUMPIFNEQ ");
	strAddString (code_buffer, label);

	strAddChar (code_buffer, ' ');
	generate_var (frame_type1, id1, in_while);
	strAddChar (code_buffer, ' ');
	generate_var (frame_type2, id2, in_while);
	strAddChar (code_buffer, '\n');


	strAddString (code_buffer, "JUMPIFEQ ");
	strAddString (code_buffer, label);

	strAddChar (code_buffer, ' ');
	generate_var (frame_type1, id1, in_while);
	strAddChar (code_buffer, ' ');
	strAddString (code_buffer, "string@nil\n");

	if (oper_enable) {
		strAddString (code_buffer, "JUMPIFEQ ");
		strAddString (code_buffer, label);

		strAddChar (code_buffer, ' ');
		generate_var (frame_type1, id1, in_while);
		strAddChar (code_buffer, ' ');
		strAddString (code_buffer, "string@string\n");
	}
	return true;
}

/**
 * @brief Pomocna funkce pro generovani deleni dvou termu (celociselne, decimalni).
 */
void generate_div (bool div, unsigned int label_count, bool in_while, char *operand1, char *operand2, char *data_type) {

	inf_string *code_buffer = set_out_buffer (in_while);

	if (div) {
		if (!strcmp (data_type, "int")) {
			strAddString (code_buffer, "JUMPIFEQ ");
			strAddString (code_buffer, LABEL_ERR_DIV_END);

			strAddChar (code_buffer, ' ');
			generate_var ('G', operand1, in_while);
			strAddChar (code_buffer, ' ');
			strAddString (code_buffer, "int@0\n");

			strAddChar(code_buffer, 'I');
		}
		else {
			strAddString (code_buffer, "JUMPIFEQ ");
			strAddString (code_buffer, LABEL_ERR_DIV_END);

			strAddChar (code_buffer, ' ');
			generate_var ('G', operand1, in_while);
			strAddChar (code_buffer, ' ');
			strAddString (code_buffer, "float@0x0p+0\n");
		}

		strAddString (code_buffer, "DIV ");
		generate_var ('G', operand1, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', operand2, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', operand1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "PUSHS ");
		generate_var ('G', operand1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "JUMP $end$end");
		label_id (label_count, 0, in_while);
		strAddChar (code_buffer, '\n');
	}
}

/**
 * @brief Funkce generujici mezikod pro rozpoznani dat. typu promenne.
 */
bool generate_type (char *var, char *id, char frame_type_var, char frame_type_id, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	strAddString (code_buffer, "TYPE ");
	generate_var (frame_type_var, var, in_while);
	strAddChar (code_buffer, ' ');
	generate_var (frame_type_id, id, in_while);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce provadejici konverzi datovych typu, pokud int a float.
 */
bool float_int_conv (char *id1, char *id2,  char *operand1, char *operand2, bool in_while, bool div) {

	inf_string *code_buffer = set_out_buffer (in_while);
	static unsigned int label_count = 0;

	strAddString (code_buffer, "\n# float_int_conv -----------------------\n\n");

	// if (op1 == int)
	strAddString (code_buffer, "JUMPIFNEQ $conv");

	label_id (++label_count, 0, in_while);
	strAddChar (code_buffer, ' ');

	generate_var ('G', id1, in_while);
	strAddString (code_buffer, " string@int\n");


	// if (op2 == float)
	strAddString (code_buffer, "JUMPIFNEQ $conv");
	label_id (label_count, 1, in_while);
	strAddChar (code_buffer, ' ');

	generate_var ('G', id2, in_while);
	strAddString (code_buffer, " string@float\n");

	// true branch
	//convert
	strAddString (code_buffer, "INT2FLOAT ");
	generate_var ('G', EXP_RETURN_VAL_ID, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', operand1, in_while);
	strAddChar (code_buffer, '\n');

	move_var (operand1, EXP_RETURN_VAL_ID, 'G', 'G', in_while);
	generate_type (TMP_VAR1, operand1, 'G', 'G', in_while);

	generate_div (div, label_count, in_while, operand1, operand2, "float");

	strAddString (code_buffer, "JUMP $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "LABEL $conv");
	label_id (label_count, 1, in_while);
	strAddChar (code_buffer, '\n');

	// else branch
	// Check if it is really int!
	strAddString (code_buffer, "JUMPIFNEQ $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', id2, in_while);
	strAddString (code_buffer, " string@int\n");

	generate_div (div, label_count, in_while, operand1, operand2, "int");


	strAddString (code_buffer, "JUMP $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "LABEL $conv");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "JUMPIFNEQ $conv");
	label_id (label_count, 2, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', id2, in_while);
	strAddString (code_buffer, " string@int\n");

	//Check if var2 is really float!
	strAddString (code_buffer, "JUMPIFNEQ $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', id1, in_while);
	strAddString (code_buffer, " string@float\n");

	strAddString (code_buffer, "INT2FLOAT ");
	generate_var ('G', EXP_RETURN_VAL_ID, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', operand2, in_while);
	strAddChar (code_buffer, '\n');

	move_var (operand2, EXP_RETURN_VAL_ID, 'G', 'G', in_while);
	generate_type (TMP_VAR2, operand2, 'G', 'G', in_while);

	generate_div (div, label_count, in_while, operand1, operand2, "float");

	strAddString (code_buffer, "JUMP $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "LABEL $conv");
	label_id (label_count, 2, in_while);
	strAddChar (code_buffer, '\n');

	// Check if it is really float!
	strAddString (code_buffer, "JUMPIFNEQ $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', id1, in_while);
	strAddString (code_buffer, " string@float\n");

	strAddString (code_buffer, "JUMPIFNEQ $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', id2, in_while);
	strAddString (code_buffer, " string@float\n");

	generate_div (div, label_count, in_while, operand1, operand2, "float");


	strAddString (code_buffer, "LABEL $end$end");
	label_id (label_count, 0, in_while);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici kontrolu datovych typu, pokud nejsou datove typy stejne, je proveden skok na navesti, kde je vytisknuto chybove hlaseni.
 */
bool generate_type_control (char *id1, char *id2,  bool in_while, bool div, bool oper_enable) {

	generate_type (TMP_VAR1, id1, 'G', 'G', in_while);
	generate_type (TMP_VAR2, id2, 'G', 'G', in_while);

	float_int_conv (TMP_VAR1, TMP_VAR2, id1, id2, in_while, div);
	jump_if_string_comp (TMP_VAR1, TMP_VAR2, 'G', 'G', LABEL_ERR_END, in_while, oper_enable);

	return true;
}

/**
 * @brief Funkce generujici hlavicku IFJcode18, vytvoreni zakladniho ramce a jeho presunuti na zasobnik. Definuje a inicializuje pomocne globalni promenne.
 */
bool file_header () {

	inf_string *code_buffer;
	code_buffer = output_buffer;

	//strAddString (code_buffer, "\n#----------file_header - function----------\n");

	strAddString (code_buffer, ".IFJcode18\n");

	init_default_func ();

	strAddString (code_buffer, "CREATEFRAME\n");
	strAddString (code_buffer, "PUSHFRAME\n\n");

	var_definition (EXP_RETURN_VAL_ID, 'G', false);
	var_definition ("%retval", 'L', false);
	var_definition (TMP_VAR1, 'G', false);
	var_definition (TMP_VAR2, 'G', false);
	var_definition (AUX_VAR1, 'G', false);
	var_definition (AUX_VAR2, 'G', false);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici konec programu IFJcode18. Vytvari navesti s chybovym hlasenim.
 */
bool file_end () {

	inf_string *code_buffer;
	code_buffer = output_buffer;

	//strAddString (code_buffer, "\n#----------file_end - function----------\n");

	strAddString (code_buffer, "JUMP ");
	strAddString (code_buffer, LABEL_END);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "LABEL ");
	strAddString (code_buffer, LABEL_ERR_END);
	strAddChar (code_buffer, '\n');

	tToken token_sem = {.type = T_STRING, .token_atribut.str_value = "\nERROR: Semantic analysis - wrong compatibility of type in arithmetic, string or relational expression.\n"};
	std_out_write (token_sem, false);

	strAddString (code_buffer, "JUMP ");
	strAddString (code_buffer, LABEL_END);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "LABEL ");
	strAddString (code_buffer, LABEL_ERR_DIV_END);
	strAddChar (code_buffer, '\n');

	tToken token_div = {.type = T_STRING, .token_atribut.str_value = "\nRUNTIME ERROR: Division by zero.\n"};
	std_out_write (token_div, false);

	strAddString (code_buffer, "LABEL ");
	strAddString (code_buffer, LABEL_END);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "POPFRAME\n");
	strAddString (code_buffer, "CLEARS\n");

	return true;
}

/**
 * @brief Funkce generujici definici promenne a jeji inicializaci na nil.
 */
bool var_definition (char *id, char frame_type, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	strAddString (code_buffer, "DEFVAR ");
	generate_var (frame_type, id, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "MOVE ");
	generate_var (frame_type, id, in_while);
	strAddString (code_buffer, " nil@nil\n");

	return true;
}

/**
 * @brief Funkce generujici tisk na stdout (promenna i konstanta).
 */
bool std_out_write (tToken token, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------std_out_write - function----------\n");

	strAddString (code_buffer, "WRITE ");
	get_term (token, in_while);

	return true;
}

/**
 * @brief Funkce generujici kod v IFJcode18 odpovidajici vstupnimu tokenu.
 */
bool get_term (tToken token, bool in_while) {

	//strAddString (code_buffer, "\n#----------get_term - function----------\n");

	inf_string *code_buffer = set_out_buffer (in_while);

	switch (token.type) {
		case T_INT:
			strAddString (code_buffer, "int@");

			char int_str[MAX_DIGITS];
			sprintf(int_str, "%d", token.token_atribut.int_value);

			strAddString (code_buffer, int_str);
		break;

		case T_FLOAT:
			strAddString (code_buffer, "float@");
			char float_str[MAX_DIGITS];
			sprintf(float_str, "%a", token.token_atribut.float_value);

			strAddString (code_buffer, float_str);
		break;

		case T_STRING:
			strAddString (code_buffer, "string@");
		char c = token.token_atribut.str_value[0];
		char tmp_const_str [MAX_DIGITS];

		inf_string tmp_buffer;
		strInit (&tmp_buffer);

		//printf ("%s\n", token.token_atribut.str_value);

		for (int i=1; c != '\0'; i++) {
			if (!isprint (c) || c <= 32 || c == '\\' || c == '#') {
				strAddChar (&tmp_buffer, '\\');
				sprintf (tmp_const_str, "%03d", c);
				strAddString (&tmp_buffer, tmp_const_str);
			}
			else {
				strAddChar (&tmp_buffer, c);
			}
			c = token.token_atribut.str_value[i];
		}

		strAddString (code_buffer, tmp_buffer.string);

		break;

		case T_ID:
			generate_var ('L', token.token_atribut.str_value, in_while);
		break;

		case T_NIL:
			strAddString (code_buffer, "nil@nil");
		break;

		default:
			return false;
	}
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici navesti s nazvem odpovidajicim vstupnimu retezci.
 */
bool label_func (char *label, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------label_func - function----------\n");

	strAddString (code_buffer, "LABEL ");
	strAddString (code_buffer, label);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici zacatek definice funkce IFJcode18.
 */
bool func_start (char *func_id) {

	inf_string *code_buffer;
	code_buffer = output_buffer;

	//strAddString (code_buffer, "\n\n\n#----------func_start - function----------\n");

	strAddString (code_buffer, "JUMP $end$");
	strAddString (code_buffer, func_id);
	strAddChar (code_buffer, '\n');
	label_func(func_id, false);

	strAddString (code_buffer, "PUSHFRAME\n");

	var_definition ("%retval", 'L', false);
	return true;
}

/**
 * @brief Funkce generujici konec definice funkce IFJcode18.
 */
bool func_end (int return_type, char *return_id, char *func_id) {

	inf_string *code_buffer;
	code_buffer = output_buffer;

	strAddString (code_buffer, "\n#----------func_end - function----------\n\n\n");

	switch (return_type) {

		case 0:
			strAddString (code_buffer, "MOVE LF@%retval nil@nil\n");
		break;

		case 1:
			move_var ("%retval", return_id, 'L', 'L', false);
		break;

		case 2:
			//printf ("%s\n", return_id);
			if ((!strcmp (func_id, "inputi"))||
			   (!strcmp (func_id, "inputs"))||
			   (!strcmp (func_id, "inputf")))
					move_var ("%retval", return_id, 'L', 'L', false);
			else if (!strcmp(return_id, "print"))
				strAddString (code_buffer, "MOVE LF@%retval nil@nil\n");
			else
				move_var ( "%retval", "%retval", 'L', 'T', false);
		break;

		case 3:
			move_var ("%retval", EXP_RETURN_VAL_ID, 'L', 'G', false);
		break;
	}
	strAddString (code_buffer, "POPFRAME\n");
	strAddString (code_buffer, "RETURN\n");

	strAddString (code_buffer, "LABEL $end$");
	strAddString (code_buffer, func_id);			// id ukoncovane funkce
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici volani funkce i vestavene pro ceteni a zapis.
 */
bool generator_func_call ( char *var_id, char *func_id, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------generator_func_call - function----------\n");

	if (!strcmp (func_id, "inputi"))
		std_in_read (var_id, D_TYPE_INT, in_while);
	else if (!strcmp (func_id, "inputs"))
		std_in_read (var_id, D_TYPE_STRING, in_while);
	else if (!strcmp (func_id, "inputf"))
		std_in_read (var_id, D_TYPE_FLOAT, in_while);
	else if (!strcmp (func_id, "print")) {
		tToken tmp_token = {.type = T_ID, .token_atribut.str_value = var_id};
		std_out_write (tmp_token, in_while);
	}
	else {
		strAddString (code_buffer, "CALL ");
		strAddString (code_buffer, func_id);
		strAddChar (code_buffer, '\n');
	}
	return true;
}

/**
 * @brief Funkce generujici kod pro vytvoreni datoveho ramce pro funkci.
 */
bool func_param_pass_prep () {

	inf_string *code_buffer = output_buffer;
	strAddString (code_buffer, "CREATEFRAME\n");

	return true;
}

/**
 * @brief Funkce generujici pomocne promenne a jejich inicializaci pro predavani prametru funkci.
 */
bool func_param_passing (tToken token, int tmp_index_var, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------func_param_passing - function----------\n");

	char int_str[MAX_DIGITS];
	sprintf(int_str, "%d", tmp_index_var);

	strAddString (code_buffer, "DEFVAR TF@%");
	strAddString (code_buffer, int_str);

	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "MOVE TF@%");
	strAddString (code_buffer, int_str);
	strAddString (code_buffer, " ");
	get_term(token, in_while);
	strAddChar (code_buffer, '\n');

	return true;

}

/**
 * @brief Funkce generujici predani navratove hodnoty funkce do odpovidajici promenne.
 */
bool func_return_value_after_called_func (char *id, bool default_func, bool in_while) {

	//strAddString (code_buffer, "\n#----------func_return_value_after_called_func - function----------\n");

	if (!default_func)
		move_var (id, "%retval", 'L', 'T', in_while);

	return true;
}

/**
 * @brief Funkce generujici mezikod pro nacteni hodnoty ze vstupu urciteho datoveho typu a prirazeni do promenne.
 */
bool std_in_read (char *id, int data_type, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------std_in_read - function----------\n");
	if (!strcmp (id, "")) {
		strAddString (code_buffer, "READ GF@");
		strAddString (code_buffer, EXP_RETURN_VAL_ID);
		strAddChar (code_buffer, ' ');
	}
	else {
		strAddString (code_buffer, "READ LF@");
		strAddString (code_buffer, id);
		strAddString (code_buffer, " ");
	}
	switch (data_type) {
		case D_TYPE_INT:
			strAddString (code_buffer, "int\n");
		break;

		case D_TYPE_FLOAT:
			strAddString (code_buffer, "float\n");

		break;

		case D_TYPE_STRING:
			strAddString (code_buffer, "string\n");

		break;
	}
	return true;
}

/**
 * @brief Funkce generujici podmineny skok, pokud neni v podmince relacni operator.
 */
bool jumps_func_non_rel (int index, int deep, bool in_while, bool if_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	generate_type (TMP_VAR1, EXP_RETURN_VAL_ID, 'G', 'G', in_while);

	if (if_while)
		strAddString (code_buffer, "JUMPIFEQ $end");
	else
		strAddString (code_buffer, "JUMPIFEQ ");

	label_id(index, deep, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', TMP_VAR1, in_while);
	strAddString (code_buffer, " string@nil\n");

	return true;
}

/**
 * @brief Funkce generujici podmineny skok
 */
bool jumps_func (bool switch_end, int index, int deep, char* var1, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------jump_func - function----------\n");

	if (switch_end)
		strAddString (code_buffer, "JUMPIFEQ $end");
	else
		strAddString (code_buffer, "JUMPIFEQ ");

	label_id(index, deep, in_while);

	strAddChar (code_buffer, ' ');
	generate_var ('G', var1, in_while);
	strAddChar (code_buffer, ' ');
	strAddString (code_buffer, " bool@false\n");

	return true;
}

/**
 * @brief Pomocna funkce (if_func_start) pro generovani kontroly dat.
 */
void prep_if_func_start (bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	generate_stack_pop (AUX_VAR1, 'G', in_while);
	generate_stack_pop (AUX_VAR2, 'G', in_while);
	generate_type_control (AUX_VAR1, AUX_VAR2, in_while, false, false);

	strAddString (code_buffer, "PUSHS ");
	generate_var ('G', AUX_VAR2, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "PUSHS ");
	generate_var ('G', AUX_VAR1, in_while);
	strAddChar (code_buffer, '\n');
}

/**
 * @brief Funkce generujici v pripade stejnych dat. typu porovnani dvou vyrazu. Pokud jsou typy odlišné, a jedná so o "=="/"!=" je vysldnou hodnotou nepravda. Vysledna pravdivostni hodnota je ulozena na vrchol zasobniku.
 */
bool if_func_start (Token_type type, bool in_while) {

	static unsigned int label_count = 0;
	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------if_func_start - function----------\n");

	switch (type) {
		case T_LESS:
			prep_if_func_start (in_while);
			strAddString (code_buffer, "LTS\n");
		break;

		case T_EQUAL:
			strAddString (code_buffer, "POPS ");
			generate_var ('G', AUX_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "POPS ");
			generate_var ('G', AUX_VAR1, in_while);
			strAddChar (code_buffer, '\n');

			generate_type (TMP_VAR1, AUX_VAR1, 'G', 'G', in_while);
			generate_type (TMP_VAR2, AUX_VAR2, 'G', 'G', in_while);

			float_int_conv (TMP_VAR1, TMP_VAR2, AUX_VAR1, AUX_VAR2, in_while, false);

			strAddString (code_buffer, "JUMPIFNEQ $rel$end");
			label_id (++label_count, 0, in_while);
			strAddChar (code_buffer, ' ');
			generate_var ('G', TMP_VAR1, in_while);
			strAddChar (code_buffer, ' ');
			generate_var ('G', TMP_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS ");
			generate_var ('G', AUX_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS ");
			generate_var ('G', AUX_VAR1, in_while);
			strAddChar (code_buffer, '\n');

			// true
			strAddString (code_buffer, "EQS\n");

			// false
			strAddString (code_buffer, "JUMP $rel$end");
			label_id (label_count, 1, in_while);

			strAddString (code_buffer, "\nLABEL $rel$end");
			label_id (label_count, 0, in_while);

			// false body
			strAddString (code_buffer, "\nPUSHS bool@false\n");

			strAddString (code_buffer, "LABEL $rel$end");
			label_id (label_count, 1, in_while);
			strAddChar (code_buffer, '\n');
		break;

		case T_NEQUAL:
			strAddString (code_buffer, "POPS ");
			generate_var ('G', AUX_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "POPS ");
			generate_var ('G', AUX_VAR1, in_while);
			strAddChar (code_buffer, '\n');

			generate_type (TMP_VAR1, AUX_VAR1, 'G', 'G', in_while);
			generate_type (TMP_VAR2, AUX_VAR2, 'G', 'G', in_while);

			float_int_conv (TMP_VAR1, TMP_VAR2, AUX_VAR1, AUX_VAR2, in_while, false);

			strAddString (code_buffer, "JUMPIFNEQ $rel$end");
			label_id (++label_count, 0, in_while);
			strAddChar (code_buffer, ' ');
			generate_var ('G', TMP_VAR1, in_while);
			strAddChar (code_buffer, ' ');
			generate_var ('G', TMP_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS ");
			generate_var ('G', AUX_VAR2, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS ");
			generate_var ('G', AUX_VAR1, in_while);
			strAddChar (code_buffer, '\n');

			// true
			strAddString (code_buffer, "EQS\n");
			strAddString (code_buffer, "NOTS\n");

			// false
			strAddString (code_buffer, "JUMP $rel$end");
			label_id (label_count, 1, in_while);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "LABEL $rel$end");
			label_id (label_count, 0, in_while);
			strAddChar (code_buffer, '\n');

			// false body
			strAddString (code_buffer, "PUSHS bool@true\n");

			strAddString (code_buffer, "LABEL $rel$end");
			label_id (label_count, 1, in_while);
			strAddChar (code_buffer, '\n');
		break;

		case T_MORE:
			prep_if_func_start (in_while);
			strAddString (code_buffer, "GTS\n");
		break;

		case T_LE:
			prep_if_func_start (in_while);

			strAddString (code_buffer, "LTS\n");

			strAddString (code_buffer, "PUSHS GF@");
			strAddString (code_buffer, AUX_VAR2);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS GF@");
			strAddString (code_buffer, AUX_VAR1);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "EQS\nORS\n");
		break;

		case T_ME:
			prep_if_func_start (in_while);

			strAddString (code_buffer, "GTS\n");

			strAddString (code_buffer, "PUSHS GF@");
			strAddString (code_buffer, AUX_VAR2);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "PUSHS GF@");
			strAddString (code_buffer, AUX_VAR1);
			strAddChar (code_buffer, '\n');

			strAddString (code_buffer, "EQS\nORS\n");
		break;

		default:
		break;
	}
	return true;
}

/**
 * @brief Funkce generujici nazev navesti zadaneho pomoci dvou hodnot (potrebne pro if a while).
 */
bool label_id (int label_index, int label_index_deep, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	char label_str[MAX_DIGITS];
	char label_str_deep[MAX_DIGITS];

	strAddString (code_buffer, "$label");
	sprintf(label_str, "%d", label_index);
	sprintf(label_str_deep, "%d", label_index_deep);

	strAddString (code_buffer, label_str);
	strAddString (code_buffer, "$deep");
	strAddString (code_buffer, label_str_deep);

	return true;
}

/**
 * @brief Generuje navesti zadane pomoci dvou hodnot (potrebne pro if a while).
 */
bool generate_label (int label_index, int label_index_deep, bool in_while, bool end_control) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------generate_label - function----------\n");
	if (!end_control)
		strAddString (code_buffer, "LABEL ");
	else
		strAddString (code_buffer, "LABEL $end");

	label_id (label_index, label_index_deep, in_while);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generuje nepodmineny skok na navesti zadane pomoci dvou hodnot (potrebne pro if a while).
 */
bool jump_func (int label_index, int label_index_deep, bool in_while, bool end) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------jumpe_func - function----------\n");
	if (end)
		strAddString (code_buffer, "JUMP $end");
	else
		strAddString (code_buffer, "JUMP ");

	label_id (label_index, label_index_deep, in_while);
	strAddChar (code_buffer, '\n');
	return true;
}

/**
 * @brief Funkce generuje prirazeni pomocne promenne (predavani parametru ve funkci) do zadane promenne.
 */
bool move_aux_var (char *var1, char *var2, char frame_type1, char frame_type2, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	move_var_first (var1, frame_type1, in_while);

	strAddChar (code_buffer, ' ');
	strAddChar (code_buffer, frame_type2);
	strAddString (code_buffer, "F@%");
	strAddString (code_buffer, var2);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generuje prirazeni jedne promenne do druhe.
 */
bool move_var (char *var1, char *var2, char frame_type1, char frame_type2, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------move_var - function----------\n");

	move_var_first (var1, frame_type1, in_while);

	strAddChar(code_buffer, frame_type2);
	strAddString(code_buffer, "F@");
	strAddString(code_buffer, var2);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Funkce generujici prvni cast prirazeni. Mozne vyuzit napr. v kombinaci s get_term.
 */
bool move_var_first (char *var, char frame_type, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------move_var - function----------\n");

	strAddString(code_buffer, "MOVE ");
	strAddChar(code_buffer, frame_type);
	strAddString(code_buffer, "F@");
	strAddString(code_buffer, var);
	strAddChar(code_buffer, ' ');

	return true;
}

/**
 * @brief Funkce generuje push zadaneho termu na zasobnik.
 */
bool generate_stack_push (Token_type type, tAtribut atribut, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------generate_stack_push - function----------\n");

	tToken token = {.type = type, .token_atribut = atribut};

	strAddString (code_buffer, "PUSHS ");
	get_term (token, in_while);

	return true;
}

/**
 * @brief Funkce generuje pop ze zasobniku do dane promenne.
 */
bool generate_stack_pop (char* id, char frame_type, bool in_while) {

	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------generate_stack_pop - function----------\n");

	strAddString (code_buffer, "POPS ");
	strAddChar (code_buffer, frame_type);
	strAddString (code_buffer, "F@");
	strAddString (code_buffer, id);
	strAddChar (code_buffer, '\n');

	return true;
}

/**
 * @brief Pomocna funkce pro funkci stack_operations. Generuje kod pro provadeni operaci.
 */
void generate_oper (bool in_while, char *type) {

	inf_string *code_buffer = set_out_buffer (in_while);

	generate_stack_pop (AUX_VAR1, 'G', in_while);
	generate_stack_pop (AUX_VAR2, 'G', in_while);
	generate_type_control (AUX_VAR1, AUX_VAR2, in_while, false, true);

	strAddString (code_buffer, type);

	generate_var ('G', AUX_VAR1, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', AUX_VAR2, in_while);
	strAddChar (code_buffer, ' ');
	generate_var ('G', AUX_VAR1, in_while);
	strAddChar (code_buffer, '\n');

	strAddString (code_buffer, "PUSHS ");
	generate_var ('G', AUX_VAR1, in_while);
	strAddChar (code_buffer, '\n');
}

/**
 * @brief Funkce generujici kod pro provedeni operaci scitani, odcitani, deleni a nasobeni, vola kontrolu dat. typu. U scitani moznost konkatenace retezcu.
 */
bool stack_operations (Token_type type, bool in_while) {

	static unsigned int label_count_add = 0;
	inf_string *code_buffer = set_out_buffer (in_while);

	//strAddString (code_buffer, "\n#----------stack_operations - function----------\n");

	switch (type) {

	case T_PLUS:
		generate_stack_pop (AUX_VAR1, 'G', in_while);
		generate_stack_pop (AUX_VAR2, 'G', in_while);
		generate_type_control (AUX_VAR1, AUX_VAR2, in_while, false, false);

		generate_type (TMP_VAR1, AUX_VAR1, 'G', 'G', in_while);

		strAddString (code_buffer, "JUMPIFNEQ $add");
		label_id (++label_count_add, 0, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', TMP_VAR1, in_while);
		strAddString (code_buffer, " string@string\n");

		strAddString (code_buffer, "CONCAT ");
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', AUX_VAR2, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "PUSHS ");
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "JUMP $end$add");
		label_id (label_count_add, 0, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "LABEL $add");
		label_id (label_count_add, 0, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "ADD ");
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', AUX_VAR2, in_while);
		strAddChar (code_buffer, ' ');
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "PUSHS ");
		generate_var ('G', AUX_VAR1, in_while);
		strAddChar (code_buffer, '\n');

		strAddString (code_buffer, "LABEL $end$add");
		label_id (label_count_add, 0, in_while);
		strAddChar (code_buffer, '\n');
		break;

	case T_MINUS:
		generate_oper (in_while, "SUB ");
	break;

	case T_MUL:
		generate_oper (in_while, "MUL ");
	break;

	case T_DIV:
		generate_stack_pop (AUX_VAR1, 'G', in_while);
		generate_stack_pop (AUX_VAR2, 'G', in_while);
		generate_type_control (AUX_VAR1, AUX_VAR2, in_while, true, true);
	break;
	default:
	break;
	}
	return true;
}

/**
 * @brief Pokud se nachazime v cyklu while, je vystup generovan do pomocneho dynamickeho retezce. Zde po skonceni kopirujeme do primarniho bufferu.
 */
bool while_if_end_copy () {

	char *tmp_const_str = tmp_buffer->string;
	strAddString (output_buffer, tmp_const_str);

	strClear(tmp_buffer);

	return true;
}
