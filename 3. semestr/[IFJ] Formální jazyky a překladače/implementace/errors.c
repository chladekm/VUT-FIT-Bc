/**
* Projekt: Implementace překladače imperativního jazyka IFJ18
*
* @brief Implementacia error vypisov
*
* @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
* @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
* @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
* @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
*/

#include <stdio.h>
#include <stdlib.h>
#include "errors.h"

/******************************** print_error *********************************/
void print_error(int error_code) {
  switch (error_code) {
    case LEX_ERROR:
      fprintf(stderr, "%s\n", LEX_E_MSG);
      break;
    case SYNTAX_ERROR:
      fprintf(stderr, "%s\n", SYNTAX_E_MSG);
      break;
    case SEM_ERROR_UNDEF:
      fprintf(stderr, "%s\n", SEM_UNDEF_E_MSG);
      break;
    case SEM_ERROR_COMPAT:
      fprintf(stderr, "%s\n", SEM_COMPAT_E_MSG);
      break;
    case SEM_ERROR_PARAM:
      fprintf(stderr, "%s\n", SEM_PARAM_E_MSG);
      break;
    case SEM_ERROR_OTHER:
      fprintf(stderr, "%s\n", SEM_OTHER_E_MSG);
      break;
    case ZERO_DIV_ERROR:
      fprintf(stderr, "%s\n", ZERO_DIV_E_MSG);
      break;
    case INTERNAL_ERROR:
      fprintf(stderr, "%s\n", INTERNAL_E_MSG);
      break;
  }
  exit(error_code);
}
