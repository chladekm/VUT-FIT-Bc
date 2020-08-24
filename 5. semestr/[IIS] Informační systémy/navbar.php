<?php
	session_start();

	require("connection_credentials.php");
	include("functions.php");

	try {
		$pdo = new PDO($dsn, $username, $password);
	} 
	catch (Exception $e) {
		echo "Connection error: ". $e->getMessage();
		die();
	}


	if(!($_SESSION['user'])){
		$jmeno = NULL;	
	}
	else
	{
		$stmt = $pdo->prepare('
					SELECT jmeno, prijmeni
					FROM Nereg_uzivatel
					WHERE id_uzivatel = ?');

		$return_val = $stmt->execute(array($_SESSION['id']));
		test_stmt_exec($return_val, $stmt);
		$result = $stmt->fetch();

		$jmeno = $result['jmeno'] . " " . $result['prijmeni'];
	}

?>

<nav class="navbar navbar-expand-md navbar-dark p-0">
  <a class="navbar-brand pl-2" href="index.php?">
	<img src="img/logo.png" id="logo" width="90" height="30" alt="">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	<ul class="navbar-nav mr-auto">
	  <li class="nav-item">
		<a class="nav-link" href="restaurants.php?">Provozovny</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" href="foods.php?">Nabídka jídel</a>
	  </li>
	  <?php
	  	if(($_SESSION['user'] == 'operator') or ($_SESSION['user'] == 'admin') or ($_SESSION['user'] == 'ridic'))
	  		echo '<div class=" ml-1 mr-1 vertical_separator p-0">⠀</div>';
	  	
	  	if(($_SESSION['user'] == 'operator') or ($_SESSION['user'] == 'admin'))
	  	{
		  echo '
		  <li class="nav-item">
			<a class="nav-link" href="operator_edit.php?">Správa</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="list_orders.php?">Objednávky</a>
		  </li>';
	  	}
	  	if($_SESSION['user'] == 'ridic')
	  	{
		  echo '
		  <li class="nav-item">
			<a class="nav-link" href="driver.php?', session_id(), '">Objednávky - rozvoz</a>
		  </li>';
	  	}
	  ?>
	  <!-- <li class="nav-item dropdown"> -->
	</ul>
	<ul class="navbar-nav ml-auto">
			<li class="nav-item p-2">
				<a class="nav-link" href="cart.php?<?php echo session_id()?>"><i class="fas fa-shopping-cart"></i> Košík</a>
		  	</li>
			<li class="vertical_separator p-0">⠀</li>
		  	<div id="username_block" class="p-2">
				<?php
				  	if($jmeno)
					echo ' 
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" data-hover="dropdown" aria-haspopup="true" aria-expanded="false">
				  	<i class="fas fa-user"></i> ' . $jmeno .
					'</a>
					<div id="username_dropdownmenu" class="dropdown-menu p-0" aria-labelledby="navbarDropdown">
					  <a class="nav-link dropdown-item text-center" href="edit_profile.php">Upravit profil</a>
					  <a class="nav-link dropdown-item text-center" href="my_orders.php?">Mé objednávky</a>
					  <div class="horizontal_divider"></div>
					  <a class="nav-link dropdown-item text-center" href="login.php?"><i class="fas fa-sign-out-alt"></i> Odhlásit se</a>
					</div>';
				  	else
				  	{echo'<a class="nav-link" href="login.php?' . session_id() . '">Přihlásit</a>';}
				  ?>
			</div>
	  </li>
	</ul>
  </div>
</nav>