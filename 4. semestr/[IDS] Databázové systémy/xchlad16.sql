--------------- IDS Projekt - VUT FIT 2018/19 ---------------
-- Autor:
--      Martin Chladek (xchlad16)
-------------------------------------------------------------

--------------- ZRUSENI PREDCHOZICH TABULEK ---------------

DROP TABLE Osoba CASCADE CONSTRAINTS;
DROP TABLE Veterinar CASCADE CONSTRAINTS;
DROP TABLE Sestra CASCADE CONSTRAINTS;
DROP TABLE Majitel CASCADE CONSTRAINTS;
DROP TABLE Diagnoza CASCADE CONSTRAINTS;
DROP TABLE Lecba CASCADE CONSTRAINTS;
DROP TABLE Zvire CASCADE CONSTRAINTS;
DROP TABLE Druh CASCADE CONSTRAINTS;
DROP TABLE Lek CASCADE CONSTRAINTS;
DROP TABLE Davkovani CASCADE CONSTRAINTS;
DROP TABLE Osoba_Poda_Lek CASCADE CONSTRAINTS;
DROP TABLE Veterinar_Provadi_Lecbu CASCADE CONSTRAINTS;
DROP TABLE Sestra_Provadi_Lecbu CASCADE CONSTRAINTS;
DROP TABLE Lecba_Vyzaduje_Lek CASCADE CONSTRAINTS;

DROP SEQUENCE Osoba_ID_sequence;

-------------------------------------------------------------
--                      1. ULOHA                           --
--                vytvoreni ER diagramu                    --
-------------------------------------------------------------


-------------------------------------------------------------
--                      2. ULOHA                           --
--               navrhnuti schema databaze                 --
-------------------------------------------------------------

--------------- VYTVORENI TABULEK ---------------

---- VYTVORENI TABULKY OSOBA ----
CREATE TABLE Osoba (
	id_osoby    NUMBER,
	titul       VARCHAR(20),
	jmeno       VARCHAR(20) NOT NULL,
	prijmeni    VARCHAR(20)NOT NULL,
	ulice       VARCHAR(20),
	psc         CHAR(6),
	mesto       VARCHAR(20)
);
	ALTER TABLE Osoba ADD CONSTRAINT PK_osoba PRIMARY KEY (id_osoby);


---- VYTVORENI TABULKY VETERINAR ----
CREATE TABLE Veterinar (
	id_osoby        NUMBER NOT NULL,
	id_veterinar    NUMBER NOT NULL,
	cislo_uctu      VARCHAR(24), 
	hodinova_mzda   INTEGER,

	CONSTRAINT check_veterinar_cislo_uctu CHECK(REGEXP_LIKE(cislo_uctu, '^(([0-9]{6}[-])?[0-9]{6,10}/[0-9]{4})|(CZ[0-9]{22})$'))
);
	ALTER TABLE Veterinar ADD CONSTRAINT PK_veterinar PRIMARY KEY (id_veterinar);
	ALTER TABLE Veterinar ADD CONSTRAINT FK_veterinar_osoba FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;


---- VYTVORENI TABULKY SESTRA ----
CREATE TABLE Sestra (
	id_osoby        NUMBER NOT NULL,
	id_sestra       NUMBER NOT NULL,
	cislo_uctu      VARCHAR(24),
	hodinova_mzda   INTEGER,

	CONSTRAINT check_sestra_cislo_uctu CHECK(REGEXP_LIKE(cislo_uctu, '^(([0-9]{6}[-])?[0-9]{6,10}/[0-9]{4})|(CZ[0-9]{22})$'))
);
	ALTER TABLE Sestra ADD CONSTRAINT PK_sestra PRIMARY KEY (id_sestra);
	ALTER TABLE Sestra ADD CONSTRAINT FK_sestra FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;


---- VYTVORENI TABULKY MAJITEL ----
CREATE TABLE Majitel (
	id_osoby    NUMBER NOT NULL,
	id_majitel  NUMBER NOT NULL
);
	ALTER TABLE Majitel ADD CONSTRAINT PK_majitel PRIMARY KEY (id_majitel);
	ALTER TABLE Majitel ADD CONSTRAINT FK_majitel_zvirete FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;


---- VYTVORENI TABULKY DRUH ----
CREATE TABLE Druh (
	id_druh     NUMBER NOT NULL,
	nazev       VARCHAR(30)
);
	ALTER TABLE Druh ADD CONSTRAINT PK_druh PRIMARY KEY (id_druh);


---- VYTVORENI TABULKY ZVIRE ----
CREATE TABLE Zvire (
	id_zvire        NUMBER NOT NULL,
	id_druh         NUMBER NOT NULL,
	id_majitel      NUMBER NOT NULL,
	jmeno           VARCHAR(20),
	datum_narozeni  DATE,
	posledni_prohlidka DATE
);
	ALTER TABLE Zvire ADD CONSTRAINT PK_zvire PRIMARY KEY (id_zvire);
	ALTER TABLE Zvire ADD CONSTRAINT FK_majitel FOREIGN KEY (id_majitel) REFERENCES Majitel ON DELETE CASCADE;
	ALTER TABLE Zvire ADD CONSTRAINT FK_druh_zvirete FOREIGN KEY (id_druh) REFERENCES Druh ON DELETE CASCADE;


---- VYTVORENI TABULKY LECBA ----
CREATE TABLE Lecba (
	id_lecba    NUMBER NOT NULL,
	id_zvire    NUMBER NOT NULL,
	datum       DATE NOT NULL,
	stav        VARCHAR(20),
	cena        INTEGER,
	id_osoby    NUMBER NOT NULL,
	
	CONSTRAINT check_cena CHECK (cena>=0)
);
	ALTER TABLE Lecba ADD CONSTRAINT PK_lecba PRIMARY KEY (id_lecba);
	ALTER TABLE Lecba ADD CONSTRAINT FK_zvire FOREIGN KEY (id_zvire) REFERENCES Zvire ON DELETE CASCADE;
	ALTER TABLE Lecba ADD CONSTRAINT FK_kdo_vypsal FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;


---- VYTVORENI TABULKY DIAGNOZA ----
CREATE TABLE Diagnoza (
	id_diagnoza NUMBER NOT NULL,
	id_osoby    NUMBER NOT NULL,
	id_lecba    NUMBER NOT NULL,
	nemoc       VARCHAR(20) NOT NULL
);
	ALTER TABLE Diagnoza ADD CONSTRAINT PK_diagnoza PRIMARY KEY (id_diagnoza);
	ALTER TABLE Diagnoza ADD CONSTRAINT FK_osoba FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;
	ALTER TABLE Diagnoza ADD CONSTRAINT FK_lecba FOREIGN KEY (id_lecba) REFERENCES Lecba ON DELETE CASCADE;


---- VYTVORENI TABULKY LEK ----
CREATE TABLE Lek (
	id_lek          NUMBER NOT NULL,
	typ_leku        VARCHAR(20),
	ucinna_latka    VARCHAR(20),
	kontraindikace  VARCHAR(20),
	id_osoby        NUMBER NOT NULL
);
	ALTER TABLE Lek ADD CONSTRAINT PK_lek PRIMARY KEY (id_lek);


---- VYTVORENI TABULKY DAVKOVANI ----
CREATE TABLE Davkovani (
	id_davkovani    NUMBER NOT NULL,
	id_lek          NUMBER NOT NULL,
	id_druh         NUMBER NOT NULL,
	mnozstvi_leku   VARCHAR(20),
	interval_podavani VARCHAR(20),
	doba_podavani   VARCHAR(20)
);
	ALTER TABLE Davkovani ADD CONSTRAINT PK_davkovani PRIMARY KEY (id_davkovani);
	ALTER TABLE Davkovani ADD CONSTRAINT FK_lek FOREIGN KEY (id_lek) REFERENCES Lek ON DELETE CASCADE;
	ALTER TABLE Davkovani ADD CONSTRAINT FK_druh FOREIGN KEY (id_druh) REFERENCES Druh ON DELETE CASCADE;

--------------- TABULKY PRO VZTAHY N:N ---------------

CREATE TABLE Osoba_Poda_Lek (
	id_osoby    NUMBER NOT NULL,
	id_lek      NUMBER NOT NULL
);

CREATE TABLE Veterinar_Provadi_Lecbu (
	id_veterinar    NUMBER NOT NULL,
	id_lecba        NUMBER NOT NULL
);

CREATE TABLE Sestra_Provadi_Lecbu (
	id_sestra   NUMBER NOT NULL,
	id_lecba    NUMBER NOT NULL
);

CREATE TABLE Lecba_Vyzaduje_Lek (
	id_lecba    NUMBER NOT NULL,
	id_lek      NUMBER NOT NULL
);

--------------- PK A FK PRO TABULKY REPREZENTUJICI VZTAHY N:N ---------------

ALTER TABLE Osoba_Poda_Lek ADD CONSTRAINT PK_osoba_poda_lek PRIMARY KEY (id_osoby, id_lek);
ALTER TABLE Veterinar_Provadi_Lecbu ADD CONSTRAINT PK_veterinar_provadi_lecbu PRIMARY KEY (id_veterinar, id_lecba);
ALTER TABLE Sestra_Provadi_Lecbu ADD CONSTRAINT PK_sestra_provadi_lecbu PRIMARY KEY (id_sestra, id_lecba);
ALTER TABLE Lecba_Vyzaduje_Lek ADD CONSTRAINT PK_lecba_vyzaduje_lek PRIMARY KEY (id_lecba, id_lek);

ALTER TABLE Osoba_Poda_Lek ADD CONSTRAINT FK_osoba_poda_lek FOREIGN KEY (id_osoby) REFERENCES Osoba ON DELETE CASCADE;
ALTER TABLE Osoba_Poda_Lek ADD CONSTRAINT FK_podany_lek FOREIGN KEY (id_lek) REFERENCES Lek ON DELETE CASCADE;

ALTER TABLE Veterinar_Provadi_Lecbu ADD CONSTRAINT FK_veterinar_provadi_lecbu FOREIGN KEY (id_veterinar) REFERENCES Veterinar ON DELETE CASCADE;
ALTER TABLE Veterinar_Provadi_Lecbu ADD CONSTRAINT FK_veterinar_lecba FOREIGN KEY (id_lecba) REFERENCES Lecba ON DELETE CASCADE;

ALTER TABLE Sestra_Provadi_Lecbu ADD CONSTRAINT FK_sestra_provadi_lecbu FOREIGN KEY (id_sestra) REFERENCES Sestra ON DELETE CASCADE;
ALTER TABLE Sestra_Provadi_Lecbu ADD CONSTRAINT FK_sestra_lecba FOREIGN KEY (id_lecba) REFERENCES Lecba ON DELETE CASCADE;

ALTER TABLE Lecba_Vyzaduje_Lek ADD CONSTRAINT FK_lecba_vyzaduje_lek FOREIGN KEY (id_lecba) REFERENCES Lecba ON DELETE CASCADE;
ALTER TABLE Lecba_Vyzaduje_Lek ADD CONSTRAINT FK_vyzadovany_lek FOREIGN KEY (id_lek) REFERENCES Lek ON DELETE CASCADE;


-------------------------------------------------------------
--                      4. ULOHA                           --
--               trigery, procedury, ...                   --
-------------------------------------------------------------

-- TRIGGER c.1: U osob, kde neni primarni klic uveden vlozi ID
CREATE SEQUENCE Osoba_ID_sequence
  START WITH 10000
  INCREMENT BY 1;

CREATE OR REPLACE TRIGGER Osoba_generuj_PK 
	BEFORE INSERT OR UPDATE
	ON Osoba 
	FOR EACH ROW
	BEGIN
		IF :NEW.id_osoby IS NULL
		THEN
		 :NEW.id_osoby := Osoba_ID_sequence.nextval;
		END IF;
	END;
/

-- TRIGGER c.2: V pripade, ze (studujici) sestra dovrsi vzdelani a dostane titul, bude ji zvednut plat
-- V pripade Bakalarskeho diplomu -> navyseni o 15%
-- V pripade Magisterskeho diplomu -> navyseni o dalsich 10%
CREATE OR REPLACE TRIGGER Sestra_zvedni_plat
	AFTER UPDATE ON Osoba
	FOR EACH ROW
	DECLARE
		procento_mzdy NUMBER;
		aktualni_mzda NUMBER;
		nova_mzda NUMBER;
	
	BEGIN
		procento_mzdy := 0;
		nova_mzda := 0;

		SELECT TO_NUMBER(hodinova_mzda) INTO aktualni_mzda FROM Sestra WHERE id_osoby = :NEW.id_osoby;
		
		IF ((:OLD.titul IS NULL) AND (:NEW.titul = 'Bc'))
		THEN 
			procento_mzdy := (aktualni_mzda * 15) / 100;
		ELSIF ((:OLD.titul = 'Bc') AND ((:NEW.titul = 'Mgr') OR (:NEW.titul = 'Ing')))
		THEN 
			procento_mzdy := (aktualni_mzda * 10) / 100;
		END IF;

		nova_mzda := aktualni_mzda + procento_mzdy;
		nova_mzda := ROUND(nova_mzda, 0);
		UPDATE Sestra SET hodinova_mzda = CAST(nova_mzda AS CHAR(3)) WHERE id_osoby = :NEW.id_osoby;
	END;
/


-- PROCEDURA c.1:
-- Vypocita kolik procent vsech leceb lecil dany veterinar nebo sestra s id_osoby
-- Pokud v databazi nejsou ulozene zadne lecby, vyvola se vyjimka
-- Použitelnost v aplikaci: Například při vypisování odměn zaměstancům
CREATE OR REPLACE PROCEDURE vypocitej_procento_leceb(arg_id_osoby IN NUMBER) AS
	BEGIN
		DECLARE 
			CURSOR cursor_lecby IS SELECT Lecba.id_lecba, Lecba.id_osoby FROM Lecba;
			osobaID Lecba.id_osoby%TYPE;
			lecbaID Lecba.id_lecba%TYPE;
			os_jmeno Osoba.jmeno%TYPE;
			os_prijmeni Osoba.prijmeni%TYPE;
			je_osoba_veterinar NUMBER;
			je_osoba_sestra NUMBER;
			osoba_leceb NUMBER;
			celkem_leceb NUMBER;
			procenta NUMBER;
		
		BEGIN
			osoba_leceb := 0;
			celkem_leceb := 0;
			procenta := 0;
			
			SELECT jmeno INTO os_jmeno FROM Osoba WHERE id_osoby = arg_id_osoby;
			SELECT prijmeni INTO os_prijmeni FROM Osoba WHERE id_osoby = arg_id_osoby; 
			SELECT COUNT(id_veterinar) INTO je_osoba_veterinar FROM Veterinar WHERE id_osoby = arg_id_osoby; 
			SELECT COUNT(id_sestra) INTO je_osoba_sestra FROM Sestra WHERE id_osoby = arg_id_osoby; 
			
			IF (je_osoba_veterinar = 0) AND (je_osoba_sestra = 0)
			THEN
				RAISE NO_DATA_FOUND;
			END IF;

			OPEN cursor_lecby;
			
			LOOP 
				FETCH cursor_lecby INTO lecbaID, osobaID;
				EXIT WHEN cursor_lecby%NOTFOUND;
				IF osobaID = arg_id_osoby
				THEN 
					osoba_leceb := osoba_leceb + 1;
				END IF;
				celkem_leceb := celkem_leceb + 1;
			END LOOP;
			
			CLOSE cursor_lecby;

			procenta := osoba_leceb / celkem_leceb;
			procenta := procenta * 100;
			procenta := ROUND(procenta, 2);
			
			IF je_osoba_veterinar <> 0
			THEN
				DBMS_OUTPUT.put_line('Veterinar ' || os_jmeno || ' ' || os_prijmeni || ' provedl ' || procenta || '% z celkoveho poctu leceb.');
			ELSE
				DBMS_OUTPUT.put_line('Sestra ' || os_jmeno || ' ' || os_prijmeni || ' provedla ' || procenta || '% z celkoveho poctu leceb.');
			END IF;
			
			EXCEPTION 
				WHEN ZERO_DIVIDE THEN 
					BEGIN
						DBMS_OUTPUT.put_line('Zadne zaznamy o lecbach v databazi.');
					END;
				WHEN NO_DATA_FOUND THEN
					BEGIN
						DBMS_OUTPUT.put_line('Chyba ve vstupnim parametru. Nebylo zadano ID ani veterinare ani sestry.');
					END;
				WHEN OTHERS THEN
					BEGIN
						DBMS_OUTPUT.put_line('Chyba v procedure.');
					END;
		END;
	END;
/

-- PROCEDURA c.2:
-- Spocitani mesicniho platu pri odpracovani standardnich 8 hodin denne
-- Na vstupu ocekava id_osoby a pocet dni v mesici (Z toho budou odecteny vikendy)
CREATE OR REPLACE PROCEDURE vypocitej_mesicni_vyplatu(arg_id_osoby IN NUMBER, arg_pocet_dni IN NUMBER) AS
	BEGIN
		DECLARE 
			mesicni_vyplata NUMBER;
			hodinova_castka NUMBER;
			je_osoba_veterinar NUMBER;
			je_osoba_sestra NUMBER;
			pocet_tydnu NUMBER;
			pocet_prac_dni NUMBER;
		BEGIN
			mesicni_vyplata := 0;
			je_osoba_veterinar := 0;
			pocet_tydnu := 0;
			pocet_prac_dni := 0;

			SELECT COUNT(id_veterinar) INTO je_osoba_veterinar FROM Veterinar WHERE id_osoby = arg_id_osoby;
			SELECT COUNT(id_sestra) INTO je_osoba_sestra FROM Sestra WHERE id_osoby = arg_id_osoby; 

			IF (je_osoba_veterinar = 0) AND (je_osoba_sestra = 0)
			THEN
				RAISE NO_DATA_FOUND;
			END IF;

			IF je_osoba_veterinar <> 0
			THEN
				SELECT hodinova_mzda INTO hodinova_castka FROM Veterinar WHERE id_osoby = arg_id_osoby;
			ELSE	
				SELECT hodinova_mzda INTO hodinova_castka FROM Sestra WHERE id_osoby = arg_id_osoby;
			END IF;

			pocet_tydnu := ROUND((arg_pocet_dni / 7), 0);

			-- Odecteni vikendu ze zadanych dni v mesici
			pocet_prac_dni := arg_pocet_dni - (pocet_tydnu * 2);

			mesicni_vyplata := (hodinova_castka * 8) * pocet_prac_dni;

			DBMS_OUTPUT.put_line('Mesicni vyplata pro tuto osobu je: ' || mesicni_vyplata || 'Kc.');

			EXCEPTION 
				WHEN NO_DATA_FOUND THEN
					BEGIN
						DBMS_OUTPUT.put_line('Nebyla nalezena data.');
					END;
				WHEN OTHERS THEN
					BEGIN
						DBMS_OUTPUT.put_line('Neznama chyba v procedure.');
					END;
		END;
	END;
/

-- PROCEDURA c.3:
-- Vypocita zastoupeni zvirat v jednotlivych skupinkach podle veku (0-2 roky, 2-4, 4-6, 6-8, 8-10, 10 a vic)
CREATE OR REPLACE PROCEDURE vypocitej_vekove_zastoupeni_zvirat AS
	BEGIN
		DECLARE 
			CURSOR cursor_zvirat IS SELECT id_zvire, datum_narozeni FROM Zvire WHERE Zvire.datum_narozeni IS NOT NULL;
			
			zvireID Zvire.id_zvire%TYPE;
			datum_nar Zvire.datum_narozeni%TYPE;

			vek_0_az_2 NUMBER;
			vek_2_az_4 NUMBER;
			vek_4_az_6 NUMBER;
			vek_6_az_8 NUMBER;
			vek_8_az_10 NUMBER;
			vek_10_a_vic NUMBER;
			celkovy_pocet NUMBER;
			vek_zvirete NUMBER;
			
		BEGIN
			vek_0_az_2 := 0;
			vek_2_az_4 := 0;
			vek_4_az_6 := 0;
			vek_6_az_8 := 0;
			vek_8_az_10 := 0;
			vek_10_a_vic := 0;
			celkovy_pocet := 0;
			vek_zvirete := 0;
			
			OPEN cursor_zvirat;

			LOOP 
				FETCH cursor_zvirat INTO zvireID, datum_nar;
				EXIT WHEN cursor_zvirat%NOTFOUND;
				
				vek_zvirete := FLOOR(MONTHS_BETWEEN(SYSDATE, datum_nar) / 12);

				IF vek_zvirete BETWEEN 0 AND 2 THEN vek_0_az_2 := vek_0_az_2 + 1;
				ELSIF vek_zvirete BETWEEN 2 AND 4 THEN vek_2_az_4 := vek_2_az_4 + 1;
				ELSIF vek_zvirete BETWEEN 4 AND 7 THEN vek_4_az_6 := vek_4_az_6 + 1;
				ELSIF vek_zvirete BETWEEN 6 AND 8 THEN vek_6_az_8 := vek_6_az_8 + 1;
				ELSIF vek_zvirete BETWEEN 8 AND 10 THEN vek_8_az_10 := vek_8_az_10 + 1;
				ELSIF vek_zvirete >= 10 THEN vek_10_a_vic := vek_10_a_vic + 1; 
				END IF;

				celkovy_pocet := celkovy_pocet + 1;

			END LOOP;
			
			CLOSE cursor_zvirat;

			IF celkovy_pocet = 0 
			THEN
				RAISE NO_DATA_FOUND;
			END IF;
			
			DBMS_OUTPUT.put_line('0 az 2 roky:   ' || vek_0_az_2);
			DBMS_OUTPUT.put_line('2 az 4 roky:   ' || vek_2_az_4);
			DBMS_OUTPUT.put_line('4 az 6 let:    ' || vek_4_az_6);
			DBMS_OUTPUT.put_line('6 az 8 let:    ' || vek_6_az_8);
			DBMS_OUTPUT.put_line('8 az 10 let:   ' || vek_8_az_10);
			DBMS_OUTPUT.put_line('10 a vice let: ' || vek_10_a_vic);

			EXCEPTION 
				WHEN NO_DATA_FOUND THEN 
					BEGIN
						DBMS_OUTPUT.put_line('Zadne zaznamy o zviratech s datem narozeni nebyly v databazi nalezeny.');
					END;
				WHEN OTHERS THEN
					BEGIN
						DBMS_OUTPUT.put_line('Neznama chyba v procedure.');
					END;
		END;
	END;
/



--------------------------------------------------------------

--------------- VLOZENI DAT ---------------

---- VLOZENI DAT OSOB ----
INSERT INTO osoba(/*id_osoby,*/ titul, jmeno, prijmeni, ulice, psc, mesto) VALUES(/*'9704101307',*/ 'MVDr', 'Jan', 'Novak', 'Ceska', '60200', 'Brno');
INSERT INTO osoba(id_osoby, titul, jmeno, prijmeni, ulice, psc, mesto) VALUES('9804101307', 'MVDr', 'Karel', 'Cerny', 'Moravska', '60200', 'Brno');
INSERT INTO osoba(id_osoby, titul, jmeno, prijmeni, ulice, psc, mesto) VALUES('9904101307', null, 'Tereza', 'Novotna', 'Purkynova', '61200', 'Brno');
INSERT INTO osoba(/*id_osoby,*/ titul, jmeno, prijmeni, ulice, psc, mesto) VALUES(/*'9404101307',*/ 'Bc', 'Anna', 'Slaba', 'Ceska', '60200', 'Brno');
INSERT INTO osoba(id_osoby, titul, jmeno, prijmeni, ulice, psc, mesto) VALUES('9504101307', 'Bc', 'Marek', 'Dvorak', 'Bozetechova', '61200', 'Brno');
INSERT INTO osoba(id_osoby, titul, jmeno, prijmeni, ulice, psc, mesto) VALUES('9204101307', 'Mgr', 'Ondrej', 'Brzobohaty', 'Orechovska', '60444', 'Orechov');
INSERT INTO osoba(id_osoby, titul, jmeno, prijmeni, ulice, psc, mesto) VALUES('9304101307',  null, 'Ladislav', 'Bily', 'Brnenska', '66451', 'Slapanice');

---- VLOZENI DAT VETERINARU ----
INSERT INTO veterinar(id_osoby, id_veterinar, cislo_uctu, hodinova_mzda) VALUES('10000', '1', '098630-32042007/0800', '300');
INSERT INTO veterinar(id_osoby, id_veterinar, cislo_uctu, hodinova_mzda) VALUES('9804101307', '2', 'CZ6508000000192000145399', '300');

---- VLOZENI DAT SESTER -----
INSERT INTO sestra(id_osoby, id_sestra, cislo_uctu, hodinova_mzda) VALUES('9904101307', '1', '234573820/0300', '130');
INSERT INTO sestra(id_osoby, id_sestra, cislo_uctu, hodinova_mzda) VALUES(Osoba_ID_sequence.CURRVAL, '2', 'CZ6508003860192000220990', '180');

---- VLOZENI DAT MAJITELU -----
INSERT INTO majitel(id_osoby, id_majitel) VALUES('9504101307', '1');
INSERT INTO majitel(id_osoby, id_majitel) VALUES('9204101307', '2');
INSERT INTO majitel(id_osoby, id_majitel) VALUES('9304101307', '3');

---- VLOZENI DRUHU ZVIRAT -----
INSERT INTO druh(id_druh, nazev) VALUES('1','Pes');
INSERT INTO druh(id_druh, nazev) VALUES('2','Kocka');
INSERT INTO druh(id_druh, nazev) VALUES('3','Papousek');
INSERT INTO druh(id_druh, nazev) VALUES('4','Kralik');
INSERT INTO druh(id_druh, nazev) VALUES('5','Krecek');
INSERT INTO druh(id_druh, nazev) VALUES('6','Prase');
INSERT INTO druh(id_druh, nazev) VALUES('7','Ovce');
INSERT INTO druh(id_druh, nazev) VALUES('8','Pstros');
INSERT INTO druh(id_druh, nazev) VALUES('9','Had');
INSERT INTO druh(id_druh, nazev) VALUES('10','Vydra');

---- VLOZENI DAT ZVIRAT -----
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('1', '1', '1', 'Azor', date '2009-05-06', date '2018-09-23');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('2', '1', '1', 'Alik', date '2012-11-06', date '2017-11-26');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('3', '3', '1', 'Jack', date '2010-07-16', date '2019-01-22');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('4', '4', '1', 'Hop', date '2015-01-06', date '2016-09-02');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('5', '9', '1', 'Emil', date '2016-07-21', date '2018-12-08');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('6', '10', '2', 'Kartel', date '2014-04-05', date '2019-04-07');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('7', '8', '2', 'Igor', date '1999-03-16', date '2018-02-11');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('8', '5', '3', 'Batman', date '2016-08-01', date '2017-08-01');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('9', '3', '3', 'Ignac', date '2004-11-03', date '2010-06-27');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('10', '7', '3', 'Schon', date '2007-09-12', date '2011-03-11');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('11', '1', '3', 'Agata', date '2010-04-04', date '2016-08-04');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('12', '1', '3', 'Irena', date '2008-06-20', date '2017-09-09');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('13', '10', '3', 'Hugo', date '2014-12-10', date '2018-01-08');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('14', '5', '3', 'Pan Minus', date '2012-08-20', date '2014-08-20');
INSERT INTO zvire(id_zvire, id_druh, id_majitel, jmeno, datum_narozeni, posledni_prohlidka) VALUES('15', '6', '3', 'Lucka', date '2006-03-03', date '2008-01-29');

---- VLOZENI DAT O LECBE -----
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('1', '1', date '2019-01-03', 'probiha', '2000', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('2', '2', date '2018-11-20', 'probiha', '3000', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('3', '3', date '2018-08-01', 'dokoncena', '2500', '9904101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('4', '4', date '2017-12-06', 'dokoncena', '1000', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('5', '4', date '2018-02-02', 'dokoncena', '3260', Osoba_ID_sequence.CURRVAL);
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('6', '4', date '2018-05-06', 'dokoncena', '800', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('7', '4', date '2018-09-14', 'dokoncena', '1400', '9904101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('8', '4', date '2019-04-02', 'dokoncena', '12000', Osoba_ID_sequence.CURRVAL);
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('9', '5', date '2016-02-04', 'dokoncena', '400', Osoba_ID_sequence.CURRVAL);
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('10', '5', date '2019-02-09', 'probiha', '1200', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('11', '6', date '2015-12-30', 'dokoncena', '6200', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('12', '7', date '2019-01-08', 'probiha', '2500', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('13', '8', date '2019-11-02', 'probiha', '750', '9904101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('14', '9', date '2006-03-16', 'dokoncena', '1000', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('15', '9', date '2008-10-22', 'dokoncena', '2450', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('16', '9', date '2018-12-31', 'probiha', '750', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('17', '10', date '2010-02-04', 'dokoncena', '3400', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('18', '11', date '2017-01-30', 'dokoncena', '500', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('19', '12', date '2017-07-12', 'dokoncena', '800', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('20', '12', date '2018-10-30', 'probiha', '2200', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('21', '13', date '2018-12-04', 'probiha', '3400', '10000');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('22', '14', date '2016-07-25', 'dokoncena', '400', '9804101307');
INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('23', '15', date '2016-09-15', 'dokoncena', '700', '10000');

---- VLOZENI DAT O DIAGNOZACH -----
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('1', '10000', '1', 'zlomenina');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('2', '10000', '2', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('3', '9804101307', '3', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('4', '10000', '4', 'zlomenina');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('5', '9804101307', '5', 'parazite');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('6', '10000', '6', 'podchlazeni');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('7', '10000', '7', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('8', '9804101307', '8', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('9', '10000', '9', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('10', '9804101307', '10', 'parazite');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('11', '9804101307', '11', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('12', '9804101307', '12', 'podchlazeni');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('13', '10000', '13', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('14', '10000', '14', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('15', '9804101307', '15', 'zlomenina');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('16', '9804101307', '16', 'podchlazeni');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('17', '10000', '17', 'parazite');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('18', '10000', '18', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('19', '9804101307', '19', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('20', '9804101307', '20', 'zanet');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('21', '10000', '21', 'zlomenina');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('22', '9804101307', '22', 'alergie');
INSERT INTO diagnoza(id_diagnoza, id_osoby, id_lecba, nemoc) VALUES('23', '10000', '23', 'zanet');

---- VLOZENI DAT O LECICH -----
INSERT INTO lek(id_lek, typ_leku, ucinna_latka, kontraindikace, id_osoby) VALUES('1', 'sprej', 'prallethrine', null, '9904101307');
INSERT INTO lek(id_lek, typ_leku, ucinna_latka, kontraindikace, id_osoby) VALUES('2', 'tablety', 'biotin', null, '9804101307');
INSERT INTO lek(id_lek, typ_leku, ucinna_latka, kontraindikace, id_osoby) VALUES('3', 'tablety', 'DL-methionin', null, '9904101307');
INSERT INTO lek(id_lek, typ_leku, ucinna_latka, kontraindikace, id_osoby) VALUES('4', 'roztok', 'Hyaluron', null, Osoba_ID_sequence.CURRVAL);
INSERT INTO lek(id_lek, typ_leku, ucinna_latka, kontraindikace, id_osoby) VALUES('5', 'roztok', 'Chondroitin ', null, Osoba_ID_sequence.CURRVAL);

---- VLOZENI DAT O DAVKOVANICH -----
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('1','2','1','500mg','2/den','5 tydnu');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('2','1','2','100mg','1/den','1 tyden');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('3','3','3','50mg','1/den','1 rok');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('4','5','4','5ml','3/den','2 mesice');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('5','4','5','15ml','1/tyden','1 mesic');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('6','1','6','800mg','1/tyden','7 tydnu');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('7','2','7','400mg','1/tyden','3 tydny');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('8','3','8','450mg','1/tyden','6 tydnu');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('9','4','9','30ml','6/den','3 mesice');
INSERT INTO davkovani(id_davkovani, id_lek, id_druh, mnozstvi_leku, interval_podavani, doba_podavani) VALUES('10','5','10','40ml','2/tyden','1 mesic');


---- VLOZENI DAT PRO TABULKY REPREZENTUJICI VZTAHY N:N ----
INSERT INTO Osoba_Poda_Lek(id_osoby, id_lek) VALUES('9904101307', '1');
INSERT INTO Osoba_Poda_Lek(id_osoby, id_lek) VALUES('9904101307', '2');
INSERT INTO Osoba_Poda_Lek(id_osoby, id_lek) VALUES('9804101307', '3');
INSERT INTO Osoba_Poda_Lek(id_osoby, id_lek) VALUES('10000', '2');
INSERT INTO Osoba_Poda_Lek(id_osoby, id_lek) VALUES('9904101307', '5');

INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '1');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '2');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '4');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '6');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '14');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '17');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '18');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '21');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('1', '23');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '10');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '11');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '12');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '15');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '16');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '19');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '20');
INSERT INTO Veterinar_Provadi_Lecbu(id_veterinar, id_lecba) VALUES('2', '22');

INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('1', '3');
INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('1', '7');
INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('1', '13');
INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('2', '5');
INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('2', '8');
INSERT INTO Sestra_Provadi_Lecbu(id_sestra, id_lecba) VALUES('2', '9');

INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('1', '2');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('2', '2');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('3', '3');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('4', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('5', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('6', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('7', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('8', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('9', '4');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('10', '4');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('11', '5');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('12', '3');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('13', '4');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('14', '3');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('15', '3');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('16', '3');
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('17', '2'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('18', '2'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('19', '2'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('20', '2'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('21', '5'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('22', '4'); 
INSERT INTO Lecba_Vyzaduje_Lek(id_lecba, id_lek) VALUES('23', '1'); 

--------------- POTVRZENI VSECH ZMEN ---------------
COMMIT;

--------------- VYPIS VSECH TABULEK ---------------

-- SELECT * FROM Osoba;
-- SELECT * FROM Veterinar;
-- SELECT * FROM Majitel;
-- SELECT * FROM Diagnoza;
-- SELECT * FROM Veterinar_Provadi_Lecbu;
-- SELECT * FROM Sestra_Provadi_Lecbu;
-- SELECT * FROM Lecba;
-- SELECT * FROM Zvire;
-- SELECT * FROM Druh;
-- SELECT * FROM Lecba_Vyzaduje_Lek;
-- SELECT * FROM Lek;
-- SELECT * FROM Davkovani;
-- SELECT * FROM Osoba_Poda_Lek;


-------------------------------------------------------------
--                      3. ULOHA                           --
--                   prikazy select                        --
-------------------------------------------------------------

-- Vypis majitelu a jejich zvirat
SELECT Osoba.jmeno, Osoba.prijmeni, Druh.nazev, Zvire.jmeno
FROM Osoba INNER JOIN Majitel ON Osoba.id_osoby = Majitel.id_osoby INNER JOIN Zvire ON Majitel.id_majitel = Zvire.id_majitel INNER JOIN Druh ON Zvire.id_druh = Druh.id_druh
ORDER BY Majitel.id_majitel ASC;

-- Ktere leky jsou doporuceny pro psa?
SELECT typ_leku, ucinna_latka, mnozstvi_leku, interval_podavani ,doba_podavani
FROM Lek NATURAL JOIN Davkovani NATURAL JOIN Druh
WHERE nazev = 'Pes';

-- Ktere lecby jakych nemoci zacali veterinari lecit v roce 2018 a stale probihaji?
SELECT Osoba.titul, Osoba.jmeno, Osoba.prijmeni, Diagnoza.nemoc, Zvire.jmeno, Lecba.datum
FROM Osoba INNER JOIN Veterinar ON Osoba.id_osoby = Veterinar.id_osoby INNER JOIN Diagnoza ON Osoba.id_osoby = Diagnoza.id_osoby INNER JOIN Lecba ON Diagnoza.id_lecba = Lecba.id_lecba INNER JOIN Zvire ON Lecba.id_zvire = Zvire.id_zvire
WHERE Lecba.datum BETWEEN date '2018-01-01' AND date '2018-12-31' AND stav = 'probiha'
ORDER BY Osoba.id_osoby ASC;

-- Ktere zvire bylo nejcasteji leceno
SELECT id_zvire, jmeno, COUNT (*) AS pocet_leceb
FROM Zvire NATURAL JOIN Lecba
GROUP BY id_zvire, jmeno
HAVING COUNT(*) >= ALL (
	SELECT COUNT(*)
	FROM Zvire NATURAL JOIN Lecba
	GROUP BY id_zvire
);

-- Kteri majitele vlastni minimalne 3 mazlicky a leceni je dohromady uÅ¾ stalo vice nez 15 000?
SELECT Majitel.id_majitel, Osoba.jmeno, Osoba.prijmeni, SUM(Lecba.cena) AS celkova_cena
FROM Osoba INNER JOIN Majitel ON Osoba.id_osoby = Majitel.id_osoby INNER JOIN Zvire ON Majitel.id_majitel = Zvire.id_majitel INNER JOIN Lecba ON Zvire.id_zvire = Lecba.id_zvire
GROUP BY Majitel.id_majitel, Osoba.jmeno, Osoba.prijmeni
HAVING SUM(Lecba.cena) >= 15000 AND COUNT(*) >= 3 
ORDER BY celkova_cena DESC;

-- Ktery veterinar lecil zlomeniny v roce 2017?
SELECT titul, jmeno, prijmeni 
FROM Osoba NATURAL LEFT JOIN Veterinar
WHERE id_osoby IN
	(SELECT id_osoby
	FROM Diagnoza
	WHERE nemoc = 'zlomenina' AND id_lecba IN
		(SELECT id_lecba
		FROM Lecba
		WHERE datum BETWEEN date '2017-01-01' AND date '2017-12-31'
		)
	)
ORDER BY id_veterinar;

-- Ktera zvirata nejsou aktualne lecena?
SELECT DISTINCT Zvire.id_zvire, Zvire.jmeno
FROM Zvire
WHERE NOT EXISTS 
	(SELECT *
	FROM Lecba
	WHERE Zvire.id_zvire = Lecba.id_zvire AND stav = 'probiha')
ORDER BY id_zvire;

-------------------------------------------------------------
--                      4. ULOHA                           --
--           vypisy pro trigery, procedury,...             --
-------------------------------------------------------------

-- Kontrolni vypis pro trigger c.1 --
	SELECT id_osoby, titul, jmeno, prijmeni FROM Osoba;

-- Kontrolni vypis pro trigger c.2 --
	SELECT titul, jmeno, prijmeni, hodinova_mzda
	FROM Osoba NATURAL JOIN Sestra
	ORDER BY id_sestra;

	UPDATE Osoba SET titul = 'Bc' WHERE id_osoby = '9904101307';

	SELECT titul, jmeno, prijmeni, hodinova_mzda
	FROM Osoba NATURAL JOIN Sestra
	ORDER BY id_sestra;

	UPDATE Osoba SET titul = 'Mgr' WHERE id_osoby = '10001';

	SELECT titul, jmeno, prijmeni, hodinova_mzda
	FROM Osoba NATURAL JOIN Sestra
	ORDER BY id_sestra;

-- Kontrolni vypis pro proceduru c.1 --
	EXEC vypocitej_procento_leceb('10000');
	EXEC vypocitej_procento_leceb('10001');

-- Kontrolni vypis pro proceduru c.2 --
	EXEC vypocitej_mesicni_vyplatu('10001', 31);
	EXEC vypocitej_mesicni_vyplatu('9804101307', 28);
							-- dala vypoved v pulce mesice
	EXEC vypocitej_mesicni_vyplatu('9904101307', 15);

-- Kontrolni vypis pro proceduru c.3 --
	EXEC vypocitej_vekove_zastoupeni_zvirat;

-- Explain plan --

-- DROP INDEX indexZ;
-- DROP INDEX indexL;

EXPLAIN PLAN FOR
SELECT jmeno, AVG(cena)
FROM Zvire NATURAL JOIN Lecba
GROUP BY cena, jmeno;
SELECT * FROM TABLE(DBMS_XPLAN.display);

CREATE INDEX indexZ ON Zvire(id_zvire, jmeno);
CREATE INDEX indexL ON Lecba(id_lecba, cena);

EXPLAIN PLAN FOR
SELECT /*+ INDEX(Zvire indexZ) INDEX(Lecba indexL) LEADING (Lecba)*/ jmeno, AVG(cena)
FROM Zvire NATURAL JOIN Lecba
GROUP BY cena, jmeno;
SELECT * FROM TABLE(DBMS_XPLAN.display);

-- Pridani pristupu

GRANT ALL ON Osoba TO xchlad16;
GRANT ALL ON Veterinar TO xchlad16;
GRANT ALL ON Sestra TO xchlad16;
GRANT ALL ON Majitel TO xchlad16;
GRANT ALL ON Diagnoza TO xchlad16;
GRANT ALL ON Lecba TO xchlad16;
GRANT ALL ON Zvire TO xchlad16;
GRANT ALL ON Druh TO xchlad16;
GRANT ALL ON Lek TO xchlad16;
GRANT ALL ON Davkovani TO xchlad16;

GRANT EXECUTE ON vypocitej_procento_leceb TO xchlad16;
GRANT EXECUTE ON vypocitej_mesicni_vyplatu TO xchlad16;
GRANT EXECUTE ON vypocitej_vekove_zastoupeni_zvirat TO xchlad16;

-- Materialized view -- 
DROP MATERIALIZED VIEW MV;

CREATE MATERIALIZED VIEW LOG ON Zvire WITH PRIMARY KEY, ROWID;
CREATE MATERIALIZED VIEW LOG ON Lecba WITH PRIMARY KEY, ROWID;

CREATE MATERIALIZED VIEW MV
	NOLOGGING
	CACHE						-- postupne optimalizuje cteni z pohledu
	BUILD IMMEDIATE				-- naplni pohled ihned po jeho vytvoreni
	REFRESH ON COMMIT			-- aktualizuje pohled dle logu master tabulek
	ENABLE QUERY REWRITE		-- bude používán optimalizátorem
AS 
SELECT id_lecba, stav, cena, jmeno, datum_narozeni
FROM Zvire NATURAL JOIN Lecba;

GRANT ALL ON MV TO xchlad16;

-- Spusteni --

SELECT id_lecba, stav, cena, jmeno, datum_narozeni
FROM MV;

INSERT INTO lecba(id_lecba, id_zvire, datum, stav, cena, id_osoby) VALUES('24', '1', date '2019-01-03', 'probiha', '9999', '10000');

COMMIT;

SELECT id_lecba, stav, cena, jmeno, datum_narozeni
FROM MV;

DELETE FROM lecba WHERE id_lecba = '20';

COMMIT;

SELECT id_lecba, stav, cena, jmeno, datum_narozeni
FROM MV;

DELETE FROM lecba WHERE id_lecba = '24';

COMMIT;
