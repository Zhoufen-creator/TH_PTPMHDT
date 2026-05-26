<?php include 'app/views/shares/header.php'; ?>

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); }
    .btn-neon { background: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); font-weight: bold; transition: 0.3s; }
    .btn-neon:hover { background: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .tech-card { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); overflow: hidden; }
    .price-tag { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-size: 2.5rem; font-weight: bold; }
    .category-tag { background: rgba(255, 42, 117, 0.15); border: 1px solid #ff2a75; color: #fff; padding: 5px 15px; border-radius: 20px; font-size: 1rem; font-family: 'Orbitron', sans-serif; display: inline-block; }
    .tech-label { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; border-bottom: 1px solid rgba(255, 42, 117, 0.3); padding-bottom: 5px; margin-bottom: 15px; }
</style>

<div class="container mt-5 mb-5" style="max-width: 1000px;">
    <div class="mb-4">
        <a href="/product/" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-2"></i>Trở về hệ thống</a>
    </div>

    <div class="tech-card row g-0">
        <div class="col-md-5 d-flex align-items-center justify-content-center bg-dark p-4" style="border-right: 1px solid rgba(255, 42, 117, 0.3);">
            <?php if (!empty($product->image)): ?>
                <img src="/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded" style="box-shadow: 0 0 20px rgba(255,42,117,0.3); max-height: 400px; object-fit: contain;">
            <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-image mb-3" style="font-size: 4rem; color: #333;"></i><br>
                    <span style="font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: #444;">NO IMAGE</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-7 p-5">
            <div class="mb-3">
                <span class="category-tag"><?php echo htmlspecialchars($product->category_name ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            
            <h1 class="tech-title mb-4" style="font-size: 2.5rem; text-transform: uppercase;">
                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            
            <div class="price-tag mb-4">
                $<?php echo number_format(htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'), 2); ?>
            </div>

            <div class="mb-5">
                <h5 class="tech-label">MÔ TẢ DỮ LIỆU</h5>
                <p style="color: #ccc; line-height: 1.8; font-size: 1rem;">
                    <?php 
                        $desc = $product->description;
                        echo !empty($desc) ? nl2br(htmlspecialchars($desc, ENT_QUOTES, 'UTF-8')) : '<span style="font-style: italic; opacity: 0.5;">[ Trống ]</span>'; 
                    ?>
                </p>
            </div>

            <div class="d-flex gap-3 mt-4 pt-4 border-top border-secondary flex-wrap">
                <form method="POST" action="/Cart/addToCart/<?php echo $product->id; ?>" class="d-flex gap-2">
                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width: 80px; background-color: #2a2a32; border-color: rgba(255, 42, 117, 0.5); color: #fff;">
                    <button type="submit" class="btn btn-neon px-4">
                        <i class="fas fa-shopping-cart me-2"></i>THÊM VÀO GIỎ
                    </button>
                </form>
                <a href="/product/edit/<?php echo $product->id; ?>" class="btn btn-neon px-4">
                    CHỈNH SỬA
                </a>
                <a href="/product/delete/<?php echo $product->id; ?>" 
                   class="btn btn-danger px-4"
                   onclick="return confirm('CẢNH BÁO: Xóa dữ liệu sản phẩm này khỏi hệ thống?');">
                    XÓA BẢN GHI
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>