<?php
	session_start();
	
	require("connection_credentials.php");		

	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}

	$html_output = "";

	// Uzivatel je neni ani admin ani operator, nema tu co delat -> presmerovat na domovskou stranku
	if (($_SESSION['user'] != 'operator') and ($_SESSION['user'] != 'admin'))
	{
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/index.php");	
	}

	$html_output .= html_intro();

	// ---------------------------- VYBER SEKCE ----------------------------

	// Zvolena sekce Provozovny
	if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['provozovny']))
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/operator_edit_provozovny.php?");
	
	// Zvolena sekce Polozky
	if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['polozky']))
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/operator_edit_polozky.php?");
	
	// Zvolena sekce Uzivatele
	if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['uzivatele']))
		header("Location: http://{$_SERVER['SERVER_NAME']}/~". $username ."/admin_edit_uzivatele.php?");
			

	// ---------------------------- UZAVRENI OBJEDNAVEK ----------------------------

	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['close_orders']))
	{
		$myfile = fopen("close_orders_log", "w");
		fwrite($myfile, '1');
		fclose($myfile);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['open_orders']))
	{
		$myfile = fopen("close_orders_log", "w");
		fwrite($myfile, '0');
		fclose($myfile);
	}

	$myfile = fopen("close_orders_log", "r");
	
	if(fread($myfile,1) == '1')
	{
		$is_closed = true;
		$revert_button = html_openbutton();
	}
	else
	{
		$is_closed = false;
	}

	fclose($myfile);

	// Zvoleni sekce
	function html_intro()
	{
		return '
			<h3 class="mt-5 text-center">Co chcete editovat?</h3><br>
			<form action="' . $_SERVER['PHP_SELF'] . '" method="get">
			<div class="row ">
				<div class="col-md-3"></div>
				<div class="' . (($_SESSION['user'] == 'admin') ? "col-md-2" : "col-md-3") . '">
					<input type="submit" class="btn p-3 mt-2 btn-info w-100" name="provozovny" value="Provozovny">
				</div>
				<div class="' . (($_SESSION['user'] == 'admin') ? "col-md-2" : "col-md-3") . '">
					<input type="submit" class="btn p-3 mt-2 btn-info w-100" name="polozky" value="Položky">
				</div>'
			
				. (($_SESSION['user'] == 'admin') ? '
				<div class="col-md-2">
					<input type="submit" class="btn p-3 mt-2 btn-info w-100" name="uzivatele" value="Uživatelé">
				</div>' : "") . 
			
				'<div class="col-md-3"></div>
			</div>
			</form>';
	}

	function html_openbutton()
	{
		return '<div class="edit_separator"></div>
		<p class="text-center mb-2">Objednávky byly již uzavřeny.</p>
		<p class="text-center mb-2">
		<form class="text-center m-auto" action="' . $_SERVER['PHP_SELF'] .'" method="post">
			<input type="submit" class="btn btn-success p-3 w-50" name="open_orders" value="Opět otevřít"><br>
		</form></p>';
	}


	require("navbar.php");
	include("links.php");

?>

	<body class="gray_body">
		<div class="container-fluid">
			<?php echo $html_output; ?>
			<h3 class="text-center pt-5 mt-5 mb-4">Uzavření objednávek</h3>
			<div class="row">
				<div class="col-md-4 hidden-xs"></div>
				<div class="col-md-4">
					<!-- <?php //if($_GLOBALS['closed']) ?> -->
					<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
						<!-- <button class="btn btn-danger p-3 w-100">Uzavřít objednávky</button> -->
						<input type="submit" class="btn btn-danger <?php if($is_closed){echo 'disabled';}?>  p-3 w-100" name="close_orders" value="Uzavřít objednávky">
						<?php echo $revert_button; ?>
						<br>
					</form>
				</div>
				<div class="col-md-4 hidden-xs"></div>
			</div>
		</div>
	</body>
