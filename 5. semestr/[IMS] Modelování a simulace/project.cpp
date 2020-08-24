
// Soubor: project.cpp
// Autori: Martin Chladek, Borek Reich
// Datum: 9.12.2019
// Kurz: IMS - Uhlikova stopa v doprave

#include "simlib.h"
#include <stdio.h>
#include <string.h>
#include <iostream>
#include <string>

// Ktera zeme je vybrana
#define CR 1
#define D 2

// Prumerna cena auta
// CR pro 2019
// D pro 2016
#define PRICE_CR 519600
#define PRICE_D 757400

// Prumerna cena elektrickeho vozidla (34 091 usd)
#define PRICE_ELECTRIC 786800

// Prumerna hruba mzda
// CR 2019
// D 2016
// https://www.statista.com/statistics/416207/average-annual-wages-germany-y-on-y-in-euros/
// https://www.finance.cz/519126-prumerna-mzda-2019-cr-eu-nemecko-rakousko/
#define SALARY_CR 31851
#define SALARY_D 83000
#define SALARY_VARIABILITY 25000

// Rozdeleni populace podle pohlavi
#define SEX_RATIO_CR 49.1
#define SEX_RATIO_D 48.6515

#define FEMALE 0
#define MALE 1

// Disttribuce vekovych kategorii kupujicich nova auta
// Statistika od VWE
#define PROB_AGE_18_25 2
#define PROB_AGE_26_35 9
#define PROB_AGE_36_45 21
#define PROB_AGE_46_55 44
#define PROB_AGE_56_65 71
#define PROB_AGE_66_PLUS 100

#define AGE_18_25 0
#define AGE_26_35 1
#define AGE_36_45 2
#define AGE_46_55 3
#define AGE_56_65 4
#define AGE_66_PLUS 5

// Minimalni mzda v zemich
// CR 2019
// D 2016
#define MINIMAL_SALARY_CR 13350
#define MINIMAL_SALARY_D 40197

// Vyska dotace v jednotlivych statech
// Dotace na nakup auta do 1,2 milionu
#define SUBSIDY_CR 200000	// Navrhovana vyse dotace
#define SUBSIDY_D 102000	// Aktualni vyse dotace

// ***** Elektricke vozidlo *****
// Na vyrobu jedne kWh baterie je vyprodukovano prumerne 147kg CO2
#define EL_PRODUCTION_CO2_kWh 147000
#define EL_PRODUCTION_CO2_CAR 7700000 //Elektroauto prumerne 7,7 tuny CO2 bez baterie
#define EL_PRODUCTION_CO2_CAR_VARIABILITY 2500000 //g CO2 (2,5 tun)
#define EL_AVG_BATTERY_CAPACITY 70.46 //Prumerna velikost baterie elektromobilu
#define EL_AVG_BATTERY_VARIABILITY 15 //kWh - rozptyl pokryje 99% vozu
#define EL_TRAVEL_CO2_PER_KM 83 //g CO2 na 1 km (vyprodukovane elektrarnou)

// ***** Vozidlo se spalovacim motorem *****
// Na vyrobu auta se spalovacim motorem je vyprodukovano prumerne 9,7 tuny CO2
#define GAS_PRODUCTION_CO2_CAR 9700000	
#define GAS_PRODUCTION_CO2_CAR_VARIABILITY 3000000 //g CO2 (3 tuny)
#define GAS_TRAVEL_CO2_PER_KM 120.5 //g CO2 na 1 km


// https://www.odyssee-mure.eu/publications/efficiency-by-sector/transport/distance-travelled-by-car.html
// Kolik prumerne v zemi auto denne procestuje km
#define AVERAGE_CAR_TRAVEL_CR 8000
#define AVERAGE_CAR_TRAVEL_D 14000
#define TRAVEL_VARIANCE 4000

// https://www.statista.com/statistics/452286/czech-republic-number-of-registered-passenger-cars/
#define NUMBER_CARS_CR 5500000
// https://www.best-selling-cars.com/germany/2016-germany-total-number-registered-cars/
#define NUMBER_CARS_D 45070000

// Nazor na elektromobily
// https://www.eon.com/de/ueber-uns/presse/pressemitteilungen/2019/nur-16-prozent-der-deutschen-wuerden-sich-ein-elektroauto-kaufen.html
// Predpokladame, ze prumerny clovek bude mit prumerny nazor a bude dal determinovan jinymi priznaky
#define FIRST_IMPRESSION_CR 0.13
#define FIRST_IMPRESSION_D 0.16

double t_end = 3 * 365;
int country;
long car_count = 0;
long electric_car_count = 0;
double cars_per_year;
double day_cars;
double number_of_buyers;
double average_car_price;
double average_car_travel;
double sex_ratio;
double minimal_salary;
double average_salary;
double subsidy;
double electro_car_threshold;
double number_cars;
double first_impression;

long double total_emissions = 0;
long double all_salaries = 0;
long double total_el_production = 0;
long double total_gas_production = 0;

// Transakce reprezentujici spotrebitele
class Buyer : public Process
{

	double electro_car_probability;
	int age;
	int gender;
	double salary;
	double age_rand_num;
	double car_travel = 0;
	
	void Behavior()
	{
		double time_start = Time;	// Ulozeni casu vzniku auta
		car_count++;

		// Nastaveni nazoru na elektroautomobily
		electro_car_probability = first_impression;

		// Zeny jsou pri nakupu rozvaznejsi a opatrnejsi, 
		// je vetsi pravdepodobnost, ze nakup odlozi

		// Muz
		if(Uniform(0,100) <= sex_ratio)
		{	
			// Muz je pravdepodobne vice spontanni pri nakupech a pravdepodobne vice riskuji
			// https://www.psychological-consultancy.com/blog/women-twice-likely-cautious-risk-men/
			// 45% sance, ze bude spise vahavy a opatrny
			if(Uniform(0,100) <= 45)
				electro_car_probability -= 0.05;
			else
				electro_car_probability += 0.05;
		}
		// Zena
		else
		{
			// Zena je pravdepodobne vice opatrna pri nakupu a pravdepodobne mene riskuji
			// https://www.psychological-consultancy.com/blog/women-twice-likely-cautious-risk-men/
			// 57% sance, ze bude spise vahavy a opatrny
			if(Uniform(0,100) <= 57)
				electro_car_probability -= 0.05;
			else
				electro_car_probability += 0.05;
		}
		

		// Nastaveni veku na zaklade pravdepodobnosti
		age_rand_num = Uniform(0,100);

		if(age_rand_num <= PROB_AGE_18_25)
		{
			age = AGE_18_25;
		}
		else if (age_rand_num <= PROB_AGE_26_35)
		{
			age = AGE_26_35;
		}
		else if (age_rand_num <= PROB_AGE_36_45)
		{
			age = AGE_36_45;
		}
		else if (age_rand_num <= PROB_AGE_46_55)
		{
			age = AGE_46_55;
		}
		else if (age_rand_num <= PROB_AGE_56_65)
		{
			age = AGE_56_65;
		}
		else {
			age = AGE_66_PLUS;
		}

		// Nastaveni mzdy kupujiciho
		salary = Normal(average_salary, SALARY_VARIABILITY);
		// Mzda musi byt vetsi nez minimalni mzda v dane zemi
		if (salary < minimal_salary){
			salary = minimal_salary + (rand() % ((int)(average_salary/4)));
			salary = minimal_salary;
		}

		// Vypocet ujetych kilometru na zaklade casu koupe auta
		for (double i = 0.0; i < t_end/365 - Time/365; ++i) {
			car_travel += Normal(average_car_travel, TRAVEL_VARIANCE);
		}

		//************** Rozhodovani o koupi ******************

		// Vek ma vliv na konzervatismus a tradicionalismus
		
		// Snizeni o 5 %
		if (age == AGE_56_65)
			electro_car_probability -= 0.05;

		// Snizeni o 10 %
		if (age == AGE_66_PLUS)
			electro_car_probability -= 0.1;


		// Nejvetsi vliv ma mzda kupujiciho
		if (salary > electro_car_threshold) 
		{
			// Vyplata je vyssi nez vypocitany doporuceny meznik pro nakup elektroauta
			// -> pravdepodobnost nakupu se zvysuje

			electro_car_probability += 0.01*(27.5*electro_car_threshold)/salary;
		}
		else 
		{
			// Vyplata je nizsi nez vypocitany doporuceny meznik pro nakup elektroauta
			// -> pravdepodobnost nakupu se snizuje

			electro_car_probability -= 0.01*(27.5*electro_car_threshold)/salary;
		}

		// Vliv vyse platu na rozhodovani cloveka
		// Pro cloveka s vyssim platem bude dotace pri rozhodovani mene ovlivnovat 
		electro_car_probability += 0.01 * (subsidy/salary);

		// Zdurazneni vysky dotace pri vysokych dotacich
		electro_car_probability += 0.01 * (subsidy/salary)/(electro_car_threshold/minimal_salary);
	
		//[1] https://pdfs.semanticscholar.org/3c96/48892f4c46d7d1a2c1346f21bd10c782c732.pdf
		//[2] https://www.mdpi.com/2071-1050/11/17/4734/pdf
		// Cim vic elektromobilu, tim vic lide nakloneni si ho taky koupit
		// Monzost navyseni az o 20 % [1] = kdyz by byla vsechna auta elektromobily.
		// Zanedbavam rocni ubytek poctu automobilu.
		electro_car_probability += 0.2 * electric_car_count/(number_cars+car_count);

		if(electro_car_probability < 0)
			electro_car_probability = 0;

		// Urcita mira nejistoty pri rozhodovani spotrebitele
		if (Uniform(40, 60) <= (electro_car_probability*100))
		{
			// ******** Elektroauto ********
			electric_car_count++;

			// Velikost baterie u elektromobilu
			double el_car_battery_capacity;
			el_car_battery_capacity = Normal(EL_AVG_BATTERY_CAPACITY, EL_AVG_BATTERY_VARIABILITY);

			// Produkce CO2 pri vyrobe elektromobilu bez baterie
			double el_car_production;
			el_car_production = Normal(EL_PRODUCTION_CO2_CAR, EL_PRODUCTION_CO2_CAR_VARIABILITY);
			
			// Celkove emise na vyrobu elektrickeho vozidla
			double electric_car_production_co2;
			electric_car_production_co2 = el_car_production + (el_car_battery_capacity * EL_PRODUCTION_CO2_kWh);

			// printf("Emise na elektromobil: %.2lf tun........%.lf tun........%.lf kWh\n", electric_car_production_co2/1000000, el_car_production/1000000, el_car_battery_capacity);

			total_el_production += electric_car_production_co2;

			total_emissions += (long double) electric_car_production_co2;
			total_emissions += (long double) car_travel * EL_TRAVEL_CO2_PER_KM; //Emise vytvorene najezdem auta
		}
		else
		{
			// ******** Spalovaci motor ********

			double gas_car_production;
			gas_car_production = Normal(GAS_PRODUCTION_CO2_CAR, GAS_PRODUCTION_CO2_CAR_VARIABILITY);

			total_gas_production += gas_car_production;
			
			// printf("Emise na spal. auto:   %.2lf tun\n", gas_car_production/1000000);

			total_emissions += (long double) gas_car_production;
			total_emissions += (long double) car_travel * GAS_TRAVEL_CO2_PER_KM;
		}
	};

};

// Generovani pozadavku na vyrizeni
class Generator : public Event {
	void Behavior() {
		(new Buyer)->Activate();
		Activate(Time + day_cars);
	}
};

int main(int argc, char *argv[])
{

	double coeff;
	number_of_buyers = 0;
	
	//******* Nemecko *******
	if(strcmp(argv[1], "de") == 0) {
		// Zdroj: https://www.acea.be/statistics/tag/category/by-country-registrations
		// Pocty novych aut za roky:
		// 2018 = 3 435 800
		// 2017 = 3 441 400
		// 2016 = 3 351 600
		// 2015 = 3 206 000
		// 2014 = 3 037 000
		// 2013 = 2 952 000

		// Vyvoj poctu novych aut:
		// 2017-2018 = -0,2 %
		// 2016-2017 =  2,7 %
		// 2015-2016 = 4,5 %
		// 2014-2015 = 5,6 %
		// 2013-2014 = 2,9 %

		// Stredni hodnota = 3,1
		// Rozptyl = 24,1
		// Predpoklad poctu prodanych aut za rok 2019 = 3435800 + 0.01 * Normal(3.1, 24.1) * cars_per_year

		// Je zadano v procentech, musime nasobit 0.01

		cars_per_year = 3351800.0;

		for (int i = 0; i < t_end/365; ++i)
		{
			coeff = 0.01 * Normal(3.1, 24.1);
			
			// Trh stabilni, max pripustna zmena 10 %
			if (coeff > 0.1)
				coeff = 0.1;
			
			if (coeff < -0.1)
				coeff = -0.1;

			cars_per_year += coeff * cars_per_year;
			number_of_buyers += cars_per_year;
		}

		day_cars = t_end/number_of_buyers;

		average_car_price = PRICE_D;		
		sex_ratio = SEX_RATIO_D;
		average_salary = SALARY_D;
		minimal_salary = MINIMAL_SALARY_D;
		subsidy = SUBSIDY_D;
		average_car_travel = AVERAGE_CAR_TRAVEL_D;
		number_cars = NUMBER_CARS_D;
		first_impression = FIRST_IMPRESSION_D;

		if (argc > 3)
		{
			subsidy = std::stod(argv[3]);
		}

		electro_car_threshold = (PRICE_ELECTRIC - subsidy) * 10 / (12 * 10);
		if(electro_car_threshold < 0)
			electro_car_threshold = MINIMAL_SALARY_D;
	}
	//***********************

	//******* Cesko *******
	if(strcmp(argv[1], "cs") == 0) {
		// Zdroj: https://www.acea.be/statistics/tag/category/by-country-registrations
		// Pocty novych aut za roky:
		// 2018 = 261 400
		// 2017 = 271 600
		// 2016 = 259 700
		// 2015 = 230 900
		// 2014 = 192 300
		// 2013 = 164 700

		// Vyvoj poctu novych aut:
		// 2017-2018 = -3,9 %
		// 2016-2017 = 4,6 %
		// 2015-2016 = 12,5 %
		// 2014-2015 = 20 %
		// 2013-2014 = 16,8 %

		// Stredni hodnota = 10
		// Rozptyl = 122,582
		// Predpoklad poctu prodanych aut za rok 2019 = 261400 + Normal(10, 122.582) * 0.01 * cars_per_year

		// Je zadano v procentech, musime nasobit 0.01
		cars_per_year = 261400.0;

		t_end = std::stod(argv[2]) * 365;

		for (int i = 0; i < t_end/365; ++i)
		{
			coeff = 0.01 * Normal(10, 122.582);
			
			// Trh promenlivy, max pripustna zmena 20 %
			if (coeff > 0.2)
				coeff = 0.2;
			
			if (coeff < -0.2)
				coeff = -0.2;

			cars_per_year += coeff * cars_per_year;
			number_of_buyers += cars_per_year;
		}
		
		day_cars = t_end/number_of_buyers;

		average_car_price = PRICE_CR;
		sex_ratio = SEX_RATIO_CR;
		average_salary = SALARY_CR;
		minimal_salary = MINIMAL_SALARY_CR;
		subsidy = SUBSIDY_CR;
		average_car_travel = AVERAGE_CAR_TRAVEL_CR;
		number_cars = NUMBER_CARS_CR;
		first_impression = FIRST_IMPRESSION_CR;

		if (argc > 3)
		{
			subsidy = std::stod(argv[3]);
		}

		electro_car_threshold = (PRICE_ELECTRIC - subsidy) * 10 / (12 * 10);
		if(electro_car_threshold < 0)
			electro_car_threshold = MINIMAL_SALARY_CR;
	}

	Init(0, t_end);		// nastaveni delky simulace

	(new Generator)->Activate();
	
	Run();				// start simulace

	printf("--------------------VYSTUP--------------------\n");
	printf("-------Simulace spustena pro %.lf roky/let-------\n", t_end/365);
	printf("Vyska dotace: \t%.lf\n", subsidy);
	printf("Zarazka: \t%.2lf\n", electro_car_threshold);
	printf("Celkem vozidel: %ld\n", car_count);
	printf("Elektromobilu:  %ld (%.2lf%)\n", electric_car_count, (electric_car_count/(double)car_count)*100);
	printf("Spalovaci auta: %ld (%.2lf%)\n", car_count-electric_car_count, ((car_count-electric_car_count)/(double)car_count)*100);
	// printf("Prumerna mzda: %.2Lf\n", all_salaries/car_count);
	// printf("Pomer elektromobilu: %.2lf \n", (electric_car_count/(double)car_count)*100);
	printf("Vyroba elektromobilu:         %.6Lf tun CO2\n", total_el_production/1000000);
	printf("Vyroba aut se spal. motorem:  %.6Lf tun CO2\n", total_gas_production/1000000);
	
	printf("Prumerne na 1 el. auto:    %.6Lf tun CO2\n", total_el_production/(1000000 * (long double)electric_car_count));
	printf("Prumerne na 1 spal. auto:  %.6Lf tun CO2\n", total_gas_production/(1000000 * (long double)(car_count - electric_car_count)));
	printf("----------------CELKOVE EMISE-----------------\n");
	printf("%.8Lf tun CO2\n", total_emissions/1000000);
	printf("----------------------------------------------\n");

}