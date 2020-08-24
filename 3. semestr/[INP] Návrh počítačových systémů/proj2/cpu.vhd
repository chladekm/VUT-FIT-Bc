-- cpu.vhd: Simple 8-bit CPU (BrainF*ck interpreter)
-- Copyright (C) 2018 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): Martin Chladek
--

library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet ROM
   CODE_ADDR : out std_logic_vector(11 downto 0); -- adresa do pameti
   CODE_DATA : in std_logic_vector(7 downto 0);   -- CODE_DATA <- rom[CODE_ADDR] pokud CODE_EN='1'
   CODE_EN   : out std_logic;                     -- povoleni cinnosti
   
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(9 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- mem[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_RDWR  : out std_logic;                    -- cteni z pameti (DATA_RDWR='1') / zapis do pameti (DATA_RDWR='0')
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA obsahuje stisknuty znak klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna pokud IN_VLD='1'
   IN_REQ    : out std_logic;                     -- pozadavek na vstup dat z klavesnice
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- pokud OUT_BUSY='1', LCD je zaneprazdnen, nelze zapisovat,  OUT_WE musi byt '0'
   OUT_WE   : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is

   -- Potřebné čítače
      signal PC_mem: std_logic_vector(11 downto 0); -- Ukazatel do paměti programu. (4kB)
      signal PTR_data: std_logic_vector(9 downto 0); -- Ukazatel do paměti dat. (kruhový buffer - max 1023 - 9 bitů, potom přetečení)
      signal CNT_while: std_logic_vector(7 downto 0); -- Určení začátku a konce příkazu while.
   
   -- Inkrementace a dekrementace čítačů
      signal PC_mem_increase: std_logic;
      signal PC_mem_decrease: std_logic;
      signal PTR_data_increase: std_logic;
      signal PTR_data_decrease: std_logic;
      signal CNT_while_increase: std_logic;
      signal CNT_while_decrease: std_logic;

   -- Nulování čítačů
      signal PC_mem_null: std_logic;
      signal PTR_data_null: std_logic;
      signal CNT_while_null: std_logic;

   -- Multiplexor 4-1
      signal multiplexor_select: std_logic_vector(1 downto 0);

   -- Pomocný signál pro zápis čísel a znaků do paměti 
      signal write_number_letter: std_logic_vector(7 downto 0);

   -- Konečný automat, který ovládá řídící signály (FSM).
   -- Definice jednotlivých stavů konečného automatu
   type fsm_state is (
      st_entry, -- výchozí stav
      st_ins_fetch, -- načtení instrukce
      st_ins_decode, -- dekódování instrukce
      st_ptr_increase, -- > -- inkrementace hodnoty ukazatele
      st_ptr_decrease, -- < -- dekrementace hodnoty ukazatele
      st_cell_increase_0, st_cell_increase_1, -- + -- inkrementace hodnoty aktuální buňky 
      st_cell_decrease_0, st_cell_decrease_1, -- - -- dekrementace hodnoty aktuální buňky
      st_l_brac_while_0, st_l_brac_while_1, st_l_brac_while_2, st_l_brac_while_3, -- [ -- začátek cyklu while
      st_r_brac_while_0, st_r_brac_while_1, st_r_brac_while_2, st_r_brac_while_3, st_r_brac_while_4, -- ] -- konec cyklu while
      st_putchar_0, st_putchar_1, -- . -- vytiskni hodnotu aktuální buňky
      st_getchar_0, st_getchar_1, -- , -- načti hodnotu a ulož ji do aktuální buňky
      st_comment_start, st_comment_do, st_comment_end, -- # -- blokový komentář
      st_number, -- 0-9 -- / přepis hodnoty aktuální buňky na hexadecimální hodnotou  0xV0, 
      st_letter, -- A-F -- / kde hodnota horních 4 bitů (V) odpovídá znaku příkazu, tj. 0-9 nebo A-F 
      st_null, -- null -- zastav vykonávání programu
      st_others -- ostatní znaky budou ignorovány
   );

   signal fsm_prev_state: fsm_state := st_entry;
   signal fsm_next_state: fsm_state; 


begin


   -- Ukazatel do paměti programu.
   PC_counter: process(RESET, CLK, PC_mem, PC_mem_increase, PC_mem_decrease)
      begin
         if(RESET = '1') then
            PC_mem <= (others=>'0');
         elsif (CLK'event) and (CLK = '1') then
            if(PC_mem_increase = '1') then
               PC_mem <= PC_mem + 1;
            elsif(PC_mem_decrease = '1') then
               PC_mem <= PC_mem - 1;
            elsif(PC_mem_null = '1') then
               PC_mem <= (others=>'0');
            end if;
         end if;
      end process;
		 
	CODE_ADDR <= PC_mem;


   -- Ukazatel do paměti dat.
   PTR_counter: process(RESET, CLK, PTR_data, PTR_data_increase, PTR_data_decrease)
      begin
         if(RESET = '1') then
            PTR_data <= (others=>'0');
         elsif (CLK'event) and (CLK = '1') then
            if(PTR_data_increase = '1') then
               PTR_data <= PTR_data + 1;
            elsif(PTR_data_decrease = '1') then
               PTR_data <= PTR_data - 1;
            elsif(PTR_data_null = '1') then
               PTR_data <= (others=>'0');
            end if;
         end if;
      end process;

	DATA_ADDR <= PTR_data;

   -- Určení začátku a konce příkazu while, počítaní závorek.
   CNT_counter: process(RESET, CLK, CNT_while_increase, CNT_while_decrease)
      begin
         if(RESET = '1') then
            CNT_while <= (others=>'0');
         elsif (CLK'event) and (CLK = '1') then
            if(CNT_while_increase = '1') then
               CNT_while <= CNT_while + 1;
            elsif(CNT_while_decrease = '1') then
               CNT_while <= CNT_while - 1;
            elsif(CNT_while_null = '1') then
               CNT_while <= (others=>'0');
            end if;
         end if;
      end process;
            
   -- Multiplexor 4-1
   multiplexor: process(IN_DATA, DATA_RDATA, multiplexor_select)
      begin
         case multiplexor_select is
            when "00" => DATA_WDATA <= IN_DATA;
            when "01" => DATA_WDATA <= DATA_RDATA + 1;
            when "10" => DATA_WDATA <= DATA_RDATA - 1;
            when "11" => DATA_WDATA <= write_number_letter;
            when others =>
                  null;
         end case;
      end process;


   -- ------------------------------------------------------------ --   
   --             Implementace konečného automatu (FSM)            --
   -- ------------------------------------------------------------ --

   -- Aktuální stav

   fsm_prev_state_process: process(CLK, RESET, EN)
      begin
         if RESET = '1' then
            fsm_prev_state <= st_entry;
         elsif (CLK'event) and (CLK = '1') then
            if EN = '1' then
               fsm_prev_state <= fsm_next_state;
            end if;
         end if;
      end process;

   -- Logika následujícího stavu && Výstupní logika

   fsm_next_state_process: process(CODE_DATA, IN_VLD, OUT_BUSY, DATA_RDATA, CNT_while, fsm_prev_state)
      begin

         -- Počáteční inicializace --
         OUT_WE <= '0';
         IN_REQ <= '0';
         CODE_EN <= '0'; 
         DATA_EN <= '0'; 
         DATA_RDWR <= '0';

         PC_mem_increase <= '0';
         PC_mem_decrease <= '0';
         PTR_data_increase <= '0';
         PTR_data_decrease <= '0';
         CNT_while_increase <= '0';
         CNT_while_decrease <= '0';
         
         PC_mem_null <= '0';
         PTR_data_null <= '0';
         CNT_while_null <= '0'; 

         multiplexor_select <= "00";

         case fsm_prev_state is
            
            -- Výchozí stav --
            when st_entry =>

               PC_mem_null <= '1';
               PTR_data_null <= '1';
               CNT_while_null <= '1'; 

               fsm_next_state <= st_ins_fetch;

            -- Načtení instrukce --
            when st_ins_fetch =>
               CODE_EN <= '1';

               fsm_next_state <= st_ins_decode;

            -- Dekódování instrukce -- 
            when st_ins_decode =>

               case CODE_DATA is 

               	  ---------------- Instrukce ----------------
                  when X"3E" => fsm_next_state <= st_ptr_increase;
                  when X"3C" => fsm_next_state <= st_ptr_decrease;
                  when X"2B" => fsm_next_state <= st_cell_increase_0;
                  when X"2D" => fsm_next_state <= st_cell_decrease_0;
                  when X"5B" => fsm_next_state <= st_l_brac_while_0;
                  when X"5D" => fsm_next_state <= st_r_brac_while_0;
                  when X"2E" => fsm_next_state <= st_putchar_0;
                  when X"2C" => fsm_next_state <= st_getchar_0;
                  when X"23" => fsm_next_state <= st_comment_start;

                  ---------------- Čísla 0-9 ----------------
                  when X"30" => fsm_next_state <= st_number;
                  when X"31" => fsm_next_state <= st_number;
                  when X"32" => fsm_next_state <= st_number;
                  when X"33" => fsm_next_state <= st_number;
                  when X"34" => fsm_next_state <= st_number;
                  when X"35" => fsm_next_state <= st_number;
                  when X"36" => fsm_next_state <= st_number;
                  when X"37" => fsm_next_state <= st_number;
                  when X"38" => fsm_next_state <= st_number;
                  when X"39" => fsm_next_state <= st_number;

                  ---------------- Znaky A-F ----------------
                  when X"41" => fsm_next_state <= st_letter;
                  when X"42" => fsm_next_state <= st_letter;
                  when X"43" => fsm_next_state <= st_letter;
                  when X"44" => fsm_next_state <= st_letter;
                  when X"45" => fsm_next_state <= st_letter;
                  when X"46" => fsm_next_state <= st_letter;

                  ---------------- Ukončení -----------------
                  when X"00" => fsm_next_state <= st_null;

                  when others => fsm_next_state <= st_others;

               end case;

            -- > -- Inkrementace hodnoty ukazatele v paměti dat (RAM)
            when st_ptr_increase =>

               PC_mem_increase <= '1';
               PTR_data_increase <= '1';

               fsm_next_state <= st_ins_fetch;

            -- < -- Dekrementace hodnoty ukazatele v paměti dat (RAM)
            when st_ptr_decrease =>

               PC_mem_increase <= '1';
               PTR_data_decrease <= '1';

               fsm_next_state <= st_ins_fetch;

            -- + -- Inkrementace hodnoty aktuální buňky v paměti dat (RAM)
            when st_cell_increase_0 =>

               DATA_EN <= '1';
               DATA_RDWR <= '1';

               fsm_next_state <= st_cell_increase_1;

            when st_cell_increase_1 =>

               DATA_EN <= '1';
               DATA_RDWR <= '0';
               PC_mem_increase <= '1';
			   multiplexor_select <= "01";

               fsm_next_state <= st_ins_fetch;

            -- - -- Dekrementace hodnoty aktuální buňky v paměti dat (RAM)
            when st_cell_decrease_0 =>

               DATA_EN <= '1';
               DATA_RDWR <= '1';

               fsm_next_state <= st_cell_decrease_1;

            when st_cell_decrease_1 =>

               DATA_EN <= '1';
               DATA_RDWR <= '0';
               PC_mem_increase <= '1';
               multiplexor_select <= "10";

               fsm_next_state <= st_ins_fetch;

            -------------------------- WHILE CYKLUS --------------------------

            -- [ -- Začátek cyklu while
            when st_l_brac_while_0 =>

               PC_mem_increase <= '1';
               DATA_EN <= '1';
               DATA_RDWR <= '1';

               fsm_next_state <= st_l_brac_while_1;

            when st_l_brac_while_1 =>

               if (DATA_RDATA = "00000000") then
                  CNT_while_increase <= '1';
                  fsm_next_state <= st_l_brac_while_2;
               else
                  fsm_next_state <= st_ins_fetch;
               end if;

            when st_l_brac_while_2 =>

               if (CNT_while /= "00000000") then
                  CODE_EN <= '1';
                  fsm_next_state <= st_l_brac_while_3;
               else
                  fsm_next_state <= st_ins_fetch;
               end if;

            when st_l_brac_while_3 =>

               if (CODE_DATA = X"5D") then
                  CNT_while_decrease <= '1';
               elsif (CODE_DATA = X"5B") then
                  CNT_while_increase <= '1';
               end if;
               PC_mem_increase <= '1';

               fsm_next_state <= st_l_brac_while_2;

            -- ] -- Konec cyklu while

            when st_r_brac_while_0 =>

               DATA_EN <= '1';
               DATA_RDWR <= '1';

               fsm_next_state <= st_r_brac_while_1;

            when st_r_brac_while_1 =>

               if (DATA_RDATA = "00000000") then
                  PC_mem_increase <= '1';
                  fsm_next_state <= st_ins_fetch;
               else
                  CNT_while_increase <= '1';
                  PC_mem_decrease <= '1';
                  fsm_next_state <= st_r_brac_while_2;
               end if;

            when st_r_brac_while_2 =>

               if (CNT_while /= "00000000") then
                  CODE_EN <= '1';
                  fsm_next_state <= st_r_brac_while_3;
               else
                  fsm_next_state <= st_ins_fetch;
               end if;

            when st_r_brac_while_3 =>

               if (CODE_DATA = X"5D") then
                  CNT_while_increase <= '1';
               elsif (CODE_DATA = X"5B") then
                  CNT_while_decrease <= '1';
               end if;

               fsm_next_state <= st_r_brac_while_4;

            when st_r_brac_while_4 =>

               if (CNT_while = "00000000") then
                  PC_mem_increase <= '1';
               else
                  PC_mem_decrease <= '1';
               end if;
   
               fsm_next_state <= st_r_brac_while_2;

            -------------------------- KONEC WHILE --------------------------

            -- . -- Vytiskni hodnotu aktuální buňky z paměti dat (RAM)
            when st_putchar_0 =>

               if (OUT_BUSY = '1') then
                  fsm_next_state <= st_putchar_0;
               else
                  DATA_EN <= '1';
                  DATA_RDWR <= '1';
                  
                  fsm_next_state <= st_putchar_1; 
               end if;

            when st_putchar_1 => 

               OUT_WE <= '1';
               OUT_DATA <= DATA_RDATA;
               PC_mem_increase <= '1'; 

               fsm_next_state <= st_ins_fetch;

            -- , -- Načti hodnotu a ulož ji do aktuální buňky v paměti dat (RAM)
            when st_getchar_0 => 

               IN_REQ <= '1';
               if (IN_VLD /= '1') then
                  fsm_next_state <= st_getchar_0;
               else
                  fsm_next_state <= st_getchar_1;
               end if;

            when st_getchar_1 =>

               multiplexor_select <= "00";
               DATA_EN <= '1';
               DATA_RDWR <= '0';
               PC_mem_increase <= '1';

               fsm_next_state <= st_ins_fetch;

            -- # -- Blokový komentář
            when st_comment_start =>

               PC_mem_increase <= '1';

               fsm_next_state <= st_comment_do;

            when st_comment_do =>

               CODE_EN <= '1';

            	fsm_next_state <= st_comment_end;

            when st_comment_end =>

               CODE_EN <= '1';

               if (CODE_DATA /= X"23") then
                  PC_mem_increase <= '1';
                  fsm_next_state <= st_comment_end;
               else
                  fsm_next_state <= st_ins_fetch;
               end if;

            -- Načteno číslo 0-9
            when st_number =>

               DATA_EN <= '1';
               write_number_letter <= (CODE_DATA(3 downto 0) & "0000");
               PC_mem_increase <= '1';
               multiplexor_select <= "11";

               fsm_next_state <= st_ins_fetch;

            -- Načten znak A-F
            when st_letter =>

               DATA_EN <= '1';
               write_number_letter <= (CODE_DATA(3 downto 0) + "1001") & "0000";
               PC_mem_increase <= '1';
               multiplexor_select <= "11";

               fsm_next_state <= st_ins_fetch;

            -- null -- Zastav vykonávání programu
            when st_null =>

               fsm_next_state <= st_null;

            -- Ostatní znaky budou ignorovány
            when st_others => 

               PC_mem_increase <= '1';

               fsm_next_state <= st_ins_fetch;

            -- Nedefinovaný stav konečného automatu (tento případ by neměl nikdy nastat)
            when others =>
               null;

         end case;

      end process;


end behavioral;
 
