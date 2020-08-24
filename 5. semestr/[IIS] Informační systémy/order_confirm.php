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

	require("navbar.php");
	include("links.php");

	// SELECT vsechny polozky, ktere ma uzivatel v kosiku
	$stmt = $pdo->prepare('SELECT id_polozka, id_objednavka_polozka, nazev, cena, cas_platnosti, pocet FROM Polozka NATURAL JOIN Objednavka_je_Tvorena
		WHERE Objednavka_je_Tvorena.id_objednavka = ?;');
	if(!($stmt->execute(array($_SESSION['order_id'])))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}
	$result = $stmt->fetchall();


	// Zobrazeni udaju uzivatele pro dodani objednavky
	$stmt = $pdo->prepare('SELECT jmeno, prijmeni, adresa, psc FROM Nereg_uzivatel
		WHERE id_uzivatel = ?;');
	if(!($stmt->execute(array($_SESSION['id'])))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}


	$user_info = $stmt->fetch();

	$is_available = true;
	// Zobrazeni obsahu objednavky
	html_output($result, $user_info);

	function html_output($result, $user_info)
	{
		html_start();

		$total_price = 0;
		global $is_available;

		echo '
		<div class="row p-0">
			<div class="col-md-6" style="padding: 0 2vw;">
			<h4 class="pb-3">Objednávka</h4>';

		foreach ($result as $row)
		{
			
			if(strlen($row["nazev"]) > 30)
			{
				$pos = strpos($row["nazev"], ' ', 25);
				$nazev = substr($row["nazev"] , 0, $pos);
				$nazev .= '...';
			}
			else
			{
				$nazev = $row['nazev'];
			}

			if ($row['cas_platnosti'] == "denni" and $row['pocet'] == 0)
				$is_available = false;

			echo '
				<div class="row cart_item mb-2">
					<div class="col-md-10 p-0">
						<span class="cart_item_name">' . $row["nazev"]. '</span>
					</div>
					<div class="col-md-2 text-right align-bottom">' . $row["cena"] . ' Kč';
						
			if (!$is_available)
				echo "<br>Vyprodáno<br>";
					
			echo	'</div>
				</div>';

			$total_price += $row["cena"];
		}


		echo '</div>
			<div class="col-md-6" style="padding: 0 2vw;">
			<h4 class="pb-3">Údaje</h4>

			<div class="row mb-2">
				<div class="col">Jméno: <span class="cart_item_name">'. $user_info['jmeno'] .'</span></div>
			</div>
			<div class="row mb-2">
				<div class="col">Příjmení: <span class="cart_item_name">'. $user_info['prijmeni'] .'</span></div>
			</div>
			<div class="row mb-2">
				<div class="col">Adresa: <span class="cart_item_name">'. $user_info['adresa'] .'</span></div>
			</div>
			<div class="row mb-2">
				<div class="col">PSČ: <span class="cart_item_name">'. $user_info['psc'] .'</span></div>
			</div>';

		if($_SESSION['user'])
		{
			echo '
			<div class="row mb-2">&nbsp;</div>
			<div class="row mb-2">
				<div class="col"><a class="text-info" href="edit_profile.php?">Upravit údaje profilu</a></div>
			</div>
			';
		}
	

		echo '</div>
		</div>';

		echo'
		<div class="row p-0 mt-4">
			<div class="col-md-8 p-0 m-auto">
				<span class="cart_price"><strong>Celková cena: </strong> '. $total_price .' Kč</span>
			</div>
			<div class="col-md-4 p-0 m-auto">
				<form class="d-block m-auto" action="order_confirm.php" method="get">';
		$fp = fopen('close_orders_log', 'r');
		if (!$fp) {
			echo 'Could not open file needed.';
		}
		$c = fgetc($fp);
		if ($is_available and ($c == '0')) {
			echo '<input class="btn btn-success w-100" type="submit" name="confirm_order" value="Objednat" />
				<input type="hidden" name="id_polozka" value="'. $row['id_polozka']. '">
				<input type="hidden" name="cas_platnosti" value="'. $row['cas_platnosti']. '">';
		}
		else if ($c == '1'){
			echo '<input class="btn btn-danger w-100" type="submit" name="confirm_order" value="Nelze provést" disabled/>';
		}
		else {	
			echo '<input class="btn btn-danger w-100" type="submit" name="confirm_order" value="Odeberte vyprodané položky" disabled/>';
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
						<h3 class="rest_header text-center m-auto text-uppercase">Potvrzení objednávky</h3>
					</div>
					<div class="col-md-2"></div>
				</div>
				<div class="row pt-3">
					<div class="col-md-2"></div>
					<div class="col-md-8 cart_box">';
	}

	function html_end()
	{
		echo '
					</div>
					<div class="col-md-2"></div>
				</div>
			</div>
		</body>'; 
	}

	if($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['confirm_order']) and $is_available) {

		require ("connection_credentials.php");
		
		if ($_GET['cas_platnosti'] == "denni") {
			$stmt = $pdo->prepare('UPDATE Polozka SET pocet=pocet-1 WHERE id_polozka=?;');
			if(!($stmt->execute(array($_GET['id_polozka'])))) {
				echo "\nPDOStatement::errorInfo():\n";
				$arr = $stmt->errorInfo();
				print_r($arr);
			}
		}

		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="odeslana" WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_SESSION['order_id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$_SESSION['order_id'] = NULL;
		?>
			<script type="text/javascript">
				window.location.href = '<?php require ("connection_credentials.php"); echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '?'. session_id(); ?>';
			</script>
		<?php
	}

?>