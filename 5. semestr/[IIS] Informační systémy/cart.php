<?php

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

	// Presmerovani na stranku pro shrnuti objednavky
	if($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['send_order'])) {	
		require ("connection_credentials.php");
		header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username . '/order.php?'. session_id());
	}

	if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['button_delete'])) {	

		$stmt = $pdo->prepare('DELETE FROM Objednavka_je_Tvorena WHERE id_objednavka_polozka=?');

		if(!($stmt->execute(array($_POST['id_objednavka_polozka'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

	require("navbar.php");
	include("links.php");

	$item_id = $_SESSION['item_id'];
	$_SESSION['item_id'] = NULL;

	function show_order_price ($pdo) {
		$stmt = $pdo->prepare('SELECT cena FROM Objednavka WHERE id_objednavka = ?;');
		if(!($stmt->execute(array($_SESSION['order_id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();

		return $result["cena"];
	}

	// Zobrazeni pokud uz mame id objednavky a nepridavame zadnou polozku
	if (!($item_id)) {
		$stmt = $pdo->prepare('SELECT id_objednavka_polozka, cas_platnosti, pocet, nazev, cena FROM Polozka NATURAL JOIN Objednavka_je_Tvorena
		WHERE Objednavka_je_Tvorena.id_objednavka = ?;');
		if(!($stmt->execute(array($_SESSION['order_id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetchall();
		
		if ($_SESSION['order_id'])
		{
			show_order_price($pdo);
			html_output($result);
		}
		else
			html_output(NULL);
		return;
	}

	// Pridani polozky do objednavky------------------------------------------------------
	// Vytvoreni uzivatele pro ucel prideleni objednavky, pokud neni prihlasen a nema uz id
	if (!($_SESSION['id'])) {
		$stmt = $pdo->prepare('INSERT INTO Nereg_uzivatel () 
			VALUES ()');
		if(!($stmt->execute(array($_SESSION['id'], $result['cena'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		// Zjisteni ID objednavky, ktera byla prave vytvorena
		$_SESSION['id'] = $pdo->lastInsertId();
	}

	// Existuje vubec uzivatel a ma uz objednavku?
	$stmt = $pdo->prepare('SELECT id_objednavka, cena FROM Objednavka WHERE id_uzivatel=? AND stav="neodeslana"');
	if(!($stmt->execute(array($_SESSION['id'])))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}
	$result = $stmt->fetch();

	// Objednavka jeste neexistuje, takze ji vytvorime
	if (!$result) {
		$stmt = $pdo->prepare('SELECT cena FROM Polozka WHERE id_polozka=?');
		if(!($stmt->execute(array($item_id)))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();

		// Vytvorit novou objednavku, zadnou uzivatel zatim nema
		$stmt = $pdo->prepare('INSERT INTO Objednavka (id_uzivatel, stav, cena) 
			VALUES (?, "neodeslana", ?)');
		if(!($stmt->execute(array($_SESSION['id'], $result['cena'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		// Zjisteni ID objednavky, ktera byla prave vytvorena
		$order_id = $pdo->lastInsertId();
	}
	else {
		// Ulozime hodnoty do pomoc. promennych
		$cena = $result['cena'];
		$order_id = $result['id_objednavka'];

		$stmt = $pdo->prepare('SELECT cena FROM Polozka WHERE id_polozka=?');
		if(!($stmt->execute(array($item_id)))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();

		$cena += $result['cena'];
		// Aktualizuje objednavku, uzivatel jiz mel objednavku vytvorenou
		$stmt = $pdo->prepare('UPDATE Objednavka SET cena = ? WHERE id_objednavka = ?;');
		if(!($stmt->execute(array($cena, $order_id)))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

	// Pridani polozky do objednavky
	$stmt = $pdo->prepare('INSERT INTO Objednavka_je_Tvorena (id_objednavka, id_polozka) 
		VALUES (?, ?)');
	if(!($stmt->execute(array($order_id, $item_id)))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}

	// SELECT vsechny polozky, ktere ma uzivatel v kosiku
	$stmt = $pdo->prepare('SELECT id_objednavka_polozka, cas_platnosti, pocet, nazev, cena FROM Polozka NATURAL JOIN Objednavka_je_Tvorena
		WHERE Objednavka_je_Tvorena.id_objednavka = ?;');
	if(!($stmt->execute(array($order_id)))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}
	$result = $stmt->fetchall();
	$_SESSION['order_id'] = $order_id;

	
	// foreach ($result as $row) {
	// 	echo "XX <strong>" . $row["nazev"]. "</strong><br>" . $row["cena"] . " Kc<br>";
	// }

	
	show_order_price($pdo);

	// Zobrazeni obsahu objednavky
	html_output($result);


	function html_output($result)
	{
		if($result == NULL)
		{
			html_start();
			echo "<div class=\"text-center\">Váš nákupní košík je prázný</div>";
			html_end();
			return;
		}

		html_start();
		$total_price = 0;
		$is_available = true;

		foreach ($result as $row)
		{
			$is_available = true;

			if ($row['cas_platnosti'] == "denni" and $row['pocet'] == 0)
				$is_available = false;

			echo '
				<div class="row cart_item mb-2">
					<div class="col-md-10 p-0">
						<form class="d-inline-block" action="' . $_SERVER['PHP_SELF'] . '" method="post">
							<input type="submit" class="mr-2 btn btn-danger" name="button_delete" value="Smazat">
							<input type="hidden" name="id_objednavka_polozka" value="'.$row["id_objednavka_polozka"].'">
						</form>';

		if (!$is_available)
			echo "<span class=\" d-inline-block mr-2 btn btn-secondary disabled\">Vyprodáno</span>";

			echo		'<span class="cart_item_name">' . $row["nazev"]. '</span>
					</div>
					<div class="col-md-2 text-right align-bottom">' . $row["cena"] . ' Kč';
					
						
					
			echo	'
					</div>
				</div>';

			$total_price += $row['cena'];
		}

		echo'
		<div class="row p-0 mt-4">
			<div class="col-md-8 p-0 m-auto">
				<span class="cart_price"><strong>Celková cena: </strong> '. $total_price .' Kč</span>
			</div>
			<div class="col-md-4 p-0 m-auto">
				<form class="d-block m-auto" action="cart.php" method="get">';			
		$fp = fopen('close_orders_log', 'r');
		if (!$fp) {
  		 	echo 'Could not open file needed.';
		}
		$c = fgetc($fp);
		if ($is_available and ($c=='0')) {
			echo '<input class="btn btn-success w-100" type="submit" name="send_order" value="Pokračovat v objednání" />';
		}
		else if ($c=='1'){
			echo '<input class="btn btn-success w-100" type="submit" name="send_order" value="Nelze provést" disabled/>';			
		}
		else {	
			echo '<input class="btn btn-success w-100" type="submit" name="send_order" value="Pokračovat v objednání" disabled/>';
		}
		
		echo '
				</form>
			</div>
		</div>';

		html_end();
	}

	function html_start()
	{
		echo '
		<body class="gray_body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-2"></div>
					<div class="col-md-8">
						<h3 class="rest_header text-center m-auto text-uppercase">Nákupní košík</h3>
					</div>
					<div class="col-md-2"></div>
				</div>
				<div class="row pt-3">
					<div class="col-md-2"></div>
					<div class="col-md-8 cart_box">
						';
	}

	function html_end()
	{
		echo '		</div>
					<div class="col-md-2"></div>
				</div>
			</div>
		</body>'; 
	}
?>



