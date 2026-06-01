<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); letter-spacing: 2px; }
    .btn-neon { background-color: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); transition: all 0.3s; font-weight: bold; }
    .btn-neon:hover { background-color: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .success-container { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 30px; margin: 40px 0; text-align: center; }
    .success-icon { font-size: 80px; color: #2ecc71; margin-bottom: 20px; text-shadow: 0 0 20px rgba(46, 204, 113, 0.5); }
    .order-details { background-color: rgba(255, 42, 117, 0.05); border: 2px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 20px; margin: 20px 0; text-align: left; }
    .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255, 42, 117, 0.1); }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #ff2a75; font-weight: bold; }
    .detail-value { color: #ccc; }
    .items-table { background-color: #121216; border: 1px solid rgba(255, 42, 117, 0.2); border-radius: 8px; overflow: hidden; margin: 20px 0; }
    .items-table thead { background-color: rgba(255, 42, 117, 0.1); border-bottom: 2px solid rgba(255, 42, 117, 0.3); }
    .items-table th { color: #ff2a75; font-family: 'Orbitron', sans-serif; padding: 12px; font-weight: bold; }
    .items-table td { color: #ccc; padding: 12px; border-bottom: 1px solid rgba(255, 42, 117, 0.1); }
    .price-tag { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-weight: bold; }
</style>

<div class="container mt-5 mb-5" style="max-width: 900px;">
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1 class="tech-title mb-3" style="font-size: 2rem;">THANH TOÁN THÀNH CÔNG</h1>
        <p style="color: #ccc; font-size: 1.1rem;">Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được tạo thành công!</p>
    </div>

    <div class="order-details">
        <h3 class="tech-title mb-4" style="font-size: 1.3rem;">CHI TIẾT ĐƠN HÀNG</h3>
        
        <div class="detail-row">
            <span class="detail-label">Mã Đơn Hàng:</span>
            <span class="detail-value" style="font-family: 'Orbitron', sans-serif; color: #ff2a75; font-weight: bold;"><?= htmlspecialchars($order->order_code) ?></span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Tên Khách Hàng:</span>
            <span class="detail-value"><?= htmlspecialchars($order->name) ?></span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value"><?= htmlspecialchars($order->email) ?></span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Số Điện Thoại:</span>
            <span class="detail-value"><?= htmlspecialchars($order->phone) ?></span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Địa Chỉ:</span>
            <span class="detail-value"><?= htmlspecialchars($order->address) ?></span>
        </div>
        
        <div class="detail-row" style="border-bottom: 2px solid rgba(255, 42, 117, 0.3); padding-bottom: 15px; margin-bottom: 15px;">
            <span class="detail-label">Phương Thức Thanh Toán:</span>
            <span class="detail-value"><?= $order->payment_method === 'vnpay' ? 'VNPay' : 'Thanh toán khi nhận hàng' ?></span>
        </div>
        
        <div style="display: flex; justify-content: space-between; font-size: 1.1rem;">
            <span class="detail-label">Tổng Tiền:</span>
            <span class="price-tag"><?= number_format($order->total_amount, 0, ',', '.') ?> ₫</span>
        </div>
    </div>

    <div>
        <h3 class="tech-title mb-3" style="font-size: 1.3rem;">DANH SÁCH SẢN PHẨM</h3>
        
        <table class="items-table w-100">
            <thead>
                <tr>
                    <th style="width: 50%;">SẢN PHẨM</th>
                    <th style="width: 15%;">SỐ LƯỢNG</th>
                    <th style="width: 17%;">GIÁ</th>
                    <th style="width: 18%;">THÀNH TIỀN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): 
                    $subtotal = $item->quantity * $item->price;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item->product_name ?? 'Sản phẩm') ?></td>
                        <td style="text-align: center;"><?= $item->quantity ?></td>
                        <td><span class="price-tag"><?= number_format($item->price, 0, ',', '.') ?> ₫</span></td>
                        <td><span class="price-tag"><?= number_format($subtotal, 0, ',', '.') ?> ₫</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="order-details mt-4" style="text-align: center; padding: 20px; background: rgba(46, 204, 113, 0.05); border-color: rgba(46, 204, 113, 0.3);">
        <p style="color: #ccc; margin: 0 0 15px 0;">Chúng tôi sẽ gửi xác nhận đơn hàng và thông tin vận chuyển đến email <span style="color: #ff2a75; font-weight: bold;"><?= htmlspecialchars($order->email) ?></span></p>
        <p style="color: #888; font-size: 0.95rem; margin: 0;">Vui lòng kiểm tra email của bạn trong vài phút tới. Nếu không nhận được, hãy kiểm tra mục spam.</p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="/Product" class="btn btn-neon px-5 py-2" style="font-size: 1rem;">
            <i class="fas fa-shopping-bag me-2"></i>TIẾP TỤC MUA SẮM
        </a>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
