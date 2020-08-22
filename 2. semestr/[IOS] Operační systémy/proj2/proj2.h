/**
 * The Senate Bus problem
 * Hlavičkový soubor - 2. projekt IOS 2017/18
 * @file proj2.h
 * @author  Martin Chládek
 * @contact xchlad16@stud.fit.vutbr.cz
 * @date    28. 4. 2018
 * @version 7
 */

#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <time.h>

#ifndef proj2_h
#define proj2_h

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>
#include <stdbool.h>
#include <time.h> 
#include <unistd.h> 
#include <semaphore.h>
#include <sys/types.h> 
#include <sys/wait.h> 
#include <sys/ipc.h> 
#include <sys/shm.h> 
#include <sys/stat.h>
#include <sys/mman.h> 
#include <fcntl.h>

/* Inicializace barev */
#define RED   "\x1B[31m"
#define GRN   "\x1B[32m"
#define YEL   "\x1B[33m"
#define BLU   "\x1B[34m"
#define MAG   "\x1B[35m"
#define CYN   "\x1B[36m"
#define WHT   "\x1B[0m"
#define RESET "\x1B[0m"

#define RETURN_OK 0
#define RETURN_ERROR -1
#define RETURN_FORK_ERROR 2
#define RETURN_USAGE_ERROR 1

#define SEM_BUS_NAME "xchlad16.ios_proj2.semaphore.bus"
#define SEM_BUSSTOP_NAME "xchlad16.ios_proj2.semaphore.busstop"
#define SEM_TRIP_NAME "xchlad16.ios_proj2.semaphore.trip"
#define SEM_MAIN_NAME "xchlad16.ios_proj2.semaphore.main"
#define SEM_ENDPROCESS_NAME "xchlad16.ios_proj2.semaphore.endprocess"
#define SEM_WRITE_NAME "xchlad16.ios_proj2.semaphore.write"
#define SEM_BUSBOARDING_NAME "xchlad16.ios_proj2.semaphore.busboarding"
#define SEM_GENERATOR_NAME "xchlad16.ios_proj2.semaphore.generator"

// Funkce udělá z obyčejné globální proměnné sdílenou proměnnou
#define MMAP(pointer) {(pointer) = mmap(NULL, sizeof(*(pointer)), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);}
#define UNMAP(pointer) {munmap((pointer), sizeof((pointer)));}

// Makro pro jméno výstupniho souboru
#define FILE_NAME "proj2.out"

/**
 * @brief Struktura pro ukladani parametru programu 
 * @param R Počet procesů rider
 * @param C Kapacita autobusu
 * @param ART Horní hranice intervalu pro načítání riderů
 * @param ABT Horní hranice intervalu pro simulaci jízdy procesu bus
 */
typedef struct
{
	unsigned int R;
	unsigned int C;
	unsigned int ART;
	unsigned int ABT;
} Tparams;

/**
 * @brief Funkce pro inicializaci sdílených proměnných a semaforů
 */
int init();

/**
 * @brief Funkce k odalokování všech alokovaných položek
 */
void clean_memory();

/**
 * @brief Funkce pro načtení argumentů ze stdin
 * @param argc Počet zadaných argumentů
 * @param argv Ukazatel na pole argumentů
 */
Tparams load_params(int argc, char *argv[]);

/**
 * @brief Proces simulující autobus
 * @param capacity Maximální kapacita autobusu
 * @param delay Horní hranice intervalu pro simulování jízdy autobusu
 */
void process_bus(int capacity, int delay);

/**
 * @brief Proces simulující jednoho člověka (cestujícícho)
 */
void process_rider();

/**
 * @brief Pomocný proces generující cestující
 * @param riders Počet cestujících k vygenerování
 * @param delay Horní hranice intervalu pro pauzu mezi generováním jednotlivých procesů rider
 */ 
void riders_generator(int riders, int delay);

/**
 * @brief Funkce uspí daný proces na náhodný čas z intervalu <0, time>
 * @param time Horní hranice intervalu pro uspání procesu
 */
void random_sleep(int time);

#endif /* proj2_h */
