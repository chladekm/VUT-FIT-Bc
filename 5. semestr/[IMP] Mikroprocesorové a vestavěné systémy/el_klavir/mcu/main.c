/********************* 1. PROJEKT DO IMP 2019/2020 *********************/
/*																	   */
/*		Autor: 	Martin Chládek										   */
/*		Login:	xchlad16											   */
/*		Kontakt: xchlad16@stud.fit.vutbr.cz							   */
/*		Posledni editace: 22/12/2019							   	   */
/*		Zmeny: original										   		   */
/*																	   */
/***********************************************************************/


#include <fitkitlib.h>

#include <lcd/display.h>
#include <keyboard/keyboard.h>

// Definice frekvence tonu
// zdroj: https://mathisdeliciousdotcom.files.wordpress.com/2015/08/ca549-c6c096c49af3c766ea47df64e7b18ee5.gif
#define c 33
#define d 37
#define e 41
#define f 44
#define g 49
#define a 55
#define h 62

// Definice oktav
#define OCTAVE_1 1
#define OCTAVE_2 2
#define OCTAVE_3 4
#define OCTAVE_4 8
#define OCTAVE_5 16
#define OCTAVE_6 32
#define OCTAVE_7 64

#define TICKS_PER_SECOND 32768 //pocet tiku hodin za 1s (pro pocitani s frekvenci)

#define MAX_PEAK 255 // vzorkovani na max 255 (8 bitu) (1.5V)
#define ZERO 0 // vzorkovani na 0 (0V)
#define NO_OF_SAMPLES 2 // Pocet vzorku -> 2 (0 a 255)

#define DEFAULT_tone_duration 600 //trvani tonu

int tone_duration; //trvani tonu

int irq_interval; // po kolika ticich ma dojit k preruseni - 0x8000 = 32768 (1 sekunda)
char irq_enable;
char square_signal_switcher = 1; // prepinac mezi vzorky na 0 (0V) a 255 (1.5V)
char orig_tone;
char tone_fade;

char previous_char; //naposledy precteny znak
int tone_frequency; //frekvence daneho tonu
int tone_octave;

int downsizing;
int downsizing_limit;


void print_user_help(void) {
term_send_str_crlf("\n----------------------------------------------------\n");
term_send_str("Je potreba mit pripojen reproduktor na vyvod 31 (JP9) a \n");
term_send_str("druhy drat upevnit na zem\n"); term_send_str("--- Ovladani:
\n"); term_send_str("klavesy 1 - 7 reprezentuji klavesy klaviru c - h\n");
term_send_str("klavesa 0 spusti refren pisne Jingle Bells\n");
term_send_str("klavesy A,B,C,D,#,0 meni oktavu\n");	
term_send_str_crlf("----------------------------------------------------\n"); }

unsigned char decode_user_cmd(char *cmd_ucase, char *cmd)
{
	if(strcmp5(cmd_ucase, "BELLS"))
	{
		term_send_str("(4. oktava)(D) e(3) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) e(3) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) e(3) - g(5) - c(1) - d(2) - e(3)\n");
		term_send_str("(3. oktava)(C) c(1) - d(2) - e(3)\n");
		term_send_str("(4. oktava)(D) f(4) - f(4) - f(4)\n\n");

		term_send_str("(4. oktava)(D) f(4) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) e(3) - d(2) - d(2) - e(3) - d(2)\n\n");

		term_send_str("(4. oktava)(D) g(5)\n\n");

		term_send_str("(4. oktava)(D) e(3) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) e(3) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) e(3) - g(5) - c(1) - d(2) - e(3)\n");
		term_send_str("(3. oktava)(C) c(1) - d(2) - e(3)\n");
		term_send_str("(4. oktava)(D) f(4) - f(4) - f(4)\n\n");

		term_send_str("(4. oktava)(D) f(4) - e(3) - e(3)\n\n");

		term_send_str("(4. oktava)(D) g(5) - g(5) - f(4) - d(2) - c(1)\n");
		 
		 
		return USER_COMMAND;
	}
	else if(strcmp5(cmd_ucase, "TEACH"))
	{
		term_send_str("(D) - 3 - 3 - 3\n\n");

		term_send_str("3 - 3 - 3\n\n");

		term_send_str("3 - 5 - 1 - 2 - 3\n");
		term_send_str("(C) - 1 - 2 - 3\n");
		term_send_str("(D) - 4 - 4 - 4\n\n");

		term_send_str("4 - 3 - 3\n\n");

		term_send_str("3 - 2 - 2 - 3 - 2\n\n");

		term_send_str("5\n\n");

		term_send_str("3 - 3 - 3\n\n");

		term_send_str("3 - 3 - 3\n\n");

		term_send_str("3 - 5 - 1 - 2 - 3\n");
		term_send_str("(C) - 1 - 2 - 3\n");
		term_send_str("(D) - 4 - 4 - 4\n\n");

		term_send_str("4 - 3 - 3\n\n");

		term_send_str("5 - 5 - 4 - 2 - 1\n");

		return USER_COMMAND;
	}
	else if(strcmp4(cmd_ucase, "PLAY"))
	{
		play_jingleBells();
		return USER_COMMAND;
	}

	return (CMD_UNKNOWN);
}

// Funkce zajistujici zahrati tonu
void play_tone(int frequency, int duration, int pause)
{
	tone_frequency = frequency;
	tone_duration = duration;

	set_interrupt_interval();

	// Povoleni hrani tonu pri preruseni
	irq_enable = 1;
	orig_tone = 1;

	if(tone_fade)
	{
		downsizing = 0;

		switch(tone_octave)
		{
			// Ziskano experimentovanim
			case OCTAVE_1: downsizing_limit=(int) (tone_frequency / 10); break;
			case OCTAVE_2: downsizing_limit=(int) (tone_frequency / 10); break;
			case OCTAVE_3: downsizing_limit=(int) (tone_frequency / 10); break;
			case OCTAVE_4: downsizing_limit=(int) (tone_frequency / 15); break;
			case OCTAVE_5: downsizing_limit=(int) (tone_frequency / 25); break;
			default: break;
		}

		delay_ms(tone_duration/2);
		orig_tone = 0;
		delay_ms(tone_duration/2);
	}
	else
	{
		delay_ms(tone_duration);
	}


	// Preruseni nebude moct zahrat ton
	irq_enable = 0;
	delay_ms(pause);

}

// Nastaveni delky preruseni
void set_interrupt_interval()
{
	irq_interval = TICKS_PER_SECOND / tone_frequency / 2;
	CCR0 = irq_interval;
}

interrupt (TIMERA0_VECTOR) Timer_A (void)
{  

	if(irq_enable)
	{

		if(orig_tone)
		{
			if(square_signal_switcher)
			{
				DAC12_0DAT = MAX_PEAK;
				square_signal_switcher = 0;
			}
			else
			{
				DAC12_0DAT = ZERO;
				square_signal_switcher = 1;
			}
			CCR0 = irq_interval;
		}
		else
		{
			if(square_signal_switcher)
			{
				DAC12_0DAT = MAX_PEAK;
				square_signal_switcher = 0;
			}
			else
			{
				DAC12_0DAT = ZERO;
				square_signal_switcher = 1;
			}

			downsizing += 1;

			if((downsizing == downsizing_limit)||(downsizing > downsizing_limit))
			{
				CCR0 += 1;
				downsizing = 0;
			}
		}

	}
}

// zdroj: https://snipplr.com/view/62662/jingle-bells-song/
void play_jingleBells()
{
	LCD_clear();
	LCD_write_string("Hraje Jingle Bells");

	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(g * OCTAVE_4, 300, 10);
	play_tone(c * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	 
	play_tone(c * OCTAVE_3, 300, 10);
	play_tone(d * OCTAVE_3, 300, 10);
	play_tone(e * OCTAVE_3, 300, 10);
	 
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(f * OCTAVE_4, 300, 300);
	 
	 
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 300);
	 
	play_tone(g * OCTAVE_4, 300, 300);
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	 
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(g * OCTAVE_4, 300, 10);
	play_tone(c * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	 
	play_tone(c * OCTAVE_3, 300, 10);
	play_tone(d * OCTAVE_3, 300, 10);
	play_tone(e * OCTAVE_3, 300, 10);
	 
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(f * OCTAVE_4, 300, 300);
	 
	 
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 10);
	play_tone(e * OCTAVE_4, 300, 300);
	 
	 
	play_tone(g * OCTAVE_4, 300, 10);
	play_tone(g * OCTAVE_4, 300, 10);
	play_tone(f * OCTAVE_4, 300, 10);
	play_tone(d * OCTAVE_4, 300, 10);
	play_tone(c * OCTAVE_4, 600, 600);
	 
}

// Nastaveni prepinace odezneni
void toggle_tone_fade()
{
	tone_fade = !tone_fade;
	LCD_clear();
	if(tone_fade)
	{
		LCD_append_string("Odezneni zapnuto"); 
		term_send_str("Odezneni zapnuto\n");
	}
	else
	{
		LCD_append_string("Odezneni vypnuto");
		term_send_str("Odezneni vypnuto\n");
	}
}

// Nastaveni a vypis oktavy
void set_octave(char octave)
{
	tone_octave = octave;

	LCD_clear();
	switch(tone_octave)
	{
		case OCTAVE_1: LCD_append_string("Nastavena 1. oktava"); term_send_str("Nastavena 1. oktava\n"); break;
		case OCTAVE_2: LCD_append_string("Nastavena 2. oktava"); term_send_str("Nastavena 2. oktava\n"); break;
		case OCTAVE_3: LCD_append_string("Nastavena 3. oktava"); term_send_str("Nastavena 3. oktava\n"); break;
		case OCTAVE_4: LCD_append_string("Nastavena 4. oktava"); term_send_str("Nastavena 4. oktava\n"); break;
		case OCTAVE_5: LCD_append_string("Nastavena 5. oktava"); term_send_str("Nastavena 5. oktava\n"); break;
		case OCTAVE_6: LCD_append_string("Nastavena 6. oktava"); term_send_str("Nastavena 6. oktava\n"); break;
		case OCTAVE_7: LCD_append_string("Nastavena 7. oktava"); term_send_str("Nastavena 7. oktava\n"); break;
		default: break;
	}
}

// Nastaveni a vypis tonu
void set_tone(int tone)
{
	int frequency = tone * tone_octave;

	LCD_clear();
	switch(tone)
	{
		case c: LCD_append_string("Hraje ton: c"); break;
		case d: LCD_append_string("Hraje ton: d"); break;
		case e: LCD_append_string("Hraje ton: e"); break;
		case f: LCD_append_string("Hraje ton: f"); break;
		case g: LCD_append_string("Hraje ton: g"); break;
		case a: LCD_append_string("Hraje ton: a"); break;
		case h: LCD_append_string("Hraje ton: h"); break;
		default: break;
	}

	// Zahrej ton
	play_tone(frequency, DEFAULT_tone_duration, 10);
}

// Obsluha klavesnice
void keyboard_idle()
{

	unsigned char new_char;

	new_char = key_decode(read_word_keyboard_4x4());

	if (new_char != previous_char) 
	{
		previous_char = new_char;
		if (new_char != 0) 
		{
			switch(new_char)
			{
				case '1': set_tone(c); break;
				case '2': set_tone(d); break;
				case '3': set_tone(e); break;
				case '4': set_tone(f); break;
				case '5': set_tone(g); break;
				case '6': set_tone(a); break;
				case '7': set_tone(h); break;
				// case '8': break;
				// case '9': break;
				case '*': toggle_tone_fade(); break;
				case '0': term_send_str("Hraje Jingle Bells, pockejte na dokonceni skladby\n"); play_jingleBells(); break;
				case '#': set_octave(OCTAVE_5); break;
				case 'A': set_octave(OCTAVE_1); break;
				case 'B': set_octave(OCTAVE_2); break;
				case 'C': set_octave(OCTAVE_3); break;
				case 'D': set_octave(OCTAVE_4); break;
				default: 
					term_send_str("Neocekavany vstup\n");
					LCD_clear(); 
					LCD_append_string("Neplatne tlacitko");
					break;
			}	
		  
		}
	}
}


// Inicializace FPGA
void fpga_initialized()
{
	term_send_str_crlf("----------------------------------------------------");
	term_send_str_crlf("Aplikace je pripravena k behu.");
	term_send_str_crlf("Pro napovedu zadejte 'help'.");
	term_send_str_crlf("Reproduktor je treba pripojit na vyvod 31 (JP9) a uzemneni");
	term_send_str_crlf("----------------------------------------------------");

	LCD_init();
	LCD_write_string("Klavir je pripraven hrat!");
	LCD_send_cmd(LCD_DISPLAY_ON_OFF | LCD_DISPLAY_ON | LCD_CURSOR_OFF, 10);  // Vypnuti kurzoru
}


//	manual: http://www.ti.com/lit/ug/slau144j/slau144j.pdf
int main(void)
{

	initialize_hardware();
	WDG_stop();		// zastaveni Watchdogu

	tone_octave = OCTAVE_3; // inicializace octavy na nejakou

	// Inicializace -> napeti 0V	
	irq_enable = 0;

	irq_interval = 500;
	tone_fade = 0;

	// Nastaveni casovace [manual str. 369]
	CCTL0 = CCIE; // povoleni preruseni
	CCR0 = irq_interval; // inicializace komparatoru
	TACTL = TASSEL_1 + MC_1; //ACLK, Up mode

	// Nastaveni DAC [manual str. 595]
	ADC12CTL0 |= 0x0020;    // nastaveni referencniho napeti z ADC na 1,5 V
	DAC12_0CTL |= 0x1060;   // nastaveni kontrolniho registru DAC (8-bitovy rezim, medium speed)
	DAC12_0CTL |= 0x100;    // vystupni napeti nasobit 1x
	DAC12_0DAT = 0;         // vynulovani vystupni hodnoty DAC

	while (1)
	{
		keyboard_idle();      // obsluha klavesnice
		terminal_idle();      // obsluha terminalu
	}


}
