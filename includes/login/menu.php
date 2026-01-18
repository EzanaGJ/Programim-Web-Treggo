<?php

if(isset($_SESSION['id'])) {
    include "menu.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treggo | Menu</title>

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
            <input class="form-control mr-sm-2"
                   type="search"
                   placeholder="Search for items..."
                   name="top-search"
                   style="width: 350px;">
        </form>


        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-bell"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php"><i class="fa fa-user"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fa fa-sign-out"></i> Log out</a>
            </li>
        </ul>
    </nav>

