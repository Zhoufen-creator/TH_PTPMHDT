<?php
require_once 'app/helpers/SessionHelper.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore | Quản lý sản phẩm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .tech-navbar {
            background-color: rgba(18, 18, 22, 0.9) !important;
            border-bottom: 1px solid rgba(255, 42, 117, 0.3);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .tech-navbar .navbar-brand {
            color: #ff2a75 !important;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(255, 42, 117, 0.5);
            letter-spacing: 1px;
        }

        .tech-navbar .nav-link {
            color: #cccccc !important;
            transition: 0.3s;
        }

        .tech-navbar .nav-link:hover {
            color: #ff2a75 !important;
            text-shadow: 0 0 8px rgba(255, 42, 117, 0.5);
        }

        .username {
            color: #00d4ff !important;
            font-weight: 600;
        }

        .admin-badge {
            color: gold !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100"
      style="background-color: #121216; overflow-x: hidden;">

    <nav class="navbar navbar-expand-lg tech-navbar sticky-top">
        <div class="container">

            <a class="navbar-brand" href="/Product/">
                STORE CUA KHANG
            </a>

            <button class="navbar-toggler border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"
                      style="filter: invert(1) sepia(1) saturate(5) hue-rotate(300deg);">
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <!-- Danh sách sản phẩm -->
                    <li class="nav-item">
                        <a class="nav-link" href="/Product/">
                            Danh sách sản phẩm
                        </a>
                    </li>

                    <!-- Thêm sản phẩm -->
                    <li class="nav-item">
                        <a class="nav-link" href="/Product/add">
                            Thêm sản phẩm
                        </a>
                    </li>

                    <!-- Giỏ hàng -->
                    <li class="nav-item">
                        <a class="nav-link" href="/Cart/index">
                            <i class="fas fa-shopping-cart"
                            style="color: #ff2a75;"></i>
                            Giỏ hàng

                            <?php
                            $cartCount = 0;

                            if (SessionHelper::isLoggedIn()) {

                                require_once 'app/Services/CartService.php';
                                require_once 'app/config/database.php';

                                $db = (new Database())->getConnection();
                                $cartService = new CartService($db);

                                $cartCount = $cartService->getCartCount();
                            }

                            if ($cartCount > 0) {
                                echo '<span
                                        style="
                                            background-color:#ff2a75;
                                            color:white;
                                            border-radius:50%;
                                            padding:2px 6px;
                                            font-size:0.8rem;
                                            margin-left:5px;
                                        ">
                                        ' . $cartCount . '
                                    </span>';
                            }
                            ?>
                        </a>
                    </li>

                    <?php if (SessionHelper::isLoggedIn()) : ?>

                        <!-- Username -->
                        <li class="nav-item">
                            <span class="nav-link username">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($_SESSION['fullname']) ?>
                            </span>
                        </li>

                        <!-- Admin -->
                        <?php if (SessionHelper::isAdmin()) : ?>
                            <li class="nav-item">
                                <span class="nav-link admin-badge">
                                    <i class="fas fa-crown"></i>
                                    Admin
                                </span>
                            </li>
                        <?php endif; ?>

                        <!-- Logout -->
                        <li class="nav-item">
                            <a class="nav-link text-danger"
                               href="/account/logout">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>

                    <?php else : ?>

                        <!-- Login -->
                        <li class="nav-item">
                            <a class="nav-link"
                               href="/account/login">
                                <i class="fas fa-sign-in-alt"></i>
                                Login
                            </a>
                        </li>

                        <!-- Register -->
                        <li class="nav-item">
                            <a class="nav-link"
                               href="/account/register">
                                <i class="fas fa-user-plus"></i>
                                Register
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 position-relative">