<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); letter-spacing: 2px; }
    .btn-neon { background-color: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); transition: all 0.3s; font-weight: bold; }
    .btn-neon:hover { background-color: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .cart-container { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 20px; margin: 20px 0; }
    .cart-table { background-color: #121216; border: 1px solid rgba(255, 42, 117, 0.2); border-radius: 8px; overflow: hidden; }
    .cart-table thead { background-color: rgba(255, 42, 117, 0.1); border-bottom: 2px solid rgba(255, 42, 117, 0.3); }
    .cart-table th { color: #ff2a75; font-family: 'Orbitron', sans-serif; padding: 15px; font-weight: bold; }
    .cart-table td { color: #ccc; padding: 15px; border-bottom: 1px solid rgba(255, 42, 117, 0.1); }
    .cart-table tr:last-child td { border-bottom: none; }
    .price-tag { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-weight: bold; }
    .cart-summary { background-color: rgba(255, 42, 117, 0.05); border: 2px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 25px; }
    .summary-row { display: flex; justify-content: space-between; margin: 12px 0; color: #ccc; font-size: 16px; }
    .summary-row.total { font-size: 20px; font-weight: bold; color: #ff2a75; border-top: 2px solid rgba(255, 42, 117, 0.3); padding-top: 15px; margin-top: 15px; }
    .product-image { max-width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background-color: #121216; padding: 5px; }
</style>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="tech-title m-0">GIỎ HÀNG</h1>
        <a href="/Product" class="btn btn-neon rounded-pill px-4 text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>TIẾP TỤC MUA SẮM
        </a>
    </div>

    <?php if (empty($cartDetails['items'])): ?>
        <div class="cart-container text-center">
            <div style="padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: rgba(255, 42, 117, 0.3); margin-bottom: 20px; display: block;"></i>
                <h3 style="color: #ff2a75; font-family: 'Orbitron', sans-serif;">GIỎ HÀNG CỦA BẠN TRỐNG</h3>
                <p style="color: #ccc; margin: 15px 0;">Hãy thêm sản phẩm để tiếp tục mua sắm</p>
                <a href="/Product" class="btn btn-neon mt-3 px-4">KHÁM PHÁ SẢN PHẨM</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-container">
                    <table class="cart-table w-100">
                        <thead>
                            <tr>
                                <th style="width: 10%;"></th>
                                <th style="width: 35%;">SẢN PHẨM</th>
                                <th style="width: 15%;">GIÁ</th>
                                <th style="width: 20%;">SỐ LƯỢNG</th>
                                <th style="width: 15%;">THÀNH TIỀN</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartDetails['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['image']): ?>
                                            <img src="/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                        <?php else: ?>
                                            <div class="product-image d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image" style="color: #555;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="color: #ff2a75; font-family: 'Orbitron', sans-serif;"><?= htmlspecialchars($item['name']) ?></div>
                                    </td>
                                    <td>
                                        <span class="price-tag"><?= number_format($item['price'], 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td>
                                        <form method="POST" action="/Cart/updateQuantity/<?= $item['id'] ?>" class="d-flex gap-2">
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="0" class="form-control form-control-sm" style="width: 70px; background-color: #2a2a32; border-color: rgba(255, 42, 117, 0.5); color: #fff;">
                                            <button type="submit" class="btn btn-sm btn-neon" style="font-size: 0.8rem; padding: 5px 10px;">CẬP NHẬT</button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="price-tag"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td>
                                        <form method="POST" action="/Cart/removeFromCart/<?= $item['id'] ?>" style="display: inline;">
                                            <button type="submit" class="btn btn-sm btn-danger" style="font-size: 0.8rem; padding: 5px 10px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-container mt-3 d-flex gap-2 flex-wrap">
                    <a href="/Product" class="btn btn-outline-light">
                        <i class="fas fa-plus me-2"></i>TIẾP TỤC MUA SẮM
                    </a>
                    <form method="POST" action="/Cart/clearCart" style="display: inline;">
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa toàn bộ giỏ hàng?')">
                            <i class="fas fa-trash me-2"></i>XÓA GIỎ HÀNG
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="tech-title mb-4">ĐƠN HÀNG CỦA BẠN</h5>
                    
                    <div class="summary-row">
                        <span>Số loại sản phẩm:</span>
                        <span style="color: #ff2a75; font-weight: bold;"><?= $cartDetails['itemsCount'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tổng số lượng:</span>
                        <span style="color: #ff2a75; font-weight: bold;"><?= $cartDetails['itemCount'] ?> sản phẩm</span>
                    </div>
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span class="price-tag"><?= number_format($cartDetails['totalPrice'], 0, ',', '.') ?> ₫</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span style="color: #aaa; font-size: 14px;">Tính khi thanh toán</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>TỔNG CỘNG:</span>
                        <span><?= number_format($cartDetails['totalPrice'], 0, ',', '.') ?> ₫</span>
                    </div>

                    <a href="/Checkout" class="btn btn-neon w-100 mt-4 py-3" style="font-size: 1.1rem;">
                        <i class="fas fa-credit-card me-2"></i>THANH TOÁN NGAY
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
