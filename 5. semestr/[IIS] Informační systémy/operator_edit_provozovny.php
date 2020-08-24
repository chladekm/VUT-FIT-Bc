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

	// ---------------------------- ZVOLENE OPERACE ----------------------------

	// Zobrazit list
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_list']))
	{
		$stmt = $pdo->prepare("SELECT nazev, adresa FROM Provozovna");
 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);
		$result = $stmt->fetchall();

		$html_output = 
		'<table class="edit_table">
		<tr>
			<th>Název</th>
			<th>Adresa</th>
		</tr>';

		foreach ($result as $row) {
			$html_output .= '<tr><td>' . $row["nazev"] .'</td><td>'. $row["adresa"]. "</td></tr>";
		}

		$html_output .= '</table>';
	}

	// Pridat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_add']))
	{
		$html_output .= html_provozovna_form(NULL, "Přidat", "add");
	}

	// Upravit
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_edit']))
	{
		$html_output .= html_select($pdo, 'edit', NULL);
		// $html_output .= html_provozovna_form(NULL, "Provést změny", "edit");
	}

	// Smazat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_delete']))
	{	
		$html_output .= html_select($pdo, 'delete', NULL);
		// $html_output .= html_provozovna_form(NULL, "Opravdu smazat", "delete");
	}

	// ---------------------------- VYKONANI OPERACE ----------------------------

	// Vykonani pridani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_confirm_add']))
	{
		$stmt = $pdo->prepare("INSERT into Provozovna (nazev, adresa) VALUES (?,?);");
 					
		$return_val = $stmt->execute(array($_POST['nazev'],$_POST['adresa']));
		test_stmt_exec($return_val, $stmt);
		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně vytvořen!</p>";
	}

	// Vykonani aktualizace
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_confirm_edit']))
	{
		$stmt = $pdo->prepare("UPDATE Provozovna SET nazev = ?, adresa = ? WHERE id_provozovna = ?");
 					
		$return_val = $stmt->execute(array($_POST['nazev'],$_POST['adresa'],$_POST['id_provozovna']));
		test_stmt_exec($return_val, $stmt);
		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";
	}

	// Vykonani smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_confirm_delete']))
	{
		$stmt = $pdo->prepare("DELETE FROM Provozovna WHERE id_provozovna = ?");
 					
		$return_val = $stmt->execute(array($_POST['id_provozovna']));
		test_stmt_exec($return_val, $stmt);
		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně smazán!</p>";
	}

	// ---------------------------- VYBER PROVOZOVNY ----------------------------	

	// Vyber pro upravu
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_select_edit']))
	{
		$stmt = $pdo->prepare("SELECT id_provozovna, nazev, adresa FROM Provozovna WHERE id_provozovna = ?");
 					
		$return_val = $stmt->execute(array($_POST['select_rest']));
		test_stmt_exec($return_val, $stmt);

		$result = $stmt->fetch();

		$html_output .= html_select($pdo, 'edit', $_POST['select_rest']);
		$html_output .= html_provozovna_form($result, "Provést změny", "edit");
	}

	// Vyber pro smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['provozovny_button_select_delete']))
	{
		$stmt = $pdo->prepare("SELECT id_provozovna, nazev, adresa FROM Provozovna WHERE id_provozovna = ?");
 					
		$return_val = $stmt->execute(array($_POST['select_rest']));
		test_stmt_exec($return_val, $stmt);

		$result = $stmt->fetch();

		$html_output .= html_select($pdo, 'delete', $_POST['select_rest']);
		$html_output .= html_provozovna_form($result, "Opravdu smazat", "delete");
	}

	
	// echo $html_output;

	// Zvoleni operace
	function html_operations()
	{
		$selected_section = 'provozovny';

		return '
		<div class="container-fluid">
		<h3 class="text-center mb-4 mt-2">Správa provozoven</h3>
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

	
	function html_select($pdo, $action, $selected)
	{
		$stmt = $pdo->prepare("SELECT * FROM `Provozovna` ORDER BY nazev");
	 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);

		$return_string = '

		<form class="btn btn-secondary" action="' . $_SERVER['PHP_SELF'] . '" method="post">
		Zvolte provozovnu: <select class="mr-2" name="select_rest">';

		while ($row = $stmt->fetch())
		{
			if($row["id_provozovna"] == $selected)
				$return_string .= '<option selected="selected" value="' . $row["id_provozovna"] . '">' . $row["nazev"] . '</option>';
			else
				$return_string .= '<option value="' . $row["id_provozovna"] . '">' . $row["nazev"] . '</option>';
		}
		
		$return_string .= '
		<input class="btn btn-dark" type="submit" name="provozovny_button_select_' . $action .'" value="Vybrat">
		</select>
		</form>';

		return $return_string;
	}


	function html_provozovna_form($result, $btn_text, $action)
	{
		return '' .
			(!($result["nazev"]) ? "<h3>Provozovna</h3>" : ('<h3>' . $result["nazev"]) . '</h3>') . '<br>
			<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<input type="hidden" name="id_provozovna" value="'.$result["id_provozovna"].'">
			<label for="nazev">Název:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="nazev" id="nazev" pattern="[a-zA-ZÀ-ž0-9 ]+" value="' . $result["nazev"] . '" required="true">
			<br>
			<label for="adresa">Adresa:</label>
			<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="adresa" id="adresa" pattern="[a-zA-ZÀ-ž, ]+ [0-9]+[a-z\/A-ZÀ-ž0-9, ]*" value="' . $result["adresa"] . '" required="true">
			<br>
			<input type="submit" class="edit_submit_btn btn ' . 
			(($action == "add")? "btn-success" : "") .
			(($action == "edit")? "btn-info" : "") .
			(($action == "delete")? "btn-danger" : "") . 
			' " name="provozovny_confirm_'. $action .'" value="' . $btn_text .'">
			<br>
			</div>
			</form>';
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
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<?php echo $html_output; ?>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
