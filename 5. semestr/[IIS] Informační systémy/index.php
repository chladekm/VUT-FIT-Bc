<?php

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
		include("links.php");
		require("navbar.php");
		
	// 	if ($_SESSION['user']) {
	// 		echo '<span class="text-white">Already logged in!<br><strong>Your ID (id_uzivatel) is: </strong>' .  $_SESSION['id'] . "<br></span>";
	// 	}
	// 	else {
	// 		$_SESSION['user'] = NULL;
	// 	}
	}

	// echo $_SESSION['user'];
	// echo session_id();

	function html_nereg_user()
	{
		echo '
		<div class="row">
			<div class="col-md-6 p-1">
				<div class="index_item">
					<a class="" href="restaurants.php?"><span><i class="fas fa-utensils"></i><br>Provozovny</span></a>
				</div>
			</div>
			<div class="col-md-6 p-1">
				<div class="index_item">
					<a class="" href="foods.php?"><span><i class="fas fa-hamburger"></i><br>Nabídka jídel</span></a>
				</div>
			</div>
		</div>';
	}

	function html_user()
	{
		echo '
		<div class="row">
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="restaurants.php?"><span><i class="fas fa-utensils"></i><br>Provozovny</span></a>
				</div>
			</div>
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="foods.php?"><span><i class="fas fa-hamburger"></i><br>Nabídka jídel</span></a>
				</div>
			</div>
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="my_orders.php?"><span><i class="fas fa-shipping-fast"></i></i><br>Mé objednávky</span></a>
				</div>
			</div>
		</div>';
	}

	function html_ridic()
	{
		echo '
		<h3 class="index_header mt-5 mb-4">Administrativa</h3>		
		<div class="row">
			<div class="col-md-4 p-1"></div>
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="driver.php?"><span><i class="fas fa-car"></i><br>Rozvoz</span></a>
				</div>
			</div>
			<div class="col-md-4 p-1"></div>';
	}

	function html_operator_admin()
	{
		echo '
		<h3 class="index_header mt-5 mb-4">Administrativa</h3>		
		<div class="row">
			<div class="col-md-2 p-1"></div>
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="operator_edit.php?"><span><i class="fas fa-edit"></i><br>Správa</span></a>
				</div>
			</div>
			<div class="col-md-4 p-1">
				<div class="index_item">
					<a class="" href="list_orders.php?"><span><i class="fas fa-list"></i><br>Objednávky</span></a>
				</div>
			</div>
			<div class="col-md-2 p-1"></div>
		</div>';
	}

	function is_employee()
	{
		if( (!($_SESSION['user'])) or ($_SESSION['user'] == 'stravnik') )
			return 0;
		else
			return 1;
	}
?>

<!---------------------------- Zacatek html dokument vypisu ------------------------------------->
<!DOCTYPE html>
<html lang="cz">
<head>
	<title>EatIT</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">
		button .id_select {
			visibility: hidden;
		}
	</style>
</head>

<body class="index_body">
	<div class="container-fluid">
		<!-- <div class="row bg-success"> -->
		<div class="m-auto w-50 <?php if(is_employee()){echo "index_container_employee";}else{echo "index_container_user";} ?>" >
				<div class="login_div m-auto text-center">
					<h3 class="index_header mb-4">Domovská stránka</h3>
					<?php 
					if(!($_SESSION['user'])) 
						html_nereg_user(); 
					elseif($_SESSION['user'] == 'stravnik') 
						html_user();
					elseif($_SESSION['user'] == 'ridic'){
						html_user();
						html_ridic();
					}
					elseif(($_SESSION['user'] == 'admin') or ($_SESSION['user'] == 'operator')){
						html_user();
						html_operator_admin();
					}

					?>
				</div>

		</div>
	</div>
</body>
</html>
