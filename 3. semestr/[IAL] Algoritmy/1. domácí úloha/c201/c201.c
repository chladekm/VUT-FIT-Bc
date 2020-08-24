
/* c201.c *********************************************************************}
{* Téma: Jednosměrný lineární seznam
**
**                     Návrh a referenční implementace: Petr Přikryl, říjen 1994
**                                          Úpravy: Andrea Němcová listopad 1996
**                                                   Petr Přikryl, listopad 1997
**                                Přepracované zadání: Petr Přikryl, březen 1998
**                                  Přepis do jazyka C: Martin Tuček, říjen 2004
**                                            Úpravy: Kamil Jeřábek, září 2018
**
** Implementujte abstraktní datový typ jednosměrný lineární seznam.
** Užitečným obsahem prvku seznamu je celé číslo typu int.
** Seznam bude jako datová abstrakce reprezentován proměnnou typu tList.
** Definici konstant a typů naleznete v hlavičkovém souboru c201.h.
**
** Vaším úkolem je implementovat následující operace, které spolu s výše
** uvedenou datovou částí abstrakce tvoří abstraktní datový typ tList:
**
**      InitList ...... inicializace seznamu před prvním použitím,
**      DisposeList ... zrušení všech prvků seznamu,
**      InsertFirst ... vložení prvku na začátek seznamu,
**      First ......... nastavení aktivity na první prvek,
**      CopyFirst ..... vrací hodnotu prvního prvku,
**      DeleteFirst ... zruší první prvek seznamu,
**      PostDelete .... ruší prvek za aktivním prvkem,
**      PostInsert .... vloží nový prvek za aktivní prvek seznamu,
**      Copy .......... vrací hodnotu aktivního prvku,
**      Actualize ..... přepíše obsah aktivního prvku novou hodnotou,
**      Succ .......... posune aktivitu na další prvek seznamu,
**      Active ........ zjišťuje aktivitu seznamu.
**
** Při implementaci funkcí nevolejte žádnou z funkcí implementovaných v rámci
** tohoto příkladu, není-li u dané funkce explicitně uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam předá
** někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako
** procedury (v jazyce C procedurám odpovídají funkce vracející typ void).
**/

#include "c201.h"

int errflg;
int solved;

void Error() {
/*
** Vytiskne upozornění na to, že došlo k chybě.
** Tato funkce bude volána z některých dále implementovaných operací.
**/
    printf ("*ERROR* The program has performed an illegal operation.\n");
    errflg = TRUE;                      /* globální proměnná -- příznak chyby */
}

void InitList (tList *L) {
/*
** Provede inicializaci seznamu L před jeho prvním použitím (tzn. žádná
** z následujících funkcí nebude volána nad neinicializovaným seznamem).
** Tato inicializace se nikdy nebude provádět nad již inicializovaným
** seznamem, a proto tuto možnost neošetřujte. Vždy předpokládejte,
** že neinicializované proměnné mají nedefinovanou hodnotu.
**/

	L->Act = NULL;
	L->First = NULL;

	//Pokud se nepovede nastavení prvního na NULL, vypíše se chyba
	if(L->First != NULL)
	{
		Error();
		return;
	}

}

void DisposeList (tList *L) {
/*
** Zruší všechny prvky seznamu L a uvede seznam L do stavu, v jakém se nacházel
** po inicializaci. Veškerá paměť používaná prvky seznamu L bude korektně
** uvolněna voláním operace free.
***/

	tElemPtr element;

	//Mazu prvni prvek tak dlouho, dokud nebude ukazovat na NULL
	while(L->First != NULL)
	{
		element = L->First;
		L->First = element->ptr;
		free(element);
	}

	//Nebude žádný aktivní prvek
	L->Act = NULL;
	L->First = NULL;

}

void InsertFirst (tList *L, int val) {
/*
** Vloží prvek s hodnotou val na začátek seznamu L.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci Error().
**/

	tElemPtr element = malloc(sizeof(struct tElem));

	//Test, zda se povedla alokace
	if(element == NULL)
	{
		Error();
		return;
	}

	element->data = val;
	element->ptr = L->First; //Ukazuj tam, kam začátek
	L->First = element; //Začátek ukazuje na nový prvek

}

void First (tList *L) {
/*
** Nastaví aktivitu seznamu L na jeho první prvek.
** Funkci implementujte jako jediný příkaz, aniž byste testovali,
** zda je seznam L prázdný.
**/
	L->Act = L->First;

}

void CopyFirst (tList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu prvního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci Error().
**/
	if(L->First == NULL)
	{
		Error();
		return;
	}
	else
	{
		tElemPtr element;
		element = L->First;
		*val = element->data;
	}

}

void DeleteFirst (tList *L) {
/*
** Zruší první prvek seznamu L a uvolní jím používanou paměť.
** Pokud byl rušený prvek aktivní, aktivita seznamu se ztrácí.
** Pokud byl seznam L prázdný, nic se neděje.
**/
	if(L->Act == L->First) // splněno -> Rušený prvek je aktivní
	{
		L->Act = NULL;
	}


	if(L->First != NULL)
	{
		tElemPtr element;
		element = L->First;
		L->First = element->ptr; //Ukazatel na prvni prvek bude další prvek za rušeným
		free(element);
	}

}	

void PostDelete (tList *L) {
/* 
** Zruší prvek seznamu L za aktivním prvkem a uvolní jím používanou paměť.
** Pokud není seznam L aktivní nebo pokud je aktivní poslední prvek seznamu L,
** nic se neděje.
**/
	
	if(L->Act != NULL)
	{
		tElemPtr element1, element2;

		element1 = L->Act; //element1 bude aktivní prvek
		
		if(element1->ptr != 0)
		{
			element2 = element1->ptr; //element2 - prvek co chci smazat
			element1->ptr = element2->ptr; //Ukazatel na prvek ob jeden co chci vymazat
			free(element2);
		}
	}

}

void PostInsert (tList *L, int val) {
/*
** Vloží prvek s hodnotou val za aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje!
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** zavolá funkci Error().
**/
	
	if(L->Act != NULL)
	{
		tElemPtr element1, element2;

		element2 = malloc(sizeof(struct tElem));

		if(element2 == NULL)
		{
			Error();
			return;
		}

		element1 = L->Act; // Aktivní prvek za který budeme vkládat

		element2->data = val; // Vklad hodnoty do nového prvku
		element2->ptr = element1->ptr; //Novy prvek bude ukazovat tam kam ukazuje aktivni prvek
		element1->ptr = element2; //Aktivní prvek bude ukazovat na nově vzniklý prvek
	}

}

void Copy (tList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu aktivního prvku seznamu L.
** Pokud seznam není aktivní, zavolá funkci Error().
**/

	// Načtu aktivní prvek a následně uloží jeho hodnotu do *val
	if(L->Act != NULL)
	{
		tElemPtr element;
		element = L->Act;
		*val = element->data;
	}
	else
	{
		Error();
		return;
	}
	
}

void Actualize (tList *L, int val) {
/*
** Přepíše data aktivního prvku seznamu L hodnotou val.
** Pokud seznam L není aktivní, nedělá nic!
**/
	if(L->Act != NULL)
	{
		tElemPtr element;
		element = L->Act;
		element->data = val; // Do aktivního prvku vložím novou hodnotu
	}
	
}

void Succ (tList *L) {
/*
** Posune aktivitu na následující prvek seznamu L.
** Všimněte si, že touto operací se může aktivní seznam stát neaktivním.
** Pokud není předaný seznam L aktivní, nedělá funkce nic.
**/

	if(L->Act != NULL)
	{
		tElemPtr element;
		element = L->Act;
		L->Act = element->ptr; // Přesun aktivity na následující prvek
	}

}

int Active (tList *L) {
/*
** Je-li seznam L aktivní, vrací nenulovou hodnotu, jinak vrací 0.
** Tuto funkci je vhodné implementovat jedním příkazem return. 
**/
	return (L->Act) ? TRUE : FALSE; //FALSE = 0, TRUE = 1
	
}

/* Konec c201.c */
