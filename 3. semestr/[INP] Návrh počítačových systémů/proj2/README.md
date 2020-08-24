## [INP] Projekt č.2 – Jednoduchá shluková analýza

### Zadání

Cílem tohoto projektu je implementovat pomocí VHDL procesor, který bude schopen vykonávat program napsaný v rozšířené verzi jazyka BrainF\*ck. Ačkoliv jazyk BrainF\*ck používá pouze osm jednoduchých příkazů (instrukcí), jedná se o výpočetně úplnou sadu, pomocí které je možné popsat libovolný algoritmus. Na ověření korektní funkce poslouží několik testovacích programů (výpis textu, výpis prvočísel, rozklad čísla na prvočísla, apod.).

Podrobnosti viz. zadani.pdf

### Hodnocení 

Ověření činnosti kódu CPU:

 |     | **testovaný program (kód)**     |**výsledek**|
 |:---:|:-------------------------------:|:--------:|
 | 1.  | ++++++++++                      |  **ok**  |
 | 2.  | ----------                      |  **ok**  |
 | 3.  | +>++>+++                        |  **ok**  |
 | 4.  | <+<++<+++                       |  **ok**  |
 | 5.  | .+.+.+.                         |  **ok**  |
 | 6.  | ,+,+,+,                         |  **ok**  |
 | 7.  | [........]noLCD[.........]      |  **ok**  |
 | 8.  | +++[.-]                         |  **ok**  |
 | 9.  | +++++[>++[>+.<-]<-]             |  **ok**  |
 | 10. | 0123456789ABCDEF                |  **ok**  |
 | 11. | /0123456789:;@ABCDEFGHIJKLMN    |  **ok**  |
 | 12. | .#++++........#+.#[.........]#  |  **ok**  |

  Podpora jednoduchých cyklů: ano\
  Podpora vnořených cyklů: ano

Poznámky k implementaci:
  - Nekompletní sensitivity list; chybějící signály: write_number_letter
  - Možné problematické řízení následujících signálů: OUT_DATA, write_number_letter

Celkem bodů za CPU implementaci: **17** (z 17)

&nbsp;
&nbsp;

Získáno bodů: 22 / 23
