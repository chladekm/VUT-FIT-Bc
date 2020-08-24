/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 *
 * @brief Implementacia parseru
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#include <stdlib.h>
#include <string.h>
#include "parser.h"
#include "scanner.h"
#include "errors.h"
#include "symtable.h"
#include "generator.h"
#include "expression.h"

/******************************************************************************/
/*                            Definicie funkcii                               */
/******************************************************************************/

/******************************* init_par_data ********************************/
int init_par_data(tPData *par_data) {
  bool internal_err = 0;

  /*------------------- Inicializacia globalnej tabulky symbolov -------------*/
  symtable_init(par_data->glob_table, &internal_err);
  if (internal_err) {
    return INTERNAL_ERROR;
  }

  /*------------------- Inicilizacia lokalnej tabulky symbolov --------------*/
  symtable_init(par_data->loc_table, &internal_err);
  if (internal_err) {
    return INTERNAL_ERROR;
  }

  /*------------------- Inicilizacia sym_tab pre main ------------------------*/
  symtable_init(par_data->main_table, &internal_err);
  if (internal_err) {
    return INTERNAL_ERROR;
  }

  /*---------------------- Inicializacia ostatnych dat -----------------------*/
  par_data->label_i = 0;
  par_data->label_deep = 0;

  par_data->freturn_type = 0;
  par_data->freturn_id = NULL;

  par_data->in_main = false;
  par_data->in_var_init = false;
  par_data->in_while = false;
  par_data->rel_op = false;

  par_data->extra_t_used = false;
  par_data->control_par_num = 0;
  par_data->in_function_def = false;

  par_data->default_func_print = false;


  par_data->act_f_id = NULL;
  par_data->act_cf_id = NULL;
  par_data->act_id = NULL;

  return 0;
}

/******************************* free_par_data ********************************/
int free_par_data(tPData *par_data) {
  bool internal_err = 0;

  /*----------------------- Uvolnenie tabuliek symbolov ----------------------*/
  symtable_free(par_data->glob_table, &internal_err);
  symtable_free(par_data->loc_table, &internal_err);
  symtable_free(par_data->main_table, &internal_err);
  if (internal_err) {
    return INTERNAL_ERROR;
  }

  return 0;
}


/************************************ parse ***********************************/
int parse() {
  int result = 0;

  /*-------------------- Inicializacia stringu pre scanner -------------------*/
  inf_string string;
  if (strInit(&string)) {
    return INTERNAL_ERROR;
  }
  /* Do globalnej premmenej bufferu priradim ukazatel stringu */
  set_buffer(&string);

  /*----------------------- Parse data inicializacia ------------------------*/
  tPData par_data;
  if (init_par_data(&par_data)) {
    strFree(&string);
    return INTERNAL_ERROR;
  }

  /*------------------- IFJcode18 - Vypis hlavicky suboru --------------------*/
  file_header();

  /*--------------------------- Nacitaj prvy token ---------------------------*/
  if ((result = get_token(&(par_data.token))) != COMP_SUCC) {
    strFree(&string);
    if (free_par_data(&par_data)) {
      return INTERNAL_ERROR;
    }

    return result;
  }

  /*------------------------- Start <program> rule ---------------------------*/
  result = program(&par_data);

  /*------------------- IFJcode18 - Vypis konca suboru ---------------------- */
  file_end();

  /*----------------------------- Uvolnenie zdrojov -----------------------------*/
  strFree(&string);
  if (free_par_data(&par_data)) {
    return INTERNAL_ERROR;
  }

  return result;
}

/*********************************** program **********************************/
int program(tPData *par_data) {
  int result;
  bool internal_err = 0;

  switch (par_data->token.type) {
    /*<program>->“def” “id” “(” <param> “)” “EOL” <stat> “end” “EOL” <program>*/
    case T_DEF:
      par_data->in_main = false;

      /*------------------------------- "id" ---------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_ID);

      /* Kontrola ci nieje uz rovnako pomenovana funkcia v MAINE */
      if (symtable_search(par_data->main_table, &(par_data->token), &internal_err)) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }

      /* Kontrola a pridanie ID do glob. tabulky symbolov */
      if ((par_data->act_f_id = symtable_insert(par_data->glob_table, &(par_data->token), &internal_err))) {
        /* Polozka bola vytvorena == Funkcia je definovana 1 krat a este nebola volana */
        /* Inicializacia funkcie */
        par_data->act_f_id->symbol_type = S_TYPE_FUNCTION;
        par_data->act_f_id->func_data.arg_num = 0;
        par_data->act_f_id->func_data.defined = true;
        /*---------------- IFJcode18 - Vypis zaciatku funkcie ----------------*/
        func_start (par_data->act_f_id->key);
      }
      /* Polozka nebola vytvorena == Interna chyba / Redefinicia / Definicia az po volani */
      else {
        /* Interna chyba */
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        /*Vyhladam ID funckie, ktora uz je v tabulke symbolov */
        par_data->act_f_id = symtable_search(par_data->glob_table, &(par_data->token), &internal_err);
        /* Interna chyba */
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        /* Redefinicia funkcie == CHYBA */
        if (par_data->act_f_id->func_data.defined == true) {
          return SEM_ERROR_UNDEF;
        }
        /* Ak sa nesplnila predosla podmienka funkcia bola najprv volana a teraz ide byt definovana,
           priznak definovania sa v tomto pripade nastavi az za EOL na konci definicie,
           aby sme vedeli ako pristupovat k parametrom (kontrolovat/pridavat) */
        else {
          par_data->control_par_num = 0;
        /*---------------- IFJcode18 - Vypis zaciatku funkcie ----------------*/
        func_start (par_data->act_f_id->key);
        }
      }
      /* Uvolnujem atribut v tokene (namiesto scanneru)*/
      free(par_data->token.token_atribut.str_value);

      /*-------------------------------- "(" ---------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_LBRAC);

      /*------------------------------ <param> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(param);

      /*-------------------------------- ")" ---------------------------------*/
      CHECK_TOKEN(T_RBRAC);

      /*------------------------------- "EOL" --------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);
      /* Kontrola poctu parametrov v pripade ze funkcia bola najprv volana */
      if (par_data->act_f_id->func_data.defined == false) {
        if (par_data->control_par_num != par_data->act_f_id->func_data.arg_num) {
          return SEM_ERROR_PARAM;
        }
        /* V tomto pripade mozme oznacit funkciu za definovanu aj ked bola najprv volana */
        par_data->act_f_id->func_data.defined = true;
      }
      /* vstupujem do definicie funkcie */
      par_data->in_function_def = true;

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);

      /*------------------------------- "end" --------------------------------*/
      CHECK_TOKEN(T_END);
      /* Uvolnenie lokalnej tabulky symbolov aktualnej funkcie */
      symtable_free(par_data->loc_table, &internal_err);
      if (internal_err) {
        return INTERNAL_ERROR;
      }
      par_data->in_function_def = false;

      /*------------------ IFJcode18 - Vypis konca funkcie -------------------*/
      func_end(par_data->freturn_type, par_data->freturn_id, par_data->act_f_id->key);

      par_data->freturn_type = 0;
      free(par_data->freturn_id);
      par_data->freturn_id = NULL;

      /*------------------------------- "EOL" --------------------------------*/
      /* Dve variany podla toho ci bol nacitany extra token*/
      if (par_data->extra_t_used) {
        CHECK_EXTRA_TOKEN(T_EOL);
      }
      else {
        GET_TOKEN();
        CHECK_TOKEN(T_EOL);
      }

      /*----------------------------- <program> ------------------------------*/
      GET_TOKEN();
      CALL_RULE(program);
      return COMP_SUCC;

    /*------------- <program> → “EOL” <program> (nove pravidlo) --------------*/
    case T_EOL:
      GET_TOKEN();
      CALL_RULE(program);
      return COMP_SUCC;

    /*-------------------------- <program> → “EOF” ---------------------------*/
    case T_EOF:

      /* Overenie ci boli vsetky volane funkcie definovane */
      if (!(symtable_foreach(par_data->glob_table, &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }

      /* Uvolnenie globalnej tabulky symbolov s funkciami */
      symtable_free(par_data->glob_table, &internal_err);
      if (internal_err) {
        return INTERNAL_ERROR;
      }

      /* Uvolnenie tabulky symbolov pre main */
      symtable_free(par_data->main_table, &internal_err);
      if (internal_err) {
        return INTERNAL_ERROR;
      }

      return COMP_SUCC;

    /*-------------------- <program> → <stat> <program> ----------------------*/
    case T_ID:
    case T_INT:
    case T_FLOAT:
    case T_STRING:
    case T_IF:
    case T_WHILE:
    case T_INPUTI:
  	case T_INPUTS:
  	case T_INPUTF:
  	case T_PRINT:
  	case T_LENGTH:
  	case T_SUBSTR:
  	case T_ORD:
  	case T_CHR:
    case T_LBRAC:
    case T_NIL:
      par_data->in_function_def = false;
      par_data->in_main = true;

      /*------------------------------- <stat> -------------------------------*/
      CALL_RULE(stat);

      /*----------------------------- <program> ------------------------------*/
      CALL_RULE(program);
      return COMP_SUCC;
    default:
      return SYNTAX_ERROR;
  }
}

/************************************ param ***********************************/
int param(tPData *par_data) {
  int result = 0;
  bool internal_err = 0;

  switch (par_data->token.type) {
    /*---------------------- <param> → “id” <param_list> ---------------------*/
    case T_ID:
      /* Pridavanie nazvu parametra do funkcie v oboch pripadoch */
      /* Kontrola ci parameter nema zhodny nazov s dakou funkciou */
      if (symtable_search(par_data->glob_table, &(par_data->token), &internal_err)) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }
      /* Kontrola a pridanie parametru do lok. tabulky symbolov */
      if (!(par_data->act_id = symtable_insert(par_data->loc_table, &(par_data->token), &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }
      /* Inicilizacia 1 parametru */
      par_data->act_id->symbol_type = S_TYPE_VARIABLE;
      /*----------------- IFJcode18 - Vypis 1 parametru ----------------------*/
      var_definition (par_data->act_id->key, 'L', false);
	  move_var (par_data->act_id->key, "%1", 'L', 'L', par_data->in_while);
      /* Funkcia nebola volana pred samotnou definiciou => Idem navysovat pocet parametrov vo funkcii */
      if (par_data->act_f_id->func_data.defined == true) {
        /* Pocet parametrov funkcie + 1 */
        (par_data->act_f_id->func_data.arg_num)++;
      }
      /* Funkcia bola volana pred samotnou definiciou => Idem navysovat pocet parametrov na kontrolu */
      else {
        /* Pocet parametrov funkcie + 1 (pre kontrolu) */
        (par_data->control_par_num)++;
      }
      /* Uvolnenie str_value v tokene po praci s ID (namiesto scanneru)*/
      free(par_data->token.token_atribut.str_value);

      /*--------------------------- <param_list> -----------------------------*/
      GET_TOKEN();
      CALL_RULE(param_list);
      return COMP_SUCC;

    /*----------------------------- <param> → ε ------------------------------*/
    default:
      /* Pocet parametrov == 0 */
      if ((par_data->act_f_id->func_data.defined) == true) {
        par_data->act_f_id->func_data.arg_num = 0;
      }
      else {
        par_data->control_par_num = 0;
      }
      return COMP_SUCC;
  }
}

/********************************* param_list *********************************/
int param_list(tPData *par_data) {
  int result = 0;
  bool internal_err = 0;
  static int param_num = 1;

  switch (par_data->token.type) {
    /*----------------- <param_list> → “,” “id” <param_list> -----------------*/
    case T_COMMA:

      /*-------------------------------- "id" --------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_ID);
      /* Kontrola ci parameter nema zhodny nazov s dakou funkciou */
      if (symtable_search(par_data->glob_table, &(par_data->token), &internal_err)) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }
      /* Kontrola a pridanie parametru do lok. tabulky symbolov */
      if (!(par_data->act_id = symtable_insert(par_data->loc_table, &(par_data->token), &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }
      /* Inicilizacia parametru */
      par_data->act_id->symbol_type = S_TYPE_VARIABLE;
      /*-------------- IFJcode18 - Vypis dalsich parametrov funkcie ----------*/
      char param_str[MAX_DIGITS];
	  sprintf(param_str, "%d", ++param_num);
	  var_definition (par_data->act_id->key, 'L', false);
	  move_aux_var (par_data->act_id->key, param_str, 'L', 'L', par_data->in_while);

      /* Funkcia nebola volana pred samotnou definiciou => Idem navysovat pocet parametrov vo funkcii */
      if (par_data->act_f_id->func_data.defined == true) {
        /* Pocet parametrov funkcie + 1 */
        (par_data->act_f_id->func_data.arg_num)++;
      }
      /* Funkcia bola volana pred samotnou definiciou => Idem navysovat pocet parametrov na kontrolu */
      else {
        /* Pocet parametrov funkcie + 1 (pre kontrolu) */
        (par_data->control_par_num)++;
      }
      /* Uvolnenie str_value v tokene po praci s ID (namiesto scanneru)*/
      free(par_data->token.token_atribut.str_value);

      /*---------------------------- <param_list> ----------------------------*/
      GET_TOKEN();
      CALL_RULE(param_list);
      return COMP_SUCC;

    /*-------------------------- <param_list> → ε ----------------------------*/
    default:
      return COMP_SUCC;
  }
}

/************************************ stat ************************************/
int stat(tPData *par_data) {
  int result = 0;
  bool internal_err = 0;

  switch (par_data->token.type) {
    /*----------------- <stat> -> <expression> "EOL" <stat> ------------------*/
    case T_INT:
    case T_STRING:
    case T_FLOAT:
    case T_NIL:
    case T_LBRAC:

      /* Inicilizacia parametrov func_end pre generator - VYRAZ */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          par_data->freturn_type = 3;
          free(par_data->freturn_id);
          par_data->freturn_id = NULL;
        }
      }

      /*---------------------------- <expression> ----------------------------*/
      CALL_RULE(expression);

      /*----------------------------- "EOL" ----------------------------*/
      CHECK_TOKEN(T_EOL);

      /*---------------------------- <stat> ----------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);
      return COMP_SUCC;

    /* <stat> → <expression> "EOL" <stat> / <func_call> "EOL" <stat> / "id" <var_init> "EOL" */
    case T_ID:
      /* Kontrola ID v tabulke funkcii, ak najde, je to funkcia */
      if ((par_data->act_cf_id = symtable_search(par_data->glob_table, &(par_data->token), &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        /*---------- <stat> → <func_call> "EOL" <stat> (definovany) ----------*/

        /*--------------------------- <func_call> ----------------------------*/
        CALL_RULE(func_call);

        /*-------------------------------- "EOL" ------------------------------*/
        CHECK_TOKEN(T_EOL);

        /*------------------------------- <stat> ------------------------------*/
        GET_TOKEN();
        CALL_RULE(stat);
        return COMP_SUCC;
      }
      else {
        /* Nacitam extra token do par_data->extra_token */
        GET_EXTRA_TOKEN();

		par_data->return_id_var_tok = par_data->token;

        switch (par_data->extra_token.type) {
          /*------------ <stat> → “id” <var_init> "EOL" <stat> ---------------*/
          case T_EOL:
          case T_EQSIGN:
            par_data->in_var_init = true;

            /* Kontrola a pridanie ID do lok. tabulky symbolov */
            if (!(par_data->act_id = symtable_search(CHOOSE_SYMTAB(par_data->main_table, par_data->loc_table), &(par_data->token), &internal_err))) {
              if (internal_err) {
                return INTERNAL_ERROR;
              }
              par_data->act_id = symtable_insert(CHOOSE_SYMTAB(par_data->main_table, par_data->loc_table), &(par_data->token), &internal_err);
              if (internal_err) {
                return INTERNAL_ERROR;
              }
              /* Inicilizacia itemu (ID) */
              par_data->act_id->symbol_type = S_TYPE_VARIABLE;
              /*---------- IFJcode18 - Definicia lokalnej premmennej ---------*/
              var_definition(par_data->act_id->key, 'L', false);
            }

            /* Inicilizacia parametrov func_end pre generator - Premenna */
            if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
              if (par_data->label_deep == 0) {   /* Ak nie som v if / while */

                par_data->freturn_type = 1;
                free(par_data->freturn_id);
                par_data->freturn_id = malloc(sizeof(char) * (strlen(par_data->act_id->key) + 1));
                strcpy(par_data->freturn_id, par_data->act_id->key);
              }
            }

            /* Uvolnenie str_value v tokene po praci s ID (namiesto scanneru)*/
            free(par_data->token.token_atribut.str_value);
            /*----------------------------- <var_init> -----------------------------*/
            CALL_RULE(var_init);

            par_data->in_var_init = false;


            /*------------------------------- "EOL" --------------------------------*/
            if (par_data->extra_t_used) {
              CHECK_EXTRA_TOKEN(T_EOL);
            }
            else {
              CHECK_TOKEN(T_EOL);
            }

            /*------------------------------- <stat> -------------------------------*/
            GET_TOKEN();
            CALL_RULE(stat);

            return COMP_SUCC;

          /*---------------- <stat> → <expression> "EOL" <stat> --------------*/
          case T_PLUS:
          case T_MINUS:
          case T_MUL:
          case T_DIV:
          case T_LESS:
          case T_MORE:
          case T_LE:
          case T_ME:
          case T_EQUAL:
          case T_NEQUAL:

            /* Inicilizacia parametrov func_end pre generator - VYRAZ */
            if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
              if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
                par_data->freturn_type = 3;
                free(par_data->freturn_id);
                par_data->freturn_id = NULL;
              }
            }

            /*---------------------------- <expression> ----------------------------*/
              CALL_RULE(expression);

            /*----------------------------- "EOL" ----------------------------*/
            CHECK_TOKEN(T_EOL);

            /*---------------------------- <stat> ----------------------------*/
            GET_TOKEN();
            CALL_RULE(stat);
            return COMP_SUCC;

          /*-------- <stat> → <func_call> "EOL" <stat> (nedefinovany) --------*/
          case T_LBRAC:
          case T_FLOAT:
          case T_INT:
          case T_STRING:
          case T_NIL:
          case T_ID:
            /*----------------- Nedefinovany <func_call> ---------------------*/
            CALL_RULE(func_call);

            /*----------------------------- "EOL" ----------------------------*/
            CHECK_TOKEN(T_EOL);

            /*---------------------------- <stat> ----------------------------*/
            GET_TOKEN();
            CALL_RULE(stat);
            return COMP_SUCC;
          default:
            return SYNTAX_ERROR;
        }
    }
    /* <stat> → “if” <expression> “then” “EOL” <stat> “else” “EOL” <stat> “end”
                "EOL" <stat> */
    case T_IF:

	  par_data->in_while = true;

      /* Inicilizacia parametrov func_end pre generator - IF */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
          par_data->freturn_type = 0;
          free(par_data->freturn_id);
          par_data->freturn_id = NULL;
      }

      /* Praca s labelami if / while */
      if (par_data->label_deep == 0) {
        (par_data->label_i)++;
        (par_data->label_deep)++;
      }
      else {
        (par_data->label_deep)++;
      }

      /*---------------------------- <expression> ----------------------------*/
      GET_TOKEN();
      CALL_RULE(expression);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      strAddString(output_buffer, "\n# IF start\n");

      if (par_data->rel_op) {
        jumps_func(false, par_data->label_i, par_data->label_deep, EXP_RETURN_VAL_ID, par_data->in_while);
      }
	    else {
        jumps_func_non_rel (par_data->label_i, par_data->label_deep, par_data->in_while, false);
      }

      /*------------------------------- "then" -------------------------------*/
      CHECK_TOKEN(T_THEN);

      /*-------------------------------- "EOL" -------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      jump_func(par_data->label_i, par_data->label_deep, par_data->in_while, true);
	    par_data->rel_op = false;
      /*------------------------------- "else" -------------------------------*/
      CHECK_TOKEN(T_ELSE);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      generate_label(par_data->label_i, par_data->label_deep, par_data->in_while, false);

      /*-------------------------------- "EOL" --------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);

      /*-------------------------------- "end" -------------------------------*/
      CHECK_TOKEN(T_END);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      generate_label(par_data->label_i, par_data->label_deep, par_data->in_while, true);
	    strAddString(output_buffer, "# IF end\n\n");

      /* Praca s labelami if / while */
      if (par_data->label_deep != 0) {
        (par_data->label_deep)--;
      }

      /*-------------------------------- "EOL" -------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);

	    par_data->in_while = false;
	    while_if_end_copy ();

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);

      return COMP_SUCC;

    /*-- <stat> → “while” <expression> “do” “EOL” <stat> “end” "EOL" <stat> --*/
    case T_WHILE:

	  par_data->in_while = true;

      /* Inicilizacia parametrov func_end pre generator - WHILE */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
          par_data->freturn_type = 0;
          free(par_data->freturn_id);
          par_data->freturn_id = NULL;
      }

      /* Praca s labelami if / while */
      if (par_data->label_deep == 0) {
        (par_data->label_i)++;
        (par_data->label_deep)++;
      }
      else {
        (par_data->label_deep)++;
      }

	   /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      strAddString(output_buffer, "\n# WHILE start\n");
	  generate_label(par_data->label_i, par_data->label_deep, par_data->in_while, false);

      /*---------------------------- <expression> ----------------------------*/
      GET_TOKEN();
      CALL_RULE(expression);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      if (par_data->rel_op)
		jumps_func(true, par_data->label_i, par_data->label_deep, EXP_RETURN_VAL_ID, par_data->in_while);
	  else
		jumps_func_non_rel (par_data->label_i, par_data->label_deep, par_data->in_while, true);

      /*-------------------------------- "do" --------------------------------*/
      CHECK_TOKEN(T_DO);

      /*-------------------------------- "EOL" -------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);

      /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      jump_func(par_data->label_i, par_data->label_deep, par_data->in_while, false);
  	  generate_label(par_data->label_i, par_data->label_deep, par_data->in_while, true);

	  par_data->rel_op = false;
	  par_data->in_while = false;
	  while_if_end_copy ();

	  strAddString(output_buffer, "# WHILE end\n\n");

      /*-------------------------------- "end" -------------------------------*/
      CHECK_TOKEN(T_END);
      /* Praca s labelami if / while */
      if (par_data->label_deep != 0) {
        (par_data->label_deep)--;
      }

      /*-------------------------------- "EOL" -------------------------------*/
      GET_TOKEN();
      CHECK_TOKEN(T_EOL);

      /*------------------------------- <stat> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);
      return COMP_SUCC;

      /*----------- <stat> → <func_call> <stat> (vstavane funkcie) -----------*/
    case T_INPUTI:
  	case T_INPUTS:
  	case T_INPUTF:
  	case T_PRINT:
  	case T_LENGTH:
  	case T_SUBSTR:
  	case T_ORD:
  	case T_CHR:
      /*--------------------------- <func_call> ----------------------------*/
      par_data->act_cf_id = NULL; /* NULL pretoze vsatavane funkcie nebudem ukladat do tabulky symbolov */
      CALL_RULE(func_call);

      /*-------------------------------- "EOL" ------------------------------*/
      CHECK_TOKEN(T_EOL);

      /*------------------------------- <stat>-------------------------------*/
      GET_TOKEN();
      CALL_RULE(stat);
      return COMP_SUCC;

      /*----------------------- <stat> → "EOL" <stat> ------------------------*/
    case T_EOL:
      GET_TOKEN();
      CALL_RULE(stat);
      return COMP_SUCC;
    default:
        /*------------------------------- <stat> → ε --------------------------*/
        return COMP_SUCC;
  }
}

/********************************** var_init **********************************/
int var_init(tPData *par_data) {
  int result = 0;
  bool internal_err = 0;

  switch (par_data->extra_token.type) {
    /*------------- <var_init> → “=” <expression>/<func_call> ----------------*/
    case T_EQSIGN:
      /* Nebudem volat CHECK_EXTRA_TOKEN, takze musim nastavit na false explicitne */
      par_data->extra_t_used = false;
      GET_TOKEN();

      /*-------------------- <var_init> → “=” <expression> -------------------*/
      if (((par_data->token.type >= T_INT) && (par_data->token.type <= T_STRING)) || (par_data->token.type == T_LBRAC)) {

        /*---------------------------- <expression> ----------------------------*/
        CALL_RULE(expression);

        /*------------- IFJcode18 - [Dokomentovat] -------------------*/
        move_var(par_data->act_id->key, EXP_RETURN_VAL_ID, 'L', 'G', par_data->in_while);

        return COMP_SUCC;
      }
      /* Kontrola ID v tabulke funkcii, ak najde, je to funkcia */
      else if ((par_data->act_cf_id = symtable_search(par_data->glob_table, &(par_data->token), &internal_err))) {

		    par_data->default_func = false;

        /*------------------- <var_init> -> = <func_call> -------------------*/
        if (internal_err) {
          return INTERNAL_ERROR;
        }

        /*--------------------------- <func_call> ----------------------------*/
        CALL_RULE(func_call);

		    func_return_value_after_called_func (par_data->act_id->key, par_data->default_func, par_data->in_while);

        return COMP_SUCC;
      }
      else if ((T_INPUTI <= par_data->token.type) && (par_data->token.type <= T_CHR)) {

		    if ((T_INPUTI <= par_data->token.type) && (par_data->token.type <= T_PRINT)) {
          par_data->default_func = true;
        }
		    else {
          par_data->default_func = false;
        }

        /*------------- <var_init> -> = <func_call> (vstavany) ---------------*/

        /*--------------------------- <func_call> ----------------------------*/
        par_data->act_cf_id = NULL; /* NULL pretoze vsatavane funkcie nebudem ukladat do tabulky symbolov */
        CALL_RULE(func_call);

		    func_return_value_after_called_func (par_data->act_id->key, par_data->default_func, par_data->in_while);

		    return COMP_SUCC;
      }
      else {
        GET_EXTRA_TOKEN();
        switch (par_data->extra_token.type) {
          case T_PLUS:
        	case T_MINUS:
        	case T_MUL:
          case T_DIV:
          case T_LESS:
	        case T_MORE:
	        case T_LE:
	        case T_ME:
	        case T_EQUAL:
	        case T_NEQUAL:
          case T_EOL:  // ak je v priradeni iba jedna premenna

            /*-------------------------- <expression> ------------------------*/
            CALL_RULE(expression);

      			/*------------- IFJcode18 - [Dokomentovat] -------------------*/
      			move_var(par_data->act_id->key, EXP_RETURN_VAL_ID, 'L', 'G', par_data->in_while);

            return COMP_SUCC;
          case T_LBRAC:
          case T_FLOAT:
          case T_INT:
          case T_STRING:
          case T_ID:
          case T_NIL:

            /*----------------- Nedefinovany <func_call> ---------------------*/
            CALL_RULE(func_call);
            return COMP_SUCC;
          default:
            return SYNTAX_ERROR;
        }
      }

    /*--------------------------- <var_init> → ε -----------------------------*/
    default:
      return COMP_SUCC;
  }
}

/********************************* func_call **********************************/
int func_call(tPData *par_data) {
  int result = 0;
  bool internal_err = 0;

  switch (par_data->token.type) {
    /*----------------------- <func_call> → “id” <arg> -----------------------*/
    case T_ID:

	  /*------------- IFJcode18 - [Dokomentovat] -------------------*/
      func_param_pass_prep();

      /* Funkcia bola definovana */
      if ((par_data->act_cf_id = symtable_search(par_data->glob_table, &(par_data->token), &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        par_data->act_cf_id->func_data.defined = true;
        par_data->control_par_num = 0;
      }
      /*Funkcia este nebola definovana */
      else {
        /* Ak som v MAINe a volana funkcia este nebola definovana, tak koncim */
        if (!par_data->in_function_def) {
          return SEM_ERROR_UNDEF;
        }
        /* Ak som v definicii funkcii tak volana funkcia nemusi byt definovana */
        else {
          if ((par_data->act_cf_id = symtable_insert(par_data->glob_table, &(par_data->token), &internal_err))) {
            if (internal_err) {
              return INTERNAL_ERROR;
            }
            par_data->act_cf_id->symbol_type = S_TYPE_FUNCTION;
            par_data->act_cf_id->func_data.arg_num = 0;
            par_data->act_cf_id->func_data.defined = false;
          }
        }
      }

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen(par_data->act_cf_id->key) + 1));
            strcpy(par_data->freturn_id, par_data->act_cf_id->key);
          }
        }
      }

      /*------------------------------- <arg> --------------------------------*/
      /* Volam iba ak nemam nacitany extra token, tj. funkcia uz bola definovana */
      if (!par_data->extra_t_used) {
        GET_TOKEN();
      }

      CALL_RULE(arg);
      /* Ak bola funkcia definovana kontrolujem pocet paramtrov */
      if (par_data->act_cf_id->func_data.defined == true) {
        if (par_data->act_cf_id->func_data.arg_num != par_data->control_par_num) {
          return SEM_ERROR_PARAM;
        }
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if ((par_data->act_cf_id->func_data.arg_num > 0)&&(par_data->in_var_init == true)) {
        generator_func_call(par_data->act_id->key,  par_data->act_cf_id->key, par_data->in_while);
      }
      else {
        generator_func_call("",  par_data->act_cf_id->key, par_data->in_while);
      }

	    return COMP_SUCC;

    /*--------------------- <func_call> → “inputs” <arg> ---------------------*/
    case T_INPUTS:
      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("inputs") + 1));
            strcpy(par_data->freturn_id, "inputs");
          }
        }
      }

      /*------------------------------- <arg> --------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 1) */
      if (par_data->control_par_num != INPUTx_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "inputs", par_data->in_while);
      }
  	  else {
        generator_func_call("", "inputs", par_data->in_while);
      }

      return COMP_SUCC;

    /*--------------------- <func_call> → “inputi” <arg> ---------------------*/
    case T_INPUTI:
      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("inputi") + 1));
            strcpy(par_data->freturn_id, "inputi");
          }
        }
      }

      /*------------------------------- <arg> --------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 1) */
      if (par_data->control_par_num != INPUTx_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "inputi", par_data->in_while);
      }
	    else {
        generator_func_call("", "inputi", par_data->in_while);
      }

      return COMP_SUCC;

    /*------------------- <func_call> → “inputf” <arg> -----------------------*/
    case T_INPUTF:
      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("inputf") + 1));
            strcpy(par_data->freturn_id, "inputf");
          }
        }
      }

      /*------------------------------- <arg> --------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 1) */
      if (par_data->control_par_num != INPUTx_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "inputf", par_data->in_while);
      }
	    else {
        generator_func_call("", "inputf", par_data->in_while);
      }

      return COMP_SUCC;

    /*-------------------- <func_call> → “print” <arg> -----------------------*/
    case T_PRINT:

      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("print") + 1));
            strcpy(par_data->freturn_id, "print");
          }
        }
      }

	    par_data->default_func_print = true;

      /*-------------------------------- <arg> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);

      /* Kontrola poctu parametrov (nesmie byt 0) */
      if (par_data->control_par_num == PRINT_NOT_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      //generator_func_call(par_data->act_id->key, "print");

  	  par_data->default_func_print = false;

  	  return COMP_SUCC;

    /*-------------------- <func_call> → “length” <arg> ----------------------*/
    case T_LENGTH:

	  func_param_pass_prep();

      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("length") + 1));
            strcpy(par_data->freturn_id, "length");
          }
        }
      }

      /*-------------------------------- <arg> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 1) */
      if (par_data->control_par_num != LENGTH_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "length", par_data->in_while);
      }
  	  else {
        generator_func_call("", "length", par_data->in_while);
      }

      return COMP_SUCC;

    /*------------------- <func_call> → “substr” <arg> -----------------------*/
    case T_SUBSTR:

	  func_param_pass_prep();

      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("substr") + 1));
            strcpy(par_data->freturn_id, "substr");
          }
        }
      }

      /*-------------------------------- <arg> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 3) */
      if (par_data->control_par_num != SUBSTR_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
  	  if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "substr", par_data->in_while);
      }
  	  else {
        generator_func_call("", "substr", par_data->in_while);
      }

      return COMP_SUCC;

    /*-------------------- <func_call> → “ord” <arg> -------------------------*/
    case T_ORD:

	  func_param_pass_prep();

      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("ord") + 1));
            strcpy(par_data->freturn_id, "ord");
          }
        }
      }

      /*-------------------------------- <arg> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 2) */
      if (par_data->control_par_num != ORD_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "ord", par_data->in_while);
      }
  	  else {
        generator_func_call("", "ord", par_data->in_while);
      }

      return COMP_SUCC;

    /*--------------------- <func_call> → “chr” <arg> ------------------------*/
    case T_CHR:

	    func_param_pass_prep();

      par_data->act_cf_id = NULL;
      par_data->control_par_num = 0;

      /*  Inicilizacia parametrov func_end pre generator - Volanie funkcie */
      if (par_data->in_function_def) {     /* Ak som v definicii funkcie */
        if (par_data->label_deep == 0) {   /* Ak nie som v if / while */
          if (par_data->in_var_init == 0) {
            par_data->freturn_type = 2;
            free(par_data->freturn_id);
            par_data->freturn_id = malloc(sizeof(char) * (strlen("chr") + 1));
            strcpy(par_data->freturn_id, "chr");
          }
        }
      }


      /*-------------------------------- <arg> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(arg);
      /* Kontrola poctu parametrov (musi byt 1) */
      if (par_data->control_par_num != CHR_PARAM_N) {
        return SEM_ERROR_PARAM;
      }
      /*-------------- IFJcode18 - Genorovanie volania funkcii ---------------*/
      if (par_data->in_var_init == true) {
        generator_func_call(par_data->act_id->key, "chr", par_data->in_while);
      }
  	  else {
        generator_func_call("", "chr", par_data->in_while);
      }

      return COMP_SUCC;

    default:
      return SYNTAX_ERROR;
  }
}

/************************************ arg *************************************/
int arg(tPData *par_data) {
  int result = 0;

  if (par_data->extra_t_used) {
    par_data->extra_t_used = false;
    par_data->token = par_data->extra_token;
  }

  switch (par_data->token.type) {
    /*------------------ <arg> → "(" <value> <arg_list> ")" ------------------*/
    case T_LBRAC:

      /*------------------------------ <value> -------------------------------*/
      GET_TOKEN();

      /*---------- Explicitne doplnene pravidlo <arg> → "(" ")" --------------*/
      if (par_data->token.type == T_RBRAC) {
        GET_TOKEN();
        return COMP_SUCC;
      }

      CALL_RULE(value);

      /*----------------------------- <arg_list> -----------------------------*/
      GET_TOKEN();
      CALL_RULE(arg_list);

      /*-------------------------------- ")" ---------------------------------*/
      CHECK_TOKEN(T_RBRAC);
      GET_TOKEN();
      return COMP_SUCC;

    /*--------------------- <arg> → <value> <arg_list> -----------------------*/
    case T_FLOAT:
    case T_INT:
    case T_STRING:
    case T_NIL:
    case T_ID:

      /*------------------------------ <value> -------------------------------*/
      CALL_RULE(value);

      /*----------------------------- <arg_list> -----------------------------*/
      GET_TOKEN();
      CALL_RULE(arg_list);
      return COMP_SUCC;

    /*---------------------------- <arg> → ε ---------------------------------*/
    case T_EOL:
      return COMP_SUCC;
    default:
      return SYNTAX_ERROR;
  }
}

/******************************** arg_list ************************************/
int arg_list(tPData *par_data) {
  int result = 0;

  switch (par_data->token.type) {
    /*------------------ <arg_list> → “,” <value> <arg_list> -----------------*/
    case T_COMMA:

      /*------------------------------ <value> -------------------------------*/
      GET_TOKEN();
      CALL_RULE(value);

      /*----------------------------- <arg_list> -----------------------------*/
      GET_TOKEN();
      CALL_RULE(arg_list);
      return COMP_SUCC;

    /*--------------------------- <arg_list> → ε -----------------------------*/
    default:
      return COMP_SUCC;
  }
}


/*********************************** value ************************************/
int value(tPData *par_data) {
  bool internal_err = 0;

  switch (par_data->token.type) {
    /*-------------------------- <value> → “id” ------------------------------*/
    case T_ID:
      /* Kontrola, ci exituje ID premennej */
      if (!(symtable_search(CHOOSE_SYMTAB(par_data->main_table, par_data->loc_table), &(par_data->token), &internal_err))) {
        if (internal_err) {
          return INTERNAL_ERROR;
        }
        return SEM_ERROR_UNDEF;
      }
      /* Volana funkcia je vstavana */
      if (par_data->act_cf_id == NULL) {
        (par_data->control_par_num)++;

        /*------------ IFJcode18 - Generovanie argumentov funkcie ------------*/
		if (par_data->default_func_print == true) {
			std_out_write (par_data->token, par_data->in_while);
		}
		else
			func_param_passing (par_data->token, par_data->control_par_num, par_data->in_while);

      }
      else {
        /* Volana funkcia je definovana */
        if (par_data->act_cf_id->func_data.defined == true) {
          (par_data->control_par_num)++;
          /*------------ IFJcode18 - Generovanie argumentov funkcie ------------*/
          func_param_passing(par_data->token, par_data->control_par_num, par_data->in_while);
        }
        /* Volana funckia este nieje definovana */
        else {
          /*------------ IFJcode18 - Generovanie argumentov funkcie ------------*/
          func_param_passing(par_data->token, par_data->act_cf_id->func_data.arg_num, par_data->in_while);
          (par_data->act_cf_id->func_data.arg_num)++;
        }
      }
      return COMP_SUCC;

    /*---------------------- <value> → “DOUBLE_NUMBER” -----------------------*/
    case T_FLOAT:
    /*--------------------- <value> → “INTEGER_NUMBER” -----------------------*/
    case T_INT:
    /*------------------------ <value> → “STRING” ----------------------------*/
    case T_STRING:
    /*-------------------------- <value> → nil -------------------------------*/
    case T_NIL:

      /* Volana funkcia je vstavana */
      if (par_data->act_cf_id == NULL) {
        (par_data->control_par_num)++;

        /*------------ IFJcode18 - Generovanie argumentov funkcie ------------*/
		if (par_data->default_func_print == true) {
			std_out_write (par_data->token, par_data->in_while);
		}
		else
			func_param_passing(par_data->token, par_data->control_par_num, par_data->in_while);

      }
      else {
        /* Volana funkcia je definovana */
        if (par_data->act_cf_id->func_data.defined == true) {
          (par_data->control_par_num)++;

          /*------------ IFJcode18 - Generovanie argumentov funkcie ------------*/
          func_param_passing(par_data->token, par_data->control_par_num, par_data->in_while);
        }
        /* Volana funckia este nieje definovana */
        else {
          (par_data->act_cf_id->func_data.arg_num)++;

          /*----------- IFJcode18 - Generovanie argumentov funkcie -----------*/
          func_param_passing(par_data->token, par_data->act_cf_id->func_data.arg_num, par_data->in_while);
        }
      }
      return COMP_SUCC;
    default:
      return SYNTAX_ERROR;
  }
}
