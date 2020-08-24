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
	if($_SESSION['user'] != 'stravnik' and $_SESSION['user'] != 'operator' and $_SESSION['user'] != 'admin' and $_SESSION['user'] != 'ridic')
	{
		?>
			<script type="text/javascript">
				window.location.href = '<?php require ("connection_credentials.php"); echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '/index.php'; ?>';
			</script>
		<?php
	}


    if($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_items_action'])) {
    	show_order_items($pdo);
    }
    
	elseif($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_order_detail_action'])) {
		order_detail($pdo);
    }

    $stmt = $pdo->prepare('SELECT id_objednavka, Nereg_uzivatel.jmeno, Nereg_uzivatel.prijmeni, adresa, psc, stav 
		FROM Objednavka INNER JOIN Nereg_uzivatel 
		ON Objednavka.id_uzivatel = Nereg_uzivatel.id_uzivatel
		WHERE Objednavka.id_uzivatel=? 
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
				<h3 class="rest_header text-center m-auto text-uppercase" style="border-color: #01A9A9;">Všechny vaše objednávky</h3>
				<div class="pt-4"></div>
<?php
	$i = count($result);
	if($i != 0)
	{
			echo '<table class="edit_table m-auto w-50">
				<tr>
					<th colspan="2">Číslo</th>
					<th>Adresa</th>
					<th>PSČ</th>
					<th>Stav</th>
				</tr>';
	}
	else
	{
		echo '<p>Doposud jste nevytvořili žádné objednávky</p>';
	}
	
	foreach ($result as $row) {
    	echo '<tr><td>' . $i .'</td><td><a class="btn btn-info order_link" href="?show_order_detail_action='. $row["id_objednavka"]. '">podrobnosti</a></td><td>'. $row["adresa"] .'</td><td>'. $row["psc"] .'</td><td>'. $row["stav"] . "</td></tr>";
		$i--;
	}

	if(count($result)!=0)
		echo '</table>';
?>
		</div>
		<div class="pb-4"></div>
	</div>
</body>