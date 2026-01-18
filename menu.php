<?php
require_once "includes/login/top_menu.php";
?>

<div class="container-fluid p-0">
    <!-- Top Navbar -- -->
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
                <a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i></a>
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


    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav navbar-nav-center">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Women</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Women">All</a>
                        <a class="dropdown-item" href="products.php?category=Women&subcategory=Clothing">Clothing</a>
                        <a class="dropdown-item" href="products.php?category=Women&subcategory=Shoes">Shoes</a>
                        <a class="dropdown-item" href="products.php?category=Women&subcategory=Accessories">Accessories</a>
                        <a class="dropdown-item" href="products.php?category=Women&subcategory=Beauty">Beauty</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Men</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Men">All</a>
                        <a class="dropdown-item" href="products.php?category=Men&subcategory=Clothing">Clothing</a>
                        <a class="dropdown-item" href="products.php?category=Men&subcategory=Shoes">Shoes</a>
                        <a class="dropdown-item" href="products.php?category=Men&subcategory=Accessories">Accessories</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Kids</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Kids">All</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Clothing">Clothing</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Toys">Toys</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Nursing">Nursing</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Shoes">Shoes</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Bedding">Bedding</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Pushchairs%20%26%20Car%20Seats">Pushchairs & Car Seats</a>
                        <a class="dropdown-item" href="products.php?category=Kids&subcategory=Other">Other</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Home</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Kitchen%20tools">Kitchen tools</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Tableware">Tableware</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Household%20care">Household care</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Textiles">Textiles</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Home%20accessories">Home accessories</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Office%20supplies">Office supplies</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Tools%20%26%20DIY">Tools & DIY</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Outdoor%20%26%20Garden">Outdoor & Garden</a>
                        <a class="dropdown-item" href="products.php?category=Home&subcategory=Pet%20care">Pet care</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Electronics</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Computers%20%26%20Accessories">Computers & Accessories</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Mobile%20Phones%20%26%20Communication">Mobile Phones & Communication</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Audio%20%26%20Headphones">Audio & Headphones</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Cameras%20%26%20Accessories">Cameras & Accessories</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Tablets,%20e-readers%20%26%20Accessories">Tablets, e-readers & Accessories</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=TV%20%26%20Wearables">TV & Wearables</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Beauty%20%26%20Personal%20Care%20Electronics">Beauty & Personal Care Electronics</a>
                        <a class="dropdown-item" href="products.php?category=Electronics&subcategory=Other">Other</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Entertainment</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Entertainment&subcategory=Books">Books</a>
                        <a class="dropdown-item" href="products.php?category=Entertainment&subcategory=Magazines">Magazines</a>
                        <a class="dropdown-item" href="products.php?category=Entertainment&subcategory=Music">Music</a>
                        <a class="dropdown-item" href="products.php?category=Entertainment&subcategory=Video">Video</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Hobbies & Collectables</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Cards">Cards</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Board%20Games">Board Games</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Puzzles">Puzzles</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Stamps%20%26%20Postcards">Stamps & Postcards</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Arts%20%26%20Crafts">Arts & Crafts</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Coins%20%26%20Banknotes">Coins & Banknotes</a>
                        <a class="dropdown-item" href="products.php?category=Hobbies%20%26%20Collectables&subcategory=Musical%20Instruments">Musical Instruments</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">Sports</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Cycling">Cycling</a>
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Fitness%20%26%20Yoga">Fitness & Yoga</a>
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Water%20Sports">Water Sports</a>
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Outdoor%20Sports">Outdoor Sports</a>
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Winter%20Sports">Winter Sports</a>
                        <a class="dropdown-item" href="products.php?category=Sports&subcategory=Casual%20Sports">Casual Sports</a>
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

