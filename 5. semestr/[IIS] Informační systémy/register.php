<?php
	session_start();
	
	require("connection_credentials.php");
	include("functions.php");

	// Uzivatel je prihlasen, nema na registraci co delat -> presmerovat na odhlaseni
	if ($_SESSION['user']) {
		header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username ."/login.php");	
	}
	else
	{
		$html_output = "";

		if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['jmeno']) and isset($_POST['prijmeni']) and isset($_POST['adresa']) and isset($_POST['telefon']) and isset($_POST['psc']) and isset($_POST['uziv_jmeno']) and isset($_POST['heslo1']) and isset($_POST['heslo2']))
		{
			$jmeno = $_POST['jmeno'];
			$prijmeni = $_POST['prijmeni'];
			$adresa = $_POST['adresa'];
			$telefon = $_POST['telefon'];
			$psc = $_POST['psc'];
			$uziv_jmeno = $_POST['uziv_jmeno'];
			$heslo1 = $_POST['heslo1'];
			$heslo2 = $_POST['heslo2'];
			
			// Kontrola shody hesel
			if($heslo1 != $heslo2)
			{
				$html_output .= "<p class=\"info-danger\">Zadana hesla nejsou stejna!</p>";
			}
			else
			{

				try {
					$pdo = new PDO($dsn, $username, $password);
				} 
				catch (Exception $e) {
					echo "Connection error: ". $e->getMessage();
					die();
				}

				$stmt = $pdo->prepare('SELECT id_stravnik, uziv_jmeno, heslo FROM Stravnik WHERE uziv_jmeno=?');
				
				$stmt->execute(array($uziv_jmeno));
				$result = $stmt->fetch();

				// Test zda uzivatelske jmeno uz v db není
				if ($result)
				{
					$html_output .= "<p class=\"info-warning\">Zadane uzivatelske jmeno jiz existuje.</p>";
					$uziv_jmeno = null;
				}
				else
				{
					$stmt = $pdo->prepare("INSERT into Nereg_uzivatel (jmeno,prijmeni,adresa,psc,telefon) VALUES (?,?,?,?,?);");
 					
 					$return_val = $stmt->execute(array($jmeno, $prijmeni, $adresa, $psc, $telefon));
 					test_stmt_exec($return_val, $stmt);

					$stmt = $pdo->prepare("INSERT into Stravnik(id_uzivatel,uziv_jmeno,heslo) VALUES (?,?,?);");
					
					if(!($stmt->execute(array($pdo->lastInsertId(), $uziv_jmeno, $heslo1))))
 					{
 						echo "\nPDOStatement::errorInfo():\n";
 						$arr = $stmt->errorInfo();
						print_r($arr);
 					}
 					else
					{
						$html_output .= "<p class=\"bg-success\">Registrace byla úspěšná.</p>";
						
						$_SESSION['user'] = 'stravnik';
						$_SESSION['id'] = $id_uzivatel;
						header("Location: http://{$_SERVER['SERVER_NAME']}/~".  $username);
					}

				}
			}

			echo $html_output;

		}
	}

	include('links.php');
?>

<body class="login_body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-1 login_back_btn"><a href="index.php?"><i class="fas fa-arrow-circle-left"></i></a></div>
			<div class="col-md-9"></div>			
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
			<div class="col-md-4 col-sm-8 login_div register_div_position">
				<a href="index.php?"><img src="img/logo.png" class="login_logo"></a>
				<h3 class="text-light text-center">Nová registrace</h3><br>
				<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
				<div class="row login_row">
					<div class="col-4">
						<label class="text-right d-block" for="jmeno">Jméno:</label>
					</div>
					<div class="col-8">
						<input type="text" name="jmeno" id="jmeno" pattern="[a-zA-ZÀ-ž ]+" required="true" value="<?php echo htmlspecialchars($jmeno); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="prijmeni">Příjmení:</label>
					</div>
					<div class="col-8">
						<input type="text" name="prijmeni" id="prijmeni" pattern="[a-zA-ZÀ-ž ]+" required="true" value="<?php echo htmlspecialchars($prijmeni); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="adresa">Adresa:</label>
					</div>
					<div class="col-8">
						<input type="text" name="adresa" id="adresa" pattern="[a-zA-ZÀ-ž, ]+ [0-9]+[a-z\/A-ZÀ-ž0-9, ]*" required="true" value="<?php echo htmlspecialchars($adresa); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="psc">PSČ:</label>
					</div>
					<div class="col-8">
						<input type="text" name="psc" id="psc" pattern="[0-9]{5}" maxlength="5" required="true" value="<?php echo htmlspecialchars($psc); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="telefon">Telefonní číslo:</label>
					</div>
					<div class="col-8">
						<input type="tel" name="telefon" id="telefon" pattern="((([\+][0-9]{3})|([0-9]{5}))?[0-9]{9})" minlength="9" maxlength="15" required="true" value="<?php echo htmlspecialchars($telefon); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="uziv_jmeno">Uživatelské jméno:</label>
					</div>
					<div class="col-8">
						<input type="text" name="uziv_jmeno" pattern="([a-zA-Z0-9_]+)" id="uziv_jmeno" required="true" value="<?php echo htmlspecialchars($uziv_jmeno); ?>">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="heslo1">Heslo:</label>
					</div>
					<div class="col-8">
						<input type="password" name="heslo1" pattern="([a-zA-Z0-9_]+)" id="heslo1" required="true">
					</div>
				</div>
				<div class="row login_row mt-2">
					<div class="col-4">
						<label class="text-right d-block" for="heslo2">Heslo znovu:</label>
					</div>
					<div class="col-8">
						<input type="password" name="heslo2" pattern="([a-zA-Z0-9_]+)" id="heslo2" required="true">
					</div>
				</div>
				<div class="container-fluid login_submits">
					<input class="btn btn-success d-block m-auto" type="submit" value="Registrovat">
				</div>
				</form>
			<div class="col-md-4 col-sm-2 hidden-xs"></div>
		</div>
		<div class="container-fluid">
		<div class="row">
			<div class="col-md-2 d-block"></div>
			<div class="col-md-4 d-block p-0">
				<ol class="register_help">
					<li>Adresa je očekávana ve formátu "Město, Ulice č.p."</li>
					<li>Telefonní číslo je očekáváno ve formátu s mezinárodní telefonní předvolbou, <br>v případě čísla ČR může být uvedeno bez ní</li>
				</ol>
			</div>
			<div class="col-md-4 d-block">
				<ol class="register_help" start="3">
					<li>Povolené znaky v uživatelském jméně jsou malá, velká písmena, číslice <br>a podtržítko.</li>
					<li>Pro heslo jsou povolené znaky stejné jak pro uživatelské jméno</li>
				</ol>
			</div>
			<div class="col-md-2 d-block"></div>
		</div>
		</div>
	</div>
</body>
		