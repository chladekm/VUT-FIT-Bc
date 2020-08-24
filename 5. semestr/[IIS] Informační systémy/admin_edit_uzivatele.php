<?php
	session_start();
	
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


	// Uzivatel je neni ani admin ani operator, nema tu co delat -> presmerovat na domovskou stranku
	if(($_SESSION['user'] != 'operator') and ($_SESSION['user'] != 'admin'))
	{
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/index.php");	
	}
	if($_SESSION['user'] == 'operator')
	{
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/operator_edit.php");
	}

	// ---------------------------- ZVOLENE OPERACE ----------------------------

	// Zobrazit list
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_list']))
	{
		$stmt = $pdo->prepare("SELECT jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo FROM Nereg_uzivatel NATURAL JOIN Stravnik");
 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);
		$result = $stmt->fetchall();

		$html_output = 
		'<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-10">
		<table class="edit_table">
		<tr>
			<th>Jméno</th>
			<th>Příjmení</th>
			<th>Adresa</th>
			<th>PSČ</th>
			<th>Telefon</th>
			<th>Uživatelské jméno</th>
		</tr>';

		foreach ($result as $row) {
			$html_output .= '<tr><td>' . $row["jmeno"] .'</td><td>'. $row["prijmeni"] .'</td><td>'. $row["adresa"] .'</td><td>'. $row["psc"] .'</td><td>'. $row["telefon"] .'</td><td>'. $row["uziv_jmeno"]."</td></tr>";
		}

		$html_output .= '
		</div>
		<div class="col-md-1"></div>
		</div>';
	}

	// Pridat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_add']))
	{
		$html_output .= html_uzivatel_radiobuttons(NULL);
	}

	// Upravit
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_edit']))
	{
		$html_output .= html_select($pdo, 'edit', NULL);
		// $html_output .= html_uzivatel_form(NULL, "Provést změny", "edit");
	}

	// Smazat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_delete']))
	{	
		$html_output .= html_select($pdo, 'delete', NULL);
		// $html_output .= html_uzivatel_form(NULL, "Opravdu smazat", "delete");
	}

	// ---------------------------- VYKONANI OPERACE ----------------------------

	// Vykonani pridani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_confirm_add']))
	{
		// Vlozeni dat do Nereg_uzivatel
		$stmt = $pdo->prepare("INSERT into Nereg_uzivatel (jmeno,prijmeni,adresa,psc,telefon) VALUES (?,?,?,?,?);");
 					
		$return_val = $stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['adresa'], $_POST['psc'], $_POST['telefon']));
		test_stmt_exec($return_val, $stmt);
		$id = $pdo->lastInsertId();

		// Vlozeni dat do Stravnik
		$stmt = $pdo->prepare("INSERT into Stravnik (id_uzivatel,uziv_jmeno,heslo) VALUES (?,?,?);");
		$return_val = $stmt->execute(array($id, $_POST['uziv_jmeno'], password_hash($_POST['heslo'], PASSWORD_DEFAULT)));
		test_stmt_exec($return_val, $stmt);
		
		if($return_val and $_POST['user_type'] == 'stravnik')
			echo "<p class=\"info-success\">Záznam byl úspešně vytvořen!</p>";

		if($_POST['user_type'] == 'ridic')
		{
			$stmt = $pdo->prepare("INSERT into Ridic (id_uzivatel,licence) VALUES (?,?);");
 			echo $_POST['licence'];

			$return_val = $stmt->execute(array($id, $_POST['licence']));
			test_stmt_exec($return_val, $stmt);

			if($return_val)
				echo "<p class=\"info-success\">Záznam byl úspešně vytvořen!</p>";
		}

		if($_POST['user_type'] == 'operator' || $_POST['user_type'] == 'admin')
		{
			$stmt = $pdo->prepare("INSERT into Operator_Admin (id_uzivatel,rodne_cislo,mzda,cislo_uctu,je_admin) VALUES (?,?,?,?,?);");
 					
			$return_val = $stmt->execute(array($id, $_POST['rodne_cislo'], $_POST['mzda'], $_POST['cislo_uctu'], $_POST['je_admin']));
			test_stmt_exec($return_val, $stmt);

			if($return_val)
				echo "<p class=\"info-success\">Záznam byl úspešně vytvořen!</p>";
		}
	}

	// ************************************************************************************
	// Vykonani aktualizace
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_confirm_edit']))
	{
		$stmt = $pdo->prepare('
			UPDATE Nereg_uzivatel
			SET jmeno = ?, prijmeni = ?, adresa = ?, psc = ?, telefon = ?
			Where id_uzivatel = ?');
	
		$return_val = $stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['adresa'], $_POST['psc'], $_POST['telefon'], $_POST['id_uzivatel']));
		test_stmt_exec($return_val, $stmt);

		// Strávník, Ridic, Operator, Admin
		if($POST_['user_type'] != 'nereg_uzivatel')
		{
			$stmt = $pdo->prepare('
				UPDATE Stravnik
				SET uziv_jmeno = ?, heslo = ?
				Where id_uzivatel = ?');

			$return_val = $stmt->execute(array($_POST['uziv_jmeno'], password_hash($_POST['heslo'], PASSWORD_DEFAULT), $_POST['id_uzivatel']));
			test_stmt_exec($return_val, $stmt);

			// Ridic
			if(($_POST['user_type'] == 'ridic'))
			{
				$stmt = $pdo->prepare('
				UPDATE Ridic
				SET licence = ?
				Where id_uzivatel = ?');

				$return_val = $stmt->execute(array($_POST['licence'], $_POST['id_uzivatel']));
				test_stmt_exec($return_val, $stmt);

				if($return_val)
					echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";
			}
			// Operator, Admin
			elseif(($_POST['user_type'] == 'operator') or ($_POST['user_type'] == 'admin'))
			{
				$stmt = $pdo->prepare('
				UPDATE Operator_Admin
				SET rodne_cislo = ?, mzda = ?, cislo_uctu = ?, je_admin = ?
				Where id_uzivatel = ?');

				$return_val = $stmt->execute(array($_POST['rodne_cislo'], $_POST['mzda'], $_POST['cislo_uctu'], $_POST['je_admin'], $_POST['id_uzivatel']));
				test_stmt_exec($return_val, $stmt);

				if($return_val)
					echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";
			}
			else
			{
				if($return_val)
					echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";
			}
		}
		else
		{
			if($return_val)
				echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";
		}

		$user_type = $_POST['user_type'];

		if($user_type == 'operator' and ($_POST['je_admin'] == TRUE))
			$user_type = 'admin';
		elseif($user_type == 'admin' and !($_POST['je_admin']))
			$user_type = 'operator';

		$result = load_current_data($pdo, $user_type, $_POST['id_uzivatel']);

		$html_output .= html_select($pdo, 'edit', $_POST['id_uzivatel']);
		$html_output .= html_uzivatel_form($result, "Provést změny", "edit", $user_type);
	}

	// ************************************************************************************
	// Vykonani smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_confirm_delete']))
	{
		if(($POST_['user_type'] == 'operator') or ($POST_['user_type'] == 'admin'))
		{
			$stmt = $pdo->prepare('DELETE FROM Operator_Admin WHERE id_uzivatel = ?;');
			$return_val = $stmt->execute(array($_POST['id_uzivatel']));
			test_stmt_exec($return_val, $stmt);
		}
		elseif($POST_['user_type'] == 'ridic')
		{
			$stmt = $pdo->prepare('DELETE FROM Ridic WHERE id_uzivatel = ?;');
			$return_val = $stmt->execute(array($_POST['id_uzivatel']));
			test_stmt_exec($return_val, $stmt);
		}
		
		if(($POST_['user_type'] == 'stravnik') or ($POST_['user_type'] == 'operator') or ($POST_['user_type'] == 'admin'))
		{
			$stmt = $pdo->prepare('DELETE FROM Stravnik WHERE id_uzivatel = ?;');
			$return_val = $stmt->execute(array($_POST['id_uzivatel']));
			test_stmt_exec($return_val, $stmt);
		}
		
		$stmt = $pdo->prepare('DELETE FROM Nereg_uzivatel WHERE id_uzivatel = ?;');
		$return_val = $stmt->execute(array($_POST['id_uzivatel']));
		test_stmt_exec($return_val, $stmt);

		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně smazán!</p>";
	}

	// ---------------------------- VYBER uzivatele ----------------------------	

	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_radiobutton_submit']))
	{
		$html_output .= html_uzivatel_radiobuttons($_POST['user']);
		$html_output .= html_uzivatel_form(NULL, "Přidat uživatele", "add", $_POST['user']);
	}

	// Vyber pro upravu
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_select_edit']))
	{
		$user_type = who_are_you($pdo, $_POST['select_user']);
		$result = load_current_data($pdo, $user_type, $_POST['select_user']);

		$html_output .= html_select($pdo, 'edit', $_POST['select_user']);
		$html_output .= html_uzivatel_form($result, "Provést změny", "edit", $user_type);
	}

	// Vyber pro smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['uzivatele_button_select_delete']))
	{
		$user_type = who_are_you($pdo, $_POST['select_user']);
		$result = load_current_data($pdo, $user_type, $_POST['select_user']);

		$html_output .= html_select($pdo, 'delete', $_POST['select_user']);
		$html_output .= html_uzivatel_form($result, "Opravdu smazat", "delete", $user_type);
	}

	
	// Zvoleni operace
	function html_operations()
	{
		$selected_section = 'uzivatele';

		return '
		<div class="container-fluid">
		<h3 class="text-center mb-4 mt-2">Správa uživatelů</h3>
		<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-2">
					<input type="submit" class="mt-2 btn btn-dark p-3 w-100" name="'. $selected_section .'_button_list" value="Zobrazit list">
				</div>
				<div class="col-md-2">
					<input type="submit" class="mt-2 btn btn-success p-3 w-100" name="'. $selected_section .'_button_add" value="Přidat">
				</div>
				<div class="col-md-2">
					<input type="submit" class="mt-2 btn btn-info p-3 w-100" name="'. $selected_section .'_button_edit" value="Upravit">
				</div>
				<div class="col-md-2">
					<input type="submit" class="mt-2 btn btn-danger p-3 w-100" name="'. $selected_section .'_button_delete" value="Smazat">
				</div>
				<div class="col-md-2"></div>
			</div>
		</form>
		</div>';
	}

	// Funkce zjisti, zda je na danem id stravnik/operator/admin
	function who_are_you($pdo, $id)
	{
		$stmt = $pdo->prepare('SELECT id_uzivatel, je_admin FROM Operator_Admin WHERE id_uzivatel = ?');
		$return_val = $stmt->execute(array($id));
		test_stmt_exec($return_val, $stmt);
		$result = $stmt->fetch();
		
		if($result)
		{
			if ($result['je_admin'] == TRUE)
				return "admin";
			else
				return "operator";
		}
		else
		{
			$stmt = $pdo->prepare('SELECT id_uzivatel FROM Ridic WHERE id_uzivatel = ?');
			$return_val = $stmt->execute(array($id));
			test_stmt_exec($return_val, $stmt);
			$result = $stmt->fetch();

			if($result)
				return "ridic";
			else
			{
				$stmt = $pdo->prepare('SELECT id_uzivatel FROM Stravnik WHERE id_uzivatel = ?');
				$return_val = $stmt->execute(array($id));
				test_stmt_exec($return_val, $stmt);
				$result = $stmt->fetch();
				if($result)
				{
					return "stravnik";
				}
				else
				{
					return "nereg_uzivatel";
				}
			}
		}
	}

	function load_current_data($pdo, $user_type, $id)
	{
		if($user_type == 'stravnik')
		{
			$sql = 'SELECT Nereg_uzivatel.id_uzivatel, jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo 
					FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel
					WHERE Nereg_uzivatel.id_uzivatel = ?';
		}
		elseif($user_type == 'ridic')
		{
			$sql = 'SELECT Nereg_uzivatel.id_uzivatel, jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo, licence
					FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel INNER JOIN Ridic ON Stravnik.id_uzivatel = Ridic.id_uzivatel 
					WHERE Nereg_uzivatel.id_uzivatel = ?';
		}
		elseif(($user_type == 'operator') or ($user_type == 'admin'))
		{
			$sql = 'SELECT Nereg_uzivatel.id_uzivatel, jmeno, prijmeni, adresa, psc, telefon, uziv_jmeno, heslo, rodne_cislo, mzda, cislo_uctu, je_admin
					FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel INNER JOIN Operator_Admin ON Stravnik.id_uzivatel = Operator_Admin.id_uzivatel 
					WHERE Nereg_uzivatel.id_uzivatel = ?';
		}

		$stmt = $pdo->prepare($sql);
 					
		$return_val = $stmt->execute(array($id));
		test_stmt_exec($return_val, $stmt);

		return $stmt->fetch();
	}
	
	function html_select($pdo, $action, $selected)
	{
		$stmt = $pdo->prepare("SELECT * FROM Nereg_uzivatel INNER JOIN Stravnik ON Nereg_uzivatel.id_uzivatel = Stravnik.id_uzivatel ORDER BY Nereg_uzivatel.prijmeni");
	 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);

		$return_string = '
		<div class="row m-auto">
			<div class="col-md-4"></div>
			<div class="col-md-4">
			<form class="btn btn-secondary" action="' . $_SERVER['PHP_SELF'] . '" method="post">
			Zvolte uživatele: <select class="mr-2" name="select_user">';

			while ($row = $stmt->fetch())
			{
				if($row["id_uzivatel"] == $selected)
					$return_string .= '<option selected="selected" value="' . $row["id_uzivatel"] . '">' . $row["prijmeni"] . " " . $row["jmeno"] . '</option>';
				else
					$return_string .= '<option value="' . $row["id_uzivatel"] . '">' . $row["prijmeni"] . " " . $row["jmeno"] . '</option>';
			}
			
			$return_string .= '
			<input class="btn btn-dark" type="submit" name="uzivatele_button_select_' . $action .'" value="Vybrat">
			</select>
			</form>
			</div>
			<div class="col-md-4"></div>
		</div>';

		return $return_string;
	}


	function html_uzivatel_radiobuttons($user)
	{
		return '
		<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-4">
			<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
				<input type="radio"' . (($user == 'stravnik')? "checked=\"checked\"" : "") . ' name="user" value="stravnik"> Strávník<br>
				<input type="radio"' . (($user == 'ridic')? "checked=\"checked\"" : "") . ' name="user" value="ridic"> Řidič<br>
				<input type="radio"' . (($user == 'operator')? "checked=\"checked\"" : "") . ' name="user" value="operator"> Operátor<br>
				<input type="radio"' . (($user == 'admin')? "checked=\"checked\"" : "") . ' name="user" value="admin"> Admin<br>
				<input class="btn btn-secondary" type="submit" name="uzivatele_radiobutton_submit" value="Povrdit">
			</form>
		</div>
		<div class="col-md-4"></div>
		</div>
		';
	}

	function html_uzivatel_form($result, $btn_text, $action, $user_type)
	{
		$return_string = '
			<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">' .
			(!($result["nazev"]) ? "<h3>Uživatel</h3>" : ('<h3>' . $result["nazev"]) . '</h3>') . '<br>
			<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<input type="hidden" name="user_type" value="'.$user_type.'">
			<input type="hidden" name="id_uzivatel" value="' . $result["id_uzivatel"] . '">
			<label for="jmeno">Jméno:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="jmeno" id="jmeno" pattern="[a-zA-ZÀ-ž ]+" value="' . $result["jmeno"] . '" required="true">
			<br>
			<label for="prijmeni">Příjmení:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="prijmeni" id="prijmeni" pattern="[a-zA-ZÀ-ž ]+" value="' . $result["prijmeni"] . '" required="true">
			<br>
			<label for="adresa">Adresa:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="adresa" id="adresa" pattern="[a-zA-ZÀ-ž, ]+ [0-9]+[a-z\/A-ZÀ-ž0-9, ]*" value="' . $result["adresa"] . '" required="true">
			<br>
			<label for="psc">PSČ:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="psc" id="psc" pattern="[0-9]{5}" maxlength="5" value="' . $result["psc"] . '" required="true">
			<br>
			<label for="telefon">Telefonní číslo:</label>
			<input type="tel"' . (($action == "delete")? " readonly " : "") . 'name="telefon" id="telefon" pattern="((([\+][0-9]{3})|([0-9]{5}))?[0-9]{9})" minlength="9" maxlength="15" value="' . $result["telefon"] . '" required="true">
			<br>
			<label for="uziv_jmeno">Uživatelské jméno:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="uziv_jmeno" pattern="([a-zA-Z0-9_]+)" id="uziv_jmeno" value="' . $result["uziv_jmeno"] . '" required="true">
			<br>
			<label for="heslo">Heslo</label>
			<input type="password"' . (($action == "delete")? " readonly " : "") . 'name="heslo" pattern="([a-zA-Z0-9_]+)" id="heslo" placeholder="heslo" required="true">
			<br>';

		if($user_type == 'operator' or $user_type == 'admin')
		{
			$return_string .= '
			<label for="rodne_cislo">Rodné číslo:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="rodne_cislo" id="rodne_cislo" pattern="([0-9]{6}\/[0-9]{4})" minlength="11" maxlength="11" value="' . $result["rodne_cislo"] . '">
			<br>
			<label for="cislo_uctu">Číslo účtu:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="cislo_uctu" pattern="([0-9]{6}([0-9]*)\/([0-9]{4}))" id="cislo_uctu" value="' . $result["cislo_uctu"] . '">
			<br>
			<label for="mzda">Mzda:</label>
			<input type="number"' . (($action == "delete")? " readonly " : "") . 'name="mzda" id="mzda" value="' . $result["mzda"] . '">
			<br>';
		}

		if($user_type == 'ridic')
		{
			$return_string .= '
			<label for="licence">Licence:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="licence" id="licence" value="' . $result["licence"] . '">
			<br>';
		}
		
		if(($user_type == 'operator') && ($action == 'add'))
		{
			$return_string .= '<input type="hidden" name="je_admin" value="'.FALSE.'">';
		}
		elseif(($user_type == 'admin') && ($action == 'add'))
		{
			$return_string .= '<input type="hidden" name="je_admin" value='.TRUE.'>';
		}

		if(($user_type == 'operator') or ($user_type == 'admin') and ($action != 'add'))
		{
			$return_string .= '<input type="radio"' . (($user_type == 'operator')? "checked=\"checked\"" : "") . '" name="je_admin"' . (($action == "delete")? "disabled " : "") . 'value='.FALSE.'>Operátor<br>
				<input type="radio"' . (($user_type == 'admin')? "checked=\"checked\"" : "") . '" name="je_admin"' . (($action == "delete")? "disabled" : "") . 'value='.TRUE.'>Admin<br>';
		}
		
		$return_string .= '
			<input type="submit" class="edit_submit_btn btn ' . 
			(($action == "add")? "btn-success" : "") .
			(($action == "edit")? "btn-info" : "") .
			(($action == "delete")? "btn-danger" : "") . 
			' " name="uzivatele_confirm_'. $action .'" value="' . $btn_text .'">
			<br>
			</div>
			</div>
			<div class="col-md-4"></div>
			</div>
			</form>';

		return $return_string;
	}

?>

	<body class="gray_body">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-1"><a href="operator_edit.php?"><i class="fas fa-arrow-circle-left rest_item_back"></i></a></div>
				<div class="col-md-11"></div>
			</div>
			<div class="row">
				<?php echo html_operations(); ?>
			</div>
			<div class="edit_separator">⠀</div>
				<?php echo $html_output; ?>
		</div>
	</body>