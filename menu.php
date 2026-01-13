
<!-- TO DO :  Redirect to login if user not logged in
<
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treggo | Main Menu</title>

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: white;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand logo " href="#">Treggo</a>

        <form class="form-inline mx-auto" action="search_results.php">
            <input class="form-control mr-sm-2" type="search" placeholder="Search for items" name="top-search">
        </form>

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-heart"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-envelope"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-bell"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php"><i class="fa fa-user"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fa fa-sign-out"></i> Log out</a>
            </li>
        </ul>
    </nav>

    <!-- Centered Category Menu -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav navbar-nav-center">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Women</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">All</a>
                        <a class="dropdown-item" href="#">Clothing</a>
                        <a class="dropdown-item" href="#">Shoes</a>
                        <a class="dropdown-item" href="#">Accessories</a>
                        <a class="dropdown-item" href="#">Beauty</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Men</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">All</a>
                        <a class="dropdown-item" href="#">Clothing</a>
                        <a class="dropdown-item" href="#">Shoes</a>
                        <a class="dropdown-item" href="#">Accessories</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Kids</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">All</a>
                        <a class="dropdown-item" href="#">Clothing</a>
                        <a class="dropdown-item" href="#">Toys</a>
                        <a class="dropdown-item" href="#">Nursing</a>
                        <a class="dropdown-item" href="#">Shoes</a>
                        <a class="dropdown-item" href="#">Bedding</a>
                        <a class="dropdown-item" href="#">Pushchairs & Car Seats</a>
                        <a class="dropdown-item" href="#">Other</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Home</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Kitchen tools</a>
                        <a class="dropdown-item" href="#">Tableware</a>
                        <a class="dropdown-item" href="#">Household care</a>
                        <a class="dropdown-item" href="#">Textiles</a>
                        <a class="dropdown-item" href="#">Home accessories</a>
                        <a class="dropdown-item" href="#">Office supplies</a>
                        <a class="dropdown-item" href="#">Tools & DIY</a>
                        <a class="dropdown-item" href="#">Outdoor & Garden</a>
                        <a class="dropdown-item" href="#">Pet care</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Electronics</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Computers & Accessories</a>
                        <a class="dropdown-item" href="#">Mobile Phones & Communication</a>
                        <a class="dropdown-item" href="#">Audio & Headphones</a>
                        <a class="dropdown-item" href="#">Cameras & Accessories</a>
                        <a class="dropdown-item" href="#">Tablets, e-readers & Accessories</a>
                        <a class="dropdown-item" href="#">TV & Wearables</a>
                        <a class="dropdown-item" href="#">Beauty & Personal Care Electronics</a>
                        <a class="dropdown-item" href="#">Other</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Entertainment</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Books</a>
                        <a class="dropdown-item" href="#">Magazines</a>
                        <a class="dropdown-item" href="#">Music</a>
                        <a class="dropdown-item" href="#">Video</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Hobbies & Collectables</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Cards</a>
                        <a class="dropdown-item" href="#">Board Games</a>
                        <a class="dropdown-item" href="#">Puzzles</a>
                        <a class="dropdown-item" href="#">Stamps & Postcards</a>
                        <a class="dropdown-item" href="#">Arts & Crafts</a>
                        <a class="dropdown-item" href="#">Coins & Banknotes</a>
                        <a class="dropdown-item" href="#">Musical Instruments</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Sports</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Cycling</a>
                        <a class="dropdown-item" href="#">Fitness & Yoga</a>
                        <a class="dropdown-item" href="#">Water Sports</a>
                        <a class="dropdown-item" href="#">Outdoor Sports</a>
                        <a class="dropdown-item" href="#">Winter Sports</a>
                        <a class="dropdown-item" href="#">Casual Sports</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Platform</a>
                </li>

            </ul>
        </div>
    </nav>

</div>

<!-- Scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>

</body>
</html>
