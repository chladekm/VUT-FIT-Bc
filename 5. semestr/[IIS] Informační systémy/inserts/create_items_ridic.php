<?php

  require("connection_credentials.php");
    
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)){
    die('cannot connect '.mysqli_connecterror());
  }

  // Deleting all items from table
  $sql = "DELETE FROM Ridic;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql."
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Ridic (id_uzivatel, id_ridic, licence)  
          VALUES('6', '1', 'skupina B'),
                ('7', '2', 'skupina A, B'),
                ('8', '3', 'skupina B, B1'),
                ('9', '4', 'skupina B, C1'),
                ('10', '5', 'skupina B, C') 
                ;"; 
 
  if ($conn->query($sql) === true) { 
      echo "Records inserted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  $conn = null;
?>