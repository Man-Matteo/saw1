<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Bilbo+Swash+Caps">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Navbar</title>
    </head>
    <body class="navbar-body">
        <?php
            if (!isset($_SESSION['username'])) {
                header("Location: ../user/login.php");
                exit();
            }
        ?>
        <nav class="navbar">
            <div class="navbar-container">
                <div class="navbar-title">
                    <img alt="Title" src="../Images/title_white.png">
                </div>

                <form class="navbar-search" action="../navbar/search.php" method="get">
                    <input type="text" placeholder="Search..." name="search">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
                
                <ul class="navbar-nav-links">
                    <li><a href='../navbar/bestiary.php'>Bestiary</a></li>
                    <li><a href='../navbar/bestseller.php'>Bestseller</a></li>
                    <li><a href='../navbar/cart.php'>Cart</a></li>
                    <li><a href='../navbar/show_profile.php'>Profile</a></li>
                    <li><a href='../navbar/contact_us.php'>Contact us</a></li>
                </ul>
            </div>
        </nav>
    </body>
</html>