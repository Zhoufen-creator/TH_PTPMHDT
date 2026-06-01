<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); letter-spacing: 2px; }
    .btn-neon { background-color: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); transition: all 0.3s; font-weight: bold; }
    .btn-neon:hover { background-color: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .checkout-container { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 25px; }
    .form-control { background-color: #2a2a32; border: 1px solid rgba(255, 42, 117, 0.3); color: #fff; }
    .form-control:focus { background-color: #2a2a32; border-color: #ff2a75; color: #fff; box-shadow: 0 0 10px rgba(255, 42, 117, 0.3); }
    .form-label { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-weight: bold; }
    .order-summary { background-color: rgba(255, 42, 117, 0.05); border: 2px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 20px; }
    .summary-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255, 42, 117, 0.1); color: #ccc; }
    .summary-item:last-child { border-bottom: none; }
    .summary-total { display: flex; justify-content: space-between; padding: 15px 0; border-top: 2px solid rgba(255, 42, 117, 0.3); font-size: 1.3rem; font-weight: bold; color: #ff2a75; margin-top: 10px; }
    .radio-neon { accent-color: #ff2a75; }
</style>

<div class="container mt-5 mb-5" style="max-width: 1100px;">
    <div class="mb-4">
        <a href="/Cart" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="tech-title m-0">THANH TOÁN</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="checkout-container mb-4">
                <h3 class="tech-title mb-4" style="font-size: 1.5rem;">THÔNG TIN GIAO HÀNG</h3>
                <form id="checkoutForm" method="POST" action="/Checkout/processCheckout">
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Tên Khách Hàng <span style="color: #ff2a75;">*</span></label>
                        <input type="text" class="form-control" id="user_name" name="user_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_email" class="form-label">Email <span style="color: #ff2a75;">*</span></label>
                        <input type="email" class="form-control" id="user_email" name="user_email" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_phone" class="form-label">Số Điện Thoại <span style="color: #ff2a75;">*</span></label>
                        <input type="tel" class="form-control" id="user_phone" name="user_phone" placeholder="09xxxxxxxxx" required>
                    </div>

                    <div class="mb-4">
                        <label for="user_address" class="form-label">Địa Chỉ Giao Hàng <span style="color: #ff2a75;">*</span></label>
                        <textarea class="form-control" id="user_address" name="user_address" rows="3" placeholder="Nhập địa chỉ chi tiết" required></textarea>
                    </div>

                    <hr style="border-color: rgba(255, 42, 117, 0.2);">

                    <h3 class="tech-title mb-4" style="font-size: 1.5rem;">PHƯƠNG THỨC THANH TOÁN</h3>
                    
                    <div class="mb-3">
                        <div class="form-check" style="padding: 12px; background-color: rgba(255, 42, 117, 0.05); border-radius: 8px; border: 1px solid rgba(255, 42, 117, 0.3); margin-bottom: 10px;">
                            <input class="form-check-input radio-neon" type="radio" name="payment_method" id="payment_vnpay" value="vnpay" checked>
                            <label class="form-check-label" for="payment_vnpay" style="color: #ccc; cursor: pointer; width: 100%; margin: 0;">
                                <span style="color: #ff2a75; font-weight: bold;">VNPay</span> - Thanh toán qua cổng VNPay (nhanh và an toàn)
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check" style="padding: 12px; background-color: rgba(255, 42, 117, 0.05); border-radius: 8px; border: 1px solid rgba(255, 42, 117, 0.3);">
                            <input class="form-check-input radio-neon" type="radio" name="payment_method" id="payment_cod" value="cod">
                            <label class="form-check-label" for="payment_cod" style="color: #ccc; cursor: pointer; width: 100%; margin: 0;">
                                <span style="color: #ff2a75; font-weight: bold;">COD</span> - Thanh toán khi nhận hàng
                            </label>
                        </div>
                    </div>

                    <div id="errorMessages" class="alert alert-danger" style="display: none; border-color: rgba(255, 42, 117, 0.5); background-color: rgba(255, 42, 117, 0.1); color: #ff6b9d;"></div>

                    <button type="submit" class="btn btn-neon btn-lg w-100 mt-4" style="padding: 12px;">
                        <i class="fas fa-lock me-2"></i>XÁC NHẬN VÀ THANH TOÁN
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="checkout-container">
                <h3 class="tech-title mb-4" style="font-size: 1.5rem;">ĐƠN HÀNG CỦA BẠN</h3>
                
                <div class="order-summary">
                    <?php foreach ($cartDetails['items'] as $item): ?>
                        <div class="summary-item">
                            <span><?= htmlspecialchars($item['name']) ?> <span style="color: #888;">x<?= $item['quantity'] ?></span></span>
                            <span class="price-tag"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="summary-total">
                        <span>TỔNG CỘNG:</span>
                        <span><?= number_format($cartDetails['totalPrice'], 0, ',', '.') ?> ₫</span>
                    </div>
                </div>

                <a href="/Cart" class="btn btn-outline-light w-100 mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(document.getElementById('checkoutForm'));
        const errorDiv = document.getElementById('errorMessages');
        errorDiv.style.display = 'none';
        errorDiv.innerHTML = '';

        try {
            const response = await fetch('/Checkout/processCheckout', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                if (data.errors) {
                    let errorHtml = '<strong>Vui lòng kiểm tra lại:</strong><ul style="margin: 10px 0 0 20px;">';
                    data.errors.forEach(error => {
                        errorHtml += '<li>' + error + '</li>';
                    });
                    errorHtml += '</ul>';
                    errorDiv.innerHTML = errorHtml;
                } else {
                    errorDiv.innerHTML = '<strong>Lỗi:</strong> ' + (data.message || 'Đã xảy ra lỗi');
                }
                errorDiv.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // Success
            if (data.payment_url) {
                // Redirect to VNPay
                window.location.href = data.payment_url;
            } else if (data.redirect) {
                // Redirect to success page
                window.location.href = data.redirect;
            }
        } catch (error) {
            errorDiv.innerHTML = '<strong>Lỗi kết nối:</strong> ' + error.message;
            errorDiv.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>
