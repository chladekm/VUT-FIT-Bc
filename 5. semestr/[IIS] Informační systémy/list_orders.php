<?php

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
		if (!($_SESSION['user'])) {
			$_SESSION['user'] = NULL;
			$_SESSION['rights'] = NULL;
		}
	}
	
	require("navbar.php");
	include("links.php");

	require("connection_credentials.php");
	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}

	// Vime, ze chce operator potvrdit, zname id objednavky, vyber ridicu:
	if ($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action']) 
		and isset($_GET['confirm_order']) and isset($_GET['assign_driver'])) {
		driver_select($pdo);
	}

	// Potvrzeni objednavky
	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action']) and isset($_GET['confirm_order'])) {
		order_accept($pdo);
	}

	// Zamitnuti objednavky
	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action']) and isset($_GET['decline_order'])) {
		order_decline($pdo);
	}

	// Operator chce oznacit objednavku jako na ceste
	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action']) and isset($_GET['ship_order'])) {
		order_shipment($pdo);
	}

    elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_items_action'])) {
    	show_order_items($pdo);
		echo '<div class="edit_separator"></div>';
    }

	// Zobrazi detailni informace o objednavce
	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action'])) {
		order_detail($pdo);
		echo '<div class="edit_separator"></div>';
    }

    // Zobrazeni vypisu objednavek
	$stmt = $pdo->prepare('SELECT id_objednavka, jmeno, prijmeni, adresa, stav 
		FROM Objednavka NATURAL JOIN Nereg_uzivatel
		WHERE NOT stav = "neodeslana" 
		ORDER BY id_objednavka DESC;');

	if(!($stmt->execute(array($login)))) {
		echo "\nPDOStatement::errorInfo():\n";
		$arr = $stmt->errorInfo();
		print_r($arr);
	}
	$result = $stmt->fetchall();	
	
	$html_output = 
		'<table class="edit_table m-auto w-75">
		<tr>
			<th colspan="2">Číslo</th>
			<th>Stav</th>
			<th>Jméno</th>
			<th>Příjmení</th>
			<th>Adresa</th>
		</tr>';

	foreach ($result as $row) {
    	$html_output .= '<tr><td>'. $row['id_objednavka'] . "</a></td><td><a class=\"btn btn-info order_link\" href=\"?show_order_detail_action=". $row["id_objednavka"]. "\">podrobnosti</a></td><td>" . $row["stav"] ."</td><td>". $row["jmeno"] ."</td><td>". $row["prijmeni"] ."</td><td>". $row["adresa"] ."</td></tr>";
	}

	$html_output .= '</table>';

?>

	<body class="gray_body">
		<div class="container-fluid">
			<div class="text-center">
					<h3 class="mt-4 mb-4">Přehled objednávek</h3>
					<?php echo $html_output; ?>
			</div>
		</div>
	</body>

