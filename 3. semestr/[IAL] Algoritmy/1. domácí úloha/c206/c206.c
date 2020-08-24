
/* c206.c **********************************************************}
{* Téma: Dvousměrně vázaný lineární seznam
**
**                   Návrh a referenční implementace: Bohuslav Křena, říjen 2001
**                            Přepracované do jazyka C: Martin Tuček, říjen 2004
**                                            Úpravy: Kamil Jeřábek, září 2018
**
** Implementujte abstraktní datový typ dvousměrně vázaný lineární seznam.
** Užitečným obsahem prvku seznamu je hodnota typu int.
** Seznam bude jako datová abstrakce reprezentován proměnnou
** typu tDLList (DL znamená Double-Linked a slouží pro odlišení
** jmen konstant, typů a funkcí od jmen u jednosměrně vázaného lineárního
** seznamu). Definici konstant a typů naleznete v hlavičkovém souboru c206.h.
**
** Vaším úkolem je implementovat následující operace, které spolu
** s výše uvedenou datovou částí abstrakce tvoří abstraktní datový typ
** obousměrně vázaný lineární seznam:
**
**      DLInitList ...... inicializace seznamu před prvním použitím,
**      DLDisposeList ... zrušení všech prvků seznamu,
**      DLInsertFirst ... vložení prvku na začátek seznamu,
**      DLInsertLast .... vložení prvku na konec seznamu,
**      DLFirst ......... nastavení aktivity na první prvek,
**      DLLast .......... nastavení aktivity na poslední prvek,
**      DLCopyFirst ..... vrací hodnotu prvního prvku,
**      DLCopyLast ...... vrací hodnotu posledního prvku,
**      DLDeleteFirst ... zruší první prvek seznamu,
**      DLDeleteLast .... zruší poslední prvek seznamu,
**      DLPostDelete .... ruší prvek za aktivním prvkem,
**      DLPreDelete ..... ruší prvek před aktivním prvkem,
**      DLPostInsert .... vloží nový prvek za aktivní prvek seznamu,
**      DLPreInsert ..... vloží nový prvek před aktivní prvek seznamu,
**      DLCopy .......... vrací hodnotu aktivního prvku,
**      DLActualize ..... přepíše obsah aktivního prvku novou hodnotou,
**      DLSucc .......... posune aktivitu na další prvek seznamu,
**      DLPred .......... posune aktivitu na předchozí prvek seznamu,
**      DLActive ........ zjišťuje aktivitu seznamu.
**
** Při implementaci jednotlivých funkcí nevolejte žádnou z funkcí
** implementovaných v rámci tohoto příkladu, není-li u funkce
** explicitně uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam 
** předá někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako
** procedury (v jazyce C procedurám odpovídají funkce vracející typ void).
**/

#include "c206.h"

int errflg;
int solved;

void DLError() {
/*
** Vytiskne upozornění na to, že došlo k chybě.
** Tato funkce bude volána z některých dále implementovaných operací.
**/	
    printf ("*ERROR* The program has performed an illegal operation.\n");
    errflg = TRUE;             /* globální proměnná -- příznak ošetření chyby */
    return;
}

void DLInitList (tDLList *L) {
/*
** Provede inicializaci seznamu L před jeho prvním použitím (tzn. žádná
** z následujících funkcí nebude volána nad neinicializovaným seznamem).
** Tato inicializace se nikdy nebude provádět nad již inicializovaným
** seznamem, a proto tuto možnost neošetřujte. Vždy předpokládejte,
** že neinicializované proměnné mají nedefinovanou hodnotu.
**/
   
    L->First = NULL;
    L->Last = NULL;
    L->Act = NULL;

}

void DLDisposeList (tDLList *L) {
/*
** Zruší všechny prvky seznamu L a uvede seznam do stavu, v jakém
** se nacházel po inicializaci. Rušené prvky seznamu budou korektně
** uvolněny voláním operace free. 
**/
	tDLElemPtr element;


	while(L->First != NULL) //Funkce se bude provádět tak dlouho, dokud bude existovat první prvek
	{
		element = L->First; //Uložím ukazatel na první prvek, který budu mazat
		L->First = element->rptr; //První prvek bude ukazatel na další prvek za rušeným
		free(element);
	}
	
	// Obnova do stavu po inicializaci
	L->First = NULL;
    L->Last = NULL;
    L->Act = NULL;
	
}

void DLInsertFirst (tDLList *L, int val) {
/*
** Vloží nový prvek na začátek seznamu L.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	tDLElemPtr element;

	element = malloc(sizeof(struct tDLElem));

	if(element == NULL) //Pokud nastala chyba při alokaci paměti
	{
		DLError();
		return;
	}

	element->data = val; //Vložení dat do nového prvku

	element->lptr = NULL; //První prvek, tak levý ukazatel nikam ukazovat nebude
	element->rptr = L->First; //Pravý ukazatel bude ukazovat na aktuální first prvek;

	if(L->First == NULL) //Pokud seznam ještě nemá žádný první prvek - je prázdný
	{
		//Nově vkládaný prvek je zároveň prvním i posledním
		L->Last = element;
	}
	else
	{
		//Seznam nená prázdná - aktuální first prvek bude mít v levém ukazateli 
		//odkaz na mový první prvek
		L->First->lptr = element;
	}

	L->First = element;
	
}

void DLInsertLast(tDLList *L, int val) {
/*
** Vloží nový prvek na konec seznamu L (symetrická operace k DLInsertFirst).
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/ 	
	tDLElemPtr element;

	element = malloc(sizeof(struct tDLElem));

	if(element == NULL) //Pokud nastala chyba při alokaci paměti
	{
		DLError();
		return;
	}

	element->data = val;
	element->lptr = L->Last;
	element->rptr = NULL;

	if(L->First == NULL) //Pokud seznam ještě nemá žádný první prvek - je prázdný
	{
		//Nový prvek bude zárověň prvním i posledním prvkem
		L->First = element;
	}
	else
	{
		//Seznam není prázdný -> starý poslední prvek musí 
		//ukazovat na nový levý prvek
		L->Last->rptr = element;
	}

	L->Last = element;

}

void DLFirst (tDLList *L) {
/*
** Nastaví aktivitu na první prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/
	L->Act = L->First;
	
}

void DLLast (tDLList *L) {
/*
** Nastaví aktivitu na poslední prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/
	L->Act = L->Last;

}

void DLCopyFirst (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu prvního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/
	if(L->First == NULL) //Seznam je prázdný
	{
		DLError();
		return;
	}

	//Do proměnné načte hodnotu prvního prvku seznamu
	*val = L->First->data;
	
}

void DLCopyLast (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu posledního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/
	if(L->Last == NULL) //Seznam je prázdný
	{
		DLError();
		return;
	}

	//Do proměnné načte hodnotu posledního prvku seznamu
	*val = L->Last->data;
	
}

void DLDeleteFirst (tDLList *L) {
/*
** Zruší první prvek seznamu L. Pokud byl první prvek aktivní, aktivita 
** se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/
	if(L->First != NULL) //Podm. jestli je seznam prázdný
	{
		if(L->Act == L->First) //Podm . jestli je první prvek aktivní
		{
			L->Act = NULL; //Ztráta aktivity
		}
			
		tDLElemPtr element;

		element = L->First;
		L->First = L->First->rptr; //Přiřadím nový první prvek

		if(L->First == NULL) //Test zda odstraňovaný prvek byl jediným v seznamu
		{
			//Seznam je prázdný -> Ukazatel na last musí být NULL
			L->Last = NULL; 
		}
		else
		{
			//Seznam není prázdný -> Levý ukazatel nového 
			//prního prvku musí být NULL
			L->First->lptr = NULL;
		}

		free(element); //Uvolnění starého prvního prvku
	}
	
}	

void DLDeleteLast (tDLList *L) {
/*
** Zruší poslední prvek seznamu L. Pokud byl poslední prvek aktivní,
** aktivita seznamu se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/ 
	if(L->First != NULL) //Podm. jestli je seznam prázdný
	{
		if(L->Act == L->Last) //Podm . jestli je poslední prvek aktivní
		{
			L->Act = NULL; //Ztráta aktivity
		}
			
		tDLElemPtr element;

		element = L->Last;
		L->Last = L->Last->lptr; //Přiřadím nový poslední prvek

		if(L->Last == NULL) //Test zda odstraňovaný prvek byl jediným v seznamu
		{
			//Seznam je prázdný -> Ukazatel na first musí být NULL
			L->First = NULL; 
		}
		else
		{
			//Seznam není prázdný -> Pravý ukazatel nového 
			//posledního prvku musí být NULL
			L->Last->rptr = NULL;
		}

		free(element); //Uvolnění starého posledního prvku
	}
	
}

void DLPostDelete (tDLList *L) {
/*
** Zruší prvek seznamu L za aktivním prvkem.
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** posledním prvkem seznamu, nic se neděje.
**/
	if((L->Act == NULL) || (L->Act == L-> Last))
	{
		return;
	}

	tDLElemPtr element;
	element = L->Act->rptr; //Přiřazení prvku, který se má rušit

	//Aktivní prvek bude ukazovat pravým ukazatelem
	//na prvek následující za rušeným prvkem
	L->Act->rptr = element->rptr;

	if(element == L->Last) //Pokud je aktivní prvek předposlední (rušit se má poslední)
	{
		L->Last = L->Act; //Poslední prvek se ruší -> nutno změnit ukazatel na Last
	}
	else //Rušený prvek není poslední
	{	 
		//Levý ukazatel prvku za rušeným prvkem
		//bude ukazovat levým uk. na akt. prvek
		element->rptr->lptr = L->Act;
	}

	free(element); //uvolnění rušeného prvku

}

void DLPreDelete (tDLList *L) {
/*
** Zruší prvek před aktivním prvkem seznamu L .
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** prvním prvkem seznamu, nic se neděje.
**/
	if((L->Act == NULL) || (L->Act == L->First))
	{
		return;
	}

	tDLElemPtr element;
	element = L->Act->lptr;

	//Aktivní prvek bude ukazovat levým ukazatelem
	//na prvek předcházející rušenému prvku
	L->Act->lptr = element->lptr;

	if(element == L->First) //Pokud je aktivní prvek hned za prvním (rušit se má první)
	{
		L->First = L->Act; //První prvek se ruší -> nutno změnit ukazatel na First
	}
	else //Rušený prvek není první
	{
		//Pravý ukazatel prvku před rušeným prvkem
		//bude ukazovat pravým uk. na akt. prvek
		element->lptr->rptr = L->Act;
	}

	free(element); //uvolnění rušeného prvku
			
}

void DLPostInsert (tDLList *L, int val) {
/*
** Vloží prvek za aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	if(L->Act == NULL)
	{
		return;
	}

	tDLElemPtr element;

	element = malloc(sizeof(struct tDLElem)); //Pokud nastala chyba při alokaci paměti

	if(element == NULL)
	{
		DLError();
		return;
	}

	element->data = val; //Vložím hodnotu do nového prvku

	element->lptr = L->Act; //Levý ukazatel bude ukazovat na aktivní prvek
	element->rptr = L->Act->rptr; //Pravá strana bude ukazovat na další prvek (případně NULL)

	L->Act->rptr = element;

	if(L->Act == L->Last) //Zda je aktivní prvek poslední
	{
		//Pokud byl aktivní prvek poslední, 
		//tak musíme změnit Last
		L->Last = element; 
	}
	else
	{
		//Levý ukazatel následujícího prvku
		//bude ukazovat na nově vložená prvek
		element->rptr->lptr = element; 
	}

}

void DLPreInsert (tDLList *L, int val) {
/*
** Vloží prvek před aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	if(L->Act == NULL)
	{
		return;
	}

	tDLElemPtr element;

	element = malloc(sizeof(struct tDLElem)); //Pokud nastala chyba při alokaci paměti

	if(element == NULL)
	{
		DLError();
		return;
	}

	element->data = val; //Vložím hodnotu do nového prvku

	element->rptr = L->Act; //Pravý ukazatel bude ukazovat na aktivní prvek
	element->lptr = L->Act->lptr; //Levý ukazatel bude ukazovat na prvek před aktuálním (popř. NULL)

	L->Act->lptr = element;

	if(L->Act == L->First) //Zda byl aktivní prvek zároveň prvním prvkem
	{
		L->First = element; //Pokud ano, musíme změnit First
	}
	else
	{
		element->lptr->rptr = element; //Pokud ne, předchozí prvek bude ukazovat na nový prvek
	}

}

void DLCopy (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu aktivního prvku seznamu L.
** Pokud seznam L není aktivní, volá funkci DLError ().
**/

	if(L->Act == NULL)
	{
		DLError();
		return;
	}
	else
	{
		*val = L->Act->data; //Uložení dat aktivního prvku do proměnné val
	}
	
}

void DLActualize (tDLList *L, int val) {
/*
** Přepíše obsah aktivního prvku seznamu L.
** Pokud seznam L není aktivní, nedělá nic.
**/
	if(L->Act != NULL)
	{
		L->Act->data = val; //Uložení dat z hodnoty val do aktivního prvku
	}
	
}

void DLSucc (tDLList *L) {
/*
** Posune aktivitu na následující prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na posledním prvku se seznam stane neaktivním.
**/
	if(L->Act != NULL)
	{
		if(L->Act != L->Last) //Kontrola, zda aktuální prvek není poslední
		{
			L->Act = L->Act->rptr; //Posunutí aktivity na další prvek
		}
		else
		{
			L->Act = NULL; //Aktivní prvek byl poslední -> seznam se stává neaktivním
		}
	}

}


void DLPred (tDLList *L) {
/*
** Posune aktivitu na předchozí prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na prvním prvku se seznam stane neaktivním.
**/
	if(L->Act != NULL)
	{
		if(L->Act != L->First) //Kontrola, zda aktuální prvek není první
		{
			L->Act = L->Act->lptr; //Posunutí aktivity na další prvek
		}
		else
		{
			L->Act = NULL; //Aktivní prvek byl první -> seznam se stává neaktivním
		}
	}
	
}

int DLActive (tDLList *L) {
/*
** Je-li seznam L aktivní, vrací nenulovou hodnotu, jinak vrací 0.
** Funkci je vhodné implementovat jedním příkazem return.
**/
	return (L->Act != NULL) ? TRUE : FALSE;

}

/* Konec c206.c*/
