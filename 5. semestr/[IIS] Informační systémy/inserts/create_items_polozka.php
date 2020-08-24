<?php
  require("connection_credentials.php");
	
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
	die('cannot connect '.mysqli_connecterror());
  }
  
  // Deleting all items from table
  $sql = "DELETE FROM Polozka;";
  if ($conn->query($sql) === true) { 
	  echo "Records deleted successfully.<br>"; 
  } 
  else { 
	  echo "ERROR: Could not able to execute $sql. "
		  .$conn->error; 
  } 
  // Creating database
  $sql = "INSERT INTO Polozka (id_provozovna, id_polozka, nazev, popis, cena, typ, kategorie, cas_platnosti, pocet)  
		  	-- Provozovna 1
		  		-- Nápoje
		  VALUES
				('1', '551', 'Coca-cola (0,5l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('1', '552', 'Fanta (0,5l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('1', '553', 'Sprite (0,5l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('1', '554', 'Dr. Pepper (0,3l)', '', '49', 'bezne', 'napoj', 'trvala', NULL),
				('1', '555', 'Monster Energy (0,5l)', '', '59', 'bezne', 'napoj', 'trvala', NULL),

				-- Pizzy
		  		('1', '1', 'Margherita', 'rajčatová omáčka s bylinkami, mozzarella', '119', 'pizza', 'hlavni jidlo', 'trvala', NULL), 
				('1', '2', 'Salami', 'rajčatová omáčka s bylinkami, mozzarrella, salám', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL), 
				('1', '3', 'Quattro Formaggi', 'rajčatová omáčka s bylinkami, mozzarrella, Grana Padano, pecorino, gorgonzola', '199', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '4', 'BBQ Gourmet', 'BBQ omáčka, mozarella, šunka, slanina, mozarella, kuřecí maso', '229', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '5', 'Bacon burger pizza', 'rajčatová omáčka, mozarella, cibule, cheddar, slanina, hovězí maso', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '6', 'Hawai', 'rajčatová omáčka, mozarella, ananas, šunka', '169', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '7', 'Milano', 'rajčatová omáčka, mozarella, šunka, Gran Moravia, mozarella, pecorino', '229', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '8', 'Chorizo rucola', 'BBQ omáčka, smetana, cibule, slanina, rukola, gorgonzola', '185', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('1', '9', 'Veggie', 'rajčatová omáčka, zapečená bazalka, kukuřice, olivy, mozarella, čerstvý koriandr', '169', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),

			-- Provozovna 2
				-- Nápoje
				('2', '561', 'Coca-cola (0,5l)', '', '35', 'bezne', 'napoj', 'trvala', NULL),
				('2', '562', 'Fanta (0,5l)', '', '35', 'bezne', 'napoj', 'trvala', NULL),
				('2', '563', 'Sprite (0,5l)', '', '35', 'bezne', 'napoj', 'trvala', NULL),

		  		--  Polevky
				('2', '20', 'Brokolicový krém s krutony', '', '29', 'vegetarianske', 'polevka', 'trvala', NULL),
				('2', '21', 'Bramboračka', '', '29', 'vegetarianske', 'polevka', 'trvala', NULL),
				('2', '22', 'Drůbeží vývar s nudlemi', '', '19', 'bezne', 'polevka', 'trvala', NULL),
				('2', '23', 'Žampionová', '', '29', 'vegetarianske', 'polevka', 'denni','200'),
				('2', '24', 'Frankfurtská', '', '29', 'bezne', 'polevka', 'trvala', NULL),
				('2', '25', 'Hovězí vývar s kapáním', '', '19', 'bezne', 'polevka', 'trvala', NULL),
				('2', '26', 'Zelen. vývar s těstovinou', '', '29', 'vegetarianske', 'polevka', 'trvala', NULL),
				('2', '27', 'Boršč', '', '29', 'bezne', 'polevka', 'denni','150'),
				('2', '28', 'Kmínová s vejcem', '', '19', 'vegetarianske', 'polevka', 'denni','250'),

				--  Normalni pokrmy
				('2', '40', 'Bratislavská vepřová pečeně, knedlíky', '', '87', 'bezne', 'hlavni jidlo', 'denni', '250'),
				('2', '41', 'Pečené kuřecí stehno po horácku, dušená rýže', '', '87', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('2', '42', 'Tortilla s kuřecím masem, čerstvou zeleninou a česnekovým dipem', '', '87', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('2', '43', 'Čočka na kyselo, vařená vejce/klobáska, okurka, chléb', '', '85', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '44', 'Vepřové smažené pikantní nudličky, hranolky, tatarská omáčka', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '45', 'Vepřová krkovička na grilu, sázené vejce, hranolky, tatarská omáčka', '', '105', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '46', 'Koprová omáčka, vařená vejce, houskové knedlíky', '', '85', 'vegetarianske', 'hlavni jidlo', 'denni', '150'),
				('2', '47', 'Pečená plec na česneku, šťouchané brambory', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '48', 'Mexické fazole, opečená klobása, chléb', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '49', 'Kuřecí plátek, dušená rýže', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '50', 'Smažený hermelín s poličanem, vař.brambory/hranolky, tat.omáčka', '', '87', 'bezne', 'hlavni jidlo', 'denni', '300'),
				('2', '51', 'Přírodní hovězí roštěná, hranolky, tatarská omáčka', '', '139', 'bezne', 'hlavni jidlo', 'denni', '300'),
				('2', '52', 'Sekaný španělák (ml. maso, vejce, klobása,okurka, slanina), bramborová kaše', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '53', 'Vepřový plátek na kari, dušená rýže', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('2', '54', 'Dušená mrkev s hráškem, vařená vejce, vařené brambory', '', '85', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),

			-- Provozovna 3
				-- Nápoje
				('3', '570', '0,5l Domácí limonáda borůvková', '', '70', 'bezne', 'napoj', 'trvala', NULL),
				('3', '571', '0,5l Kofola Original', '', '36', 'bezne', 'napoj', 'trvala', NULL),
				('3', '572', '0,25l Bonaqua (neperlivá)', '', '25', 'bezne', 'napoj', 'trvala', NULL),
				('3', '573', '0,33l Limonáda Koli (malina)', '', '36', 'bezne', 'napoj', 'trvala', NULL),
				('3', '574', '0,33l Sprite', '', '36', 'bezne', 'napoj', 'trvala', NULL),
				('3', '575', '0,25l Kinley tonic (original, zázvorový)', '', '36', 'bezne', 'napoj', 'trvala', NULL),
				('3', '576', '0,25l Fuzetea (černý čaj s broskví)', '', '36', 'bezne', 'napoj', 'trvala', NULL),
				('3', '577', '0,25l Red Bull', '', '45', 'bezne', 'napoj', 'trvala', NULL),

				-- Polévky
				('3', '29', 'Gulášová s bramborami ', '', '55', 'bezne', 'polevka', 'denni', '100'),
				('3', '30', 'Česnečka se sýrem a opečeným chlebem', '', '55', 'vegetarianske', 'polevka', 'denni', '75'),

				-- Normální pokrmy
				('3', '55', 'Katův šleh, dušená rýže/bramboráčky', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '56', 'Šípková omáčka, hovězí maso, houskový knedlík', '', '89', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '57', 'Kuřecí steak, grilovaná zelenina, hranolky, tatarská omáčka', '', '110', 'bezne', 'hlavni jidlo', 'denni', '300'),
				('3', '58', 'Svratecký guláš, rýže', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '59', 'Francouzské zapečené brambory, sýr, okurka', '', '85', 'vegetarianske', 'hlavni jidlo', 'denni', '100'),
				('3', '60', 'Dukátové buchtičky s vanilkovým krémem', '', '83', 'vegetarianske', 'hlavni jidlo', 'denni', '300'),
				('3', '61', 'Vepřový gyros (česnek,oregáno,č.cibule, máta), tat.om., vař. brambory/hranolky', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '62', 'Smažený okoun nilský, bramborová kaše', '', '87', 'ryby', 'hlavni jidlo', 'denni', '100'),
				('3', '63', 'Grilovaná panenka s bylinkovým máslem, hranolky', '', '99', 'bezne', 'hlavni jidlo', 'denni', '150'),
				('3', '64', 'Smažený kuřecí/vepřový řízek, vařené brambory/bramborový salát', '', '79', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('3', '65', 'Vepřová kotleta zapečená slaninou a nivou, dušená rýže', '', '87', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '66', 'Hovězí nudličky na červeném víně, šťouchané brambory', '', '88', 'bezne', 'hlavni jidlo', 'denni', '150'),
				('3', '67', 'Kuřecí roláda plněná riccotou a hříbky, jasmínová rýže', '', '88', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '68', 'Špíz z vepřového masa se slaninou, klobásou a zeleninou, hranolky, tat. om', '', '98', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('3', '69', '300g Čerstvý zeleninový salát s bylinkovým pestem', '', '89', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('3', '70', '300g Salát s mozzarellou a sušenými rajčaty, bylinkové pesto, 4 půlky opečeného toastu', '', '129', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),
				('3', '71', '300g Salát s grilovanými krevetami, cherry rajčátky, jogurtový dresing, 4 půlky opečeného toastu', '', '169', 'ryby', 'hlavni jidlo', 'trvala', NULL),
				('3', '72', '300g Salát s grilovaným lososem,bylinkový dip,4 půlky toastů', '', '169', 'ryby', 'hlavni jidlo', 'trvala', NULL),
				('3', '73', '300g Salát z kuskusu s granátovým jablkem', '', '129', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('3', '74', '300g Sushi maki s tempehem', '', '229', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('3', '75', '300g Sushi maki s avokádem', '', '219', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),

				-- Ostatní
				('3', '716', '400g Sýrové prkno (mozzarella, eidam, hermelín, niva)', '', '149', 'bezne', 'predkrm', 'trvala', NULL),
				('3', '717', '15Ks Smažené cibulové kroužky s domácí tatarkou', '', '75', 'bezne', 'predkrm', 'trvala', NULL),
				('3', '718', '15Ks Smažené pikantní sýrové nugetky s domácí tatarkou', '', '99', 'bezne', 'predkrm', 'trvala', NULL),
				('3', '719', 'Utopenec s feferonkou + 2 krajíce chleba', '', '55', 'bezne', 'predkrm', 'trvala', NULL),

				-- Provozovna 4
				-- Nápoje
				('4', '500', 'Coca-cola (0,33l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '501', 'Coca-cola Zero (0,33l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '502', 'Sprite (0,33l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '503', 'Fanta (0,33l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '504', 'Tonic (0,25l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '505', 'Tonic ginger ale (0,25l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '506', 'Bonaqua-neperlivá (0,25l)', '', '28', 'bezne', 'napoj', 'trvala', NULL),
				('4', '507', 'Bonaqua-jemně perlivá (0,25l)', '', '28', 'bezne', 'napoj', 'trvala', NULL),
				('4', '508', 'Nestea zelený (0,2l)', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '509', 'Red bull (0,25l)', '', '69', 'bezne', 'napoj', 'trvala', NULL),
				('4', '511', 'Točená limonáda 0,3l', '', '22', 'bezne', 'napoj', 'trvala', NULL),
				('4', '512', 'Točená limonáda 0,5l', '', '28', 'bezne', 'napoj', 'trvala', NULL),
				('4', '513', 'Cappy juice 0,2l – různé příchutě', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('4', '514', 'Espresso, Ristreto', '', '38', 'bezne', 'napoj', 'trvala', NULL),
				('4', '515', 'Espresso s mlékem', '', '42', 'bezne', 'napoj', 'trvala', NULL),
				('4', '516', 'Cappuccino', '', '48', 'bezne', 'napoj', 'trvala', NULL),
				('4', '517', 'Latte macchiato', '', '48', 'bezne', 'napoj', 'trvala', NULL),
				('4', '518', 'Latte macchiato ochucené', '', '56', 'bezne', 'napoj', 'trvala', NULL),
				('4', '519', 'Alžírská káva', '', '62', 'bezne', 'napoj', 'trvala', NULL),
				('4', '520', 'Vídeňská káva', '', '52', 'bezne', 'napoj', 'trvala', NULL),
				('4', '521', 'Frappé', '', '44', 'bezne', 'napoj', 'trvala', NULL),
				('4', '522', 'Ledová káva se zmrzlinou', '', '52', 'bezne', 'napoj', 'trvala', NULL),
				('4', '523', 'Svařený jablečný juice se skořicí', '', '42', 'bezne', 'napoj', 'trvala', NULL),
				('4', '524', 'Svařený hruškový juice s hřebíčkem', '', '42', 'bezne', 'napoj', 'trvala', NULL),
				('4', '525', 'Teplá čokoláda', '', '44', 'bezne', 'napoj', 'trvala', NULL),
				('4', '526', 'Grog', '', '48', 'bezne', 'napoj', 'trvala', NULL),
				('4', '527', 'Svařené víno', '', '44', 'bezne', 'napoj', 'trvala', NULL),
				('4', '528', 'Čaje – AHMAD TEA – různé druhy', '', '38', 'bezne', 'napoj', 'trvala', NULL),

				-- Polévky
				('4', '31', 'Česnečka s chlebovým krutonem a vejci ', '', '48', 'veganske', 'polevka', 'denni', '100'),
				('4', '32', 'Hustá gulášová polévka z býčí kližky s bramborem a rozpékaným chlebem', '', '52', 'bezne', 'polevka', 'denni', '100'),
				
				-- Normální pokrmy
				('4', '80', 'Domácí bramborové noky s krémovou hříbkovou omáčkou , listovou petrželkou, kousky kuřecích prsou a sýrem grana padano', 'alergeny (1,3,7)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '81', 'Grilovaná vepřová kotleta s švestkovou – chilli omáčkou + gratinované smetanové brambory s pažitko', 'alergeny (1,3,7) ', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '82', 'Hovězí krk na červeném víně s cibulkovou rýží', 'alergeny (1,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '100'),
				('4', '83', 'Smažená krůtí játra s domácí tatarskou omáčkou a pažitkovými bramborami', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '150'),
				('4', '84', 'Tortilla plněná kousky grilované vepřové panenky, rajčatovým concassé, křupavou slaninou, trhanými saláty, gouda sýrem a česnekovou aïoli', 'alergeny (1,3,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('4', '85', 'Steak z kuřecího prsíčka v čerstvých bylinkách s gorgonzolovou omáčkou a pečené brambory s lahůdkovou cibulkou', 'alergeny (7,9,10) ', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '86', 'Klasické vepřo – knedlo - zelo', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('4', '87', 'Bulgurové zeleninové rizoto s grilovaným balkánským sýrem', 'alergeny (1,7,9,10)', '115', 'vegetarianske', 'hlavni jidlo', 'denni', '150'),
				('4', '88', 'Kuřecí Cordon – bleu s jemnou bramborovou kaší a rajčatovým salátkem', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('4', '89', 'Špíz z vepřové kotlety proložený maďarskou klobásou, cibulí, paprikou a cuketou + česnekové americké brambory', 'alergeny (1,3,6,7,10) ', '115', 'bezne', 'hlavni jidlo', 'denni', '250'),
				('4', '90', 'Plněný paprikový lusk s rajskou omáčkou a houskovými knedlíky', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '91', 'Tagliatelle BOLOGNESE s lístky bazalky a parmezánem', 'alergeny (1,3,7,9,10) ', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '92', 'Smažený sýrový špíz s máslovými bramborami a domácí tatarskou omáčkou', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('4', '93', 'Steak z vepřové kotlety s omáčkou z barevných pepřů a pečené kmínové brambory ve slupce', 'alergeny (1,3,6,7) ', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '94', 'Svratecký hovězí guláš s houskovými knedlíky a cibulkou', 'alergeny (1,3,7)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '95', 'Řecká MOUSAKA s rucolovým salátem', 'alergeny (1,3,5,6,7,10)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '96', 'Smažený kuřecí řízek v kukuřičných lupíncích s jemnou bramborovou kaší a ochucenými saláty', 'alergeny (1,3,7)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '97', 'Steak z vepřové kotlety se sázeným vejcem , omáčkou z výpeku a šťouchané brambory', 'alergeny (1,3,7) ', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '98', 'Špikovaná vepřová kýta na smetaně s houskovými knedlíky a nočkem brusinek', 'alergeny (1,3,7,9,10)', '115', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('4', '99', 'Vegetariánský BURGER s grilovaný řeckým sýrem SATYR, saláty,rajčaty,cibulkou a BBQ omáčkou', 'alergeny (1,3,7,9,10)', '115', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),

				-- Ostatní
				('4', '712', '120g Grilovaný hermelín s máslem a cibulovou marmeládou', '', '85', 'bezne', 'predkrm', 'trvala', NULL),
				('4', '713', '100g Tvarůžkový ´´tatarák´´ s červenou cibulkou podávaný s česnekovými topinkami', '', '95', 'bezne', 'predkrm', 'trvala', NULL),
				('4', '714', '150 g Domácí masová paštika se škvarky podávaná s cibulovou marmeládou', '', '85', 'bezne', 'predkrm', 'trvala', NULL),
				('4', '715', '1ks Bramborová lokše plněná bryndzou , slaninou a smaženou cibulkou', '', '92', 'bezne', 'predkrm', 'trvala', NULL),

			-- Provozovna 5
				-- Nápoje
				('5', '530', '0,33 l Coca Cola', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('5', '531', '0,33 l Coca Cola-light', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('5', '532', '0,33 l Fanta', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('5', '533', '0,33 l Sprite', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('5', '534', '0,25 l Tonic ', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('5', '535', '0,33 l Rájec – jemně perlivý', '', '35', 'bezne', 'napoj', 'trvala', NULL),
				('5', '536', '0,33 l Rájec – neperlivý ', '', '35', 'bezne', 'napoj', 'trvala', NULL),
				('5', '537', '0,25 l Energetický nápoj ', '', '58', 'bezne', 'napoj', 'trvala', NULL),
				('5', '538', '0,3 l Točená limonáda ', '', '23', 'bezne', 'napoj', 'trvala', NULL),
				('5', '539', '0,5 l Točená limonáda ', '', '29', 'bezne', 'napoj', 'trvala', NULL),
				('5', '540', '0,2 l Nealkoholický vinný sekt', '', '63', 'bezne', 'napoj', 'trvala', NULL),
				('5', '541', '0,5 l Domácí limonáda', '', '59', 'bezne', 'napoj', 'trvala', NULL),

				-- Polévky
				('5', '33', 'Zelná s klobásou ', 'se zakysanou smetanou', '49', 'bezne', 'polevka', 'denni', '150'),
				('5', '34', 'Brokolicová s vejcem', '', '21', 'vegetarianske', 'polevka', 'denni', '100'), 
				('5', '35', 'Česnečka se sýrem a krutony ', '', '49', 'vegetarianske', 'polevka', 'trvala', NULL),

				-- Normální pokrmy
				('5', '100', 'Vepřové výpečky, dva druhy zelí, houskový a bramborový knedlík, restovaná cibulka ', '130g masa, 130g zelí, 160g knedlíků', '169', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('5', '101', 'Konfitované kachní stehno, dva druhy zelí, karlovarský a bramborový knedlík', '300g stehno, 130g zelí, 160g knedlíků', '189', 'bezne', 'hlavni jidlo', 'denni', '300'),
				('5', '102', 'Svíčková na smetaně se šlehačkou a brusinkami, houskový knedlík', '130g masa, 160g knedlíků', '169', 'bezne', 'hlavni jidlo', 'denni', '250'),
				('5', '103', 'Penne s listovým špenátem, filirovaný kuřecí steak, česnek, smetanová omáčka, parmezán', '100g masa, 100g těstoviny, 50g smetanového špenátu', '169', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('5', '104', 'Šafránové rizoto s filirovaným kuřecím masem a parmezánem', '100g masa, 100g rýže, 30g parmezánu', '169', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('5', '105', 'Maso s dukáty pro dva  ', '200g steak z vepřové panenky v pepřovém koření a 200g kuřecí prsní steak argentina, 350g smažené bramborové dukáty s restovanou anglickou slaninou zapečené sýrem, 200g zeleninový salát s  rajčátky', '379', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('5', '106', 'ŠIBENICE', 'špíz z kuřecího a vepřového masa, proložené anglickou slaninou, klobáskou a cibulí, kořeněné americké brambory, tatarka, hořčice, kečup', '219', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('5', '107', 'Kilo MASA na TALÍŘI ', '200g filirovaný kuřecí prsní steak, 200g steak z vepřové kotlety, 200g smažená kuřecí prsa, 200g steak z vepřové krkovice, 200g steak z kuřecího stehna, 200g salát z čerstvé zeleniny, 400g amerických brambor', '589', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('5', '108', 'MASOVÝ TALÍŘ pro dva', '200g filirovaný kuřecí prsní steak a 200g steak z vepřové kotlety, 200g smažená kuřecí prsa na česneku, 200g salát z čerstvé zeleniny s rukolou, chléb', '369', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('5', '109', '350 g Salát s rukolou', 'Ledový salát, rajče, rukola, okurek, ředkvičky,olivový olej, balsamiko krém', '72', 'veganske', 'hlavni jidlo', 'denni', '100'),
				('5', '110', '300 g Salát s dukáty ze sýru camembertu', 'ledový salát, rajče, okurka, rukola, ředkvičky se smaženým sýrem camembert)', '89', 'vegetarianske', 'hlavni jidlo', 'denni', '100'),
				('5', '111', '300 g Salát s papričkami plněné sýrem', 'ledový salát, rajče, okurka, rukola, ředkvičky, jemně pikantní plněné papričky se sýrem', '98', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),
				('5', '112', '300 g Salát s tuňákem', 'ledový salát, rajče, ředkvičky, okurek, olivy, tuňák', '99', 'ryby', 'hlavni jidlo', 'trvala', NULL),
				('5', '113', '150 g Okurkový salát ve sladkokyselém nálevu', '', '45', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('5', '114', '150 g Coleslaw salát', '', '55', 'veganske', 'hlavni jidlo', 'denni', '100'),

				-- Ostatní
				('5', '709', '100 g Tatarák se třemi topinkami', 'plněné listovým špenátem, parmezán', '169', 'raw', 'predkrm', 'trvala', NULL),
				('5', '710', '1 ks Pikantní topinka s vepřovým masem, zeleninou a sýrem', 'plněné listovým špenátem, parmezán', '82', 'bezne', 'predkrm', 'trvala', NULL),
				('5', '711', '250 g Bramborové ouška s dipem z nivy a česnekem', '', '89', 'bezne', 'predkrm', 'trvala', NULL),

				-- Dezerty
				('5', '910', 'Palačinka s marmeládou a šlehačkou ', '', '55', 'bezne', 'dezert', 'trvala', NULL),
				('5', '911', 'Palačinka s ovocem – ananas, broskev, šlehačka', '', '59', 'bezne', 'dezert', 'trvala', NULL),
				('5', '912', 'Palačinka s vanilkovou zmrzlinou a šlehačkou ', '', '59', 'bezne', 'dezert', 'trvala', NULL),
				('5', '913', '250 g Teplé lesní ovoce s vanilkovou zmrzlinou a šlehačkou', '', '68', 'bezne', 'dezert', 'trvala', NULL),
				('5', '914', 'Maliny v horké čokoládě se šlehačkou', '', '75', 'bezne', 'dezert', 'trvala', NULL),
				('5', '915', '1 kopeček vanilkové zmrzliny', '', '23', 'bezne', 'dezert', 'trvala', NULL),
				('5', '916', '170g Kynutý knedlík s povidly, sypaný mákem a cukrem, přelitý máslem', '', '95', 'bezne', 'dezert', 'trvala', NULL),

				-- Provozovna 6
				-- Nápoje
				('6', '580', 'Coca-cola', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('6', '581', 'Dr. Pepper', '', '45', 'bezne', 'napoj', 'trvala', NULL),
				('6', '582', 'Bonaqua-neperlivá', '', '33', 'bezne', 'napoj', 'trvala', NULL),
				('6', '583', 'Točená kofola', '', '39', 'bezne', 'napoj', 'trvala', NULL),
				('6', '584', 'Magnesia', '', '33', 'bezne', 'napoj', 'trvala', NULL),
				('6', '585', 'Cappy', 'multivitamin', '45', 'bezne', 'napoj', 'trvala', NULL),
				('6', '586', 'Nestea', '', '49', 'bezne', 'napoj', 'trvala', NULL),
				('6', '587', 'Lipton Ice Tea', 'green', '49', 'bezne', 'napoj', 'trvala', NULL),
				('6', '588', 'Fuze tea', 'citron', '49', 'bezne', 'napoj', 'trvala', NULL),
				('6', '589', 'Rychlé špunty', '', '115', 'bezne', 'napoj', 'trvala', NULL),
				('6', '590', 'Italská stolní voda Goccia Di Carnia', '', '59', 'bezne', 'napoj', 'trvala', NULL),
				('6', '591', '0,5l Pilsner Urquell 12° světlé', '', '55', 'bezne', 'napoj', 'trvala', NULL),
				('6', '592', '0,3l Pilsner Urquell 12° světlé', '', '55', 'bezne', 'napoj', 'trvala', NULL),
				('6', '593', '0,5l Birell', 'nealkoholickyé pivo', '42', 'bezne', 'napoj', 'trvala', NULL),
				('6', '594', '0,5l Radegast 11° světlé', '', '35', 'bezne', 'napoj', 'trvala', NULL),
				('6', '595', '0,5l Velkopopovický kozel 11° světlé', '', '47', 'bezne', 'napoj', 'trvala', NULL),
				('6', '596', '0,3l Velkopopovický kozel 11° světlé', '', '47', 'bezne', 'napoj', 'trvala', NULL),
				('6', '597', '0,5l Krušovice 10° světlé', '', '42', 'bezne', 'napoj', 'trvala', NULL),
				('6', '599', '0,5l Cider', 'apple', '60', 'bezne', 'napoj', 'trvala', NULL),
				('6', '600', '0,5l Frisco', 'jablko', '40', 'bezne', 'napoj', 'trvala', NULL),

				-- Polévky
				('6', '36', 'Kuřecí vývar', 's masem a nudlemi', '49', 'bezne', 'polevka', 'denni', '100'), 
				('6', '37', 'Kuřecí vývar', 's játrovými knedlíčky a nudlemi', '49', 'bezne', 'polevka', 'denni', '150'),
				('6', '38', 'Minestrone', 'italská zeleninová polévka s parmazánem', '55', 'veganske', 'polevka', 'denni', '200'), 
				('6', '39', 'Tomatový krém ', 'se sušenými rajčaty', '60', 'veganske', 'polevka', 'trvala', NULL),
				('6', '300', '0,2l Hovězí vývar', 'játrový knedlíček, zelenina, hovězí maso, nudle', '95', 'bezne', 'polevka', 'trvala', NULL),
				('6', '301', '0,2l Minestrone', 'italská zeleninová polévka', '75', 'bezne', 'polevka', 'trvala', NULL),
				('6', '302', '0,2l Rybí polévka', 'chilli, zázvor', '189', 'ryby', 'polevka', 'trvala', NULL),

				-- Pizzy
				('6', '200', 'Pizza Pane', 'pizza chléb  ', '55', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '201', 'Pizza kapsa', 'sýr, šunka, kukuřice a bylinky  ', '169', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '202', 'Margherita', 'tomato, sýr  ', '139', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '203', 'Salami', 'tomato, sýr, salám  ', '159', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '204', 'Funghi', 'tomato, sýr, žampiony  ', '159', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '205', 'Picante con spinachi', 'tomato, sýr, pikantní italský salám Ventricina, listový špenát, žampiony, česnek  ', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '206', 'Cardinale', 'tomato, sýr, šunka  ', '165', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '207', 'Hawai', 'tomato, smetana, sýr, šunka, ananas  ', '169', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '208', 'Americana', 'tomato, sýr, šunka, žampiony ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '209', 'Al Pancetta', 'tomato, sýr, slanina, žampiony  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '210', 'Milano', 'tomato, sýr, italský salám, hermelí,  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '211', 'Bianco', 'smetana, sýr, šunka, jarní cibulka  ', '169', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '212', 'Hungaria', 'tomato, sýr, čabajka, cibule  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '213', 'Quattro stagioni', 'tomato, sýr, šunka, salám ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '214', 'San Remo', 'smetana, sýr, slanina, kuřecí maso ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '215', 'Agli Spinaci', 'tomato, sýr, gorgonzola, listový ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '216', 'Mexicana', 'tomato, sýr, salám, kukuřice, paprika ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '217', 'Messina', 'smetana, sýr, uzený sýr, šunka, cibule ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '218', 'La Patas', 'tomato, sýr, šunka, salám, žampiony, ', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '219', 'Positano', 'tomato, sýr, hermelín, uzený sýr,  herkules čerstvá cibule ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '220', 'Gurmán', 'tomato, sýr, šunka, kuřecí maso, žampiony, kukuřice, směs quattro formaggi ', '199', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '221', 'Capricciosa', 'tomato, sýr, šunka, žampiony, olivy, kapary, ančovičky, artyčoky  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '222', 'Diavola', 'tomato, sýr, salám, cibule, ančovičky, česnek, chilli olej  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '223', 'Genova', 'smetana, sýr, šunka, vejce, slanina, kukuřice  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '224', 'Fabrizio', 'tomato, sýr, šunka, suš. rajčata, olivy', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '225', 'Mario', 'tomato, sýr, slanina, špenát, česnek, kozí rohy, cibule  ', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '226', 'Pirata', 'tomato, sýr, slanina, salám, vejce, cibule', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '227', 'Pollo', 'tomato, sýr, kuřecí maso, špenát, česnek', '179', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '228', 'Con Verdure', 'smetana, sýr, čerstvá rajčata, špenát, kukuřice, žampiony  ', '169', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '229', 'Italiana', 'tomato, sýr, pikantní italský salám, Ventricina, gorgonzola, nakádané cibulky, rucola  ', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '230', 'Manzo Freddo', 'tomato, sýr, roastbeef, nakládané cibulky', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '231', 'Pizza Calzone (plněná)', 'tomato, sýr, šunka, slanina, žampiony, česnek ', '189', 'pizza', 'hlavni jidlo', 'trvala', NULL),
				('6', '232', 'Vegetariana', 'tomato, sýr, kukuřice, artyčoky, žampióny, cibule', '169', 'vegetarianske', 'hlavni jidlo', 'trvala', NULL),

				-- Ostatní
				('6', '705', '100 g Caprese speciale', 'rajčata, mozzarella, česnek, bazalka, olivový olej', '119', 'raw', 'predkrm', 'trvala', NULL),
				('6', '706', '80g Manzo Freddo con Salsa', 'jemný roastbeefový nářez s dresingem', '189', 'bezne', 'predkrm', 'trvala', NULL),
				('6', '707', '1ks Rozpečená bageta', 's česnekem nebo bylinkami', '29', 'bezne', 'predkrm', 'trvala', NULL),
				('6', '708', '70g Carpaccio z hovězí svíčkové', 'plněné listovým špenátem, parmezán', '189', 'raw', 'predkrm', 'trvala', NULL),

				--  Dezerty
				('6', '900', '100g Cheesecake', '', '99', 'bezne', 'dezert', 'trvala', NULL),
				('6', '901', '90g Čokoládový dortík s lesním ovocem a vanilkovým krémem, šlehačka', '', '99', 'bezne', 'dezert', 'trvala', NULL),
				('6', '902', '90g Čokoládový dortík se zmrzlinou a šlehačkou', '', '95', 'bezne', 'dezert', 'trvala', NULL),
				('6', '903', '100g Grilovaný ananas podávaný se skořicovou zmrzlinou', '', '99', 'bezne', 'dezert', 'trvala', NULL),
				('6', '904', '150g Tiramisu La Patas', '', '109', 'bezne', 'dezert', 'trvala', NULL),
				('6', '905', '100g Smetanovo-tvarohový dort s lesním ovocem', '', '95', 'bezne', 'dezert', 'trvala', NULL),
				('6', '906', 'Pohár La Patas – 2 kopečky vanilkové zmrzliny, přelité směsí z lesních plodů, šlehačka', '', '95', 'bezne', 'dezert', 'trvala', NULL),

			-- Provozovna 7
				-- Nápoje
				('7', '610', '0,33l MATTONI', 'JEMNĚ PERLIVÁ', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '611', '0,33l MATTONI', 'PERLIVÁ', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '612', '0,33l AQUILLA', 'NEPERLIVÁ', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '613', '0,75l MATTONI', 'PERLIVÁ', '90', 'bezne', 'napoj', 'trvala', NULL),
				('7', '614', '0,75l AQUILLA', 'NEPERLIVÁ', '90', 'bezne', 'napoj', 'trvala', NULL),
				('7', '615', '0,33l COCA COLA', 'LIGHT', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '616', '0,25l TONIC', 'ORIGINAL', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '617', '0,25l TONIC', 'LEMON', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '618', '0,25l TONIC', 'GINGER', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '619', '0,25l SCHWEPPES', 'ORANGE', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '620', '0,25l SCHWEPPES', 'LEMON', '49', 'bezne', 'napoj', 'trvala', NULL),
				('7', '621', '0,2l LEDOVÝ ČAJ', 'CITRON', '55', 'bezne', 'napoj', 'trvala', NULL),
				('7', '622', '0,2l LEDOVÝ ČAJ', 'BROSKEV', '55', 'bezne', 'napoj', 'trvala', NULL),
				('7', '623', '0,2l LEDOVÝ ČAJ', 'ZELENÝ', '55', 'bezne', 'napoj', 'trvala', NULL),
				('7', '624', '0,25l RED BULL', '', '80', 'bezne', 'napoj', 'trvala', NULL),
				('7', '625', '0,33l Kofola', '', '49', 'bezne', 'napoj', 'trvala', NULL),

				-- Normální pokrmy
				('7', '120', '200 g Steak z nízké roštěné', 'omáčka z růžového pepře s kapkou brandy, hranolky', '420', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('7', '121', '200 g Do růžova pečená vepřová panenka', 'smetanová omáčka z hřibů, bramborové dukáty', '269', 'bezne', 'hlavni jidlo', 'denni', '150'),
				('7', '122', '200 g Vepřová panenka na jehle', 'smažené cibulové kroužky, hranolky', '279', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('7', '123', '500 g Vepřová žebírka', 'pečená v pikantní medové marinádě, nakládaná zelenina, americké brambory s česnekem, česneková majonéza', '259', 'bezne', 'hlavni jidlo', 'denni', '200'),
				('7', '124', '150 g Kuřecí prsíčka s parmskou šunkou gratinovaná mozzarellou', 'restovaná zelenina, rukola, bramborová kaše', '249', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('7', '125', '150 g Lehce pikantní směs z kuřecího masa a zeleniny', 'připravená na asijský způsob, jasmínová rýž', '249', 'bezne', 'hlavni jidlo', 'denni', '100'),
				('7', '126', '180 g Smažený telecí řízek', 'podávaný s vídeňským bramborovým saláte', '299', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('7', '127', '200 g Mix grill z hovězí roštěné, vepřové panenky a kuřecích prsou', 'salátová obloha, bramborové dukáty ', '249', 'bezne', 'hlavni jidlo', 'trvala', NULL),
				('7', '128', 'Míchaný zeleninový salát', 's olivami', '110', 'veganske', 'hlavni jidlo', 'denni', '150'),
				('7', '129', 'Řecká salát', 's černými olivami a sýrem Feta', '159', 'vegetarianske', 'hlavni jidlo', 'denni', '100'),
				('7', '130', '200 g Filet z candáta ', 'pečený na másle, restovaná zelenina, bramborová kaše', '330', 'ryby', 'hlavni jidlo', 'denni', '150'),
				('7', '131', '200 g V mandlové krustě smažený filet z candáta, ', 'vídeňský bramborový salát', '330', 'ryby', 'hlavni jidlo', 'denni', '150'),
				('7', '132', '250 g Grilovaný filet z mořského vlka, tygří kreveta, mušle sv. Jakuba ', 'restovaná zelenina', '390', 'ryby', 'hlavni jidlo', 'trvala', NULL),
				('7', '133', '200 g Wok s mořskými plody ', 'jasmínová rýže', '349', 'ryby', 'hlavni jidlo', 'trvala', NULL),
				('7', '134', 'Pohanka s humusem a pečenou mrkví ', '', '249', 'veganske', 'hlavni jidlo', 'denni', '50'),
				('7', '135', 'Soba nudle s brokolicí, ořechy a tempehem', '', '229', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('7', '136', 'Hummus s červenou řepou', '', '149', 'veganske', 'hlavni jidlo', 'denni', '50'),
				('7', '137', 'Cizrna na paprice s pórkem', '', '235', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				('7', '138', 'Soba nudle s brokolicí, ořechy a tempehem', '', '229', 'veganske', 'hlavni jidlo', 'trvala', NULL),
				   
				--  Ostatní
				('7', '701', '50 g Carpaccio z hovězí svíčkové', 'ochucené sušenými olivami naloženými v panenském olivovém oleji, zdobené rukolou a hoblinkami parmazánu', '199', 'raw', 'predkrm', 'trvala', NULL),
				('7', '702', '100 g Buvolí mozzarella', 'rajčata, olivový olej', '149', 'vegetarianske', 'predkrm', 'trvala', NULL),
				('7', '703', '100 g Sashimi', 'ze žlutoploutvého tunáka', '220', 'raw', 'predkrm', 'trvala', NULL),
				('7', '704', '100 g Tatarák', 'ze žlutoploutvého tunáka', '220', 'raw', 'predkrm', 'trvala', NULL),
				
				-- Dezerty
				('7', '920', 'Makovo - skořicové parfait ', 'šlehačka, marinovaná švestka', '129', 'bezne', 'dezert', 'trvala', NULL),
				('7', '921', 'Čokoládový dortík ', 's malinovým coulis', '129', 'bezne', 'dezert', 'trvala', NULL),
				('7', '922', 'Horké maliny ', 's vanilkovou zmrzlinou, šlehačka', '129', 'bezne', 'dezert', 'trvala', NULL),
				('7', '923', 'Bramborové lokše ', 's povidly', '99', 'bezne', 'dezert', 'trvala', NULL)
				;";

  if ($conn->query($sql) === true) { 
	  echo "Records inserted successfully.<br>"; 
  } 
  else { 
	  echo "ERROR: Could not able to execute $sql. "
		  .$conn->error; 
  } 
  $conn = null;
?>