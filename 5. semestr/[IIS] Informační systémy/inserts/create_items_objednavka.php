<?php

  require("connection_credentials.php");
 
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
    die('cannot connect '.mysqli_connecterror());
  }

  // Deleting all items from table
  $sql = "DELETE FROM Objednavka;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Objednavka (id_uzivatel, id_objednavka, stav, cena)  
          VALUES('1', '1', 'odeslana', '119'), 
                ('2', '2', 'pripravena', '55'), 
                ('3', '3', 'dorucena', '110'),
                ('3', '4', 'na ceste', '317'),
                ('4', '5', 'dorucena', '219');"; 
  if ($conn->query($sql) === true) { 
      echo "Records inserted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  $conn = null;
?>