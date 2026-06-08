<?php include 'app/views/shares/header.php'; ?> 

<style>
    .tech-title { font-family: 'Orbitron', sans-serif; color: #ff2a75; text-shadow: 0 0 15px rgba(255, 42, 117, 0.6); }
    .btn-neon { background: transparent; color: #ff2a75; border: 2px solid #ff2a75; box-shadow: 0 0 8px rgba(255, 42, 117, 0.4); font-weight: bold; transition: 0.3s; }
    .btn-neon:hover { background: #ff2a75; color: #fff; box-shadow: 0 0 20px rgba(255, 42, 117, 0.8); }
    .tech-card { background-color: #1e1e26; border: 1px solid rgba(255, 42, 117, 0.3); border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
    .tech-label { color: #ff2a75; font-family: 'Orbitron', sans-serif; font-size: 0.9rem; margin-bottom: 8px; letter-spacing: 1px; }
    .tech-input { background-color: rgba(18, 18, 22, 0.8); border: 1px solid rgba(255, 42, 117, 0.5); color: #fff; }
    .tech-input:focus { background-color: #121216; border-color: #ff2a75; color: #fff; box-shadow: 0 0 10px rgba(255, 42, 117, 0.5); }
    .tech-input::file-selector-button { background-color: rgba(255, 42, 117, 0.2); color: #ff2a75; border: 1px solid #ff2a75; border-radius: 4px; padding: 5px 10px; transition: 0.3s; }
    .tech-input::file-selector-button:hover { background-color: #ff2a75; color: #fff; }
    .img-preview { border: 2px solid #ff2a75; border-radius: 8px; box-shadow: 0 0 15px rgba(255, 42, 117, 0.3); max-width: 150px; }
</style>

<div class="container mt-5 mb-5" style="max-width: 800px;">
    <div class="mb-4 border-bottom border-secondary pb-3">
        <h1 class="tech-title m-0">EDIT</h1>
    </div>

    <div id="errorContainer"></div>

    <div class="tech-card">
        <form id="editProductForm" enctype="multipart/form-data"> 
            <input type="hidden" id="productId" name="id" value="<?php echo isset($product) ? $product->id : ''; ?>"> 
            
            <div class="form-group mb-4"> 
                <label for="name" class="tech-label">Tên sản phẩm:</label> 
                <input type="text" id="name" name="name" class="form-control tech-input" value="<?php echo isset($product) ? htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') : ''; ?>" required> 
            </div> 
            
            <div class="form-group mb-4"> 
                <label for="description" class="tech-label">Mô tả:</label> 
                <textarea id="description" name="description" class="form-control tech-input" rows="4" required><?php echo isset($product) ? htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea> 
            </div> 
            
            <div class="row">
                <div class="form-group mb-4 col-md-6"> 
                    <label for="price" class="tech-label">Giá (USD):</label> 
                    <input type="number" id="price" name="price" class="form-control tech-input" step="0.01" value="<?php echo isset($product) ? htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8') : ''; ?>" required> 
                </div> 
                
                <div class="form-group mb-4 col-md-6"> 
                    <label for="category_id" class="tech-label">Danh mục:</label> 
                    <select id="category_id" name="category_id" class="form-select tech-input" required> 
                        <?php foreach ($categories as $category): ?> 
                            <option value="<?php echo $category->id; ?>" style="background: #1e1e26; color: #fff;" <?php echo (isset($product) && $category->id == $product->category_id) ? 'selected' : ''; ?>> 
                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?> 
                            </option> 
                        <?php endforeach; ?> 
                    </select> 
                </div>
            </div>

            <div class="form-group mb-5"> 
                <label for="image" class="tech-label">Cập nhật hình ảnh:</label> 
                <input type="file" id="image" name="image" class="form-control tech-input mb-3" accept="image/*"> 
                
                <?php if (isset($product) && !empty($product->image)): ?> 
                    <div class="mt-2 p-3 bg-dark rounded border border-secondary d-inline-block">
                        <span class="text-secondary small d-block mb-2 font-monospace">Hình ảnh hiện tại:</span>
                        <img src="/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" class="img-preview"> 
                    </div>
                <?php endif; ?> 
            </div> 
            
            <div class="d-flex justify-content-between align-items-center">
                <a href="/product/" class="btn btn-outline-secondary">Hủy bỏ</a> 
                <button type="submit" class="btn btn-neon px-5 py-2" id="submitBtn">GHI ĐÈ DỮ LIỆU</button> 
            </div>
        </form> 
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editProductForm');
            const submitBtn = document.getElementById('submitBtn');
            const errorContainer = document.getElementById('errorContainer');
            const productId = document.getElementById('productId').value;
            
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang lưu...';
                errorContainer.innerHTML = '';
                
                try {
                    const data = {
                        name: document.getElementById('name').value,
                        description: document.getElementById('description').value,
                        price: document.getElementById('price').value,
                        category_id: document.getElementById('category_id').value
                    };
                    
                    const response = await fetch(`/api/product/${productId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        // Success
                        alert('Sản phẩm đã được cập nhật thành công!');
                        window.location.href = '/product/';
                    } else if (result.errors) {
                        // Show validation errors
                        const errorHtml = '<div class="alert alert-danger" style="background-color: rgba(220, 53, 69, 0.2); border-color: #dc3545; color: #ffcccc;"><ul class="mb-0">' +
                            result.errors.map(err => `<li>${err}</li>`).join('') +
                            '</ul></div>';
                        errorContainer.innerHTML = errorHtml;
                    } else {
                        throw new Error(result.message || 'Lỗi không xác định');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    errorContainer.innerHTML = `<div class="alert alert-danger">Lỗi: ${error.message}</div>`;
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'GHI ĐÈ DỮ LIỆU';
                }
            });
        });
    </script>
</div>

<?php include 'app/views/shares/footer.php'; ?>