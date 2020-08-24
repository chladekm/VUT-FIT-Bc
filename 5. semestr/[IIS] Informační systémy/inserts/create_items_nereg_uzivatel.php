<?php

  require("connection_credentials.php");
    
  $conn = mysqli_init();
  if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, $socket)){
    die('cannot connect '.mysqli_connecterror());
  }

  // Deleting all items from table
  $sql = "DELETE FROM Nereg_uzivatel;";
  if ($conn->query($sql) === true) { 
      echo "Records deleted successfully.<br>"; 
  } 
  else { 
      echo "ERROR: Could not able to execute $sql. "
          .$conn->error; 
  } 

  // Creating database
  $sql = "INSERT INTO Nereg_uzivatel (id_uzivatel, jmeno, prijmeni, adresa, psc, telefon)
                -- 1 - 5 operator/admin
                -- 6 - 10 ridic
                -- 11 - 20 stravnik
                -- 21 - ... neregistrovany uzivatel

          VALUES('1', 'Jan', 'Mrázek', 'Celní 5', '63800', '+420720576617'), 
                ('2', 'Josef', 'Námraza', 'Krásná 2', '64300', '+420930432748'), 
                ('3', 'Milan', 'Snížek', 'Táborská 32', '63400', '+420638493728'),
                ('4', 'Adam', 'Lampička', 'Slovákova 21', '60200', '+420739202928'),
                ('5', 'Emil', 'Jinovatka', 'Krásná 4', '63800', '+420638281020'),
                ('6', 'Radka', 'Kotrba', 'Spádová 2', '64300', '+420283728203'),
                ('7', 'Fiona', 'Novák', 'Křenová 43', '60200', '+420736281002'),
                ('8', 'Tom', 'Votoupal', 'Krásná 2', '64300', '+420748263522'),
                ('9', 'Oleg', 'Vokoun', 'Křenová 22', '60200', '+420536729241'),
                ('10', 'Bohuslav', 'Chlup', 'Křenová 34', '60200', '+420293827121'),
                ('11', 'Vincenc', 'Neugebauer', 'Ořechovská 643', '66451', '638218299'),
                ('12', 'Richard', 'Zikmund', 'Tikovická 453', '60200', '738472382'),
                ('13', 'Jiří', 'Doubrava', 'Křenová 354', '66446', '00420637288123'),
                ('14', 'Vendelín', 'Páleník', 'Ořechovská 32', '60200', '+420840237299'),
                ('15', 'Zita', 'Ulrichová', 'Foerstrova 346', '64200', '+420748909023'),
                ('16', 'Dita', 'Koutová', 'Spáčilova 788', '60200', '+420638936273'),
                ('17', 'Justýna', 'Hrabánková', 'Vlhká 12', '60200', '+420647389233'),
                ('18', 'Lubor', 'Víšek', 'Vlhká 343', '60200', '+420637482938'),
                ('19', 'Emanuel', 'Bendík', 'Tikovická 12', '66451', '+420647839230'),
                ('20', 'Tadeáš', 'Loukota', 'Křenová 46', '66446', '+420748392021'),
                ('21', 'Růžena', 'Rašková', 'Foerstrova 352', '64200', '+420748392012'),
                ('22', 'Dana', 'Prachařová', 'Tikovická 778', '66446', '+420648392783'),
                ('23', 'Milena', 'Reková', 'Křenová 534', '60200', '720394032'),
                ('24', 'René', 'Zedník', 'Spáčilova 73', '66451', '363828488'),
                ('25', 'Jan', 'Fusek', 'Spáčilova 23', '60200', '237492832'),
                ('26', 'Vlasta', 'Hudečková', 'Tikovická 678', '64200', '+420038493221'),
                ('27', 'Božena', 'Hnízdilová', 'Ořechovská 663', '66451', '+420837261234'),
                ('28', 'Liběna', 'Bolková', 'Foerstrova 534', '66446', '00420173920322'),
                ('29', 'Zikmund', 'Mayer', 'Vlhká 23', '60200', '00420748290121')
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