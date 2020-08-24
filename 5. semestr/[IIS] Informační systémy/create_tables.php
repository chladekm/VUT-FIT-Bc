<?php

    require("connection_credentials.php");
    require("functions.php");


    try 
    {
        try {

            $pdo = new PDO($dsn, $username, $password);

            //Creating database
            // $sql = "CREATE DATABASE IIS_database CHARACTER SET utf8 COLLATE utf8_czech_ci;";
            // $pdo->exec($sql);
            // echo "Database created successfully.<br>"; 

            $sql = "SET FOREIGN_KEY_CHECKS=0";
            $pdo->exec($sql);

            $sql = "DROP TABLE Nereg_uzivatel";
            $pdo->exec($sql);

            $sql = "DROP TABLE Stravnik";
            $pdo->exec($sql);

            $sql = "DROP TABLE Operator_Admin";
            $pdo->exec($sql);

            $sql = "DROP TABLE Ridic";
            $pdo->exec($sql);

            $sql = "DROP TABLE Objednavka";
            $pdo->exec($sql);

            $sql = "DROP TABLE Provozovna";
            $pdo->exec($sql);

            $sql = "DROP TABLE Polozka";
            $pdo->exec($sql);

            $sql = "DROP TABLE Operator_prirazuje_Objednavku";
            $pdo->exec($sql);

            $sql = "DROP TABLE Objednavka_je_Tvorena";
            $pdo->exec($sql);

            $sql = "SET FOREIGN_KEY_CHECKS=1";
            $pdo->exec($sql);

            echo "-- All tables were dropped --<br>";
        }
        catch(PDOException $e)
        {
            echo "Connection error: ".$e->getMessage();
            die();
        } 
        
        // ---- VYTVORENI TABULKY NEREGISTROVANY UZIVATEL ----
        $sql = 
            "CREATE TABLE Nereg_uzivatel
            (
                id_uzivatel     INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                jmeno           VARCHAR(20),
                prijmeni        VARCHAR(20),
                adresa          VARCHAR(30),
                psc             VARCHAR(5),
                telefon        VARCHAR(15)
            );         

            CREATE TRIGGER trigger_tel
            BEFORE INSERT ON Nereg_uzivatel
            FOR EACH ROW BEGIN
            if (NEW.telefon REGEXP '^([[.plus-sign.]]420|00420)?[[. .]]?[0-9]{3}[[. .]]?[0-9]{3}[[. .]]?[0-9]{3}$') = 0 THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
            end if;
            end;

            CREATE TRIGGER trigger_psc
            BEFORE INSERT ON Nereg_uzivatel
            FOR EACH ROW BEGIN
            if (NEW.psc REGEXP '^[0-9]{3}[[. .]]?[0-9]{2}$') = 0 THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Nereg_uzivatel created successfully<br>";



        // ---- VYTVORENI TABULKY STRAVNIK ----
        $sql = 
            "CREATE TABLE Stravnik
            (
                id_uzivatel     INT(8) UNSIGNED UNIQUE,
                id_stravnik     INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                uziv_jmeno      VARCHAR(20),
                heslo           VARCHAR(255),

                CONSTRAINT FK_StravnikUzivatel FOREIGN KEY (id_uzivatel) REFERENCES Nereg_uzivatel(id_uzivatel) ON DELETE CASCADE
            );

            CREATE TRIGGER trigger_null_stravnik
            BEFORE INSERT ON Stravnik
            FOR EACH ROW BEGIN
            if (NEW.id_uzivatel IS NULL OR NEW.id_stravnik IS NULL OR NEW.uziv_jmeno IS NULL OR NEW.heslo IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí být NULL!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Stravnik created successfully<br>";

        // ---- VYTVORENI TABULKY OPERATOR/ADMIN ----
        $sql = 
            "CREATE TABLE Operator_Admin
            (
                id_uzivatel     INT(8) UNSIGNED UNIQUE CHECK(id_uzivatel > 0),
                id_operator     INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                rodne_cislo     VARCHAR(11),
                mzda            VARCHAR(10),
                cislo_uctu      VARCHAR(24),
                je_admin        BIT,

                CONSTRAINT FK_OperatorUzivatel FOREIGN KEY (id_uzivatel) REFERENCES Nereg_uzivatel(id_uzivatel) ON DELETE CASCADE
            );
            CREATE TRIGGER trigger_operator
	            BEFORE INSERT ON Operator_Admin
	            FOR EACH ROW BEGIN
	            if (NEW.id_uzivatel IS NULL OR NEW.id_operator IS NULL OR NEW.rodne_cislo IS NULL) THEN
	            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
	            end if; 
	            if (NEW.rodne_cislo REGEXP '^[0-9]{6}[[.slash.]][0-9]{3,4}$') = 0 THEN
	            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
	            end if;
	            if (NEW.mzda REGEXP '^[0-9]+$') = 0 THEN
	            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
	            end if;
	            if (NEW.cislo_uctu REGEXP '^([0-9]{0,6}-)?[0-9]{1,10}[[.slash.]][0-9]{4}$') = 0 THEN
	            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
	            end if;
	        end;
            ";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Operator_Admin created successfully<br>";

        // ---- VYTVORENI TABULKY RIDIC ----
        $sql = 
            "CREATE TABLE Ridic
            (
                id_uzivatel     INT(8) UNSIGNED UNIQUE,
                id_ridic        INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                licence         VARCHAR(30),

                CONSTRAINT FK_RidicUzivatel FOREIGN KEY (id_uzivatel) REFERENCES Nereg_uzivatel(id_uzivatel) ON DELETE CASCADE
            );

            CREATE TRIGGER trigger_null_ridic
            BEFORE INSERT ON Ridic
            FOR EACH ROW BEGIN
            if (NEW.id_uzivatel IS NULL OR NEW.id_ridic IS NULL OR NEW.licence IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí být NULL!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Ridic created successfully<br>";
                

        // ---- VYTVORENI TABULKY OBJEDNAVKA ----
        $sql = 
            "CREATE TABLE Objednavka
            (
                id_uzivatel     INT(8) UNSIGNED,
                id_objednavka   INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                stav            VARCHAR(20),
                cena            VARCHAR(10),

                CONSTRAINT FK_Uzivatel_vytvari_Objednavku FOREIGN KEY (id_uzivatel) REFERENCES Nereg_uzivatel(id_uzivatel) ON DELETE CASCADE -- vztah 1:N --
            );

            CREATE TRIGGER trigger_null_objednavka
            BEFORE INSERT ON Objednavka
            FOR EACH ROW BEGIN

            if (NEW.stav != 'neodeslana' AND NEW.stav != 'odeslana' AND NEW.stav != 'dorucena' AND NEW.stav != 'na ceste' AND NEW.stav != 'pripravena') THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Objednavka created successfully<br>";


        // ---- VYTVORENI TABULKY PROVOZOVNA ----
        $sql = 
            "CREATE TABLE Provozovna
            (
                id_provozovna   INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nazev           VARCHAR(40),
                adresa          VARCHAR(40)
            );

            CREATE TRIGGER trigger_provozovna
            BEFORE INSERT ON Provozovna
            FOR EACH ROW BEGIN

            if (NEW.nazev IS NULL OR NEW.id_provozovna IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí NULL!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Provozovna created successfully<br>";


        // ---- VYTVORENI TABULKY POLOZKA ----
        $sql = 
            "CREATE TABLE Polozka
            (
                id_provozovna   INT(8) UNSIGNED,
                id_polozka      INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nazev           VARCHAR(100),
                popis           VARCHAR(300),
                cena            VARCHAR(10),
                typ             VARCHAR(30),
                kategorie       VARCHAR(20),
                cas_platnosti   VARCHAR(10),
                pocet 			INT UNSIGNED,
                nahled          BLOB,

                CONSTRAINT FK_Polozku_nabizi_Provozovna FOREIGN KEY (id_provozovna) REFERENCES Provozovna(id_provozovna) ON DELETE CASCADE -- vztah 1:N --
            );

            CREATE TRIGGER trigger_polozka
            BEFORE INSERT ON Polozka
            FOR EACH ROW BEGIN

            if (NEW.nazev IS NULL OR NEW.cena IS NULL OR NEW.cas_platnosti IS NULL OR NEW.typ IS NULL OR NEW.kategorie IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí NULL!';
            end if;

            if (NEW.cena REGEXP '[0-9]+') = 0 THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
            end if;

            if ((NEW.kategorie != 'hlavni jidlo') AND (NEW.kategorie != 'polevka') AND (NEW.kategorie != 'predkrm') AND (NEW.kategorie != 'napoj') AND (NEW.kategorie != 'dezert')) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah! Kategorie';
            end if;

            if ((NEW.typ != 'bezne') AND (NEW.typ != 'veganske') AND (NEW.typ != 'vegetarianske') AND (NEW.typ != 'raw') AND (NEW.typ != 'pizza') AND (NEW.typ != 'ryby')) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota mimo rozsah!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Polozka created successfully<br>";


        // --------------- TABULKY PRO VZTAHY N:N ---------------
        $sql = 
            "CREATE TABLE Operator_prirazuje_Objednavku
            (
                id_operator     INT(8) UNSIGNED,
                id_objednavka   INT(8) UNSIGNED,
                id_ridic        INT(8) UNSIGNED

                -- ,
                -- CONSTRAINT FK_Operator FOREIGN KEY (id_operator) REFERENCES Provozovna(id_operator),
                -- CONSTRAINT FK_Objednavka FOREIGN KEY (id_objednavka) REFERENCES Objednavka(id_objednavka) ON DELETE CASCADE,
                -- CONSTRAINT FK_Ridic FOREIGN KEY (id_ridic) REFERENCES Ridic(id_ridic) 
            );

            CREATE TRIGGER trigger_operator_prirazuje
            BEFORE INSERT ON Operator_prirazuje_Objednavku
            FOR EACH ROW BEGIN

            if (NEW.id_operator IS NULL OR NEW.id_objednavka IS NULL OR NEW.id_ridic IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí NULL!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Operator_prirazuje_Objednavku created successfully<br>";


        $sql = 
            "CREATE TABLE Objednavka_je_Tvorena
            (
                id_objednavka_polozka INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_objednavka   INT(8) UNSIGNED,
                id_polozka      INT(8) UNSIGNED

                -- ,
                -- CONSTRAINT FK_Polozka FOREIGN KEY (id_polozka) REFERENCES Polozka(id_polozka) ON DELETE CASCADE,
                -- CONSTRAINT FK_Objednavka FOREIGN KEY (id_objednavka) REFERENCES Provozovna(id_objednavka) ON DELETE CASCADE 
            );

            CREATE TRIGGER trigger_objednavka_je_tvorena
            BEFORE INSERT ON Objednavka_je_Tvorena
            FOR EACH ROW BEGIN

            if (NEW.id_objednavka_polozka IS NULL OR NEW.id_objednavka IS NULL OR NEW.id_polozka IS NULL) THEN
            	SIGNAL SQLSTATE '22003' set message_text = 'Hodnota nesmí NULL!';
            end if;
            end;";

        if($pdo->exec($sql))
            echo "Failed to create table ";
        else
            echo "Table Objednavka_je_Tvorena created successfully<br>";

    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

    echo"<br><br>-- Filling tables with data --<br>";
    include("insert_all.php");
    $pdo = null;


?>