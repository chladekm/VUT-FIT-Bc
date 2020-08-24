<?php

	session_start();
	require("navbar.php");
	include("links.php");
?>

<!-- START OF HTML -->
<body class="gray_body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<h3 class="rest_header food_header text-center m-auto text-uppercase">Vyberte sekci</h3>
				<div class="food_select_div">

<?php

	function show_filter($selected)
	{
		?><form class="text-center" action="foods.php" method="get">
			<select name="show_filter_foods_action">
		      <option <?php if($selected == "vse") echo "selected=selected "?>value="vse">Všechny položky</option>
			  <option <?php if($selected == "pizza") echo "selected=selected "?>value="pizza">Pizzy</option>
			  <option <?php if($selected == "vegetarianske") echo "selected=selected "?>value="vegetarianske">Vegetariánské</option>
			  <option <?php if($selected == "veganske") echo "selected=selected "?>value="veganske">Veganské</option>
			  <option <?php if($selected == "ryby") echo "selected=selected "?>value="ryby">Ryby</option>
			  <option <?php if($selected == "bezne") echo "selected=selected "?>value="bezne">Masové pokrmy</option>
			  <option <?php if($selected == "predkrmy") echo "selected=selected "?>value="predkrmy">Předkrmy</option>
			  <option <?php if($selected == "polevky") echo "selected=selected "?>value="polevky">Polévky</option>
			  <option <?php if($selected == "dezerty") echo "selected=selected "?>value="dezerty">Dezerty</option>
			  <option <?php if($selected == "piti") echo "selected=selected "?>value="piti">Nápoje</option>
			</select>
			<input class="btn btn-success" type="submit" name="submit" value="Potvrdit"/>
		</form>
		<?php
	}

	// Funkce pro zobrazeni urcitych jidel
	function show_filter_foods()
	{
		global $type;

		$conn = connect_to_server();
		$sql = 'SELECT id_polozka, nazev, popis, cena, cas_platnosti, pocet, kategorie FROM Polozka';

		if ($_GET['show_filter_foods_action'] == "vse") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= ' ORDER BY FIELD(kategorie, "predkrm","polevka", "hlavni jidlo", "dezert", "napoj");';
			$type="Všechny položky";
		}
		elseif ($_GET['show_filter_foods_action'] == "pizza") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE typ='pizza' AND (kategorie='hlavni jidlo' OR kategorie='polevka')";
			$type="Pizzy";
		}
		elseif ($_GET['show_filter_foods_action'] == "vegetarianske") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE typ='vegetarianske' AND (kategorie='hlavni jidlo' OR kategorie='polevka')";
			$type="Vegetariánské pokrmy";
		}
		elseif ($_GET['show_filter_foods_action'] == "veganske") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE typ='veganske' AND (kategorie='hlavni jidlo' OR kategorie='polevka')";
			$type="Veganské";
		}
		elseif ($_GET['show_filter_foods_action'] == "ryby") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE typ='ryby' AND kategorie='hlavni jidlo'";
			$type="Ryby";
		}
		elseif ($_GET['show_filter_foods_action'] == "bezne") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE typ='bezne' AND (kategorie='hlavni jidlo' OR kategorie='polevka')";
			$type="Masové pokrmy";
		}
		elseif ($_GET['show_filter_foods_action'] == "predkrmy") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE kategorie='predkrm'";
			$type="Předkrmy";
		}
		elseif ($_GET['show_filter_foods_action'] == "dezerty") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE kategorie='dezert'";
			$type="Dezerty";
		}
		elseif ($_GET['show_filter_foods_action'] == "polevky") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE kategorie='polevka'";
			$type="Polévky";
		}
		elseif ($_GET['show_filter_foods_action'] == "piti") {
			show_filter($_GET['show_filter_foods_action']);
			$sql .= " WHERE kategorie='napoj'";
			$type="Nápoje";
		}

		if ((!($_GET['show_filter_foods_action'] == "vse")))
			$sql .= ' ORDER BY FIELD(kategorie, "predkrm","polevka", "hlavni jidlo", "dezert", "napoj")';


  		if ($result = $conn->query($sql))
	   		// output data of each row
	   		display_items($result);
	}


	if (!isset($_GET['show_filter_foods_action']) and !isset($_POST['buy_item'])) {
		show_filter("vse");
		$conn = connect_to_server();
		$sql = "SELECT id_polozka, nazev, popis, cena, cas_platnosti, pocet FROM Polozka;";
			
		if ($result = $conn->query($sql))
	  		
			$count_of_items = mysqli_num_rows($result);
			$i = 0;

	   		display_items($result);
	}
	elseif ($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_filter_foods_action'])) {
		show_filter_foods();
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

	function display_items($result)
	{
		global $html_output;

		$count_of_items = mysqli_num_rows($result);
		$i = 0;

   		while($row = $result->fetch_assoc()) 
   		{
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

			// Start výpisu položky
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
			// Je položka vyprodaná?
			if ($row['cas_platnosti'] == "trvala" or ($row['cas_platnosti'] == "denni" and $row['pocet'] > 0)) {
				// Položka není nebo nemůže být vyprodaná
	        	$html_output .='<input name="buy_item" class="btn btn-info" type="submit" value="Vložit do košíku" />';
			}
			else {
				// Položka je vyprodaná
				$html_output .= '<input disbaled name="buy_item" class="btn btn-info" type="submit" value="Vyprodané" disabled/>';
			}	
			// Konec výpisu položky
			$html_output .= '<input name="item_id" type="hidden" value="' . $row["id_polozka"] . '">
							</form>
						</div>
					</div>
				</div>';
				

			if(((($i+1) % 4 == 0) and ($i != 0))  or ($i == ($count_of_items-1)))
			{
				$html_output .= "</div>";
			}

			$i++;
    	}
	}

?>

<!-- CONTINUING IN HTML FROM START OF FILE -->
				</div>
			</div>
			<div class="col-md-1"></div>
		</div>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<h3 class="rest_header text-center m-auto text-uppercase"><?php if($type){echo $type;}else{echo "Všechny položky";}?></h3>
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