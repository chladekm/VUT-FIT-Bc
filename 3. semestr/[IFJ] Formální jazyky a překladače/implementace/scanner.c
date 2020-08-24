/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Implementace scanneru
 *
 * @author Martin Chládek <xchald16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <stdbool.h>

#include "errors.h"
#include "scanner.h"
#include "str.h"

/* Pole klicovych slov */
char *keywords[KWORDS_COUNT] = {
	"def",
	"do",
	"else",
	"end",
	"if",
	"not",
	"nil",
	"then",
	"while"
};

/* Pole nazvu preddefinovanych funkci */
char *default_funcs[DEF_FUNC_COUNT] = {
	"inputi",
	"inputs",
	"inputf",
	"print",
	"length",
	"substr",
	"ord",
	"chr"
};

/* Pole operatoru - string */
char *operators[OP_COUNT] = {
	"+",
	"-",
	"*",
	"/",
	"=",
	"<",
	">",
	"<=",
	">=",
	"==",
	"!=",
};

/* Pole operatoru - char */
char character_operators[CHAR_OP_COUNT] = {
	'+',
	'-',
	'*',
	'/',
	'<',
	'>',
	'!',
};

/* Pole separatoru */
char separators[SEP_COUNT] = {',', ';', '(', ')', '{', '}'};

/* Inicializuje a nastavi soubor */
void set_file(FILE *fr) {
	source_file = fr;
}

/* Inicializuje a nastavi buffer */
void set_buffer(inf_string *string) {
	buffer = string;
}

/* Funkce ktera rozhodne zda se jedna o klicovy identifikator jazyka */
int is_keyword(inf_string *term)
{
	for (int i = 0; i<KWORDS_COUNT;i++)
	{
		if (!strcmp(term->string, keywords[i]))
			return 1;
	}
	return 0;
}

/* Funkce ktera rozhodne zda se jedna o defualt funkci jazyka */
int is_def_func(inf_string *term)
{
	for (int i = 0; i<DEF_FUNC_COUNT;i++)
	{
		if (!strcmp(term->string, default_funcs[i]))
			return 1;
	}
	return 0;
}

/* Funkce ktera rozhodne zda muze byt nacteny znak soucasti operatoru */
int is_operator (char c)
{
	for (int i = 0; i<CHAR_OP_COUNT; i++)
	{
		if (character_operators[i] == c)
			return 1;
	}
	return 0;
}

/* Funkce ktera rozhodne zda muze byt nacteny znak soucasti separatoru */
int is_separator (char c) {
	for (int i = 0; i<SEP_COUNT; i++)
	{
		if (separators[i] == c)
			return 1;
	}
	return 0;
}

/* Funkce, ktera smaze obsah string a vrati prislusnou navratovou hodnotu */
int free_res (inf_string *buffer, int exit_code) {

	strClear (buffer);
	return exit_code;
}

/* Funkce pro prevod retezce na int */
int process_integer (inf_string *buffer, tToken *token) {

	char *endptr;

	int val = (int) strtol(buffer->string, &endptr, 10);
	if (*endptr)
		return 0;

	(*token).token_atribut.int_value = val;

	return 1;
}

/* Funkce pro prevod retezce na float */
int process_float (inf_string *buffer, tToken *token) {

	char *endptr;

	double val = (double) strtod(buffer->string, &endptr);
	if (*endptr)
		return 0;

	(*token).token_atribut.float_value = val;
	return 1;
}

/* Funkce ktera uvolni zdroje scanneru */
int clear_scanner ()
{
	strFree(buffer);

	if (fclose(source_file) == EOF)
		print_error(INTERNAL_ERROR);

	return LEX_OK;
}

/* Hlavni funkce scanneru, pomoci promene token se vraci lexem */
int get_token(tToken *token)
{

	/* Definice vsech stavu */
	typedef enum{
		INIT,
		NUMBER,
		FLOAT,
		FLOAT_FIRST,
		FLOAT_EXP,
		FLOAT_EXP_SIGN,
		FLOAT_EXP_FIRST,
		STRING,
		ESCAPE,
		STRING_HEX_FIRST,
		STRING_HEX_SEC,
		ID_KEYWORD,
		KEYWORD,
		DEF_FUNCTION,
		OPERATOR,
		LINE_COMMENT,
		BLOCK_COMMENT,
		BLOCK_COMMENT_EQUAL,
		BLOCK_COMMENT_BODY,
		BLOCK_COMMENT_START_NEW,
		BLOCK_COMMENT_END,
		BLOCK_COMMENT_EXIT
	}Tstate;

	/* Ridici promena */
	Tstate inpt_state = INIT;

	/*Kontrolni promena cyklu */
	bool TOKEN_SEARCH = true;

	bool prev_was_EOL = true;

	/* Nacitany znak */
	char c = 0;

	/* Pole pro hex. hodnotu pri escape sekvenci a operator */
	char strTemp [3] = {0};

	/* Pro =begin */
	char strCom = '0';

	//char idControl;

	char *endptr;

	static char idControl;

	bool zeroControl = false;

	strClear(buffer);

	(*token).type = T_ERR;


	do
	{
		idControl = c;

		/* Nacteni znaku ze vstupniho souboru */
		if ((c = fgetc(source_file))==EOF) {
			(*token).type = T_EOF;

			if (inpt_state == BLOCK_COMMENT || inpt_state == BLOCK_COMMENT_BODY || inpt_state == BLOCK_COMMENT_END)
				return free_res(buffer, LEX_ERROR);
			else
				return free_res(buffer, LEX_OK);
		}

		//printf ("%c %d\n", c, inpt_state);

		switch(inpt_state)
		{
			case INIT:
				/*Inicializacni stav */

				/* Line comment */
				if (c == '#') {	//Line koment
					inpt_state = LINE_COMMENT;
					prev_was_EOL = false;
				}

				/* Block comment nebo operator */
				else if (c == '=') {
					if (prev_was_EOL == true)
						inpt_state = BLOCK_COMMENT_EQUAL;
					else
						inpt_state = OPERATOR;
					prev_was_EOL = false;
					strTemp[0] = c;
				}

				/* Zacatek string */
				else if (c == '"') {
					prev_was_EOL = false;
					inpt_state = STRING;
				}
				/* Separator */
				else if (is_separator(c)) {
					prev_was_EOL = false;
					if (c == '/')
						(*token).type = T_SLASH;
					else if (c == ',')
						(*token).type = T_COMMA;
					else if (c == ';')
						(*token).type = T_SEMICOL;
					else if (c == '(')
						(*token).type = T_LBRAC;
					else if (c == ')')
						(*token).type = T_RBRAC;
					else if (c == '{')
						(*token).type = T_OPENBRAC;
					else
						(*token).type = T_CLOSEBRAC;
					return free_res(buffer, LEX_OK);
				}

				/* Cislo */
				else if (isdigit(c)) {
					if (c == '0')
						zeroControl = true;
					prev_was_EOL = false;
					inpt_state = NUMBER;

					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

				}
				/* Pismeno */
				else if (islower(c) || c == '_') {
					prev_was_EOL = false;

					inpt_state = ID_KEYWORD;

					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}

				/* Operator */
				else if (is_operator(c)) {
					prev_was_EOL = false;
					strTemp[0] = c;
					inpt_state = OPERATOR;
				}

				/* Whitespace */
				else if (isspace(c)) {
					if (c == '\n') {
						(*token).type = T_EOL;
						prev_was_EOL = true;
						return free_res(buffer, LEX_OK);
					}
					else {
						inpt_state = INIT;
						prev_was_EOL = false;
					}
				}

				else
					return free_res(buffer, LEX_ERROR);

				break;


			case NUMBER:
				/* Stav cislo */
				if (isdigit(c)) {
					if (zeroControl == true)
						return free_res(buffer, LEX_ERROR);
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else if (c == '.') {
					inpt_state = FLOAT_FIRST;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else if (tolower(c) == 'e') {
					inpt_state = FLOAT_EXP_FIRST;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else {
					ungetc (c, source_file);

					if (islower(c))
						return free_res(buffer, LEX_ERROR);

					(*token).type = T_INT;
					if (process_integer(buffer, token))
						return free_res(buffer, LEX_OK);
					else
						return free_res(buffer, LEX_ERROR);
				}

				break;


			case FLOAT_FIRST:
				/* Stav pro prvni znak po desetinne carce */
				if (!isdigit(c))
					return free_res(buffer, LEX_ERROR);
				else {
					inpt_state = FLOAT;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}

			break;


			case FLOAT:
				/* Stav pro desetinne cislo */
				if (isdigit(c)) {
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else if (tolower(c) == 'e') {
					inpt_state = FLOAT_EXP_FIRST;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else {

					if (islower(c))
						return free_res(buffer, LEX_ERROR);

					ungetc (c, source_file);
					(*token).type = T_FLOAT;
					if (process_float (buffer, token))
						return free_res(buffer, LEX_OK);
					else
						return free_res(buffer, LEX_ERROR);
				}
			break;


			case FLOAT_EXP_FIRST:
				/* Stav pro prvni znak pri exponencnim tvaru */
				if (isdigit(c)) {
					inpt_state = FLOAT_EXP;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else if (c == '+' || c == '-') {
					inpt_state = FLOAT_EXP_SIGN;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}
				else
					return free_res(buffer, LEX_ERROR);

				break;


			case FLOAT_EXP_SIGN:
				/* Stav pro pripad ze se v exponencnim tvaru vyskytuje znamenko */
				if (!isdigit(c))
					free_res(buffer, LEX_ERROR);
				else {
					inpt_state = FLOAT_EXP;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}

			break;


			case FLOAT_EXP:
				/* Stav pro exponencni tvar desetinneho cisla */
				if (!isdigit(c)) {
					(*token).type = T_FLOAT;
					ungetc(c, source_file);
					if (process_float (buffer, token))
						return free_res(buffer, LEX_OK);
					else
						return free_res(buffer, LEX_ERROR);
				}
				else
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
			break;


			case STRING:
				/* Stav pro retezec */
				if (c < 32)
					return free_res(buffer, LEX_ERROR);
				else if (c == '\\')
					inpt_state = ESCAPE;
				else if (c == '"') {
					(*token).type = T_STRING;
					token->token_atribut.str_value = (char*) malloc ((*buffer).strSize*sizeof (char));
					strcpy(token->token_atribut.str_value, buffer->string);
					return free_res(buffer, LEX_OK);				}
				else
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

				break;


			case ESCAPE:
				/* Stav pro escape sekvenci retezce */
				if (c < 32)
					return free_res(buffer, LEX_ERROR);

				else if (c == 'n') {
					c = '\n';
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

					inpt_state = STRING;
				}

				else if (c == '"') {
					c = '"';
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

					inpt_state = STRING;
				}

				else if (c == 't') {
					c = '\t';
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

					inpt_state = STRING;
				}

				else if (c == 's') {
					c = ' ';
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

					inpt_state = STRING;
				}
				else if (c == '\\') {
					c = '\\';
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);

					inpt_state = STRING;
				}

				else if (c == 'x')
					inpt_state = STRING_HEX_FIRST;

				else
					return free_res(buffer, LEX_ERROR);

				break;


			case STRING_HEX_FIRST:
				/* Stav pro prvni znak sestnactkoveho kodu symbolu */
				if (isxdigit(c)) {
					inpt_state = STRING_HEX_SEC;
					strTemp [0] = c;
				}
				else
					return free_res(buffer, LEX_ERROR);

				break;


			case STRING_HEX_SEC:
				/* Stav pro druhz znak sestnactkoveho kodu symbolu */
				inpt_state = STRING;

				if (isxdigit(c)) {

					strTemp [1] = c;

					int val = (int) strtol(strTemp, &endptr, 16);
					//printf ("%d\n", val);
					if (*endptr)
						return free_res(buffer, LEX_ERROR);

					if (val <= '~') {
						c = (char) val;
						if (strAddChar(buffer, c))
							return free_res(buffer, LEX_ERROR);
					}
				}
				else {

					ungetc(c, source_file);

					int val = (int) strtol(strTemp, &endptr, 16);
					if (*endptr)
						return free_res(buffer, LEX_ERROR);

					c = (char) val;
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
				}

				break;


			case ID_KEYWORD:
				/* Stav pro vstup kdy se porad nevi zda jde o identifikator nebo klicove slovo */
				if (isalnum(c) || c == '_' || c == '!' || c == '?') {
					if (strAddChar(buffer, c))
						return free_res(buffer, LEX_ERROR);
					if (idControl == '!' || idControl == '?')
						return free_res(buffer, LEX_ERROR);

				}
				else {
					if (is_keyword(buffer)) {
						inpt_state = KEYWORD;
						ungetc(c, source_file);
					}
					else if (is_def_func(buffer)) {
						inpt_state = DEF_FUNCTION;
						ungetc(c, source_file);
					}
					else {
						(*token).type = T_ID;
						token->token_atribut.str_value = (char*) malloc ((*buffer).strSize*sizeof (char));
						strcpy(token->token_atribut.str_value, buffer->string);
						ungetc(c, source_file);
						return free_res(buffer, LEX_OK);
					}

				}

				break;

			case KEYWORD:
				/* Stav pro klicove slovo */

				for (int i=0; i<KWORDS_COUNT; i++) {
					if (!strcmp(buffer->string, keywords[i])) {
						/* Vyber klicoveho slova podle indexu v poli klicovych slov */
						switch (i){

							case 0:
								(*token).type = T_DEF;
								break;
							case 1:
								(*token).type = T_DO;
								break;
							case 2:
								(*token).type = T_ELSE;
								break;
							case 3:
								(*token).type = T_END;
								break;
							case 4:
								(*token).type = T_IF;
								break;
							case 5:
								(*token).type = T_NOT;
								break;
							case 6:
								(*token).type = T_NIL;
								break;
							case 7:
								(*token).type = T_THEN;
								break;
							case 8:
								(*token).type = T_WHILE;
								break;
						}
						break;
					}
				}

				ungetc(c, source_file);

				return free_res(buffer, LEX_OK);

				break;


			case DEF_FUNCTION:
				/* Stav pro predeefinovanou funkci jazyka */

				for (int i=0; i<DEF_FUNC_COUNT; i++) {
					if (!strcmp(buffer->string, default_funcs[i])) {
						/* Vyber preddefinovane funkce dle indexu v poli funkci */
						switch (i){

							case 0:
								(*token).type = T_INPUTI;
								break;
							case 1:
								(*token).type = T_INPUTS;
								break;
							case 2:
								(*token).type = T_INPUTF;
								break;
							case 3:
								(*token).type = T_PRINT;
								break;
							case 4:
								(*token).type = T_LENGTH;
								break;
							case 5:
								(*token).type = T_SUBSTR;
								break;
							case 6:
								(*token).type = T_ORD;
								break;
							case 7:
								(*token).type = T_CHR;
								break;
						}
						break;
					}
				}

				ungetc(c, source_file);

				return free_res(buffer, LEX_OK);

				break;

			case OPERATOR:
				/* Stav pro operator */

				/* Vyber operatoru podle indexu v poli operatoru */
				if (strTemp[0] == '+') {
					ungetc (c, source_file);
					(*token).type = T_PLUS;
				}
				else if (strTemp[0] == '-') {
					ungetc(c, source_file);
					(*token).type = T_MINUS;
				}
				else if (strTemp[0] == '*') {
					ungetc(c, source_file);
					(*token).type = T_MUL;
				}
				else if (strTemp[0] == '/') {
					ungetc(c, source_file);
					(*token).type = T_DIV;
				}
				else if (strTemp[0] == '=' && c != '=') {
					ungetc(c, source_file);
					(*token).type = T_EQSIGN;
				}
				else if (strTemp[0] == '<' && c != '=') {
					ungetc(c, source_file);
					(*token).type = T_LESS;
				}
				else if (strTemp[0] == '>' && c != '=') {
					ungetc(c, source_file);
					(*token).type = T_MORE;
				}
				else if (strTemp[0] == '=' && c == '=') {
					(*token).type = T_EQUAL;
				}
				else if (strTemp[0] == '!' && c == '=') {
					(*token).type = T_NEQUAL;
				}
				else if (strTemp[0] == '<' && c == '=') {
					(*token).type = T_LE;
				}
				else
					(*token).type = T_ME;

				strTemp[0] = '\0';
				return free_res(buffer, LEX_OK);

				break;


			case LINE_COMMENT:
				/* Stav pro radkovy komentar */
				if (c == '\n') {
					inpt_state = INIT;
					ungetc(c, source_file);
					prev_was_EOL = true;
				}

				else if (c == EOF)
					free_res(buffer, LEX_ERROR);

				break;


			case BLOCK_COMMENT_EQUAL:
				/* Stav kdy neni jiste zda jde o blokovy komentar nebo o operator rovna se */
				if (c == 'b') {
					inpt_state = BLOCK_COMMENT;
					strCom = c;
				}
				else {
					ungetc(c, source_file);
					inpt_state = OPERATOR;
				}

				break;


			case BLOCK_COMMENT:
				/* Stav pro zacatek blokoveho komentare */
				if 	((c == 'e' && strCom == 'b') ||
					(c == 'g' && strCom == 'e') ||
					(c == 'i' && strCom == 'g') ||
					(c == 'n' && strCom == 'i'))
						strCom = c;

				else if ((isspace(c))&&(strCom == 'n'))	{
						if (c != '\n') {
							inpt_state = BLOCK_COMMENT_BODY;
							strCom = '0';
						}
						else {
							inpt_state = BLOCK_COMMENT_END;
							strCom = '0';
						}
				}
				else
					return free_res(buffer, LEX_ERROR);

				break;


			case BLOCK_COMMENT_BODY:
				/* Stav pro telo blokoveho komentare */
				if (c == '\n') {
					inpt_state = BLOCK_COMMENT_END;
					strCom = '0';
				}
				else if (strCom == '\n' && c == '=') {
					inpt_state = BLOCK_COMMENT_START_NEW;
					strCom = c;
				}
				else
					strCom = c;
				break;

			case BLOCK_COMMENT_START_NEW:
				if 	((c == 'e' && strCom == 'b') ||
					(c == 'g' && strCom == 'e') ||
					(c == 'i' && strCom == 'g') ||
					(c == 'n' && strCom == 'i'))
						strCom = c;

				else if ((isspace(c))&&(strCom == 'n'))	{
						return free_res (buffer, LEX_ERROR);
				}
				else {
					inpt_state = BLOCK_COMMENT_BODY;
					strCom = '0';
				}
			break;


			case BLOCK_COMMENT_END:
				/* Stav pro konec blokoveho komentare */
				if 	((c == '=' && strCom == '0') ||
					(c == 'e' && strCom == '=') ||
					(c == 'n' && strCom == 'e') ||
					(c == 'd' && strCom == 'n'))
						strCom = c;

				else if (c == '\n' && strCom == 'd') {
						inpt_state = INIT;
						prev_was_EOL = true;
				}
				else if ((isspace(c)) && (strCom == 'd'))
						inpt_state = BLOCK_COMMENT_EXIT;

				else {
					inpt_state = BLOCK_COMMENT_BODY;
					ungetc (c, source_file);
					strCom = '\n';
				}
				break;


			case BLOCK_COMMENT_EXIT:
				/* Stav pro vystup z blokoveho komentare */
				if (c == '\n') {
					inpt_state = INIT;
					prev_was_EOL = true;
				}
			break;
		}
	}while (TOKEN_SEARCH);


	return LEX_OK;
}
