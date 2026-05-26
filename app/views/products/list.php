<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); letter-spacing: 2px; }
    .btn-neon { background-color: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); transition: all 0.3s; font-weight: bold; }
    .btn-neon:hover { background-color: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .tech-card { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; overflow: hidden; }
    .tech-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(255, 42, 117, 0.3); border-color: #ff2a75; }
    .price-tag { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-size: 1.25rem; font-weight: bold; }
    .category-tag { font-size: 0.8rem; background: rgba(255, 42, 117, 0.1); border: 1px solid rgba(255, 42, 117, 0.5); color: #ff2a75; padding: 2px 8px; border-radius: 4px; display: inline-block; }
    .card-img-wrapper { 
        height: 220px; /* Tăng nhẹ chiều cao khung ảnh cho thoáng */
        width: 100%; 
        border-bottom: 1px solid rgba(255, 42, 117, 0.3); 
        background-color: #121216; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        padding: 15px; /* Thêm khoảng đệm bao quanh để ảnh không bị dính sát viền card */
    }
    .card-img-wrapper img { 
        max-width: 100%; 
        max-height: 100%; 
        object-fit: contain; /* Thay đổi từ 'cover' thành 'contain' để ảnh thu nhỏ vừa vặn, không bị méo/mất góc */
        border-radius: 8px; /* Bo tròn nhẹ các góc của bức ảnh cho tinh tế */
        transition: transform 0.3s ease; /* Thêm hiệu ứng mượt mà khi di chuột */
    }
    /* Hiệu ứng zoom nhẹ ảnh khi di chuột vào thẻ sản phẩm */
    .tech-card:hover .card-img-wrapper img {
        transform: scale(1.05);
    }
</style>

<canvas id="tech-bg-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none;"></canvas>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="tech-title m-0">PRODUCTS</h1>
        <a href="/product/add" class="btn btn-neon rounded-pill px-4 text-decoration-none">
            + THÊM SẢN PHẨM MỚI
        </a>
    </div>

    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card tech-card h-100">
                    
                    <div class="card-img-wrapper">
                        <?php if (!empty($product->image)): ?>
                            <img src="/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
                        <?php else: ?>
                            <span style="color: #ff2a75; font-family: 'Orbitron', sans-serif; opacity: 0.5;">NO IMAGE</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <span class="category-tag"><?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <h4 class="card-title text-light mb-3" style="font-family: 'Orbitron', sans-serif;">
                            <a href="/product/show/<?php echo $product->id; ?>" class="text-light text-decoration-none" style="transition: 0.3s;" onmouseover="this.style.color='#ff2a75'" onmouseout="this.style.color='#f8f9fa'">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h4>
                        <p class="card-text" style="color: #cccccc; font-size: 0.9rem; line-height: 1.5; margin-bottom: 15px;">
                            <?php 
                                $desc = $product->description;
                                echo !empty($desc) ? nl2br(htmlspecialchars(substr($desc, 0, 80) . '...', ENT_QUOTES, 'UTF-8')) : '<span style="color: #ff2a75; font-style: italic; opacity: 0.7;">[ Trống ]</span>';
                            ?>
                        </p>
                        <div class="price-tag mb-3">
                            $<?php echo number_format(htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'), 2); ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top border-secondary d-flex justify-content-between gap-2 p-3 flex-wrap">
                        <form method="POST" action="/Cart/addToCart/<?php echo $product->id; ?>" class="d-flex gap-2 flex-grow-1">
                            <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="max-width: 60px; background-color: #2a2a32; border-color: rgba(255, 42, 117, 0.5); color: #fff;">
                            <button type="submit" class="btn btn-sm btn-neon flex-grow-1">
                                <i class="fas fa-shopping-cart me-1"></i>Thêm Giỏ
                            </button>
                        </form>
                        <a href="/product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-outline-light">Sửa</a>
                        <a href="/product/delete/<?php echo $product->id; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('CẢNH BÁO: Xóa dữ liệu sản phẩm này khỏi hệ thống?');">
                            Xóa
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const canvas = document.getElementById('tech-bg-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    let particlesArray = [];
    class Particle {
        constructor() { this.x = Math.random() * canvas.width; this.y = Math.random() * canvas.height; this.size = Math.random() * 2 + 1; this.speedX = (Math.random() * 1 - 0.5); this.speedY = (Math.random() * 1 - 0.5); }
        update() { this.x += this.speedX; this.y += this.speedY; if (this.x < 0 || this.x > canvas.width) this.speedX *= -1; if (this.y < 0 || this.y > canvas.height) this.speedY *= -1; }
        draw() { ctx.fillStyle = '#ff2a75'; ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fill(); }
    }
    function init() { particlesArray = []; for (let i = 0; i < (canvas.height * canvas.width) / 10000; i++) particlesArray.push(new Particle()); }
    function connect() {
        for (let a = 0; a < particlesArray.length; a++) {
            for (let b = a; b < particlesArray.length; b++) {
                let distance = ((particlesArray[a].x - particlesArray[b].x) ** 2) + ((particlesArray[a].y - particlesArray[b].y) ** 2);
                if (distance < 15000) { ctx.strokeStyle = 'rgba(255, 42, 117,' + (1 - distance/15000) + ')'; ctx.lineWidth = 1; ctx.beginPath(); ctx.moveTo(particlesArray[a].x, particlesArray[a].y); ctx.lineTo(particlesArray[b].x, particlesArray[b].y); ctx.stroke(); }
            }
        }
    }
    function animate() { requestAnimationFrame(animate); ctx.clearRect(0, 0, canvas.width, canvas.height); particlesArray.forEach(p => { p.update(); p.draw(); }); connect(); }
    window.addEventListener('resize', () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; init(); });
    init(); animate();
</script>

<?php include 'app/views/shares/footer.php'; ?>