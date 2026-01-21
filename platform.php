<?php
require_once "includes/login/menu.php";
?>

<div class="container my-5">

    <!-- Hero Section -->
    <div class="jumbotron text-center bg-light p-5 rounded">
        <h1 class="display-4">Welcome to <span class="text-success">Treggo</span> Platform</h1>
        <p class="lead">A modern selling platform to manage your products, orders, and customers easily.</p>
        <a href="products.php" class="btn btn-primary btn-lg mt-3">
            ← Back to Home
        </a>
    </div>

    <!-- Features Section -->
    <h2 class="mb-4 text-center">Platform Features</h2>
    <div class="row g-4">

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-box fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Product Management</h5>
                    <p class="card-text">Add, edit, and organize your products easily with categories and images.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Order Tracking</h5>
                    <p class="card-text">Monitor customer orders, update status, and manage deliveries efficiently.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-3x text-success mb-3"></i>
                    <h5 class="card-title">User & Role Management</h5>
                    <p class="card-text">Manage your team, assign roles, and control access to your platform securely.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-chart-line fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Reports & Analytics</h5>
                    <p class="card-text">Get insights on sales, customers, and products with easy-to-read analytics.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-mobile-alt fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Mobile Friendly</h5>
                    <p class="card-text">Access the platform from any device, anytime, anywhere.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5">
        <a href="login.php" class="btn btn-success btn-lg">
            Get Started with Treggo
        </a>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="css/plugins/toastr/toastr.min.css">
<script src="js/plugins/toastr/toastr.min.js"></script>
<script src="js/inactivityLogout.js"></script>
