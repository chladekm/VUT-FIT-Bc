/**
* Projekt: Implementace překladače imperativního jazyka IFJ18
*
* @brief Definicia errorov a deklaracia funkcie print_error
*
* @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
* @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
* @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
* @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
*/

#ifndef ERRORS_H_INCLUDED
#define ERRORS_H_INCLUDED

/*------------------------- Defintion of error codes -------------------------*/
#define COMP_SUCC 0
#define LEX_ERROR 1
#define SYNTAX_ERROR 2
#define SEM_ERROR_UNDEF 3
#define SEM_ERROR_COMPAT 4
#define SEM_ERROR_PARAM 5
#define SEM_ERROR_OTHER 6
#define ZERO_DIV_ERROR 9
#define INTERNAL_ERROR 99

/*------------------------ Defintion of error messages -----------------------*/
#define LEX_E_MSG "ERROR: Lexical analysis - wrong structure of actual lexem."
#define SYNTAX_E_MSG "ERROR: Syntax analysis - wrong syntax of program."
#define SEM_UNDEF_E_MSG "ERROR: Semantic analysis - undefined function/variable or attempt to redefine function, ..."
#define SEM_COMPAT_E_MSG "ERROR: Semantic analysis - wrong compatibility of type in arithmetic, string or relational expression."
#define SEM_PARAM_E_MSG "ERROR: Semantic analysis - wrong number of parameters in the function call."
#define SEM_OTHER_E_MSG "ERROR: Sematic analysis - other error."
#define ZERO_DIV_E_MSG "RUNTIME ERROR: Division by zero."
#define INTERNAL_E_MSG "COMPLIER ERROR: Internal error of the compiler (fail of memory allocation, ...)."

/*-------------------------- Declaration of functions ------------------------*/
/**
 * Function throws an error message on stderr and calls exit with exit code.
 *
 * @param error_code Return value of compiler.
 */
void print_error(int error_code);

#endif //ERRORS_H_INCLUDED
