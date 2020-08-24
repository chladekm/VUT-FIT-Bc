<?php

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
		if (!($_SESSION['user'])) {
			$_SESSION['user'] = NULL;
			$_SESSION['rights'] = NULL;
		}
	}

	// pro vypis html - musime poslat az na konci, pokud chceme posilat heders
	$html_output = "";

	require("connection_credentials.php");
	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}
	
	require('functions.php');

	// Tlacitko odhlaseni
	if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['sign_off_action'])) {
		// Vymazeme kosik uzivatele co se odhlasuje
		$stmt = $pdo->prepare('DELETE FROM Objednavka WHERE id_uzivatel=? AND stav="neodeslana"');
		$return_val = $stmt->execute(array($_SESSION['id']));
		test_stmt_exec($return_val, $stmt);
		session_destroy();

		$_SESSION['user'] = NULL;
		$_SESSION['id'] = NULL;
	}

	// Uzivatel je prihlasen, chce se odhlasit?
	if ($_SESSION['user']) {
		$html_output = log_off_function($html_output);	
	}
	// Uzivatel neni prihlasen, formular prihlaseni.
	else {

		$id_uzivatel_pudovni = $_SESSION['id'];
		// Kontrola prihlasovacich udaju uzivatele oproti zaznamu v tabulce
		// TODO sifrovani, bude easy
		if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['login']) and isset($_POST['pwd'])) {
			
			$login = $_POST['login'];
			$pwd = $_POST['pwd'];

			// Existuje vubec uzivatel?
			$stmt = $pdo->prepare('SELECT id_uzivatel, uziv_jmeno, heslo FROM Stravnik WHERE uziv_jmeno=?');
			
			$return_val = $stmt->execute(array($login));
			test_stmt_exec($return_val, $stmt);
			
			$result = $stmt->fetch();
			// Ulozeni id uzivatele pro dalsi pouziti
			$id_uzivatel = $result['id_uzivatel'];

			if (!$result) {
				// User nenalezen
				echo "<p class=\"info-danger text-center text-light\">Špatné uživatelské jméno!<p>";
			}
			else {
				// User nalezen
				$correct_pwd = $result['heslo'];

				// Je uzivatel admin, nebo operator?
				$stmt = $pdo->prepare('SELECT je_admin FROM Operator_Admin WHERE id_uzivatel=?');
				
				$return_val = $stmt->execute(array($id_uzivatel));
				test_stmt_exec($return_val, $stmt);
				
				$result = $stmt->fetch(); 

				if (!$result) {
					// Neni, je to ridic?
					$stmt = $pdo->prepare('SELECT id_ridic FROM Ridic WHERE id_uzivatel=?');
					
					$return_val = $stmt->execute(array($id_uzivatel));
					test_stmt_exec($return_val, $stmt);
					
					$result = $stmt->fetch(); 

					if (!($result)) {
						// Neni ani ridic, je to stravnik
						if (password_verify($pwd, $correct_pwd)) {
							$_SESSION['user'] = 'stravnik';
							$_SESSION['id'] = $id_uzivatel;
							header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username);
						}
						else
							$html_output .= "Wrong password!<br>";
					}
					else {
						// Je to ridic
						if (password_verify($pwd, $correct_pwd)) {
							$_SESSION['user'] = 'ridic';
							$_SESSION['id'] = $id_uzivatel;
							header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username);
						}
						else
							echo "<p class=\"info-danger text-center text-light\">Špatné heslo!<p>";
					}
				}
				else {
					// Uzivatel je operator nebo admin
					if (password_verify($pwd, $correct_pwd)) {
						if ($result['je_admin'] == true)
						{
							// Je to admin
							$_SESSION['user'] = 'admin';
							$_SESSION['id'] = $id_uzivatel;	
						}
						else
						{
							// Je to operator
							$_SESSION['user'] = 'operator';
							$_SESSION['id'] = $id_uzivatel;
						}
						header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username);
					}
					else
						echo "<p class=\"info-danger text-center text-light\">Špatné heslo!<p>";
				}
			
			}
			// Priradim objednavku/kosik prihlasenemu uzivateli
			if ($id_uzivatel_pudovni && $_SESSION['order_id']) {
				$stmt = $pdo->prepare('UPDATE Objednavka SET id_uzivatel = ? WHERE id_uzivatel = ?');
				
				$return_val = $stmt->execute(array($_SESSION['id'], $id_uzivatel_pudovni));
				test_stmt_exec($return_val, $stmt);

				$result = $stmt->fetchall();
			}
		}

		$html_output .= login_function($html_output);
	}

require('links.php');
?>

<body class="login_body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-1 login_back_btn "><a href="index.php?"><i class="fas fa-arrow-circle-left"></i></a></div>
			<div class="col-md-9"></div>			
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
			<div class="login_div col-md-4 col-sm-8">
				<a href="index.php?"><img src="img/logo.png" width="300" height="100" class="login_logo"></a>
				<div class="login_form_block"><?php echo $html_output; ?></div>			
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
		</div>
	</div>
</body>

<?php
	

	function login_function ()
	{
		$html_output .= "<form action=\"";
		$html_output .= $_SERVER['PHP_SELF'];
		$html_output .= '"method="post">
		<div class="row login_row">
			<div class="col-4 m-auto">
				<label class="text-right d-block" for="login">Login:</label>
			</div>
			<div class="col-8">
				<input class="" type="text" name="login" id="login" required="true">
			</div>
		</div>
		<div class="row mt-2 login_row">
			<div class="col-4 m-auto">
				<label class="text-right d-block" for="pwd">Heslo:</label>
			</div>
			<div class="col-8">
				<input class="" type="password" name="pwd" id="pwd" required="true">
			</div>
		</div>
		<div class="container-fluid login_submits">
			<input type="submit" class="btn btn-success d-block m-auto" value="Přihlásit"><br>
			<div class="d-block text-center m-auto"><a href="register.php"><span>Pokud nemáte účet, můžete se</span><br>zaregistrovat</a></div>
		<div>
		</form>';
		return $html_output;
	}

	function log_off_function ()
	{
		$html_output .= "<p class=\"text-center text-light\">Jste již přihlášen</p>";
		$html_output .= "<form action=\"";
		$html_output .= $_SERVER['PHP_SELF'];
		$html_output .= '" method="post">
				<input class="btn btn-success login_submit_btn d-block m-auto" type="submit" name="sign_off_action" value="Odhlásit se"/>
			</form>';
				
		return $html_output;	
	}

?>