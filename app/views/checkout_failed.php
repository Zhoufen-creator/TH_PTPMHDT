<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); letter-spacing: 2px; }
    .btn-neon { background-color: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); transition: all 0.3s; font-weight: bold; }
    .btn-neon:hover { background-color: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .error-container { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 30px; margin: 40px 0; text-align: center; }
    .error-icon { font-size: 80px; color: #e74c3c; margin-bottom: 20px; text-shadow: 0 0 20px rgba(231, 76, 60, 0.5); }
</style>

<div class="container mt-5 mb-5" style="max-width: 600px;">
    <div class="error-container">
        <div class="error-icon">✕</div>
        <h1 class="tech-title mb-3" style="font-size: 2rem;">THANH TOÁN THẤT BẠI</h1>
        <p style="color: #ccc; font-size: 1.1rem;">Đã xảy ra lỗi trong quá trình thanh toán</p>

        <div style="background-color: rgba(231, 76, 60, 0.1); border: 2px solid rgba(231, 76, 60, 0.5); border-radius: 8px; padding: 15px; margin: 20px 0; color: #ff6b7a;">
            <strong>Lỗi:</strong>
            <p style="margin: 10px 0 0 0;"><?= htmlspecialchars($errorMessage ?? 'Lỗi không xác định') ?></p>
        </div>

        <p style="color: #aaa; margin: 20px 0;">
            <strong>Mã đơn hàng:</strong> <span style="color: #ff2a75; font-family: 'Orbitron', sans-serif;">
                <?= htmlspecialchars($order_code ?? 'N/A') ?>
            </span>
        </p>

        <p style="color: #ccc; margin: 20px 0;">Vui lòng kiểm tra lại thông tin thanh toán và thử lại. Nếu vấn đề vẫn tiếp tục, vui lòng liên hệ với chúng tôi.</p>

        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
            <a href="/Checkout" class="btn btn-neon px-4 py-2">
                <i class="fas fa-redo me-2"></i>THỬ LẠI
            </a>
            <a href="/Cart" class="btn btn-outline-light px-4 py-2">
                <i class="fas fa-shopping-cart me-2"></i>GIỎ HÀNG
            </a>
            <a href="/Product" class="btn btn-outline-light px-4 py-2">
                <i class="fas fa-shopping-bag me-2"></i>DANH SÁCH SẢN PHẨM
            </a>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
