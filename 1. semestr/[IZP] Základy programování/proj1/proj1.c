/******************************************************/
/* * *       Virtuální navigace v jazyce C        * * */
/* * *                                            * * */
/* * *                 Verze: 1                   * * */
/* * *                                            * * */
/* * *              Martin Chládek                * * */
/* * *                říjen 2017                  * * */
/******************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <stdbool.h>

#define ASCIILENGTH 129 //délka pole pro enable - základní ascii znaky
#define MAXLENGTH 101 //maximální délka adresy
#define MAXIMAL 42 //maximální počet adres

//Funkce porovnává znaky ze vstupu a adresy
bool porovnani_znaku(char znak, char znak_adresy)
{
    return znak==znak_adresy;
}

//Funkce, která porovnává stringy v případě, že by adresa už odpovídala, ale mohla by ještě pokračovat
bool porovnani_stringu(char *vstup, char *adresy)
{
    int a=1;
    a=strcmp(vstup, adresy); //pro a==0 -> TRUE
    return a==0;
}

//Funkce převádí znaky na velká písmena
void upper(char *pole)
{
    int i=0;
    while(pole[i]!='\0')
    {
        pole[i]=toupper(pole[i]);
        i++;
    }
}

//Funkce na test, jestli v enable nebudou dve stejna pismena
void test_duplicity(char *adresy, char *pole_znaku, int *k, int i)
{
    int stejne=0,j=0; //stejne - pomocná, která říká, jestli je v pole_znaku už stejný znak
    
    for(j=0;j<=(*k);j++)
    {
        if(pole_znaku[j]==adresy[i])
        {
            stejne=1;
        }
    }
    
    if(stejne==0)
    {
        pole_znaku[*k]=adresy[i];
        (*k)++;
    }
    stejne=0;
}

//Funkce přeháže znaky ve stringu podle abecedy
void abeceda(char *pole, int k)
{
    int i=0,j=0;
    int a=0,b=0; //pomocné k přetypování a porovnání, který znak je dříve v ASCII
    char pom; //pomocná na výměnu prvků
    
    for(i=0;i<k-1;i++)
    {
        for(j=0;j<k-1;j++)
        {
            a=pole[j];
            b=pole[j+1];
            if(a>b)
            {
                pom=pole[j+1];
                pole[j+1]=pole[j];
                pole[j]=pom;
            }
            a=0;
            b=0;
        }
    }
}

//Funkce vypisující errory
void errors(int i)
{
    switch(i)
    {
        case 1:
            fprintf(stderr,"Too many adresses.\n");
            break;
        case 2:
            fprintf(stderr,"Adress is too long.\n");
            break;
        case 3:
            fprintf(stderr,"File is empty.\n");
            break;
    }
exit(EXIT_FAILURE);
}

//Funkce, která načítá vstup
void nacteni(char *adresy, char *znak, int *min, int *max)
{
    int i=0;
    
    *max=*max+1; //počítá průchody - maximálně 42
    if(*max>MAXIMAL)
    {errors(1);}
    
    //načítá adresu, dokud není konec řádku, nebo konec souboru
    while (((*znak=getchar()) != '\n') && *znak!=EOF)
    {
        adresy[i] = *znak;
        i++;
        
        if(i>MAXLENGTH)
        {errors(2);}
        *min=*min+1;
    }
    
    adresy[i] = '\0';
    upper(adresy);
}

//Funkce, která vypíše Enable, pokud nebyl zadán žádný argument
void test_argumentu()
{
    char adresy[MAXLENGTH];
    char pole_znaku[ASCIILENGTH];
    char znak;
    int k=0,max=0,min=0;
    
    do{
        nacteni(adresy, &znak, &min, &max);
        test_duplicity(adresy, pole_znaku, &k, 0);
        
    }while(znak!=EOF);
    
    if(min==0)
    {errors(3);}
    
    abeceda(pole_znaku, k);
    printf("Enable: %s\n",pole_znaku);
    
    exit(EXIT_SUCCESS);
}

//Výpis výstupu
void vypis(int min, int poc, int en, int k, char *pole_znaku, char *vysl_pole)
{
    
    if(min==0) //pokud je proměnná minimum 0 -> soubor je prázdný
    {
        errors(3);
    }
    
    if(poc==0) //pokud je proměnná počítadlo 0 -> nebyla nalezena shoda se žádnou s adres
    {
        printf("Not found\n");
        en=0;
    }
    
   
    if(poc==1)  //pokud je proměnná počítadlo 1 -> byla nalezena právě 1 shoda
    {
        printf("Found: %s\n", vysl_pole);
        en=0;
    }
    
    if(en==1) //pokud je proměnná enable 1 -> vypíší se první písmena adres, které odpovídají zadanému argumentu
    {
        abeceda(pole_znaku, k);
        printf("Enable: %s\n", pole_znaku);
    }
}

//Hlavní část programu - funkce, která probíhá, dokud není konec souboru
void telo_programu(char *vstup)
{
    char adresy[MAXLENGTH];
    char znak;
    int i=0,k=0,spol,en=0,poc=0,max=0,min=0;
    char pole_znaku[ASCIILENGTH];
    char vysl_pole[MAXLENGTH];
    
    /*** Vysvětlivky k proměnným ***
     
     spol - pomocná, kolik znaků mají společných argv a adresy
     k - počet znaků vypsaných v ENABLE
     en - pomocná pro enable, pokud je 1, bude se tisknout výstup enable
     poc - počítadlo, kolik adres ještě odpovídá
     max - maximální počet adres
     min - minimální počet adres -> pokud je 0 -> error
     
     ***/
    
    do{
        nacteni(adresy, &znak, &min, &max);
        
        i=0;
        spol=0;
        
        for(i=0; vstup[i]!='\0'; i++)
        {
            if((porovnani_znaku(vstup [i], adresy[i]))==true)
            {
                spol++;
            }
        }
        
        if(spol==i) //pokud jsou shodná všechna písmena
        {
            if((porovnani_stringu(vstup,adresy))==true)
            {printf("Found: %s\n", adresy);}
            
            //pokud je počítadlo 1, zkopíruje se adresa do vysl_pole, aby se na konci mohla vytisknout
            //pokud je odpovídajících adres víc -> poc>1 a nic se nestane
            poc++;
            if(poc==1)
            {strcpy(vysl_pole,adresy);}
            
            test_duplicity(adresy, pole_znaku, &k, i);
            en=1;
        }
        
        i=0;
        
    }while(znak!=EOF);
    
    vypis(min, poc, en, k, pole_znaku, vysl_pole);
}

//Funkce main
int main(int argc, const char * argv[])
{
    
    //Kontrola, jestli je byla zadána nějaká adresa
    if(argc==1)
    {test_argumentu();}
    
    //Zkopírování vstupu z "argv" do "vstup"
    char vstup[100];
    strcpy(vstup, argv[1]);
    upper(vstup);
    
    telo_programu(vstup);
    
    return 0;
}
