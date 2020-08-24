<?php

	$html_btn="";

	// Funkce pro navazani spojeni se serverem
	function connect_to_server()
	{
	  	require("connection_credentials.php");
		$conn = mysqli_init();
		if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
			die('cannot connect '.mysqli_connecterror());
		}
  		return $conn;
	}

	function test_stmt_exec($return_val, $stmt) {
		if(!$return_val)
		{
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

	function driver_select ($pdo) {

		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="potvrzena" WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}

		$stmt = $pdo->prepare('INSERT INTO Operator_prirazuje_Objednavku (id_operator, id_objednavka, id_ridic)
			VALUES (?, ?, ?);');
		if(!($stmt->execute(array($_SESSION['id'], $_GET['show_order_detail_action'], $_GET['assign_driver'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
	}

	function order_accept ($pdo) {
		$stmt = $pdo->prepare('SELECT jmeno, prijmeni, id_uzivatel 
			FROM Nereg_uzivatel NATURAL JOIN Ridic;');
		if(!($stmt->execute())) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetchall();

		global $html_btn;

		$html_btn .='
			<form class="mt-2 text-center" action="'. $_SERVER['PHP_SELF'] .'" method="get">
			   	<select class="mt-2 mb-2 order_select" name="assign_driver">';
      			
      			foreach ($result as $row) {
	      			$html_btn .= '<option value="' . $row['id_uzivatel'] . '">' . $row['jmeno'] . '&nbsp' . $row['prijmeni'] . '</option>';
	      		}
			      		
			   	$html_btn .= '</select>
			   	<br>
			   	<input class="mt-2 order_button btn btn-secondary" type="submit" name="hide_order" value="Zrušit" />
			   	<input class="mt-2 order_button btn btn-success" type="submit" name="confirm_order" value="Přiřadit" />
			   	<input type="hidden" name="show_order_detail_action" value='. $_GET['show_order_detail_action'] .'>
			</form>
			';

		$stmt = $pdo->prepare('SELECT jmeno, prijmeni, adresa, PSC, stav 
			FROM Objednavka NATURAL JOIN Nereg_uzivatel
			WHERE id_objednavka=?;');

		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();
	
		echo '
		<h3 class="text-center mt-4 mb-4">Vybraná objednávka</h3>
		<table class="edit_table m-auto w-50 text-center">
		<tr>
			<th class="text-center">Jméno</th>
			<th class="text-center">Adresa</th>
			<th class="text-center">PSČ</th>
			<th class="text-center">Stav</th>
		</tr><tr>
			<td>' . $result['jmeno'] . '&nbsp;' . $result['prijmeni'] . '</td><td>', 
			$result['adresa'] .'</td><td>'. $result['PSC'] .'</td><td>'. 
			$result['stav'] . '</td>
		</tr></table>';

		echo $html_btn;
	}

	function order_decline ($pdo) {
		
		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="zamitnuto" WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}	
	}

	function order_shipment ($pdo) {
		
		$stmt = $pdo->prepare('UPDATE Objednavka SET stav="pripravena" WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}	
	}

	function order_detail ($pdo) {
		
		require "connection_credentials.php";

		$stmt = $pdo->prepare('SELECT jmeno, prijmeni, adresa, PSC, stav 
			FROM Objednavka NATURAL JOIN Nereg_uzivatel
			WHERE id_objednavka=?;');

		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();

		global $html_btn;

		$control_site_name = '/~'. $username .'/list_orders.php';

		if ($_SESSION['user'] == "operator" or $_SESSION['user'] == "admin" and $_SERVER['PHP_SELF'] == $control_site_name) {
			if ($result['stav'] == "odeslana") {
				$html_btn .='
				<form class="mt-2 text-center" action="' . $_SERVER['PHP_SELF'] .'" method="get">
				   	<input class="mt-2 order_button btn btn-danger" type="submit" name="decline_order" value="Zamítnout" />
				   	<input class="mt-2 order_button btn btn-success" type="submit" name="confirm_order" value="Potvrdit" />
				   	<input type="hidden" name="show_order_detail_action" value=' . $_GET['show_order_detail_action'] .'>
				</form>';
			}

			elseif ($result['stav'] == "potvrzena") {
				$html_btn .='
				<form class="mt-2 text-center" action="' . $_SERVER['PHP_SELF'] .'" method="get">
				   	<input class="mt-2 order_button btn btn-success" type="submit" name="ship_order" value="Objednávka připravena" />
				   	<input type="hidden" name="show_order_detail_action" value=' . $_GET['show_order_detail_action'] .'>
				</form>';
			}
		}	

		$html_output .= '
		<h3 class="mt-4 mb-4 text-center">Vybraná objednávka</h3>
		<table class="edit_table m-auto w-50 text-center">
		<tr>
			<th class="text-center">Jméno</th>
			<th class="text-center">Adresa</th>
			<th class="text-center">PSČ</th>
			<th class="text-center">Stav</th>
			<th class="text-center">Řidič</th>
			<th class="text-center">Položky</th>
		</tr>';

		$html_output .=  "<tr><td>" . $result['jmeno'] .'&nbsp;'. $result['prijmeni'] .'</td><td>'. $result['adresa'] .'</td><td>'. $result['PSC'] .'</td><td>'. 
			$result['stav'] .'</td><td>';

		$stmt = $pdo->prepare('SELECT jmeno, prijmeni 
			FROM Operator_prirazuje_Objednavku INNER JOIN Nereg_uzivatel
			ON Operator_prirazuje_Objednavku.id_ridic = Nereg_uzivatel.id_uzivatel
			WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_GET['show_order_detail_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetch();	
		
		if($result)
			$html_output .= $result['jmeno'] . '&nbsp'. $result['prijmeni'];
		else
			$html_output .= "-";

		$html_output .= '</td><td><a class="btn btn-info order_link" href="?show_order_items_action='. $_GET['show_order_detail_action']. '&show_order_detail_action='. $_GET['show_order_detail_action'].'">Zobrazit</a></td></tr></table>';

		echo $html_output;
		echo $html_btn;
	}


	function show_order_items($pdo)
	{
		order_detail($pdo);

		$stmt = $pdo->prepare('SELECT nazev, cena FROM Objednavka_je_Tvorena NATURAL JOIN Polozka
			WHERE id_objednavka=?;');
		if(!($stmt->execute(array($_GET['show_order_items_action'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		$result = $stmt->fetchall();
		
		echo '<h4 class="mt-4 mb-4 text-center">Položky objednávky</h4>
			<table class="edit_table m-auto w-50 text-center">
			<tr>
				<th class="text-center">Název</th>
				<th class="text-center">Cena</th>
			</tr>';
		foreach ($result as $row) {
				echo "<tr><td>", $row['nazev'], "</td><td>", $row['cena'], "</td></tr>";
			}
		echo "</table>";		
	}
?>