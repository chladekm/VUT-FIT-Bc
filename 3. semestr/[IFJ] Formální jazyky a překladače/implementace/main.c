/**
 * Projekt: Implementace překladače imperativního jazyka IFJ18
 *
 * @brief Main
 *
 * @author Martin Chládek <xchlad16@stud.fit.vutbr.cz>
 * @author Peter Krutý <xkruty00@stud.fit.vutbr.cz>
 * @author Michal Krůl <xkrulm00@stud.fit.vutbr.cz>
 * @author Bořek Reich <xreich06@stud.fit.vutbr.cz>
 */


#include <stdio.h>
#include <stdlib.h>

#include <stdio.h>
#include "parser.h"
#include "scanner.h"
#include "errors.h"
#include "generator.h"

int main (void)
{

	set_file(stdin); // Funkcia definovana v scanneri

	/*----------------------- Inicializacia generatoru -------------------------*/
	init_generator ();

	/* Analysis */
	int result = parse(); // from parser.h
	if (result != COMP_SUCC) {
		strFree(output_buffer);
		print_error(result);
	}

	/*--------------------------- Vypis IFJcode18 ------------------------------*/
	printf("%s\n", output_buffer->string);

	clear_generator ();


	return 0;
}
