## [IIS] Semestrální projekt – Informační systém

Téma: Jídelna - objednávka a rozvoz jídel

### Zadání

Cílem projektu je navrhnout a implementovat informační systém s webovým rozhraním pro zvolené zadání jedné z variant. Postup řešení by měl být následující:
Analýza a návrh informačního systému (analýza požadavků, tvorba diagramu případů užití, modelu relační databáze)
Volba implementačního prostředí - databázového serveru a aplikační platformy
Implementace navrženého databázového schématu ve zvoleném DB systému
Návrh webového uživatelského rozhraní aplikace
Implementace vlastní aplikace

#### Rozsah implementace

Implementovaný systém by měl být prakticky použitelný pro účel daný zadáním. Mimo jiné to znamená:

- Musí umožňovat vložení odpovídajících vstupů.
- Musí poskytovat výstupy ve formě, která je v dané oblasti využitelná. Tedy nezobrazovat obsah tabulek databáze, ale prezentovat uložená data tak, aby byla pro danou roli uživatele a danou činnost užitečná (např. spojit data z více tabulek, je-li to vhodné, poskytnout odkazy na související data, apod).
- Uživatelské rozhraní musí umožňovat snadno realizovat operace pro každou roli vyplývající z diagramu případů použití (use-case). Je-li cílem např. prodej zboží, musí systém implementovat odpovídající operaci, aby uživatel nemusel při každém prodeji ručně upravovat počty zboží na skladě, pamatovat si identifikátory položek a přepisovat je do objednávky a podobně.


Kromě vlastní funkcionality musí být implementovány následující funkce:

- Správa uživatelů a jejich rolí (podle povahy aplikace, např. obchodník, zákazník, administrátor). Tím se rozumí přidávání nových uživatelů u jednotlivých rolí, stejně tak možnost editace a mazání nebo deaktivace účtů. Musí být k dispozici alespoň dvě různé role uživatelů.
- Ošetření všech uživatelských vstupů tak, aby nebylo možno zadat nesmyslná nebo nekonzistentní data.
	- Povinná pole formulářů musí být odlišena od nepovinných.
	- Hodnoty ve formulářích, které nejsou pro fungování aplikace nezbytné, neoznačujte jako povinné (např. adresy, telefonní čísla apod.) Nenuťte uživatele (opravujícího) vyplňovat desítky zbytečných řádků.
	- Při odeslání formuláře s chybou by správně vyplněná pole měla zůstat zachována (uživatel by neměl být nucen vyplňovat vše znovu).
	- Pokud je vyžadován konkrétní formát vstupu (např. datum), měl by být u daného pole naznačen.
	- Pokud to v daném případě dává smysl, pole obsahující datum by měla být předvyplněna aktuálním datem.
	- Nemělo by být vyžadováno zapamatování a zadávání generovaných identifikátorů (cizích klíčů), jako např. ID položky na skladě. To je lépe nahradit výběrem ze seznamu. Výjimku tvoří případy, kdy se zadáním ID simuluje např. čtečka čipových karet v knihovně. V takovém případě prosím ušetřete opravujícímu práci nápovědou několika ID, která lze použít pro testování.
	- Žádné zadání nesmí způsobit nekonzistentní stav databáze (např. přiřazení objednávky neexistujícímu uživateli).
- Přihlašování a odhlašování uživatelů přes uživatelské jméno (případně e-mail) a heslo. Automatické odhlášení po určité době nečinnosti.

#### Implementační prostředky

- Uživatelské rozhraní
	- HTML5 + CSS, s využitím JavaScriptu, pokud je to vhodné. Je povoleno využití libovolných volně šířených JavaScriptových a CSS frameworků (jQuery, Bootstrap, atd.)
	- Případně lze využít i AJAX či pokročilejší klientské frameworky (Angular, React, apod.), není to ale vyžadováno.
	- Rozhraní musí být funkční přinejmenším v prohlížečích Chrome, Firefox a Internet Explorer.
- Implementační prostředí
	- PHP + MySQL (server eva nebo jiný dostupný), případně libovolný open source PHP framework.
	- Alternativně jiná serverová technologie (např. Java, .NET, Python, Ruby, apod.) - domluvte se na detailech (viz kontakt níže).
	- Při použití relační databáze specifikujte integritní omezení (např. unikátní hodnoty, cizí klíče, apod.) při vytváření databáze. Neponechávejte zajištění konzistence dat pouze na aplikaci. V MySQL je k tomu třeba použít tabulky typu InnoDB (typ je možno zvolit při vytváření tabulky nebo změnit dodatečně).

Použití jiné platformy je možné, ale je nutné se předem domluvit se cvičícím a samostatně si zajistit umístění vytvořené aplikace, aby bylo možno ji předvést.

#### Dokumentace

Součástí projektu je stručná dokumentace k implementaci, která popisuje, které PHP skripty (případně kontrolery, presentery apod. podle zvoleného frameworku) implementují jednotlivé případy použití. Tato dokumentace je součástí dokumentu doc.html, viz níže.

### Hodnocení 

Přehledný interface. Jen lepší by bylo objednávku přiřadit k emailu. Po registraci zákazník nemá svou osobní inf. Přidání nové položky jídla ukazuje chybu. Jako zaregistrovaný zakaznik po odhlášení se nemůžu zase přihlásit. Vzhledem k tomu, co jste implementovali a udělali velký kus práce, máte více bodů.

Získáno bodů: 27 / 30
