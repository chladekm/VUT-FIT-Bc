<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      home.php                                                           !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !------------------------------------------------------------------------------->

<!doctype html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>EvidIT - Systém pro evidenci techniky</title>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700,800&amp;subset=latin-ext" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700&amp;subset=latin-ext" rel="stylesheet">

        <!-- Google jQuery CDN -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <!-- Bootstrap - Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Awesome font icon -->
        <link href="fontawesome_free_5.11.2/css/fontawesome.css" rel="stylesheet">
        <link href="fontawesome_free_5.11.2/css/brands.css" rel="stylesheet">
        <link href="fontawesome_free_5.11.2/css/solid.css" rel="stylesheet">

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/style_index.css">

        <!-- Own script -->
        <script type="text/javascript" src="js/script.js" defer></script>

        <!-- Scripts for this flow only -->
        <script type="text/javascript">
          
          // Function resizes background image based on measurements of the current window
          $(window).resize(function()
          {
            var h=$(window).height();
            var w=$(window).width();
            if((w*4)>(h*5)) //Picture ratio is 5:4 (width:height)
              $("body").css("background-size","100% auto");
            else
              $("body").css("background-size","auto 100%");
          });
      </script>

    </head>
    <body onLoad="renderTime();">
        <div class="container-fluid h-100">
            <div class="row home_top_panel">
                <div id="display_clock"></div>
                <div class="home_username_block">
                    <div class="dropdown">
                          <button type="button" class="home_username dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-user"></i>&nbsp;&nbsp;<span>Natálie Orlová</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right mt-0 home_username_dropdown">
                            <a class="dropdown-item" href="index.php"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Odhlásit</a>
                          </div>
                    </div>
                </div>
            </div>
            <main>
                <div class="col">
                        <img class="home_logo" src="img/evidit_logo.png">
                        <p class="homo_logo_text">Systém pro evidenci techniky</p>
                </div> 
                <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-2 text-center">
                                <a href="devices.php" class="icon_div_block"><div class="icon_div"><i class="fas fa-laptop"></i><h2>Zařízení</h2></div></a>
                        </div>
                        <div class="col-md-2 text-center">
                                <a href="repairs.php" class="icon_div_block"><div class="icon_div"><i class="fas fa-cog"></i><h2>Opravy</h2></div></a>
                        </div>
                        <div class="col-md-2 text-center">
                                <a href="employees.php" class="icon_div_block"><div class="icon_div"><i class="fas fa-users"></i><h2>Zaměstnanci</h2></div></a>
                        </div>
                        <div class="col-md-3"></div>
                </div>
                <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-2 text-center">
                                <a href="rooms.php?patro=1" class="icon_div_block"><div class="icon_div"><i class="fas fa-chair"></i><h2>Místnosti</h2></div></a>
                        </div>
                        <div class="col-md-2 text-center">
                                <a href="stats.php" class="icon_div_block"><div class="icon_div"><i class="fas fa-chart-pie"></i><h2>Statistiky</h2></div></a>
                        </div>
                        <div class="col-md-4"></div>
                </div>
            </main>
            
        </div>

        <footer id="footer">
                
        </footer>
    </body>
</html>