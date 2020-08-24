<?php

  require("connection_credentials.php");
    
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
    die('cannot connect '.mysqli_connecterror());
  }
  
  // Deleting all items from table
  $sql = "DELETE FROM Provozovna;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Provozovna (id_provozovna, nazev, adresa)  
          VALUES('1', 'AMICI', 'Křenová 69'), 
                ('2', 'K2', 'Táborská 71'), 
                ('3', 'Dřevěný orel', 'Královská 23'),
                ('4', 'Restaurace Sokec', 'Nádražní 12'),
                ('5', 'Restaurace Pastouška', 'Jílkova 219'),
                ('6', 'La Patas', 'U Leskavy 39'), 
                ('7', 'U Kašny', 'Rajhradská 20');"; 
  if ($conn->query($sql) === true) { 
      echo "Records inserted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  $conn = null;
?>