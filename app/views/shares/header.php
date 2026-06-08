<?php
require_once 'app/helpers/SessionHelper.php';
// Kiểm tra xem có đang ở trang Auth (Login/Register) không
$current_url = $_SERVER['REQUEST_URI'];
$is_auth_page = (strpos($current_url, '/account/login') !== false || strpos($current_url, '/account/register') !== false);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore | Future Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --neon-pink: #ff2a75; --neon-blue: #00d4ff; --dark-bg: #121216; --card-bg: #1e1e26; }
        body { background-color: var(--dark-bg); color: #fff; font-family: 'Roboto', sans-serif; overflow-x: hidden; }
        .tech-title { font-family: 'Orbitron', sans-serif; color: var(--neon-pink); text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); }
        
        /* Navbar Styling */
        .tech-navbar { 
            background: rgba(18, 18, 22, 0.95) !important; 
            border-bottom: 1px solid rgba(255, 42, 117, 0.3); 
            backdrop-filter: blur(10px); 
        }
        .navbar-brand { font-family: 'Orbitron', sans-serif; color: var(--neon-pink) !important; font-weight: bold; }
        .nav-link { color: #ccc !important; transition: 0.3s; font-size: 0.9rem; }
        .nav-link:hover, .nav-link.active { color: var(--neon-pink) !important; text-shadow: 0 0 8px var(--neon-pink); }
        
        /* Button Neon */
        .btn-neon { background: transparent; color: var(--neon-pink); border: 2px solid var(--neon-pink); transition: all 0.3s; font-family: 'Orbitron', sans-serif; font-size: 0.8rem; }
        .btn-neon:hover { background: var(--neon-pink); color: #fff; box-shadow: 0 0 20px var(--neon-pink); }
        
        /* Particle Canvas background */
        #tech-bg-canvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
        }

        main {
            flex: 1 0 auto !important; /* Lệnh này bắt phần thân trang phải phình to ra chiếm hết khoảng trống */
            width: 100%;
        }

        footer {
            flex-shrink: 0 !important; /* Cấm footer bị co rúm lại */
            width: 100%;
            margin-top: auto;
        }

    </style>
</head>
<body>
    <canvas id="tech-bg-canvas"></canvas>

    <?php if (!$is_auth_page): // Nếu KHÔNG PHẢI trang Login/Register thì mới hiện Header ?>
    <nav class="navbar navbar-expand-lg tech-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/Product/">TECH<span style="color: #fff;">STORE</span></a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="filter: invert(1);">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/Product/"><i class="fas fa-th-large me-1"></i>Sản phẩm</a></li>
                    
                    <?php if (SessionHelper::isAdmin()): // CHỈ ADMIN MỚI THẤY ?>
                    <li class="nav-item"><a class="nav-link" href="/Admin/Dashboard"><i class="fas fa-user-shield me-1"></i>Quản trị</a></li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (!SessionHelper::isAdmin()): // Admin không cần giỏ hàng ?>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="/Cart/index">
                            <i class="fas fa-shopping-cart" style="color: var(--neon-pink);"></i> Giỏ hàng
                            <?php 
                                // Logic lấy số lượng giỏ hàng
                                $cartCount = 0;
                                if(SessionHelper::isLoggedIn()){
                                    require_once 'app/Services/CartService.php';
                                    require_once 'app/config/database.php';
                                    $cartService = new CartService((new Database())->getConnection());
                                    $cartCount = $cartService->getCartCount();
                                }
                                if($cartCount > 0) echo '<span class="badge rounded-pill bg-danger">'.$cartCount.'</span>';
                            ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (SessionHelper::isLoggedIn()) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1" style="color: var(--neon-blue);"></i>
                                <?= htmlspecialchars($_SESSION['fullname'] ?? "") ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                <li><a class="dropdown-item text-light" href="/Account/Profile">Hồ sơ</a></li>
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                <li><a class="dropdown-item text-danger" href="/account/logout">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item"><a class="nav-link" href="/account/login">Đăng nhập</a></li>
                        <li class="nav-item"><a href="/account/register" class="btn btn-neon rounded-pill px-3 ms-2">Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="container py-4">