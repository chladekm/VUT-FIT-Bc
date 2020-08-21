/**
 * Dokumentace hlavičkového souboru - 3. projekt IZP 2017/18
 * @file proj3.h
 * @author  Martin Chládek
 * @contact xchlad16@stud.fit.vutbr.cz
 * @date    7. 12. 2017
 * @version 4
 */

/**
 * @brief Struktura objektu
 * @param id jedinečný indetifikátor
 * @param x souřadnice objektu [x]
 * @param y souřadnice objektu [y]
 */
struct obj_t {
    int id;
    float x;
    float y;
};

/**
 * @brief Struktura shluku objektů
 * @param size počet objektů ve shluku
 * @param capacity kapacita shluku (počet objektů, pro které je rezervováno místo v poli)
 * @param obj ukazatel na pole shluku
 */
struct cluster_t {
    int size;
    int capacity;
    struct obj_t *obj;
};

/**
 * @addtogroup group5
 * @{*/

/**
 * @brief Inicializace shluku, funkce alokuje paměť pro kapacitu objektu
 * @param c ukazatel na konkrétní shluk, pro který bude inicializována paměť
 * @param cap kapacita shluku, která bude alokována
 * @pre kapacita 'cap' musí být větší, nebo rovno 0; shluk 'c' musí být nenulový
 */
void init_cluster(struct cluster_t *c, int cap);
/** @} */

/**
 * @addtogroup group2
 * @{*/

/**
 * @brief Funkce uvolní z paměti alokované místo pro jeden shluk, pole shluků, velikost i kapacita se nastaví na 0
 * @param c ukazatel na konkrétní shluk, se kterým funkce pracuje
 */
void clear_cluster(struct cluster_t *c);
/**@}*/

/**
 * @param CLUSTER_CHUNK hodnota pro realokaci kapacity shluku
 */
extern const int CLUSTER_CHUNK;

/**
 * @brief Funkce mění kapacitu shluku
 * @param c ukazatel na shluk, jehož kapacitu bude funkce měnit
 * @param new_cap nová hodnota kapacity, která se přiřadí shluku
 * @pre kapacita shluku 'c' musí být nenulová; nová kapacita musí být větší než 0
 * @return shluk nastavený na novou kapacitu
 */
struct cluster_t *resize_cluster(struct cluster_t *c, int new_cap);

/**
 * @brief Funkce přidá konkrétní objekt na konec shluku
 * @param c ukazatel na shluk, do kterého se bude objekt přidávat
 * @param obj objekt, který se bude přidávat na konec shluku
 */
void append_cluster(struct cluster_t *c, struct obj_t obj);

/** @defgroup group1 Nakopírování obsahu shluku
 * @{*/

/**
 * @brief Funkce přidá do shluku 'c1' objekty shluku 'c2', shluk 'c1' bude v pripade nutnosti rozšířen.
 * @param c1 ukazatel na shluk, co kterého budou objekty přidány
 * @param c2 ukazatel na shluk, z kterého budou objekty přidány do shluku 'c1'
 * @pre shluk 'c1' a shluk 'c2' musí být nenulový
 * @post ve shluku 'c1' jsou přidány objekty ze shluku 'c2' a jsou seřazeny vzestupně podle identifikačního čísla, shluk 'c2' zůstává nezměněn
 * @sa append_cluster
 */
void merge_clusters(struct cluster_t *c1, struct cluster_t *c2);
/**@}*/

/** @defgroup group2 Odstranění shluku
 * @{*/

/**
 * @brief Funkce odstraní shluk z pole shluků
 * @param carr ukazatel na pole shluku
 * @param narr velikost pole 'carr', množství položek, které obsahuje
 * @param idx index, na kterém se nachází shluk pro odstranění
 * @pre index 'idx' musí být menší, než množství položek v poli; počet položek v poli musí být větší než 0
 * @return nový počet shluků v poli
 */
int remove_cluster(struct cluster_t *carr, int narr, int idx);
/**@}*/

/**
 * @addtogroup group3
 * @{*/

/**
 * @brief Funkce počítá Euklidovskou vdálenost mezi dvěma objekty
 * @param o1 ukazatel na 1. objekt, se kterým se bude počítat
 * @param o2 ukazatel na 2. objekt, se kterým se bude počítat
 * @pre objekty 'o1' a 'o2' musí být nenulové
 * return vzdálenost mezi dvěma objekty
 */
float obj_distance(struct obj_t *o1, struct obj_t *o2);
/** @}*/

/**
 * @addtogroup group3
 * @{*/

/**
 * @brief Funkce počítá vzdálenost dvou shluků
 * @param c1 ukazatel na 1. shluk, se kterým se bude počítat
 * @param c2 ukazatel na 2. shluk, se kterým se bude počítat
 * @pre shluky 'c1'a 'c2'musí být nenulové a jejich velikost musí být větší než 0
 * @return vzdálenost mezi dvěma shluky
 */
float cluster_distance(struct cluster_t *c1, struct cluster_t *c2);
/** @} */

/**
 * @defgroup group3 Nalezení dvou nejbližších shluků
 * @{*/

/**
 * @brief Funkce najde dva nejbližší shluky, využívá metodu metodu "Unweighted pair-group average"
 * @param carr ukazatel na pole shluků
 * @param narr velikost pole 'carr', množství položek, které obsahuje
 * @param c1 index prvního shluku, který má nejkratší vzdálenost s 'c2'
 * @param c2 index druhého shluku, který má nejkratší vzdálenost s 'c1'
 * @pre množství položek v poli 'carr' musí být větší než 0
 * @post do 'c1' a 'c2' jsou uloženy indexy dvou nejbližších shluků
 */
void find_neighbours(struct cluster_t *carr, int narr, int *c1, int *c2);
/** @} */

/**
 * @addtogroup group1
 * @{*/

/**
 * @brief Funkce řadí objekty ve shluku vzestupně podle jejich identifikátoru.
 * @parmam c ukazatel na shluk, který má být seřazen
 */
void sort_cluster(struct cluster_t *c);
/**@}*/

/**
 * @addtogroup group4
 * @{ */

/**
 * @brief Funkce tiskne shluk 'c' na stdout
 * @param c ukazatel na konkrétní shluk, který má být vytisknut
 */
void print_cluster(struct cluster_t *c);
/** @} */

/**
 * @defgroup group5 Načtení a inicializace
 * @{ */

/**
 * @brief Funkce ze souboru 'filename' načte objekty. Pro každý objekt vytvoří shluk a uloží
 jej do pole shluku. Alokuje prostor pro pole všech shluků a ukazatel na první
 položku pole uloží do paměti, kam se odkazuje parametr 'arr'.
 * @param filename název souboru, který zadal uživatel argumentem
 * @param arr ukazatel na pole clusterů
 * @pre ukazatel 'arr' musí být různý od 0
 * @sa append_cluster
 * @return počet načtených objektů (shluků)
 */
int load_clusters(char *filename, struct cluster_t **arr);
/** @} */

/**
 * @defgroup group4 Výpis shluků
 * @{ */

/**
 * @brief Funkce tiskne pole shluků
 * @param carr ukazatel na první položku v poli shluků
 * @param narr počet shluků ,které mají být vytištěny (tiskne se prvních 'narr' shluků)
 */
void print_clusters(struct cluster_t *carr, int narr);
/** @} */
