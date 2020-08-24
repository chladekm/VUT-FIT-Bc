<?php

  require("connection_credentials.php");
 
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
    die('cannot connect '.mysqli_connecterror());
  }

  // Deleting all items from table
  $sql = "DELETE FROM Objednavka_je_Tvorena;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Objednavka_je_Tvorena (id_objednavka_polozka, id_objednavka, id_polozka)  
          VALUES('1', '1', '1'),
                ('2', '2', '38'), 
                ('3', '3', '57'), 
                ('4', '4', '67'),
                ('5', '4', '74'),
                ('6', '5', '75');"; 
  if ($conn->query($sql) === true) { 
      echo "Records inserted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  $conn = null;
?>