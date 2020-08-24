/**
 *
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 * @brief Deklaracia operaci struktur parseru
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#ifndef PARSER_H_INCLUDED
#define PARSER_H_INCLUDED

#include "scanner.h"
#include "symtable.h"

#define INPUTx_PARAM_N 0
#define PRINT_NOT_PARAM_N 0
#define LENGTH_PARAM_N 1
#define SUBSTR_PARAM_N 3
#define ORD_PARAM_N 2
#define CHR_PARAM_N 1

#define GET_EXTRA_TOKEN()                                                      \
  do {                                                                         \
    if ((result = get_token(&(par_data->extra_token))) != COMP_SUCC) {         \
      return result;                                                           \
    }                                                                          \
    par_data->extra_t_used = true;                                             \
  } while (0);


#define GET_TOKEN()                                                            \
  do {                                                                         \
    if ((result = get_token(&(par_data->token))) != COMP_SUCC) {               \
      return result;                                                           \
    }                                                                          \
  } while (0);

#define CALL_RULE(rule)                                                        \
  if ((result = rule(par_data)) != COMP_SUCC) {                                \
    return result;                                                             \
  }

#define CHECK_TOKEN(_type_)                                                    \
  do {                                                                         \
    if (par_data->token.type != (_type_)) {                                    \
      return SYNTAX_ERROR;                                                     \
    }                                                                          \
  } while (0);

#define CHECK_EXTRA_TOKEN(_type_)                                              \
  do {                                                                         \
    if (par_data->extra_token.type != (_type_)) {                              \
      return SYNTAX_ERROR;                                                     \
    }                                                                          \
    par_data->extra_t_used = false;                                            \
  } while (0);


#define CHOOSE_SYMTAB( _symtabMain_, _symtabLocal_) (par_data->in_main) ? (_symtabMain_): (_symtabLocal_)

/**
 * @struct Parser data
 */
typedef struct par_data {
  tSymtab glob_table;             /* Global symbol table */
  tSymtab loc_table;              /* Local symbol table*/
  tSymtab main_table;             /* Symbol table for Main */

  tToken token;                   /* Token */
  tToken extra_token;             /* Token do zalohy ked nacitavam o jeden naviac */

  int label_i;                    /* Identifikator labelu if/while */
  int label_deep;                 /* Identifikator zanorenia labelu if/while */

  bool in_var_init;
  int freturn_type;               /* Typ navratu funkcie pre generator: 1 == premenna,
                                     2 == funkcia, 3 == vyraz, 0 == while/prazdny if */
  char *freturn_id;               /* Id pre generator: "id" premmenej/funkcie, NULL ak je vyraz/while/prazdny if */

  bool extra_t_used;              /* Priznak urcujuci ci bol nacitany o jeden token naviac */
  int control_par_num;            /* Na kontrolu poctu parametrov */
  bool in_function_def;           /* Is analysis in function? */
  bool in_main;                   /* Je analyza v maine? (v mojej implementacii nieje
                                     doplnkom premmenej in_function_def kvoli spracovaniu parametrom funkcie)*/

  tToken return_id_var_tok;		   /* Proemnna pro ulozeni id promenne, do ktere ma byt prirazena navatova hodnota funcke */
  bool default_func;
  bool default_func_print;

  bool rel_op;
  bool in_while;

  struct tab_item *act_f_id;      /* Actual function indetifier (item) in symbol table */
  struct tab_item *act_cf_id;     /* Actual called function indetifier (item) in symbol table */
  struct tab_item *act_id;        /* Actual indetifier (item) in symbol table */
} tPData;

/**
 * Funkcia inicializuje strukturu par_data
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int init_par_data(tPData *par_data);

/**
 * Funkcia uvolnuje strukturu par_data
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int free_par_data(tPData *par_data);

/**
 * Funkcia zavola syntakticku a semanticku analyzu
 *
 * @return Uspech volanej funkcie
 */
int parse();

/**
 * Funkcia reprezentujuca neterminal <program>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int program(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <param>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int param(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <param_list>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int param_list(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <stat>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int stat(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <var_init>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int var_init(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <func_call>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int func_call(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <arg>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int arg(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <arg_list>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int arg_list(tPData *par_data);

/**
 * Funkcia reprezentujuca neterminal <value>
 *
 * @param par_data Ukazatel na strukturu par_data
 * @return Uspech volanej funkcie
 */
int value(tPData *par_data);

#endif //PARSER_H_INCLUDED
