<?php

	require("connection_credentials.php");
	require("navbar.php");
	include("links.php");

	echo '<body class="gray_body">';

	if (!$_POST["return"]) {
		if (!$_FILES["fileToUpload"]["name"]) {
			echo "Soubor nebyl zvolen, nebylo možné nahrát obrázek.";
		}
		else {
			$target_dir = "uploads/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
			    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			    if($check !== false) {
			        $uploadOk = 1;
			    } else {
			        $uploadOk = 0;
			    }
			}

			// Check file size
			if ($_FILES["fileToUpload"]["size"] > 500000) {
			    echo '<p class="info-danger">Nahraný soubor je příliš velký.</p>';
			    $uploadOk = 0;
			}
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			    echo '<p class="info-warning">Podporované formáty jsou: JPG, JPEG, PNG & GIF.</p>';
			    $uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
			    echo '<p class="info-danger">Váš soubor bohužel nebyl nahrán.</p>';
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "uploads/". $_POST["id_polozka"])) {
			        echo '<p class="info-success">Soubor byl úspěšně nahrán.</p>';
			    } else {
			        echo '<p class="info-danger">Soubor nebylo možné nahrát.</p>';
			    }
			}
		}	
		echo '
		<div class="text-center">
			<p class="upload_go_back"><a class="" href="operator_edit_polozky.php?"><i class="fas fa-arrow-circle-left"></i><br><span>Zpět na správu položek</span></a></p>
		</div>
		';

		echo '</body>';
	}
	else {
		?>
				<script type="text/javascript">
					window.location.href = '<?php require ("connection_credentials.php"); echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '/operator_edit.php?'. session_id(); ?>';
				</script>
			<?php
		}	
?>