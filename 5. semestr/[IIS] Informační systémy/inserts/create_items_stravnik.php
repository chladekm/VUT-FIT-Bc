<?php

    require("connection_credentials.php");
        
    $conn = mysqli_init();
    if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)) {
      die('cannot connect '.mysqli_connecterror());
    }

    // Deleting all items from table
    $sql = "DELETE FROM Stravnik;";
    if ($conn->query($sql) === true) { 
        echo "Records deleted successfully.<br>"; 
    } 
    else { 
        echo "ERROR: Could not able to execute $sql. "
            .$conn->error; 
    } 

    // Creating database TODO PASSWORD!!!
    $sql = 'INSERT INTO Stravnik (id_uzivatel, id_stravnik, uziv_jmeno, heslo)  
            VALUES("1", "1", "xmrazek", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("2", "2", "xnamraza", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("3", "3", "xsnizek", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("4", "4", "xlampicka", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("5", "5", "xjinovatka", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("6", "6", "xkotrba", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("7", "7", "xnovak", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("8", "8", "xvotoupal", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("9", "9", "xvokoun", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("10", "10", "xchlup", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("11", "11", "zelvicka", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("12", "12", "zigi", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("13", "13", "jura", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("14", "14", "palenka", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("15", "15", "zita", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"),
                  ("16", "16", "koutak", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"),
                  ("17", "17", "hrabos", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("18", "18", "lubanek", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("19", "19", "mrakobor", "'. password_hash("heslo", PASSWORD_DEFAULT) .'"), 
                  ("20", "20", "dratenka", "'. password_hash("heslo", PASSWORD_DEFAULT) .'");';

    if ($conn->query($sql) === true) { 
        echo "Records inserted successfully.<br>"; 
    } 
    else { 
        echo "ERROR: Could not able to execute $sql. "
            .$conn->error; 
      } 

    $conn = null;
?>