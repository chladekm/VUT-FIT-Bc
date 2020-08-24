<!DOCTYPE html>
<?php
	session_start();
?>

<html lang="cz">
<head>
	<title>EatIT</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">
		button.id_select {
			visibility: hidden;
		}
	</style>
</head>

<body>
	<br>
	<br>
	<form action="projekt.php" method="get">
    	<input type="submit" name="show_rest_action" value="PROVOZOVNY" />
    	<input type="submit" name="show_foods_action" value="JÍDLA"/>
	</form>


	<script type="text/javascript">
		document.getElementById("id_filter").style.display="none"
	</script>

	<!--<a href="projekt.php?show_rest_action=PROVOZOVNY">PROVOZOVNY</a>
	<a href="projekt.php?show_foods_action=JÍDLA">JÍDLA</a>-->

	<?php
	 	if($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_rest_action'])) {
        	show_restaurants();
    	}
    	elseif ($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_foods_action'])) {
    		show_foods();
    	}
    	elseif ($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['show_filter_foods_action'])) {
    		show_filter_foods();
    	}
    	elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['show_food_list'])) {
    		show_food_list_by_rest();
    	}
    	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['buy_item'])) {
    		//echo $_POST['item_id'];
    		// Vytvorit objednavku a polozky objednavky, musi se take urcit jestli je uzivatel registrovany/prihlaseny a podle toho bud vygenerovat nebo priradit id uzivatele.
    		// Doted je tedy potreba brat z nejakyho nereg_uzivatele, kterej ma v databazi prava pouze na zobrazeni nekterych tabulek. Ted se ale musi bud prihlasit, nebo bude mit formular.
    		?>
    		<form action="<?= $_SERVER['PHP_SELF']?>"method="post">
				<label for="login">Login:</label>
				<input type="text" name="login" id="login" required="true">
				<br>
				<label for="pwd">Heslo:</label>
				<input type="password" name="pwd" id="pwd" required="true">
				<br>
				<input type="submit" value="Odeslat">
			</form>
    		<?php
    	}
    	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['login']) and isset($_POST['pwd'])) {
    		echo $_POST['pwd'];
    		session_start();
			$login = $_POST['login'];
			$pwd = $_POST['pwd'];
			if ($login =='admin' && $pwd =='SpravneHeslo') {
				$_SESSION['user'] = $login;
				header("Location: http://{$_SERVER['SERVER_NAME']}/admin.php");
			}
    	}

    	function show_filter(string $selected)
    	{
    		?><form action="projekt.php" method="get">
				<select name="show_filter_foods_action">
				  <option <?php if($selected == "vse") echo "selected=selected "?>value="vse">vše</option>
				  <option <?php if($selected == "pizza") echo "selected=selected "?>value="pizza">pizza</option>
				  <option <?php if($selected == "vegetarianske") echo "selected=selected "?>value="vegetarianske">vegetariánské</option>
				  <option <?php if($selected == "veganske") echo "selected=selected "?>value="veganske">veganské</option>
				  <option <?php if($selected == "ryby") echo "selected=selected "?>value="ryby">ryby</option>
				  <option <?php if($selected == "bezne") echo "selected=selected "?>value="bezne">běžné</option>
				</select>
				<input type='submit' name='submit'/>
			</form>
			<?php
    	}

    	// Funkce pro navazani spojeni se serverem
    	function connect_to_server()
    	{
    		require("connection_credentials.php");
			// Connecting to server
	  		$conn = new mysqli($servername, $username, $password, $dbname);
	  		if ($conn->connect_error) {
	     		die("Connection failed: " . $conn->connect_error);
	  		}
	  		return $conn;
    	}

    	// Funkce pro zobrazeni vsech provozoven
    	function show_restaurants()
    	{
			$conn = connect_to_server();
	  		$sql = "SELECT nazev, id_provozovna FROM Provozovna;";
	  		
	  		if ($result = $conn->query($sql))
	  			// output data of each row
		   		while($row = $result->fetch_assoc()) {
		        	echo "<a href=\"projekt.php?show_food_list=". $row["id_provozovna"]. "\">". $row["nazev"]. "</a><br>";
		    	}
		}

		// Funkce pro zobrazeni vsech jidel
		function show_foods()
    	{
    		show_filter("vse");
			$conn = connect_to_server();
	  		$sql = "SELECT id_polozka, nazev, popis, cena FROM Polozka;";
	  		
	  		
	  		if ($result = $conn->query($sql))
		  		// output data of each row
		   		while($row = $result->fetch_assoc()) {
		        	echo "<strong>" . $row["nazev"]. "</strong><br>" . $row["popis"] . "<br>" . $row["cena"] . " Kč";
					?>
					<form action="projekt.php" method="post">
						<input name="buy_item" type="submit" value="Přidat do košíku" />
						<input name="item_id" type="hidden" value="<?php echo $row["id_polozka"]; ?>">
					</form>
					<br>
					<?php
		    	}
		}

		// Funkce pro zobrazeni urcitych jidel
		function show_filter_foods()
		{
			$conn = connect_to_server();
			$sql = "SELECT id_polozka, nazev, popis, cena FROM Polozka";

			if ($_GET['show_filter_foods_action'] == "vse") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= ';';
			}
			elseif ($_GET['show_filter_foods_action'] == "pizza") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= " WHERE typ='pizza';";
			}
			elseif ($_GET['show_filter_foods_action'] == "vegetarianske") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= " WHERE typ='vegetarianske';";
			}
			elseif ($_GET['show_filter_foods_action'] == "veganske") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= " WHERE typ='veganske';";
			}
			elseif ($_GET['show_filter_foods_action'] == "ryby") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= " WHERE typ='ryby';";
			}
			elseif ($_GET['show_filter_foods_action'] == "bezne") {
				show_filter($_GET['show_filter_foods_action']);
				$sql .= " WHERE typ='bezne';";
			}

	  		if ($result = $conn->query($sql))
		   		// output data of each row
		   		while ($row = $result->fetch_assoc()) {
		        	echo "<strong>" . $row["nazev"]. "</strong><br>" . $row["popis"] . "<br>" . $row["cena"] . " Kč";
					?>
					<form action="projekt.php" method="post">
						<input name="buy_item" type="submit" value="Přidat do košíku" />
						<input name="item_id" type="hidden" value="<?php echo $row["id_polozka"]; ?>">
					</form>
					<br>
					<?php
		    	}
		}

		// Funkce pro zobrazeni jidelnicku podle provozovny
		function show_food_list_by_rest()
		{
			require("connection_credentials.php");
			$pdo = new PDO($dsn, $username, $password, $options);
			$stmt = $pdo->prepare('SELECT id_polozka, nazev, popis, cena FROM Polozka WHERE id_provozovna=:id_provozovna');
			$stmt->execute(['id_provozovna' => $_GET['show_food_list']]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				echo "<strong>" . $row["nazev"]. "</strong><br>" . $row["popis"] . "<br>" . $row["cena"] . " Kč";
				?>
				<form action="projekt.php" method="post">
					<input name="buy_item" type="submit" value="Přidat do košíku" />
					<input name="item_id" type="hidden" value="<?php echo $row["id_polozka"]; ?>">
				</form>
				<br>
				<?php
			}
		}	

  	?>

</body>
</html>
