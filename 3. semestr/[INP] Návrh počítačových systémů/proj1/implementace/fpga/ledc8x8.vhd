------------------------------------------------------------
-- Knihovny
------------------------------------------------------------

library IEEE;
use IEEE.std_logic_1164.all;
use IEEE.std_logic_arith.all;
use IEEE.std_logic_unsigned.all;

------------------------------------------------------------
-- Entity
------------------------------------------------------------

entity ledc8x8 is
port ( 
	SMCLK: in std_logic; -- Hodinovy signal
	RESET: in std_logic; -- Asynchronni inicializace
	ROW: out std_logic_vector(0 to 7); -- Signaly vybirajici radek maticoveho displeje 
	LED: out std_logic_vector(0 to 7) -- Signaly vybirajici sloupec maticoveho displeje
);
end ledc8x8;

------------------------------------------------------------
-- Architektura
------------------------------------------------------------

architecture main of ledc8x8 is
	signal rows: std_logic_vector(7 downto 0) := "10000000";
	signal leds: std_logic_vector(7 downto 0) := (others => '0');
	signal counter: std_logic_vector(11 downto 0) := (others => '0'); -- 7372800/256/8 -> 3600 -> to binary (12 mist) (/256 - zadani, /8 protoze mam 8 radku)
	signal state_counter: std_logic_vector(20 downto 0) := (others => '0'); -- 7372800/4 -> 1843200 -> to binary (21 mist) = 4 stavy (frekvence pro jeden stav)
	signal current_state: std_logic_vector(1 downto 0) := "00";
	signal clock_enable: std_logic; -- := '0';



    -- Sem doplnte definice vnitrnich signalu.

begin

    -- Sem doplnte popis obvodu. Doporuceni: pouzivejte zakladni obvodove prvky
    -- (multiplexory, registry, dekodery,...), jejich funkce popisujte pomoci
    -- procesu VHDL a propojeni techto prvku, tj. komunikaci mezi procesy,
    -- realizujte pomoci vnitrnich signalu deklarovanych vyse.

    -- DODRZUJTE ZASADY PSANI SYNTETIZOVATELNEHO VHDL KODU OBVODOVYCH PRVKU,
    -- JEZ JSOU PROBIRANY ZEJMENA NA UVODNICH CVICENI INP A SHRNUTY NA WEBU:
    -- http://merlin.fit.vutbr.cz/FITkit/docs/navody/synth_templates.html.

    -- Nezapomente take doplnit mapovani signalu rozhrani na piny FPGA
    -- v souboru ledc8x8.ucf.

    -- Vychozi (pevna) frekvence: 7372800 Hz (pocet kmitu za sekundu)


	-- Citac stavu na snizeni frekvence (delicka) --

	counter_func: process(SMCLK, RESET)
		begin
			if RESET = '1' then
				counter <= (others => '0'); -- vynulovani -> counter = "000000000000"
			
			elsif SMCLK'event and SMCLK = '1' then
				counter <= counter + 1;
				
				if counter (11 downto 0) = "111000010000" then -- "111000010000" SMCLK/256/8 (Kolik nabeznych hran mam "ignorovat", nez prijde ta moje "spravna" -> moje vlastni nabezny hrany)
					clock_enable <= '1';
					-- counter <= (others => '0');
				else
					clock_enable <= '0';
				end if;
				
			end if;
		end process counter_func;


	-- Funkce na zmenu stavu -- 

	state_change: process(SMCLK, RESET)
		begin
			if RESET = '1' then
				state_counter <= (others => '0'); -- vynulovani
			
			elsif SMCLK'event and SMCLK = '1' then
				
				state_counter <= state_counter + 1;
				
				if state_counter = "111000010000000000000" then -- "111000010000000000000" 1843200 (7Mhz/4 = 1 stav)
					current_state <= current_state + 1;
					state_counter <= (others => '0'); -- vynulovani
				end if;
			end if;
		end process state_change;


	-- Rotacni register --

	rows_func: process(SMCLK, RESET, clock_enable)
	begin
		if RESET = '1' then
			rows <= "10000000"; -- Resetuj na prvni radek
		elsif SMCLK'event and SMCLK = '1' and clock_enable = '1' then
			rows <= rows(0) & rows(7 downto 1); -- konkatenace (posouvani 1 u nabezne hrany -> 10000000 -> 01000000 -> 00100000 -> ... )
		end if;	
	end process rows_func;



	-- Dekoder --

	display_func: process(rows, current_state)
	begin
		if current_state = "00" then -- Iniciala jmena (M)
			case rows is
				when "10000000" => leds <= "01110111";
				when "01000000" => leds <= "00100111";
				when "00100000" => leds <= "01010111";
				when "00010000" => leds <= "01110111";
				when "00001000" => leds <= "01110111";
				when "00000100" => leds <= "11111111";
				when "00000010" => leds <= "11111111";
				when "00000001" => leds <= "11111111";
				when others     => leds <= "11111111";
			end case;
		
		elsif current_state = "01" then -- Nic se nezobrazuje
			case rows is
				when "10000000" => leds <= "11111111";
				when "01000000" => leds <= "11111111";
				when "00100000" => leds <= "11111111";
				when "00010000" => leds <= "11111111";
				when "00001000" => leds <= "11111111";
				when "00000100" => leds <= "11111111";
				when "00000010" => leds <= "11111111";
				when "00000001" => leds <= "11111111";
				when others     => leds <= "11111111";
			end case;
		
		elsif current_state = "10" then -- Iniciala prijmeni (CH)
			case rows is
				when "10000000" => leds <= "10110101";
				when "01000000" => leds <= "01010101";
				when "00100000" => leds <= "01110001";
				when "00010000" => leds <= "01010101";
				when "00001000" => leds <= "10110101";
				when "00000100" => leds <= "11111111";
				when "00000010" => leds <= "11111111";
				when "00000001" => leds <= "11111111";
				when others     => leds <= "11111111";
			end case;
		
		elsif current_state = "11" then -- Nic se nezobrazuje
			case rows is
				when "10000000" => leds <= "11111111";
				when "01000000" => leds <= "11111111";
				when "00100000" => leds <= "11111111";
				when "00010000" => leds <= "11111111";
				when "00001000" => leds <= "11111111";
				when "00000100" => leds <= "11111111";
				when "00000010" => leds <= "11111111";
				when "00000001" => leds <= "11111111";
				when others     => leds <= "11111111";
			end case;
		end if;

	end process display_func;

	ROW <= rows;
	LED <= leds;
	

end main;
