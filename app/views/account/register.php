<?php include 'app/views/shares/header.php'; ?>

<style>
    .auth-container { min-height: 90vh; display: flex; align-items: center; justify-content: center; }
    .auth-card { 
        background: rgba(30, 30, 38, 0.8); 
        border: 1px solid rgba(255, 42, 117, 0.3);
        border-radius: 20px; padding: 40px; width: 100%; max-width: 550px; 
        backdrop-filter: blur(10px);
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="tech-title" style="color: var(--neon-pink); text-shadow: 0 0 15px rgba(255, 42, 117, 0.6);">REGISTER</h2>
            <p class="text-secondary small">GIA NHẬP HỆ THỐNG CÔNG NGHỆ TƯƠNG LAI</p>
        </div>

        <?php if (isset($errors)): ?>
            <div class="alert alert-danger bg-transparent border-0 text-danger small">
                <?php foreach ($errors as $err) echo "<div>• $err</div>"; ?>
            </div>
        <?php endif; ?>

        <form action="/Account/save" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-secondary font-monospace">TÀI KHOẢN</label>
                    <input type="text" name="username" class="form-control form-control-tech" placeholder="username">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-secondary font-monospace">HỌ TÊN</label>
                    <input type="text" name="fullname" class="form-control form-control-tech" placeholder="Full name">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-secondary font-monospace">MẬT MÃ</label>
                <input type="password" name="password" class="form-control form-control-tech" placeholder="********">
            </div>

            <div class="mb-4">
                <label class="form-label small text-secondary font-monospace">XÁC NHẬN MẬT MÃ</label>
                <input type="password" name="confirmpassword" class="form-control form-control-tech" placeholder="********">
            </div>

            <button type="submit" class="btn btn-neon w-100 py-3 rounded-3" style="color: var(--neon-pink); border-color: var(--neon-pink);">
                TẠO TÀI KHOẢN MỚI
            </button>

            <div class="text-center mt-4">
                <p class="small text-secondary">
                    Đã có tài khoản? <a href="/account/login" class="text-white fw-bold text-decoration-none">ĐĂNG NHẬP</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>