/******************************************************/
/* * *        Iterační výpočty v jazyce C         * * */
/* * *                                            * * */
/* * *                 Verze: 4                   * * */
/* * *                                            * * */
/* * *              Martin Chládek                * * */
/* * *              listopad 2017                 * * */
/******************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <string.h>

#define NUMBER_OF_OPTIONS 4 //počet možností zadaných uživatelem
#define IMPLICIT_HEIGHT 1.5 //implicitní výška
#define MAX_ITERATION 13 //maximální počet iterací
#define NO_ITERATION 10 //number of interations - počet iterací, kdy je výsledek dostatečně přesný
#define MAX_HEIGHT 100 //maximální výška měřícího přístroje
#define MAX_RADIAN 1.4 //maximální zadaný úhel
#define PI 3.1415926535897932384626433

//NUMBER OF ARGUMENTS
#define HELP_ARG 2 //počet argumentů pro --help
#define TAN_ARG 5 //počet argumentů pro --tan
#define DIS_ARG_M 4 //počet argumentů pro -m
#define DIS_ARG_C 6 //počet argumentů pro -c

//ERRORS
#define ERR_NOARGUMENT 1 //no argument - nezadaný žádný argument
#define ERR_BADARGUMENT 2 //bad argument - zadaný špatný argument
#define ERR_AMOUNTARG 3 //bad amount of arguments - jiný počet argumentů, než je očekáván
#define ERR_BADVALUES 4 //bad values - pokud jsou zadány špatné hodnoty (např znak místo čísla)

//Funkce vypisující errory
void errors(int reason)
{
    switch (reason)
    {
        case ERR_NOARGUMENT:
            fprintf(stderr,"No argument on input.\n");
            break;
        case ERR_BADARGUMENT:
            fprintf(stderr,"Entered wrong argument.\n");
            break;
        case ERR_AMOUNTARG:
            fprintf(stderr,"Bad amount of arguments.\n");
            break;
        case ERR_BADVALUES:
            fprintf(stderr,"Entered wrong values.\n");
            
        default:
            break;
    }
    
    printf("For help enter '--help'.\n");
    exit(EXIT_FAILURE);
}

//Funkce, která vypisuje nápovědu k použití
void help(int arg)
{
    if(arg!=HELP_ARG)
    {errors(ERR_AMOUNTARG);}
    
    printf("----------------------------------------------------------\n");
    printf("\t\t\t Napoveda \n");
    printf("----------------------------------------------------------\n");
    
    printf("--tan A N M \tfunkce porovná přesnosti výpočtu tangens úhlu A");
    printf("\n\n\t\tA - uhel v radianech");
    printf("\n\t\tN - leva hodnota intervalu (podminka:  0 < N <= M)");
    printf("\n\t\tM - prava hodnota intervalu (podminka: N <= M < 14)");
    
    printf("\n\n-m A [B] \tfunkce vypocita a zmeri vzdalenosti");
    printf("\n\n\t\tA - uhel α v radianech (podminka:  0 < A <= 1.4 < π/2)\n\t\t  - program vypise vzdalenost mereneho objektu");
    printf("\n\t\tB - uhel β v radianech (podminka:  0 < B <= 1.4 < π/2)\n\t\t  - program vypise i vysku mereneho objektu\n\t\t  - argument je volitelny");
    
    printf("\n\n-c X -m A [B] \targument -c nastavuje vysku mericiho pristroje c pro vypocet");
    printf("\n\n\t\tX - vyska mericiho pristroje (podminka:  0 < X <= 100)");
    printf("\n\t\tA - uhel α v radianech (podminka:  0 < A <= 1.4 < π/2)\n\t\t  - program vypise vzdalenost mereneho objektu");
    printf("\n\t\tB - uhel β v radianech (podminka:  0 < B <= 1.4 < π/2)\n\t\t  - program vypise i vysku mereneho objektu\n\t\t  - argument je volitelny\n\n");
    
}

//Taylorův polynom
//x - úhel, n - rozvoj polynomu
double taylor_tan(double x, unsigned int n)
{
    //pole čitatelů pro Taylorův polynom
    double taylornumetor[]={1, 1, 2, 17, 62, 1382, 21844, 929569, 6404582, 443861162, 18888466084, 113927491862, 58870668456604};
    //pole jmenovatelů pro Taylorův polynom
    double taylordenominator[]={1, 3, 15, 315, 2835, 155925, 6081075, 638512875, 10854718875, 1856156927625, 194896477400625, 49308808782358125, 3698160658676859375};
    
    unsigned int i=0;
    double num=x; //numerator -> X v čitateli
    double result=0; //výsledek polynomu (operace)
    
    n++; //aby se provedl i první průchod
    
    while(i<n)
    {
        if(i!=0)
        {
            num = num * x * x;
        }
        result = result + ((num * taylornumetor[i])/taylordenominator[i]); //výpočet konkrétního členu polynomu
        i++;
    }
    
    return result;
}

//Zřetězené zlomky
//x - úhel, n - index
double cfrac_tan(double x, unsigned int n)
{
    double result=0; //result - výsledek
    
    while(n>0)
    {
    result=1/(((2*n-1)/x)-result);
    n--;
    }
        
    return result;
}
 

//Výstup funkce tangens -> volají se výpočty a vypisují výsledky
void tanoutput(double a, int n, int m)
{
    int i;
    double taylor_result=0; //výsledek Taylorova polynomu
    double taylor_diff=0; //rozdíl mezi výsledkem Taylorova polynomu a funkcí tan
    double cfrac_result=0; //výsledek zřetězeného zlomku
    double cfrac_diff=0; //rozdíl mezi výsledkem zřetězeného zlomku a funkcí tan
    
    for(i=n-1;i<=m-1;i++) //od 1. iterace do poslední určené uživatelem
    {
            taylor_result = taylor_tan(a, i);
            cfrac_result = cfrac_tan(a, i+1);
            
            taylor_diff=fabs(tan(a)-taylor_result); //výpočet rozdílu u Taylorova polynomu
            cfrac_diff=fabs(tan(a)-cfrac_result); //výpočet rozdílu u zřetězeného zlomku
        
            printf("%d %e %e %e %e %e\n",i+1,tan(a),taylor_result, taylor_diff, cfrac_result, cfrac_diff);
    }
}

//Funkce na tangens
void tanfunc(int arg, const char **values)
{
    if(arg!=TAN_ARG)
    {errors(ERR_AMOUNTARG);}
    
    double a;
    int n,m;
    
    a=atof(values[2]); //A - zadaný úhel
    n=atof(values[3]); //N - levý interval
    m=atof(values[4]); //M - pravý interval
    
    if(n==0||m==0||n<0||n>m||m>=MAX_ITERATION+1) //musí být 0 < N =< M < 14 -> pokud není splněno -> error
    {errors(ERR_BADVALUES);}
    
    tanoutput(a,n,m);
}

//Funkce na výpočet vzdálenosti
double count_distance(double x, double radianA) //X - výška, radianA - zadaný úhel alfa
{
    double distance=0; //Distance - vzdálenost
    double angle=0; //Pomocná pro výpočet úhlu
    
    //výpočet tangens úhlu
    angle=cfrac_tan(radianA, NO_ITERATION);
        
    distance=x/angle; //výpočet vzdálenosti
    
    return distance;
}

//Funkce na výpočet výšky
double count_height(double x, double radianB, double distance) //X - výška, radianB - zadaný úhel alfa
{
    double height=0; //Height - výška
    double angle=0; //Pomocná pro výpočet úhlu
    
    //výpočet tangens úhlu
    angle=cfrac_tan(radianB, NO_ITERATION);
    
    height=distance*angle; //výpočet výšky
    
    return x+height; //k vypočítané výšce se ještě přidá "výška pozorovatele"
}

//Funkce vzdálenosti a výšky - ošetřuje nesprávné vstupy a volá fuknce na ppočítání
void distance(int arg, int accarg, const char **values, double x) //accarg - acceptable arguments, x - výška
{
    if(arg!=accarg && arg!=accarg-1) //DIS_ARG-1 protože úhel beta může a nemusí být zadán
    {errors(ERR_AMOUNTARG);}
    
    double distance=0;
    double a,b;
    int i;
    
    if(arg==DIS_ARG_C||arg==DIS_ARG_C-1) //jestli bylo zadáno -m nebo -c
    {i=4;}else{i=2;} //indexy pro načtení proměnných
    
    a=atof(values[i]); //A - úhel alfa
    if(a<=0 || a>MAX_RADIAN || a>= PI/2) // 0 < A <= 1.4 < PI/2
    {errors(ERR_BADVALUES);}
    
    distance=count_distance(x, a); //výpočet vzdálenosti
    
    if(arg==DIS_ARG_C||arg==DIS_ARG_M) //pokud je počet argumentů shodný -> je zadána i beta
    {
        i++;
        b=atof(values[i]); //B - úhel beta
    
        if(b<=0 || b>MAX_RADIAN || b>= PI/2) // 0 < B <= 1.4 < PI/2
        {errors(ERR_BADVALUES);}
        
        printf("%.10e\n", distance);
        printf("%.10e\n", count_height(x,b,distance));
    }
}

//Funkce načtení výšky X
double height(const char **values)
{
    double x;
    x=atof(values[2]);
    
    if(x<=0 || x>MAX_HEIGHT) //musí být 0 < X <= 100
    {errors(ERR_BADVALUES);}
    
    return x;
}

//Funkce, která volá jednotlivé funkce podle zadaného prvního argumentu
void callfunction(int choice, int arg, const char **values)
{
    switch (choice)
    {
        case 1:
            //--help
            help(arg);
            break;
        case 2:
            //--tan
            tanfunc(arg, values);
            break;
        case 3:
            //-m
            distance(arg, DIS_ARG_M, values, IMPLICIT_HEIGHT);
            break;
        case 4:
            //-c
            distance(arg, DIS_ARG_C, values, height(values));
            break;
            
        default:
            break;
    }
    
    exit(EXIT_SUCCESS);
}

int main(int argc, const char * argv[])
{
    int i;

    //pole možností, které může uživatel zadat
    const char *option[NUMBER_OF_OPTIONS];
    option[0]="--help";
    option[1]="--tan";
    option[2]="-m";
    option[3]="-c";
    
    if(argc==1) //Pokud nebyl zadán žádný argument, bude vypsán error
    {errors(ERR_NOARGUMENT);}
    
    for(i=0;i<NUMBER_OF_OPTIONS;i++)
    {
        if(strcmp(argv[1],option[i])==0)
        {
            callfunction(i+1, argc, argv);
            break;
        }
    }
    
    errors(ERR_BADARGUMENT);
    
    return 0;
}
