<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      template_header.php                                                !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !------------------------------------------------------------------------------->

    <head>

        <?php include 'links.php';?>
        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/navbar.css">
        <!-- Own script -->
        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript" src="js/up_button.js"></script>

    </head>
    <body id="body_id" onLoad="renderTime();">

        <!-- studijni zdroj: https://stackoverflow.com/questions/48472771/bootstrap-4-responsive-sidebar-menu-to-top-navbar -->
    	<div class="container-fluid h-100">
            <div class="row p-0 h-100">
                <aside class="p-0 col-12 col-md-2 fixed-top menu_panel">
                    <nav class="p-0 navbar navbar-expand-md navbar-dark align-items-start flex-md-column flex-row">
                        <div class="container_logo">
                            <a class="nav-link text-nowrap p0" href="home.php"><img src="./img/evidit_logo.png" class="logo"></a>
                        </div>
                        <a href class="navbar-toggler" data-toggle="collapse" data-target=".sidebar">
                           <span class="navbar-toggler-icon"></span>
                        </a>
                        <div class="collapse navbar-collapse sidebar">
                            <ul class="flex-column navbar-nav w-100 justify-content-between">
                                <li class="nav-item">
                                    <a class="nav-link" href="devices.php"><i class="fas fa-laptop"></i><span>Zařízení</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="repairs.php"><i class="fas fa-cog"></i><span>Opravy</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="employees.php"><i class="fas fa-users"></i><span>Zaměstnanci</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="rooms.php?patro=1"><i class="fas fa-chair"></i><span>Místnosti</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="stats.php"><i class="fas fa-chart-pie"></i><span>Statistiky</span></a>
                                </li>
                                <li class="nav-item username_mobile_menu_link">
                                    <button type="button" data-toggle="collapse" data-target="#username_collapse">
                                        <i class="fas fa-user"></i><span>Natálie Orlová</span>
                                    </button>
                                    <div id="username_collapse" class="collapse">
                                        <div class="row">
                                            <div class="col-12 text-center"><a class="nav-link" href="index.php"><i class="fas fa-sign-out-alt"></i><span>&nbsp;&nbsp; Odhlásit</span></a></div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </aside>
                <main class="p-0 col offset-md-2 py-3">
                    <div class="row top_panel offset-md-2 fixed-top">
                        <div id="display_clock"></div>
                        <div class="username_block">
                            <div class="dropdown">
                                  <button type="button" class="username dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i>&nbsp;&nbsp;<span>Natálie Orlová</span>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-right username_dropdown">
                                    <a class="dropdown-item" href="index.php"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Odhlásit</a>
                                  </div>
                            </div>
                        </div>
                    </div>

                    <section id="main_content">

                   