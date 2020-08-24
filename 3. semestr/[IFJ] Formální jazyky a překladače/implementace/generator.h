/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * Generator IFJcode18.
 *
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 */

#ifndef GENERATOR_H_INCLUDED
#define GENERATOR_H_INCLUDED

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <stdbool.h>

#include "str.h"
#include "scanner.h"
#include "symtable.h"


// maximalni delka cisla pro prevod na retezec
#define MAX_DIGITS 60

/**
 * Konstanty - identifikatory pomocnych promennych pro generovani IFJcode18
 */
#define AUX_VAR1 "op_var1"
#define AUX_VAR2 "op_var2"
#define TMP_VAR1 "tmp_var1"
#define TMP_VAR2 "tmp_var2"
#define EXP_RETURN_VAL_ID "exp_return_value"
#define LABEL_ERR_END "$err$end"
#define LABEL_END "$end"
#define LABEL_ERR_DIV_END "$err$div$end"


/**
 * Globalni promenne pro ukladani mezikodu.
 */
inf_string* output_buffer;
inf_string output_buffer_struct;

inf_string* tmp_buffer;
inf_string tmp_buffer_struct;


// Typy relaci: >GT -LESS, ==EQ -EQUAL, <LT -GREATER
typedef enum {
	LESS,
	EQUAL,
	GREATER
}tRelations;

/**
 * Funkce, ktera alokuje pamet a inicializuje dynamicke retezce pro ukladani IFJcode18.
 */
int init_generator ();

/**
 * Funkce, ktera uvolni pamet - dynamicke retezce.
 */
void clear_generator ();

/**
 * Funkce generujici podmineny skok
 * @param index - urceni id navesti
 * @param deep - urceni id navesti
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 * @param if_while - prepinac mezi generovani if a while
 */
bool jumps_func_non_rel (int index, int deep, bool in_while, bool if_while);

/**
 * Funkce generujici kontrolu datovych typu, pokud nejsou datove typy stejne, je proveden skok na navesti, kde je vytisknuto chybove hlaseni.
 * @param id1 - promenna pro ulozeni datoveho typu
 * @param id2 - promenna, pro kterou chceme zjistit dat. typ
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 * @param div - prepinac pro vypis kodu pro deleni
 */
bool generate_type_control (char *id1, char *id2, bool in_while, bool div, bool oper_enable);

/**
 * Funkce provadejici konverzi datovych typu, pokud int a float.
 * @param id1 - dat. typ 1. promenne
 * @param id2 - dat. typ 2. promenne
 * @param operand1 - 1. promenna pro konverzi
 * @param operand2 - 2. promenna pro konverzi
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 * @param div - prepinac pro vypis kodu pro deleni
 */
bool float_int_conv (char *id1, char *id2, char *operand1, char *operand2, bool in_while, bool div);

/**
 * Funkce generujici hlavicku IFJcode18, vytvoreni zakladniho ramce a jeho presunuti na zasobnik. Definuje a inicializuje pomocne globalni promenne.
 */
bool file_header ();

/**
 * Funkce generujici konec programu IFJcode18. Vytvari navesti s chybovym hlasenim.
 */
bool file_end ();

/**
 * Funkce generujici definici promenne a jeji inicializaci na nil.
 * @param id - identifikator definovane promenne
 * @param frame_type - typ datoveho ramce
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool var_definition (char *id, char frame_type, bool in_while);

/**
 * Funkce generujici tisk na stdout (promenna i konstanta).
 * @param token - tisknuty term
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool std_out_write (tToken token, bool in_while);

/**
 * Funkce generujici kod v IFJcode18 odpovidajici vstupnimu tokenu.
 * @param token - vstupni term pro zpracovani
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool get_term (tToken token, bool in_while);

/**
 * Funkce generujici navesti s nazvem odpovidajicim vstupnimu retezci.
 * @param label - identifikator generovaneho navesti
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool label_func (char *label, bool in_while);

/**
 * Funkce generujici zacatek definice funkce IFJcode18.
 * @param func_id - identifikator funkce
 */
bool func_start (char *func_id);

/**
 * Funkce generujici konec definice funkce IFJcode18.
 * @param return_type
 * @param return_id
 * @param func_id
 */
bool func_end (int return_type, char *return_id, char *func_id);

/**
 * Funkce generujici volani funkce i vestavene pro ceteni a zapis.
 * @param var_id - identifikator promenne
 * @param func_id - identifikator funkce
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool generator_func_call ( char *var_id, char *func_id, bool in_while);

/**
 * Funkce generujici kod pro vytvoreni datoveho ramce pro funkci.
 */
bool func_param_pass_prep ();

/**
 * Funkce generujici pomocne promenne a jejich inicializaci pro predavani prametru funkci.
 * @param token - vstupni term pro zpracovani
 * @param tmp_index_var - poradove cislo predavaneho parametru
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool func_param_passing (tToken token, int tmp_index_var, bool in_while);

/**
 * Funkce generujici predani navratove hodnoty funkce do odpovidajici promenne.
 * @param id - identifikator promenne, do ktere ma byt navratova hodnota prirazena
 * @param default_func - prepinac mezi vestavenymi a definovanymi funkcemi
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool func_return_value_after_called_func (char *id, bool default_func, bool in_while);

/**
 * Funkce generujici mezikod pro nacteni hodnoty ze vstupu urciteho datoveho typu a prirazeni do promenne.
 * @param id - identifikator promenne, do ktere ma byt cteno
 * @param data_type - typ ctene promenne
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool std_in_read (char *id, int data_type, bool in_while);

/**
 * Funkce generujici podmineny skok
 * @param switch_end - prepinac, ma mit navesti nazev s predponou "$end"?
 * @param index - urceni id navesti
 * @param deep - urceni id navesti
 * @param var1 - promenna, na zaklade ktere bude/nebude skok proveden
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool jumps_func (bool switch_end, int index, int deep, char* var1, bool in_while);

/**
 * Funkce generujici v pripade stejnych dat. typu porovnani dvou vyrazu. Pokud jsou typy odlišné, a jedná so o "=="/"!=" je vysldnou hodnotou nepravda. Vysledna pravdivostni hodnota je ulozena na vrchol zasobniku.
 * @param type - typ relacniho operatoru
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool if_func_start (Token_type type, bool in_while);

/**
 * Funkce generujici nazev navesti zadaneho pomoci dvou hodnot (potrebne pro if a while).
 * @param index - urceni id navesti
 * @param deep - urceni id navesti
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool label_id (int label_index, int label_index_deep, bool in_while);

/**
 * Generuje navesti zadane pomoci dvou hodnot (potrebne pro if a while).
 * @param index - urceni id navesti
 * @param deep - urceni id navesti
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 * @param end_control -prepinac, ma mit navesti nazev s predponou "$end"?
 */
bool generate_label (int label_index, int label_index_deep, bool in_while, bool end_control);

/**
 * Funkce generuje nepodmineny skok na navesti zadane pomoci dvou hodnot (potrebne pro if a while).
 * @param index - urceni id navesti
 * @param deep - urceni id navesti
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 * @param end - prepinac, ma mit navesti nazev s predponou "$end"?
 */
bool jump_func (int label_index, int label_index_deep, bool in_while, bool end);

/**
 * Funkce generuje prirazeni pomocne promenne (predavani parametru ve funkci) do zadane promenne.
 * @param var1 - id promenne pro prirazeni
 * @param var2 - id prirazovane pomocne promenne
 * @param frame_type1 - typ datoveho ramce var1
 * @param frame_type2 - typ datoveho ramce var2
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool move_aux_var (char *var1, char *var2, char frame_type1, char frame_type2, bool in_while);

/**
 * Funkce generuje prirazeni jedne promenne do druhe.
 * @param var1 - id promenne pro prirazeni
 * @param var2 - id prirazovane promenne
 * @param frame_type1 - typ datoveho ramce var1
 * @param frame_type2 - typ datoveho ramce var2
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool move_var (char *var1, char *var2, char frame_type1, char frame_type2, bool in_while);

/**
 * Funkce generujici prvni cast prirazeni. Mozne vyuzit napr. v kombinaci s get_term.
 * @param var - id promenne pro prirazeni
 * @param frame_type - typ datoveho ramce var
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool move_var_first (char *var, char frame_type, bool in_while);

/**
 * Funkce generuje push zadaneho termu na zasobnik.
 * @param type - typ na zasobnik prirazovaneho termu
 * @param atribut - atributy prirazovaneho termu
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool generate_stack_push (Token_type type, tAtribut atribut, bool in_while);

/**
 * Funkce generuje pop ze zasobniku do dane promenne.
 * @param id - id promenne pro prirazeni hodnoty ze zasobniku
 * @param frame_type - typ datoveho ramce var
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool generate_stack_pop (char* id, char frame_type, bool in_while);

/**
 * Funkce generujici kod pro provedeni operaci scitani, odcitani, deleni a nasobeni, vola kontrolu dat. typu. U scitani moznost konkatenace retezcu.
 * @param type - typ provadene operace
 * @param in_while - prepinac generovani do primarniho/pomocneho bufferu
 */
bool stack_operations (Token_type type, bool in_while);

/**
 * Pokud se nachazime v cyklu while, je vystup generovan do pomocneho dynamickeho retezce. Zde po skonceni kopirujeme do primarniho bufferu.
 */
bool while_if_end_copy ();

#endif
