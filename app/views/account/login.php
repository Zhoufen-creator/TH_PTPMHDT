<?php include 'app/views/shares/header.php'; ?>

<style>
    .auth-container { min-height: 90vh; display: flex; align-items: center; justify-content: center; }
    .auth-card { 
        background: rgba(30, 30, 38, 0.8); 
        border: 1px solid rgba(255, 42, 117, 0.3); 
        border-radius: 20px; 
        padding: 40px; 
        width: 100%; 
        max-width: 450px; 
        backdrop-filter: blur(10px);
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
    }
    .form-control-tech {
        background: rgba(18, 18, 22, 0.5);
        border: 1px solid rgba(255, 42, 117, 0.2);
        color: #fff;
        padding: 12px;
        border-radius: 10px;
    }
    .form-control-tech:focus {
        background: rgba(18, 18, 22, 0.8);
        border-color: var(--neon-pink);
        box-shadow: 0 0 10px rgba(255, 42, 117, 0.3);
        color: #fff;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="tech-title">LOGIN</h2>
            <p class="text-secondary small">VUI LÒNG NHẬP THÔNG TIN TRUY CẬP</p>
        </div>

        <?php if (isset($error)): ?>
            <div class='alert alert-danger border-0 bg-transparent text-danger p-0 mb-3 small text-center' role='alert'>
                <i class="fas fa-exclamation-triangle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/Account/checkLogin" method="post">
            <div class="mb-4">
                <label class="form-label small text-secondary font-monospace">USERNAME</label>
                <input type="text" name="username" class="form-control form-control-tech" required placeholder="Nhập tài khoản...">
            </div>

            <div class="mb-4">
                <label class="form-label small text-secondary font-monospace">PASSWORD</label>
                <input type="password" name="password" class="form-control form-control-tech" required placeholder="********">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small text-secondary" for="remember">Ghi nhớ</label>
                </div>
                <a href="#" class="small text-decoration-none" style="color: var(--neon-blue);">Quên mật mã?</a>
            </div>

            <button type="submit" class="btn btn-neon w-100 py-3 mb-4 rounded-3">
                XÁC NHẬN ĐĂNG NHẬP
            </button>

            <div class="text-center">
                <p class="text-secondary small mb-3">HOẶC ĐĂNG NHẬP VỚI</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="/Account/googleLogin" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-google"></i></a>
                    <a href="/Account/githubLogin" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-github"></i></a>
                </div>
                <p class="mt-4 small text-secondary">
                    Chưa có tài khoản? <a href="/account/register" class="text-white fw-bold text-decoration-none">ĐĂNG KÝ NGAY</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>