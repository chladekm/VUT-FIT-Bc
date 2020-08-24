<?php

  require("connection_credentials.php");
    
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)){
    die('cannot connect '.mysqli_connecterror());
  }

  // Deleting all items from table
  $sql = "DELETE FROM Operator_Admin;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql."
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Operator_Admin (id_uzivatel, id_operator, rodne_cislo, mzda, cislo_uctu, je_admin)  
          VALUES('1', '1', '700120/4020', '12000', '4392620237/0800', 1),
                ('2', '2', '831120/4040', '32000', '1343957620/0200', 0), 
                ('3', '3', '940820/4003', '40000', '7384555362/4040', 1), 
                ('4', '4', '870720/4020', '34000', '8372882920/2050', 0), 
                ('5', '5', '820420/4060', '38000', '2034992123/0800', 0) 
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