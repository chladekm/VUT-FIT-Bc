/******************************************************/
/* * *        Jednoduchá shluková analýza         * * */
/* * *                                            * * */
/* * *                 Verze: 4                   * * */
/* * *                                            * * */
/* * *              Martin Chládek                * * */
/* * *              listopad 2017                 * * */
/******************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <math.h> // sqrtf
#include <limits.h> // INT_MAX
#include <string.h> // strcmp

#define START_SIZE 1 //velikost clusteru pro načtení ze souboru
#define SUCCESFULL_LOAD_ID_X 2 //počet úspěšných načtení pro ID a X
#define SUCCESFULL_LOAD_Y 1 //počet úspěšných načtení pro Y
#define CHOICE_AVG 1 //volba pro shlukovou metodu "Unweighted pair-group average"
#define CHOICE_MIN 2 //volba pro shlukovou metodu nejbližšího souseda
#define CHOICE_MAX 3 //volba pro shlukovou metodu nejvzdálenějšího souseda

int premium_case=0;

/*****************************************************************
 * Ladici makra. Vypnout jejich efekt lze definici makra
 * NDEBUG, napr.:
 *   a) pri prekladu argumentem prekladaci -DNDEBUG
 *   b) v souboru (na radek pred #include <assert.h>
 *      #define NDEBUG
 */

#ifdef NDEBUG
#define debug(s)
#define dfmt(s, ...)
#define dint(i)
#define dfloat(f)
#else

// vypise ladici retezec
#define debug(s) printf("- %s\n", s)

// vypise formatovany ladici vystup - pouziti podobne jako printf
#define dfmt(s, ...) printf(" - "__FILE__":%u: "s"\n",__LINE__,__VA_ARGS__)

// vypise ladici informaci o promenne - pouziti dint(identifikator_promenne)
#define dint(i) printf(" - " __FILE__ ":%u: " #i " = %d\n", __LINE__, i)

// vypise ladici informaci o promenne typu float - pouziti
// dfloat(identifikator_promenne)
#define dfloat(f) printf(" - " __FILE__ ":%u: " #f " = %g\n", __LINE__, f)

#endif

/*****************************************************************
 * Deklarace potrebnych datovych typu:
 *
 * TYTO DEKLARACE NEMENTE
 *
 *   struct obj_t - struktura objektu: identifikator a souradnice
 *   struct cluster_t - shluk objektu:
 *      pocet objektu ve shluku,
 *      kapacita shluku (pocet objektu, pro ktere je rezervovano
 *          misto v poli),
 *      ukazatel na pole shluku.
 */

struct obj_t {
    int id;
    float x;
    float y;
};

struct cluster_t {
    int size;
    int capacity;
    struct obj_t *obj;
};

/*****************************************************************
 * Deklarace potrebnych funkci.
 *
 * PROTOTYPY FUNKCI NEMENTE
 *
 * IMPLEMENTUJTE POUZE FUNKCE NA MISTECH OZNACENYCH 'TODO'
 *
 */

/*
 Funkce cisti pamet po alokaci
*/
void clean_memory(struct cluster_t *clusters, int arr_size)
{
    for(int i=0; i<arr_size; i++)
        free(clusters[i].obj);
    
    if(arr_size!=-1)
        free(clusters);
}

/*
 Inicializace shluku 'c'. Alokuje pamet pro cap objektu (kapacitu).
 Ukazatel NULL u pole objektu znamena kapacitu 0.
 */
void init_cluster(struct cluster_t *c, int cap)
{
    assert(c != NULL);
    assert(cap >= 0);
    
    if (cap>0)
    {
        c->obj=malloc(sizeof(struct obj_t)*cap);
        if(c->obj==NULL)
        {
            fprintf(stderr,"Could not allocate cluster.\n");
            clean_memory(c, c->size);
            exit(EXIT_FAILURE);
        }
        
        c->capacity=cap;
    }
    else
    {
        c->obj=NULL;
        c->capacity=0;
    }
    
    c->size=0;
}

/*
 Odstraneni vsech objektu shluku a inicializace na prazdny shluk.
 */
void clear_cluster(struct cluster_t *c)
{
    free(c->obj);
    c->obj=NULL;
    c->capacity=0;
    c->size=0;
}

/// Chunk of cluster objects. Value recommended for reallocation.
const int CLUSTER_CHUNK = 10;

/*
 Zmena kapacity shluku 'c' na kapacitu 'new_cap'.
 */
struct cluster_t *resize_cluster(struct cluster_t *c, int new_cap)
{
    // TUTO FUNKCI NEMENTE
    assert(c);
    assert(c->capacity >= 0);
    assert(new_cap >= 0);
    
    if (c->capacity >= new_cap)
        return c;
    
    size_t size = sizeof(struct obj_t) * new_cap;
    
    void *arr = realloc(c->obj, size);
    if (arr == NULL)
        return NULL;
    
    c->obj = (struct obj_t*)arr;
    c->capacity = new_cap;
    return c;
}

/*
 Prida objekt 'obj' na konec shluku 'c'. Rozsiri shluk, pokud se do nej objekt
 nevejde.
 */
void append_cluster(struct cluster_t *c, struct obj_t obj)
{
    if(c->size==c->capacity)
        c = resize_cluster(c, (c->capacity+CLUSTER_CHUNK));
    
    c->obj[c->size]=obj;
    c->size++;
}

/*
 Seradi objekty ve shluku 'c' vzestupne podle jejich identifikacniho cisla.
 */
void sort_cluster(struct cluster_t *c);


/*
 Do shluku 'c1' prida objekty 'c2'. Shluk 'c1' bude v pripade nutnosti rozsiren.
 Objekty ve shluku 'c1' budou serazeny vzestupne podle identifikacniho cisla.
 Shluk 'c2' bude nezmenen.
 */
void merge_clusters(struct cluster_t *c1, struct cluster_t *c2)
{
    assert(c1 != NULL);
    assert(c2 != NULL);
    
    int req_size= (c1->size) + (c2->size); //velikost, jakou bude mít c1 i s prvky c2
    
    int i=0;
    
    for(;(c1->size)<req_size;i++)
    {
        append_cluster(c1, c2->obj[i]);
    }
    
    sort_cluster(c1);
}

/**********************************************************************/
/* Prace s polem shluku */

/*
 Odstrani shluk z pole shluku 'carr'. Pole shluku obsahuje 'narr' polozek
 (shluku). Shluk pro odstraneni se nachazi na indexu 'idx'. Funkce vraci novy
 pocet shluku v poli.
 */
int remove_cluster(struct cluster_t *carr, int narr, int idx)
{
    assert(idx < narr);
    assert(narr > 0);
    
    int i;
    clear_cluster(&(carr[idx]));
    narr--;
    
    for(i=idx; i<narr; i++)
        {
            carr[i]=carr[i+1];
        }
    
    return narr;
}

/*
 Pocita Euklidovskou vzdalenost mezi dvema objekty.
 */
float obj_distance(struct obj_t *o1, struct obj_t *o2)
{
    assert(o1 != NULL);
    assert(o2 != NULL);
    
    float distance=0;
    
    distance = sqrt( ( ((o1->x)-(o2->x))*((o1->x)-(o2->x)) ) + ( ((o1->y)-(o2->y))*((o1->y)-(o2->y)) ) );
    
    return distance;
}

/*
 Pocita vzdalenost dvou shluku.
 */
float cluster_distance(struct cluster_t *c1, struct cluster_t *c2)
{
    assert(c1 != NULL);
    assert(c1->size > 0);
    assert(c2 != NULL);
    assert(c2->size > 0);
    
    float distance=0; //vzdálenost shluků
    float obj_dis=0; //vzdálenost objektů
    int i,j;
    
    if(premium_case==0)
        premium_case = CHOICE_AVG;
    
    switch(premium_case)
    {
        case (CHOICE_AVG):
        {
            distance=0;
            
            for(i=0; i<(c1->size); i++)
            {
                for(j=0; j<(c2->size); j++)
                {
                    distance = distance + obj_distance(&c1->obj[i], &c2->obj[j]);
                }
            }
            distance=(distance /(c1->size * c2->size));
            break;
        }

        case (CHOICE_MIN):
        {
            distance = INT_MAX; //vzdálenost shluků - 1001 (nelze, aby byla větší)
            
            for(i=0; i<(c1->size); i++)
            {
                for(j=0; j<(c2->size); j++)
                {
                    obj_dis = obj_distance(&c1->obj[i], &c2->obj[j]);
                    
                    if(obj_dis<distance)
                            distance=obj_dis;
                }
            }
            break;
        }
            
        case (CHOICE_MAX):
        {
            distance = INT_MIN; //vzdálenost shluků - první bude větší za všech případů
            
            for(i=0; i<(c1->size); i++)
            {
                for(j=0; j<(c2->size); j++)
                {
                    obj_dis = obj_distance(&c1->obj[i], &c2->obj[j]);
                    if(obj_dis>distance)
                        distance=obj_dis;
                }
            }
            //distance = distance * (-1);
            break;
        }
    }
    
    return distance;
}

/*
 Funkce najde dva nejblizsi shluky. V poli shluku 'carr' o velikosti 'narr'
 hleda dva nejblizsi shluky. Nalezene shluky identifikuje jejich indexy v poli
 'carr'. Funkce nalezene shluky (indexy do pole 'carr') uklada do pameti na
 adresu 'c1' resp. 'c2'.
 */
void find_neighbours(struct cluster_t *carr, int narr, int *c1, int *c2)
{
    assert(narr > 0);
    
    //carr pole shluků
    //narr velikost pole shluků
    int i,j;
    float var=0; //pomocná variable
    float distance=INT_MAX; //kvůli porovnání
    
    for(i=0; i<narr-1; i++) //poslední cluster už nemusím testovat - už jsem ho otestoval se všema
    {
        for(j=0; j<narr; j++)
        {
            if(i!=j)
            {
                var=cluster_distance(&(carr)[i], &(carr)[j]);
                if(var<distance)
                {
                    distance=var;
                    *c1 = i;
                    *c2 = j;
                }
            }
        }

    }
}

// pomocna funkce pro razeni shluku
static int obj_sort_compar(const void *a, const void *b)
{
    // TUTO FUNKCI NEMENTE
    const struct obj_t *o1 = (const struct obj_t *)a;
    const struct obj_t *o2 = (const struct obj_t *)b;
    if (o1->id < o2->id) return -1;
    if (o1->id > o2->id) return 1;
    return 0;
}

/*
 Razeni objektu ve shluku vzestupne podle jejich identifikatoru.
 */
void sort_cluster(struct cluster_t *c)
{
    // TUTO FUNKCI NEMENTE
    qsort(c->obj, c->size, sizeof(struct obj_t), &obj_sort_compar);
}

/*
 Tisk shluku 'c' na stdout.
 */
void print_cluster(struct cluster_t *c)
{
    // TUTO FUNKCI NEMENTE
    for (int i = 0; i < c->size; i++)
    {
        if (i) putchar(' ');
        printf("%d[%g,%g]", c->obj[i].id, c->obj[i].x, c->obj[i].y);
    }
    putchar('\n');
}

/*
 Ze souboru 'filename' nacte objekty. Pro kazdy objekt vytvori shluk a ulozi
 jej do pole shluku. Alokuje prostor pro pole vsech shluku a ukazatel na prvni
 polozku pole (ukalazatel na prvni shluk v alokovanem poli) ulozi do pameti,
 kam se odkazuje parametr 'arr'. Funkce vraci pocet nactenych objektu (shluku).
 V pripade nejake chyby uklada do pameti, kam se odkazuje 'arr', hodnotu NULL.
 */
int load_clusters(char *filename, struct cluster_t **arr)
{
    assert(arr != NULL);
    FILE *f1 = fopen(filename, "r");
    
    int i=0; //index
    int arr_size=0; //počet shluků, se kterými počítat na začátku
    int id=0;
    
    if(f1==NULL) //Test, jestli se podařilo otevřít soubor
    {
        fprintf(stderr, "Could not open the file.\n");
        return -1;
    }
    
    fscanf(f1, "count=%d", &arr_size);
    
    if(arr_size<1)
    {
        fprintf(stderr, "Number of objects must be bigger than 0.\n");
        return -1;
    }
    
    *arr=malloc(sizeof(struct cluster_t)*arr_size);
    if(*arr==NULL)
    {
        fprintf(stderr,"Could not allocate memory for array of clusters.\n");
        clean_memory(*arr, i);
        return -1;
    }
    
    struct obj_t obj[arr_size];
    char ch;
    
    for(i=0; i<arr_size; i++)
    {
        if(fscanf(f1,"%d%c",&id,&ch) == SUCCESFULL_LOAD_ID_X) //Pokud se podařilo načíst
        {
            obj[i].id=id;
            
            init_cluster(&((*arr)[i]), START_SIZE);
            
            if(&((*arr)[i])==NULL)
            {
                fprintf(stderr,"Failed to allocate memory.\n");
                clean_memory(*arr, i+1);
                return -1;
            }
            
            if(ch == '\n')
            {
                fprintf(stderr, "Failed to load coordinate X & Y. Coordinates are probably missing.\n");
                clean_memory(*arr, i+1);
                return -1;
            }
            
             //-----Načtení souřadnice X-----
            if((fscanf(f1,"%f%c",&obj[i].x,&ch))==SUCCESFULL_LOAD_ID_X)
            {
                if((obj[i].x<0)||(obj[i].x>1000)) //TEST 0=<x=<1000
                {
                    fprintf(stderr, "Wrong coordinate X on %d. row.\n",i+1);
                    clean_memory(*arr, i+1);
                    return -1;
                }
                
                if(ch == '\n')
                {
                    fprintf(stderr, "Failed to load coordinate Y. Coordinate is probably missing.\n");
                    clean_memory(*arr, i+1);
                    return -1;
                }
            }
            else
            {
                //Pokud nastane tahle situace, chybí x i y. Jinak by se do x načetlo y a chyba by se vypsala až u y
                fprintf(stderr, "Failed to load coordinate X.\n");
                clean_memory(*arr, i+1);
                return -1;
                //break;
            }
            
            //-----Načtení souřadnice Y-----
            if((fscanf(f1,"%f",&obj[i].y))==SUCCESFULL_LOAD_Y)
            {
                if((obj[i].y<0)||(obj[i].y>1000)) //TEST 0=<x=<1000
                {
                    fprintf(stderr, "Wrong coordinate Y on %d. row.\n",i+1);
                    clean_memory(*arr, i+1);
                    return -1;
                }
            }
            else
            {
                fprintf(stderr, "Failed to load coordinate Y.\n");
                clean_memory(*arr, i+1);
                return -1;
            }
            
            append_cluster(&(*arr)[i], obj[i]); //Funkce přiřadí každému objektu jeden cluster
        }
        else //Pokud je count vetší než zadané N, po načtení se velikost podle upraví podle dostupných objektů
        {
            fprintf(stderr, "Two possible errors:\n\t1) Count is bigger than quantity of objects in the file.\n\t2) Some ID in the file is wrong.\n");
            clean_memory(*arr, i);
            return -1;
        }
    }
    
    int fileclose = fclose(f1);
    
    if(fileclose==EOF)
    {
        fprintf(stderr, "Could not close the file");
        clean_memory(*arr, arr_size);
        return -1;
    }
    
    return arr_size;
}

/*
 Tisk pole shluku. Parametr 'carr' je ukazatel na prvni polozku (shluk).
 Tiskne se prvnich 'narr' shluku.
 */
void print_clusters(struct cluster_t *carr, int narr)
{
    printf("Clusters:\n");
    for (int i = 0; i < narr; i++)
    {
        printf("cluster %d: ", i);
        print_cluster(&carr[i]);
    }
}

int main(int argc, char *argv[])
{
    struct cluster_t *clusters;
    
    if (argc==1||argc>4)
    {
        fprintf(stderr,"Wrong number of arguments.\n");
        return -1;
    }
    
    int n = 1;
    if((argc == 3) || (argc == 4)) //pokud jsou zadány 2 argumenty -> načte se n, jinak n=1
    {
        n = atoi(argv[2]); //načtení n
        if(n < 1)
        {
            fprintf(stderr, "Number of final clusters cannot be less than 1.\n");
            return -1;
        }
        
        if(argc == 4)
        {
            if(strcmp(argv[3], "--avg")==0)
                premium_case = CHOICE_AVG;
            if(strcmp(argv[3], "--min")==0)
                premium_case = CHOICE_MIN;
            if(strcmp(argv[3], "--max")==0)
                premium_case = CHOICE_MAX;
            if(premium_case==0)
            {
                fprintf(stderr, "Wrong argument selected.\n");
                return -1;
            }
        }
    }

    int arr_size = load_clusters(argv[1], &clusters);
    
    if(arr_size<n)
    {
        fprintf(stderr,"Value of count is smaller than n.");
        clean_memory(clusters, arr_size);
        fclose(stdout);
        return -1;
    }
    else
    {
    int c1=0,c2=0; //souřadnice dvou nejbližších shluků
    
    while(arr_size!=n)
    {
        find_neighbours(clusters, arr_size, &c1, &c2);
        merge_clusters(&clusters[c1], &clusters[c2]);
        arr_size = remove_cluster(clusters, arr_size, c2);
    }
    
    print_clusters(clusters, arr_size);
    }
    
    clean_memory(clusters, arr_size);
    fclose(stdout);
    
    return 0;
}
