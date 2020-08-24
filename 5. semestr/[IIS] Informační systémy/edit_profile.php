<?php
	session_start();
	
	// pro vypis html - musime poslat az na konci, pokud chceme posilat heders
	$html_output = "";

	require("connection_credentials.php");
	require("navbar.php");
	include("links.php");
				
	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}

	$result = load_current_data($pdo);

    $html_output .= html_form($result);
    $html_pwd_output .= html_password();

	// Obsluha pro zmenu obecnych udaju
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['button_credentials']) and isset($_POST['jmeno']) and isset($_POST['prijmeni']) and isset($_POST['adresa']) and isset($_POST['telefon']) and isset($_POST['psc']) and isset($_POST['uziv_jmeno']))
	{

		// Aktualizace dat v databazi
		$stmt = $pdo->prepare('
			UPDATE Nereg_uzivatel
			SET jmeno = ?, prijmeni = ?, adresa = ?, psc = ?, telefon = ?
			Where id_uzivatel = ?');
	
		$return_val = $stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['adresa'], $_POST['psc'], $_POST['telefon'], $_SESSION['id']));

		test_stmt_exec($return_val, $stmt);

		// Pokud se zmenilo uzivatelske jmeno, promitneme zmeny i do stravnika
		if($result['uziv_jmeno'] != $_POST['uziv_jmeno'])
		{
			$stmt = $pdo->prepare('
			UPDATE Stravnik
			SET uziv_jmeno = ?
			Where id_uzivatel = ?');
	
			$return_val = $stmt->execute(array($_POST['uziv_jmeno'], $_SESSION['id']));
			test_stmt_exec($return_val, $stmt);
		}

		// Update udaju pro operatora a admina
		if($_SESSION['user'] == 'operator' or $_SESSION['user'] == 'admin')
		{
			$stmt = $pdo->prepare('
			UPDATE Operator_Admin
			SET rodne_cislo = ?, mzda = ?, cislo_uctu = ?
			Where id_uzivatel = ?');
	
			$return_val = $stmt->execute(array($_POST['rodne_cislo'], $_POST['mzda'], $_POST['cislo_uctu'], $_SESSION['id']));
			test_stmt_exec($return_val, $stmt);
		}
		
		$result = load_current_data($pdo);

		if($return_val)
			echo "<p class=\"bg-success\">Data úspešně aktualizována!</p>";

		$html_output = html_form($result);
		$html_pwd_output .= html_password($result);
	}
	// Obsluha pro zmenu hesla
	elseif($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['button_password']) and isset($_POST['heslo1']) and isset($_POST['heslo2']))
	{

		// Kontrola shody hesel
		if($_POST['heslo1'] != $_POST['heslo2'])
		{
			echo "<p class=\"bg-danger\">Zadana hesla nejsou stejna!</p>";
		}
		elseif($_POST['heslo1'] == '')
		{
			echo "<p class=\"bg-warning\">Pro změnu hesla musíte nějaké zadat.</p>";	
		}
		else
		{
			// Aktualizace dat v databazi
			$stmt = $pdo->prepare('
				UPDATE Stravnik
				SET heslo = ?
				Where id_uzivatel = ?');

			echo $_POST['heslo1'];
			$return_val = $stmt->execute(array(password_hash($_POST['heslo1'], PASSWORD_DEFAULT), $_SESSION['id']));
			test_stmt_exec($return_val, $stmt);

			if($return_val)
				echo "<p class=\"bg-success\">Heslo změneno!</p>";
		}

	}

	function load_current_data($pdo)
	{
		if($_SESSION['user'] == 'stravnik' or $_SESSION['user'] == 'ridic')
		{
			$sql_str = '
			SELECT jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo 
			FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel 
			WHERE Nereg_uzivatel.id_uzivatel = ?';
		}
		else
		{
			$sql_str = '
			SELECT jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo, rodne_cislo, mzda, cislo_uctu
			FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel INNER JOIN Operator_Admin ON Stravnik.id_uzivatel = Operator_Admin.id_uzivatel 
			WHERE Nereg_uzivatel.id_uzivatel = ?';
		}

		$stmt = $pdo->prepare($sql_str);
	
		$return_val = $stmt->execute(array($_SESSION['id']));
		test_stmt_exec($return_val, $stmt);

		return $stmt->fetch();
	}

	function html_form($result)
	{
		$jmeno = "this";

		$return_string .= '
			<h3 class="mt-4"> Změna údajů </h3><br>
			<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<label for="jmeno">Jméno:</label>
			<input type="text" name="jmeno" id="jmeno" pattern="[a-zA-ZÀ-ž ]+" value="' . $result["jmeno"] . '">
			<br>
			<label for="prijmeni">Příjmení:</label>
			<input type="text" name="prijmeni" id="prijmeni" pattern="[a-zA-ZÀ-ž ]+" value="' . $result["prijmeni"] . '">
			<br>
			<label for="adresa">Adresa:</label>
			<input type="text" name="adresa" id="adresa" pattern="[a-zA-ZÀ-ž, ]+ [0-9]+[a-z\/A-ZÀ-ž0-9, ]*" value="' . $result["adresa"] . '">
			<br>
			<label for="psc">PSČ:</label>
			<input type="text" name="psc" id="psc" pattern="[0-9]{5}" maxlength="5" value="' . $result["psc"] . '">
			<br>
			<label for="telefon">Telefonní číslo:</label>
			<input type="tel" name="telefon" id="telefon" pattern="((([\+][0-9]{3})|([0-9]{5}))?[0-9]{9})" minlength="9" maxlength="15" value="' . $result["telefon"] . '">
			<br>
			<label for="uziv_jmeno">Uživatelské jméno:</label>
			<input type="text" name="uziv_jmeno" pattern="([a-zA-Z0-9_]+)" id="uziv_jmeno"value="' . $result["uziv_jmeno"] . '">
			<br>';


		if($_SESSION['user'] == 'operator')
		{
			$return_string .= html_operator($result);
		}
		elseif ($_SESSION['user'] == 'admin')
		{
			$return_string .= html_operator($result);
			$return_string .= html_admin($result);
		}


		$return_string .= '
			<input type="submit" class="btn btn-success mt-2 w-100" name="button_credentials" value="Odeslat">
			<br>
			</form>';

		return $return_string;
	}

	function html_operator($result)
	{
		return '
			<label for="rodne_cislo">Rodné číslo:</label>
			<input type="text" name="rodne_cislo" id="rodne_cislo" pattern="([0-9]{6}\/[0-9]{4})" minlength="11" maxlength="11" value="' . $result["rodne_cislo"] . '">
			<br>
			<label for="cislo_uctu">Číslo účtu:</label>
			<input type="text" name="cislo_uctu" pattern="([0-9]{6}([0-9]*)\/([0-9]{4}))" id="cislo_uctu" value="' . $result["cislo_uctu"] . '">
			<br>';
	}

	function html_admin($result)
	{
		return '
			<label for="mzda">Mzda:</label>
			<input type="number" name="mzda" id="mzda" value="' . $result["mzda"] . '">
			<br>';
	}

	function html_password()
	{
		return '
			<h3> Změna hesla </h3><br>
			<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<label for="heslo1">Nové heslo:</label>
			<input type="password" name="heslo1" pattern="([a-zA-Z0-9_]+)" id="heslo1">
			<br>
			<label for="heslo2">Nové heslo znovu:</label>
			<input type="password" name="heslo2" pattern="([a-zA-Z0-9_]+)" id="heslo2">
			<br>
			<input type="submit" class="btn btn-success mt-2 w-100" name="button_password" value="Odeslat">
		</form>';
	}
?>

<body class="gray_body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-1 hidden-xs"></div>
			<div class="col-md-10">
				<h3 class="rest_header text-center m-auto text-uppercase border-info">Úprava profilu</h3>
			</div>
			<div class="col-md-1 hidden-xs"></div>
		</div>
		<div class="row pt-3">
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
			<div class="col-md-4 col-sm-8 edit_profile_div">
				<?php echo $html_output; ?>
			</div>
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
		</div>
		<div class="edit_separator"></div>
		<div class="row pt-3">
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
			<div class="col-md-4 col-sm-8 edit_profile_div">
				<?php echo $html_pwd_output; ?>
			</div>
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
		</div>
	</div>
</body>
