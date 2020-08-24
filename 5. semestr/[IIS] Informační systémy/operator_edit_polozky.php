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

	// Zvoleni operace
	function html_operations()
	{
		$selected_section = 'polozky';

		return '
		<div class="row">
				<div class="col-md-1"><a href="operator_edit.php?"><i class="ml-3 fas fa-arrow-circle-left rest_item_back"></i></a></div>
				<div class="col-md-11"></div>
		</div>
		<div class="row">
			<div class="container-fluid">
			<h3 class="text-center mb-4 mt-2">Správa položek</h3>
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
			</div>
		</div>
		<div class="edit_separator">⠀</div>';
	}

	function html_select($pdo, $action, $selected)
	{
		$stmt = $pdo->prepare("SELECT * FROM `Polozka` ORDER BY nazev");
	 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);

		$return_string = '
		<div class="row m-auto">
			<div class="col-12 text-center">
			<form class="btn btn-secondary d-inline-block" action="' . $_SERVER['PHP_SELF'] . '" method="post">
			Zvolte položku: <select class="mr-2 w-75" name="select_polozka">';

		while ($row = $stmt->fetch())
		{
			if($row["id_polozka"] == $selected)
				$return_string .= '<option selected="selected" value="' . $row["id_polozka"] . '">' . $row["nazev"] . '</option>';
			else
				$return_string .= '<option value="' . $row["id_polozka"] . '">' . $row["nazev"] . '</option>';
		}
		
		$return_string .= '
			<input class="btn btn-dark" type="submit" name="polozky_button_select_' . $action .'" value="Vybrat">
			</select>
			</form>
			</div>
		</div>';

		return $return_string;
	}


	function html_polozka_form($pdo, $result, $btn_text, $action)
	{
		$output =
			'<div class="row m-auto">
			<div class="col-md-4"></div>
			<div class="col-md-4 text-center">
			<h3>Položka</h3>'. '<br>
			<form class="text-left" action="' . $_SERVER['PHP_SELF'] . '" method="post">
				<input type="hidden" name="id_polozka" value="'.$result["id_polozka"].'">
				
				<label for="jmeno">Název:</label>
				<input class="w-75" type="text"' . (($action == "delete")? " readonly " : "") . 'name="nazev" id="nazev" value="' . $result["nazev"] . '" required="true">
				<br>
				<label for="adresa">Popis:</label>
				<input class="w-75" type="text"' . (($action == "delete")? " readonly " : "") . 'name="popis" id="popis" value="' . $result["popis"] . '">
				<br>
				<label for="cena">Cena:</label>
				<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="cena" id="cena" pattern="[0-9]+" value="' . $result["cena"] . '" required="true">
				<br>
				<label for="typ">Typ:</label>
				<select name="typ" id="typ"' . (($action == "delete")? " disabled " : "") . '>
				  <option '. (($result["typ"] == "pizza")? "selected=selected " : "").' value="pizza">pizza</option>
				  <option '. (($result["typ"] == "vegetarianske")? "selected=selected " : "").' value="vegetarianske">vegetariánské</option>
				  <option '. (($result["typ"] == "veganske")? "selected=selected " : "").' value="veganske">veganské</option>
				  <option '. (($result["typ"] == "ryby")? "selected=selected " : "").' value="ryby">ryby</option>
				  <option '. (($result["typ"] == "bezne")? "selected=selected " : "").' value="bezne">běžné</option>
				</select><br>
				<label for="kategorie">Kategorie:</label>
				<select name="kategorie" id="kategorie"' . (($action == "delete")? " disabled " : " ") . '>
				  <option '. (($result["kategorie"] == "hlavni_jidlo")? "selected=selected " : "").'value="hlavni_jidlo">hlavní jídlo</option>
				  <option '. (($result["kategorie"] == "polevka")? "selected=selected " : "").' value="polevka">polévka</option>
				  <option '. (($result["kategorie"] == "predkrm")? "selected=selected " : "").' value="predkrm">předkrm</option>
				  <option '. (($result["kategorie"] == "dezert")? "selected=selected " : "").' value="dezert">dezert</option>
				  <option '. (($result["kategorie"] == "napoj")? "selected=selected " : "").' value="piti">nápoj</option>
				</select><br>
				<label for="cas_platnosti">Cas platnosti:</label>
				<select name="cas_platnosti" id="cas_platnosti"' . (($action == "delete")? " disabled " : "") . '>
				  <option '. (($result["cas_platnosti"] == "denni")? "selected=selected " : "").' value="denni">denní</option>
				  <option '. (($result["cas_platnosti"] == "trvala")? "selected=selected " : "").' value="trvala">trvalá</option>
				</select><br>
				<label for="pocet">Pocet:</label>
				<input type="text"' . (($action == "delete")? " readonly " : "") . 'name="pocet" id="pocet" pattern="[0-9]+" value="' . $result["pocet"] . '">
				<br>
			';

			$stmt = $pdo->prepare("SELECT id_provozovna, nazev FROM `Provozovna`");
			$return_val = $stmt->execute();
			test_stmt_exec($return_val, $stmt);

			$output .= '<select name="select_polozka">';

			while ($row = $stmt->fetch())
			{
				if($row["id_provozovna"] == $result["id_provozovna"])
					$output .= '<option selected="selected" value="' . $row["id_provozovna"] . '">' . $row["nazev"] . '</option>';
				else
					$output .= '<option value="' . $row["id_provozovna"] . '">' . $row["nazev"] . '</option>';
			}
			
			$output .=  '</select>
						<br>
						<input type="submit" class="edit_submit_btn btn ' .	(($action == "add")? "btn-success" : "") .
															(($action == "edit")? "btn-info" : "") .
															(($action == "delete")? "btn-danger" : "") .
						' " name="polozky_confirm_'. $action .'" value="' . $btn_text .'">
						<br>
						<br></form>';

			$output .= (($action == "edit" and file_exists("uploads/". $result["id_polozka"]))? '<h3>Obrázek</h3><p class="text-muted">Obrázek lze změnit po potvrzení formuláře</p><br><img src="uploads/'. $result["id_polozka"].'" alt="Obrázek položky" width="300">' : "");
			
			$output .= 
			'</div>
			<div class="col-md-4"></div>
			</div>';

			return $output;
	}


	// Uzivatel je neni ani admin ani operator, nema tu co delat -> presmerovat na domovskou stranku
	if(($_SESSION['user'] != 'operator') and ($_SESSION['user'] != 'admin'))
	{
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/index.php");	
	}

	if(!($_SERVER['REQUEST_METHOD'] == 'POST' and (isset($_POST['polozky_confirm_add']) or isset($_POST['polozky_confirm_edit']))))
		$html_output = html_operations();

	//---------------------------------------------------------------------------------------
	// Zobrazit list
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_list']))
	{
		$stmt = $pdo->prepare("SELECT nazev, popis, cena, typ, kategorie FROM Polozka");
 					
		$return_val = $stmt->execute();
		test_stmt_exec($return_val, $stmt);
		$result = $stmt->fetchall();

		$html_output .= 
		'<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-10">
		<table class="edit_table">
		<tr>
			<th>Název</th>
			<th>Popis</th>
			<th>Cena</th>
			<th>Typ</th>
			<th>Kategorie</th>
		</tr>';

		foreach ($result as $row) {
			$html_output .= '<tr><td>' . $row["nazev"] .'</td><td>'. $row["popis"] .'</td><td>'. $row["cena"] .'</td><td>'. $row["typ"] .'</td><td>'. $row["kategorie"]. "</td></tr>";
		}

		$html_output .= '
		</div>
		<div class="col-md-1"></div>
		</div>';
	}

	// Pridat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_add']))
	{
		$html_output .= html_polozka_form($pdo, NULL, "Přidat", "add");
	}

	// Upravit
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_edit']))
	{
		$html_output .= html_select($pdo, 'edit', NULL);
		// $html_output .= html_provozovna_form(NULL, "Provést změny", "edit");
	}

	// Smazat
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_delete']))
	{	
		$html_output .= html_select($pdo, 'delete', NULL);
		// $html_output .= html_provozovna_form(NULL, "Opravdu smazat", "delete");
	}

	//---------------------------------------------------------------------------------------
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_confirm_add']))
	{
		$stmt = $pdo->prepare("INSERT into Polozka (id_provozovna, nazev, popis, cena, typ, kategorie, cas_platnosti, pocet) VALUES (?,?,?,?,?,?,?,?);");
 					
		$return_val = $stmt->execute(array($_POST['select_polozka'],$_POST['nazev'],$_POST['popis'],$_POST['cena'],$_POST['typ'],$_POST['kategorie'],$_POST['cas_platnosti'],$_POST['pocet']));
		test_stmt_exec($return_val, $stmt);

		$id_polozka = $pdo->lastInsertId();

		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně vytvořen!</p>";

		echo '<form action="upload.php" method="post" enctype="multipart/form-data">
			    Vyberte obrázek k položce:
			    <br>
			    <input type="file" name="fileToUpload" id="fileToUpload" />
			    <br>
			 	<input type="hidden" name="id_polozka" value="', $id_polozka, '">
			    <input id="submit" type="submit" value="Nahrát" name="submit">
			    <input type="submit" value="Nemám zájem" name="return">
			</form>';	

	}

	// Vykonani aktualizace
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_confirm_edit']))
	{
		$stmt = $pdo->prepare("UPDATE Polozka SET id_provozovna=?, nazev=?, popis=?, cena=?, typ=?, kategorie=?, cas_platnosti=?, pocet=? WHERE id_polozka=?");
 					
		$return_val = $stmt->execute(array($_POST['select_polozka'], $_POST['nazev'],$_POST['popis'],$_POST['cena'],$_POST['typ'],$_POST['kategorie'],$_POST['cas_platnosti'],$_POST['pocet'],$_POST['id_polozka']));
		test_stmt_exec($return_val, $stmt);

		$id_polozka = $_POST['id_polozka'];

		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně aktualizován!</p>";

		image_form($id_polozka, 'Změnit obrázek položky');
		
	}

	// Vykonani smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_confirm_delete']))
	{
		$stmt = $pdo->prepare("DELETE FROM Polozka WHERE id_polozka = ?");
 					
		$return_val = $stmt->execute(array($_POST['id_polozka']));
		test_stmt_exec($return_val, $stmt);
		if($return_val)
			echo "<p class=\"info-success\">Záznam byl úspešně smazán!</p>";
	}

	//-----------------------------------------------------------------------------------------
	// Vyber pro upravu
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_select_edit']))
	{
		$stmt = $pdo->prepare("SELECT * FROM Polozka WHERE id_polozka = ?");
 					
		$return_val = $stmt->execute(array($_POST['select_polozka']));
		test_stmt_exec($return_val, $stmt);

		$result = $stmt->fetch();

		$html_output .= html_select($pdo, 'edit', $_POST['select_polozka']);
		$html_output .= html_polozka_form($pdo, $result, "Provést změny", "edit");
	}

	// Vyber pro smazani
	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['polozky_button_select_delete']))
	{
		$stmt = $pdo->prepare("SELECT * FROM Polozka WHERE id_polozka = ?");
 					
		$return_val = $stmt->execute(array($_POST['select_polozka']));
		test_stmt_exec($return_val, $stmt);

		$result = $stmt->fetch();

		$html_output .= html_select($pdo, 'delete', $_POST['select_polozka']);
		$html_output .= html_polozka_form($pdo, $result, "Opravdu smazat", "delete");
	}

	function image_form($id_polozka, $header)
	{
		echo '
		<div class="container-fluid">
			<div class="text-center">
			<form action="upload.php" method="post" enctype="multipart/form-data">
			    <h3 class="text-center">' . $header .' </h3>
			    <br>
			    <input class="mb-5 btn btn-dark" type="file" name="fileToUpload" id="fileToUpload">
			    <br>
			 	<input type="hidden" name="id_polozka" value="', $id_polozka, '">
			    <input class="w-25 btn btn-success" id="submit" type="submit" value="Nahrát obrázek" name="submit"><br>
			    <input class="w-25 mt-2 btn btn-secondary" type="submit" value="Pokračovat beze změny" name="return">
			</form>
			</div>
		</div>';	

	}



?>	
	
	<body class="gray_body">
		<div class="container-fluid">
			
			<?php echo $html_output; ?>
		</div>		
	</body>