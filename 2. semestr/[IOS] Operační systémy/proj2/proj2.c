/******************************************************/
/* * *         The Senate Bus problem             * * */
/* * *                                            * * */
/* * *              VUT FIT - IOS                 * * */
/* * *                                            * * */
/* * *              Martin Chládek                * * */
/* * *                April 2018                  * * */
/******************************************************/

#include "proj2.h"


/* Inicializace sdílených proměnných */
int *Rider_ID = NULL;
int *Output_ID = NULL;
int *Busstop_riders_number = NULL;
int *Remaining_riders = NULL;
int *Passengers = NULL;
int *Count_passengers = NULL;
int *Boarding_passengers = NULL;

/* Inicializace semaforů */
sem_t *semafor_bus = NULL; // Procesy, které mohou "do autobusu"
sem_t *semafor_busstop = NULL; // "Bariéra" před zastávkou
sem_t *semafor_trip = NULL; // Nařizuje kdy se mají procesy riders zabít
sem_t *semafor_main = NULL; // Hlavní proces musí skončit jako poslední
sem_t *semafor_endprocess = NULL; // Určuje kdy může skončit hlavní process
sem_t *semafor_write = NULL; // Semafor pro zápis do souboru
sem_t *semafor_busboarding = NULL; // Aby bus počkal než všichni nastoupí
sem_t *semafor_generator = NULL; // Zajišťuje správné ID pro konkrétní proces rider


/* Soubor pro zápis */
FILE *filename;

int init()
{
    filename = fopen(FILE_NAME, "w");

    if(filename == NULL)
    {
        fprintf(stderr, "Could not open the file.\n");
        return RETURN_ERROR;
    }

    /* Vytvoření sdílených proměnných */
    MMAP(Rider_ID);
    MMAP(Output_ID);
    MMAP(Busstop_riders_number);
    MMAP(Remaining_riders);
    MMAP(Passengers);
    MMAP(Count_passengers);
    MMAP(Boarding_passengers);

    if((semafor_bus = sem_open(SEM_BUS_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_busstop = sem_open(SEM_BUSSTOP_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_trip = sem_open(SEM_TRIP_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_main = sem_open(SEM_MAIN_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_endprocess = sem_open(SEM_ENDPROCESS_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_write = sem_open(SEM_WRITE_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_busboarding = sem_open(SEM_BUSBOARDING_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    if((semafor_generator = sem_open(SEM_GENERATOR_NAME, O_CREAT | O_EXCL, 0666, 1))== SEM_FAILED) return RETURN_ERROR;
    
    /* Ujištění, že jsou všechny semafory zamknuté */
    sem_trywait(semafor_busstop); 
    sem_trywait(semafor_bus);
    sem_trywait(semafor_trip);
    sem_trywait(semafor_main); 
    sem_trywait(semafor_endprocess);
    sem_trywait(semafor_busboarding);

    return RETURN_OK;
}

void clean_memory()
{
    UNMAP(Rider_ID);
    UNMAP(Output_ID);
    UNMAP(Busstop_riders_number);
    UNMAP(Remaining_riders);
    UNMAP(Passengers);
    UNMAP(Count_passengers);
    UNMAP(Boarding_passengers);

    sem_close(semafor_bus);
    sem_close(semafor_busstop);
    sem_close(semafor_trip);
    sem_close(semafor_main);
    sem_close(semafor_endprocess);
    sem_close(semafor_write);
    sem_close(semafor_busboarding);
    sem_close(semafor_generator);
    
    sem_unlink(SEM_BUS_NAME);
    sem_unlink(SEM_BUSSTOP_NAME);
    sem_unlink(SEM_TRIP_NAME);
    sem_unlink(SEM_MAIN_NAME);
    sem_unlink(SEM_ENDPROCESS_NAME);
    sem_unlink(SEM_WRITE_NAME);
    sem_unlink(SEM_BUSBOARDING_NAME);
    sem_unlink(SEM_GENERATOR_NAME);

    if(filename != NULL)
    {
        fclose(filename);
    }
}

Tparams load_params(int argc, char *argv[])
{
    Tparams params;
    char *end_pointer;
    long int test;
    bool error = false;

    if(argc != 5)
    {
        fprintf(stderr, "ERROR - wrong number of arguments.\n");
        exit(1);
    }

    /* R > 0 */
    test = strtol(argv[1], &end_pointer, 10);

    if (test < 0 || test > UINT_MAX)
    {
        error=true;
    }
    
    params.R = (unsigned int) test;

    if(!(params.R > 0 && *end_pointer == '\0'))
    {
        error=true;
    }

    /* C > 0 */
    test = strtol(argv[2], &end_pointer, 10);
    if (test < 0 || test > UINT_MAX)
        {
            error=true;
        }
    params.C = (unsigned int) test;

    if(!(params.C > 0 && *end_pointer == '\0'))
    {
        error=true;
    }

    /* ART >= 0 && ART <= 1000 */
    test = strtol(argv[3], &end_pointer, 10);
    if (test < 0 || test > UINT_MAX)
        {
            error=true;
        }
    params.ART = (unsigned int) test;

    if(!(params.ART <= 1000 && *end_pointer == '\0'))
    {
        error=true;
    }

     /* ABT >= 0 && ABT <= 1000 */
    test = strtol(argv[4], &end_pointer, 10);
    if (test < 0 || test > UINT_MAX)
        {
            error=true;
        }
    params.ABT = (unsigned int) test;

    if(!(params.ABT <= 1000 && *end_pointer == '\0'))
    {
        error=true;
    }

    if(error)
    {
        fprintf(stderr, "Incorrect format of arguments\n");
        exit(RETURN_USAGE_ERROR);
    }
    return params;
}

void process_bus(int capacity, int delay)
{
    /* Převod na milisenkundy z mikrosekund*/
    delay = delay * 1000;

    sem_wait(semafor_write);
    fprintf(filename, "%d    \t: BUS    \t: start\n", (++*Output_ID));
    fflush(filename);
    sem_post(semafor_write);
    
    // DEBUG: printf(RED "%d: BUS: start\n", (++*Output_ID));

    /* Proces bus bude fungovat dokud neodvezl všechny cestující */
    while(*Remaining_riders > 0)
    {
        sem_wait(semafor_write);
        fprintf(filename, "%d    \t: BUS    \t: arrival\n", (++*Output_ID));
        fflush(filename);
        sem_post(semafor_write);

        // DEBUG: printf(MAG "%d: BUS: arrival\n", (++*Output_ID));

        sem_trywait(semafor_endprocess);
        
        *Count_passengers = 0;
        *Boarding_passengers = 0;

        sem_trywait(semafor_busstop);

        // DEBUG: printf(YEL "DEBUG: Prichod na zastavku zavren\n" RESET);

        /* Passengers - aktuální počet cestujících v autobuse */
        *Passengers = 0;

        if( *Busstop_riders_number != 0)
        {
            
            sem_wait(semafor_write);
            fprintf(filename, "%d    \t: BUS    \t: start boarding: %d \n", (++*Output_ID), (*Busstop_riders_number));
            fflush(filename);
            sem_post(semafor_write);

            // DEBUG: printf(MAG "%d: BUS: start boarding: %d \n", (++*Output_ID), (*Busstop_riders_number));
            

            /* Aby nezůstal otevřený semafor */
            if(*Busstop_riders_number < capacity)
            {
                /* Na zastávce je méně lidí než je max. kapacita busu */
                *Passengers = *Busstop_riders_number; 
            }
            else
            {
                /* Pokud v předchozím bude nejela plná kapacita, ale nyní je potřeba*/
                *Passengers = capacity; 
            }
            
            /* Pouštění procesů RIDER do autobusu */

            for(int i=0; i< *Passengers; i++)
            {    
                sem_post(semafor_bus);
            }

            sem_wait(semafor_busboarding);

            sem_wait(semafor_write);            
            fprintf(filename, "%d    \t: BUS    \t: end boarding: %d \n", (++*Output_ID), (*Busstop_riders_number));
            fflush(filename);
            sem_post(semafor_write);
                       
            // DEBUG: printf(MAG "%d: BUS: end boarding: %d \n", (++*Output_ID), (*Busstop_riders_number));
        }
        
        sem_wait(semafor_write);       
        fprintf(filename, "%d    \t: BUS    \t: depart\n", (++*Output_ID) );
        fflush(filename);
        sem_post(semafor_write);
        
        // DEBUG: printf(MAG "%d: BUS: depart\n", (++*Output_ID) );
        

        /* Otevření přístupu na zastávku */
        sem_post(semafor_busstop);
        

        // DEBUG: printf(YEL "DEBUG: Prichod na zastavku otevren\n" RESET);
        
        /* Uspání - simulace jízdy autobusu */
        random_sleep(delay);

        sem_wait(semafor_write);
        fprintf(filename, "%d    \t: BUS    \t: end\n", (++*Output_ID) );
        fflush(filename);
        sem_post(semafor_write);
        
        // DEBUG: printf(MAG "%d: BUS: end\n", (++*Output_ID) );

        if( *Passengers != 0 )
        {
            for(int j=0; j< *Passengers; j++)
            {
                sem_post(semafor_trip);
                usleep(20);
            }
        }
    }

    sem_wait(semafor_write);
    fprintf(filename, "%d    \t: BUS    \t: finish\n", (++*Output_ID) );
    fflush(filename);
    sem_post(semafor_write);

    // DEBUG: printf(RED "%d: BUS: finish\n", (++*Output_ID));


    //Všechny procesy riders už se ukončili, zavolá ukončení main a ukončí se samo */
    sem_wait(semafor_endprocess);
        
    // DEBUG: printf(YEL "DEBUG: Proces main se muze ukoncit\n");
    sem_post(semafor_main);
    
    exit(0);
}

void process_rider()
{
    /* ID aktualniho procesu */
    int myID = *Rider_ID;
    sem_post(semafor_generator);

    sem_wait(semafor_write);
    fprintf(filename, "%d    \t: RID %d    \t: start\n", (++*Output_ID), myID);
    fflush(filename);
    sem_post(semafor_write);

    // DEBUG: printf(WHT "%d: RID %d: start\n", (++*Output_ID), myID);

    /* Čekání na vchod na zastávku */
    sem_wait(semafor_busstop);
        
        sem_post(semafor_busstop);
        ++*Busstop_riders_number; // Počet přítomných na zastávce 

        sem_wait(semafor_write);
        fprintf(filename, "%d    \t: RID %d    \t: enter: %d\n", (++*Output_ID), myID, (*Busstop_riders_number));
        fflush(filename);
        sem_post(semafor_write);
        
        // DEBUG: printf(GRN "%d: RID %d: enter: %d\n", (++*Output_ID), myID, (*Busstop_riders_number));
    
    /* Čekání na vchod do autobusu */
    sem_wait(semafor_bus);

        --*Remaining_riders;
        --*Busstop_riders_number;

        sem_wait(semafor_write);
        fprintf(filename, "%d    \t: RID %d    \t: boarding\n", (++*Output_ID), myID);
        fflush(filename);
        sem_post(semafor_write);       
      
        // DEBUG: printf(BLU "%d: RID %d: boarding\n", (++*Output_ID), myID);

        ++*Boarding_passengers;
        if(*Boarding_passengers == *Passengers) sem_post(semafor_busboarding);

    /* Jízda autobusem -> čekání na sebevraždu procesu */
    sem_wait(semafor_trip);
    
        /* Počet zabitých procesů */
        ++*Count_passengers;

        /* Ve chvíli, kdy jsou zabity všechny procesy a byl to poslední autobus, autobus zavolá ukončení main*/
        if(*Count_passengers == *Passengers)
        {
            sem_post(semafor_endprocess);
        }

    sem_wait(semafor_write);
        
        fprintf(filename, "%d    \t: RID %d    \t: finish\n", (++*Output_ID), myID);
        fflush(filename);
        sem_post(semafor_write);

        // DEBUG: printf(CYN "%d: RID %d: finish\n", (++*Output_ID), myID);
        
    exit(0);
}

void riders_generator(int riders, int delay)
{
    /* Převod na milisekundy z mikrosekund */
    delay=delay*1000;

    /* Generování procesů */
    for (int i=1; i<= riders; i++)
    {   
        
        /* Vytvoření procesu RIDER */
        pid_t Rider_pID = fork();
        
        if(Rider_pID == 0)
        {
            process_rider();
        }

        sem_wait(semafor_generator);
        ++*Rider_ID;

        /* Testování, zda se povedl fork */
        if(Rider_pID < 0)
        {
            fprintf(stderr, "ERROR - Fork failed\n");
            kill(0, SIGKILL);
            exit(RETURN_FORK_ERROR);
        }

    /* Náhodné spoždění mezi generováním procesů */
    random_sleep(delay);

    }

    exit(0);
}

void random_sleep(int time)
{
    if(time != 0)
    {
        usleep(rand() % time);
    }
}

int main (int argc, char *argv[])
{
    if(init() == -1)
    {
        clean_memory();
        fprintf(stderr, "Initialization error.\nMemory cleaned, try that again.\n");
        return RETURN_ERROR;
    }

    Tparams params = load_params(argc, argv);

    /* Přiřaď do zbývajících cestujících počet zadaný uživatelem*/
    *Remaining_riders = params.R;

    /* Vytvoření procesu BUS */
    pid_t Bus_pID = fork();
    
    if ( Bus_pID == 0 )
    {
        process_bus(params.C, params.ABT);
    }

    /* Testování, zda se povedl fork */
    if ( Bus_pID < 0)
    {
        fprintf(stderr, "ERROR - Fork failed\n");

        clean_memory();
        return RETURN_FORK_ERROR;
    }

    /* Vytvoření pomocného procesu, který bude generovat riders */
    pid_t Generator_pID = fork();

    if( Generator_pID == 0 )
    {
        riders_generator(params.R, params.ART);
    }

    /* Testování, zda se povedl fork */
    if (Generator_pID < 0)
    {
        fprintf(stderr, "ERROR - Fork failed\n");
        
        kill(0, SIGKILL);
        clean_memory();
        return RETURN_FORK_ERROR;
    }

    /* Čeká, až skončí všechny vygenerované procesy, ukončí se jako poslední a uklidí paměť */
    sem_wait(semafor_main);
    usleep(50); // Počká chvíli na ukončení procesu bus
    
    // DEBUG: printf(YEL "DEBUG: SUCCESFULLY ENDED MAIN PROCESS\n");
    
    clean_memory();
    return RETURN_OK;
}
