<?php

    require("connection_credentials.php");

    $conn = new PDO($dsn, $username, $password, $options);
    
    try
    {
        $sql = "CREATE USER 'martin'@'$servername' IDENTIFIED BY 'martin'";
        $conn->exec($sql);
        $sql = "GRANT ALL PRIVILEGES ON `IIS_database`.* TO 'martin'@'$servername'";
        $conn->exec($sql);
        echo "Succesfully created user martin.\n";

        $sql = "CREATE USER 'borek'@'$servername' IDENTIFIED BY 'borek'";
        $conn->exec($sql);
        $sql = "GRANT ALL PRIVILEGES ON `IIS_database`.* TO 'borek'@'$servername'";
        $conn->exec($sql);
        echo "Succesfully created user borek.\n";

        $conn = new PDO($dsn, $username, $password, $options);
        $sql = "CREATE USER 'fanda'@'$servername' IDENTIFIED BY 'fanda'";
        $conn->exec($sql);
        $sql = "GRANT ALL PRIVILEGES ON `IIS_database`.* TO 'fanda'@'$servername'";
        $conn->exec($sql);
        echo "Succesfully created user fanda.\n";
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;

?>