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

	// Staci jemno, protoze povinne jsou jiz overeny ve form - HTML
	if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['jmeno'])) {

		//TODO telefon se zatim nevklada, mel by, neamam to ted v databazi TODO
		$stmt = $pdo->prepare('UPDATE Nereg_uzivatel SET jmeno=?, prijmeni=?, adresa=?, psc=? 
			WHERE id_uzivatel=?;');
		if(!($stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['adresa'], $_POST['psc'], $_SESSION['id'])))) {
			echo "\nPDOStatement::errorInfo():\n";
			$arr = $stmt->errorInfo();
			print_r($arr);
		}
		header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username . '/order_confirm.php?'. session_id());
	}

	require("navbar.php");
	include("links.php");

	// Uzivatel je prihlasen
	if ($_SESSION['user']) {
		echo "Prihlasen<br>";
		?>
		<script type="text/javascript">
			window.location.href = '<?php require ("connection_credentials.php"); echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '/order_confirm.php?'. session_id(); ?>';
		</script>
		<?php
	}
	else {
		// Potrebujeme od uzivatele nejake informace pro dokonceni objednavky
		?>

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
					<div class="col-md-8 cart_box">
						<form action="login.php" method="get">
				    		<input class="btn btn-secondary w-100" type="submit" name="login" value="Přihlásit se" />
						</form>
						<div class="text-center">-- NEBO --</div>
							<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
							<div class="row">
								<div class="col-md-3"></div>
								<div class="col-md-2 text-right">
									<label class="mt-2" for="jmeno">Jméno:</label><br>
									<label class="mt-2" for="prijmeni">Příjmení:</label><br>
									<label class="mt-2" for="adresa">Adresa:</label><br>
									<label class="mt-2" for="psc">PSČ:</label><br>
									<label class="mt-2" for="telefon">Telefonní číslo:</label><br>
								</div>
								<div class="col-md-4">
									<input class="mt-2" type="text" name="jmeno" id="jmeno" pattern="[a-zA-ZÀ-ž ]+" required="true" value="<?php echo htmlspecialchars($jmeno); ?>">
									<br>
									<input class="mt-2" type="text" name="prijmeni" id="prijmeni" pattern="[a-zA-ZÀ-ž ]+" required="true" value="<?php echo htmlspecialchars($prijmeni); ?>">
									<br>
									<input class="mt-2" type="text" name="adresa" id="adresa" pattern="[a-zA-ZÀ-ž, ]+ [0-9]+[a-z\/A-ZÀ-ž0-9, ]*" required="true" value="<?php echo htmlspecialchars($adresa); ?>">
									<br>
									<input class="mt-2" type="text" name="psc" id="psc" pattern="[0-9]{5}" maxlength="5" required="true" value="<?php echo htmlspecialchars($psc); ?>">
									<br>
									<input class="mt-2" type="tel" name="telefon" id="telefon" pattern="((([\+][0-9]{3})|([0-9]{5}))?[0-9]{9})" minlength="9" maxlength="15" required="true" value="<?php echo htmlspecialchars($telefon); ?>">
									<br>
								</div>
								<div class="col-md-3"></div>
							</div>
							<div class="text-center mt-4">
								<input class="btn btn-success m-auto w-50" type="submit" value="Pokračovat" <?php $fp = fopen('close_orders_log', 'r');
																														if (!$fp) {
																															echo 'Could not open file needed.';
																														}
																														$c = fgetc($fp);
																														if ($c == '1') {
																															echo "disabled";
																														}?>>
							</div>
							</form>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="col-md-2"></div>
				</div>
			</div>
		</body>'; 
		<?php
	}

?>