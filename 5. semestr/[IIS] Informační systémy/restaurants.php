<?php

	session_start();
	require("navbar.php");
	include("links.php");

	$conn = connect_to_server();
	
	$sql = "SELECT id_provozovna, nazev, adresa FROM Provozovna ORDER BY nazev;";
	
	$result = $conn->query($sql);
	$i = 0;

	$count_of_restaurants = mysqli_num_rows($result);
	$name_of_rest = NULL;

	// output data of each row
	while($row = $result->fetch_assoc()) {

		if($i % 2 == 0)
		{
			$html_output .= "<div class=\"row\">";
		}
		$html_output .= "
			<div class=\"col-md-6 pt-2 pb-2 rest_block\">
			<a href=\"restaurants.php?show_food_list_by_rest_action=". $row["id_provozovna"]. "\">
				<div class=\"row rest_block_inside\">
	    			<div class=\"col-2 text-right m-auto\"><i class=\"rest_icon fas fa-utensils\"></i></div>
    				<div class=\" col-10 \">
    					<span class=\"rest_name\">". $row["nazev"]. "</span><br>" . $row["adresa"] . "
    				</div>
    			</div>
    			</a>
			</div>";

		if(($i % 2 != 0) or ($i == ($count_of_restaurants-1)))
		{
			$html_output .= "</div>";
		}

		$i++;

	}

	// Funkce pro zobrazeni jidelnicku podle provozovny
	function show_food_list_by_rest()
	{
		require("connection_credentials.php");
		try {
			$pdo = new PDO($dsn, $username, $password);
		} 
		catch (Exception $e) {
			echo "Connection error: ".$e->getMessage();
			die();
		}

		$stmt = $pdo->prepare('SELECT nazev FROM Provozovna WHERE id_provozovna=?');
				
		$stmt->execute(array($_GET['show_food_list_by_rest_action']));
		$result = $stmt->fetch();
		
		global $name_of_rest;
		$name_of_rest = $result['nazev'];


		$stmt = $pdo->prepare('SELECT id_polozka, nazev, popis, cena, cas_platnosti, pocet, kategorie FROM Polozka WHERE id_provozovna=? ORDER BY FIELD(kategorie, "predkrm","polevka", "hlavni jidlo", "dezert", "napoj")');
		$stmt->execute(array($_GET['show_food_list_by_rest_action']));

		$result = $stmt->fetchall();
		
		$count_of_items = count($result);
		$i = 0;
		
		global $html_output;

		foreach ($result as $row) {


			if($i % 4 == 0)
			{
				$html_output .= "<div class=\"row\">";
			}
			

			// Nacteni obrazku
			if(file_exists("uploads/". $row["id_polozka"]))
			{
				$img_src = 'uploads/'. $row["id_polozka"];
			}
			else
			{
				$img_src = 'uploads/unknown.png';							
			}

			$html_output .='
			<div class="col-md-3 d-flex" style="padding: 0.5vw;">
				<div class="item">
					<div class="item_image"><img src="' . $img_src .'" alt="Obrázek položky" height="200"></div>
					<div class="item_content">
						<span>' . $row["nazev"] . '</span><br>' . $row["popis"] . '<br>' . '
					</div>
					<div class="item_to_cart">
						<form class="m-auto" action="restaurants.php" method="post">
						<span class="btn btn-dark"> '.$row["cena"] .' Kč</span>';
			if ($row['cas_platnosti'] == "trvala" or ($row['cas_platnosti'] == "denni" and $row['pocet'] > 0)) {				
				$html_output .= '<input name="buy_item" class="btn btn-info" type="submit" value="Vložit do košíku" />';
			}
			else {
				// Položka je vyprodaná
				$html_output .= '<input disbaled name="buy_item" class="btn btn-info" type="submit" value="Vyprodané" disabled/>';
			}
			$html_output .= '
							<input name="item_id" type="hidden" value="' . $row["id_polozka"] . '">
						</form>
					</div>
				</div>
			</div>
			';

			if(((($i+1) % 4 == 0) and ($i != 0))  or ($i == ($count_of_items-1)))
			{
				$html_output .= "</div>";
			}

			$i++;
		}
	}

	if ($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['show_food_list_by_rest_action'])) {
		$html_output = "";
		show_food_list_by_rest();
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['buy_item'])) {
		// Vytvorit objednavku a polozky objednavky, musi se take urcit jestli je uzivatel registrovany/prihlaseny a podle toho bud vygenerovat nebo priradit id uzivatele.
		// Doted je tedy potreba brat z nejakyho nereg_uzivatele, kterej ma v databazi prava pouze na zobrazeni nekterych tabulek. Ted se ale musi bud prihlasit, nebo bude mit formular.
		$_SESSION['item_id'] = $_POST['item_id'];

		?>
			<script type="text/javascript">
				window.location.href = '<?php require ("connection_credentials.php"); echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '/cart.php?'. session_id(); ?>';
			</script>
		<?php
	}	

?>

<body class="gray_body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-1"><?php if($name_of_rest){echo '<a href="restaurants.php?"><i class="fas fa-arrow-circle-left rest_item_back"></i></a>';}?></div>
			<div class="col-md-10">
				<h3 class="rest_header text-center m-auto text-uppercase"><?php if($name_of_rest){echo $name_of_rest;}else{echo "Provozovny";}?></h3>
			</div>
			<div class="col-md-1"></div>
		</div>
		<div class="row pt-3">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<?php echo $html_output; ?>
			</div>
			<div class="col-md-1"></div>
			
		</div>
	</div>
</body>