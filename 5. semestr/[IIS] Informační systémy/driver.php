<?php

	require("navbar.php");
	include("links.php");

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
		if (!($_SESSION['user'])) {
			$_SESSION['user'] = NULL;
			$_SESSION['rights'] = NULL;
		}
	}

	require("connection_credentials.php");

	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}

	// Uzivatel je neni ridic, nema tu co delat -> presmerovat na domovskou stranku
	if($_SESSION['user'] != 'ridic')
	{
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/index.php");	
	}


	if($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['shipping'])) {
		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="na ceste" WHERE id_objednavka=?;');

		if(!($stmt->execute(array($_GET['order_id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['done'])) {
		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="dorucena" WHERE id_objednavka=?;');

		if(!($stmt->execute(array($_GET['order_id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

    elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_items_action'])) {
    	show_order_items($pdo);
    }

	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action'])) {
		order_detail($pdo);
    }


	// Zobrazeni vypisu objednavek
	$stmt = $pdo->prepare('SELECT id_objednavka, Nereg_uzivatel.jmeno, Nereg_uzivatel.prijmeni, adresa, stav 
		FROM Operator_prirazuje_Objednavku NATURAL JOIN Objednavka INNER JOIN Nereg_uzivatel 
		ON Objednavka.id_uzivatel = Nereg_uzivatel.id_uzivatel
		WHERE Operator_prirazuje_Objednavku.id_ridic=? 
		ORDER BY id_objednavka DESC;');

	if(!($stmt->execute(array($_SESSION['id'])))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}
	$result = $stmt->fetchall();	
?>

	<body class="gray_body">
		<div class="container-fluid">
			<div class="text-center">
				

<?php
	$i = count($result);
	if($i != 0)
	{
			echo '
				<h3 class="mt-4 mb-4">Přehled objednávek</h3>
				<table class="edit_table m-auto w-75">
				<tr>
					<th colspan="2">Číslo</th>
					<th>Stav</th>
					<th>Jméno</th>
					<th>Příjmení</th>
					<th>Adresa</th>
					<th>&nbsp;</th>
				</tr>';
	}
	else
	{
		echo '
		<h3 class="mt-4 mb-4">Přehled objednávek</h3>
		<p>Nemáte přiřazeny žádné objednávky</p>';
	}

	$j=0;

	foreach ($result as $row) {
    	echo '<tr><td>'. $row['id_objednavka'] . "</a></td><td><a class=\"btn btn-info order_link\" href=\"?show_order_detail_action=". $row["id_objednavka"]. "\">podrobnosti</a></td><td>" .$row["stav"] ."</td><td>". $row["jmeno"] ."</td><td>". $row["prijmeni"] ."</td><td>". $row["adresa"] ."</td><td>";

    // foreach ($result as $row) {
    // 	echo "<a href=\"?show_order_detail_action=". $row["id_objednavka"]. "\">Objednavka". $row['id_objednavka']. "</a>
    // 		&nbsp". $row["stav"]. "&nbsp". $row["jmeno"]. "&nbsp". $row["prijmeni"]. "&nbsp". $row["adresa"];


		if ($row['stav'] == 'pripravena') {
			?>
				<form class="p-0 m-auto" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
					<input class="btn btn-info" type="submit" name="shipping" value="Převzít zásilku">
					<input type="hidden" name="order_id" value="<?php echo $row['id_objednavka'];?>">
				</form>	
				
			</td></tr>
			<?php

		}
		elseif ($row['stav'] == 'na ceste') {
			?>
				<form class="p-0 m-auto" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
					<input class="btn btn-success" type="submit" name="done" value="Zásilka doručena">
					<input type="hidden" name="order_id" value="<?php echo $row['id_objednavka'];?>">
				</form>	
			</td></tr>
			<?php
		}
		else 
			echo '<span class="btn btn-secondary disabled">Dokončeno</span>';
	}

	echo "</table>"
?>

		</div>
	</div>
</body>