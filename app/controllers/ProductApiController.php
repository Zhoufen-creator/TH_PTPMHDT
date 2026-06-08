<?php

require_once ("app/config/database.php");
require_once ("app/models/ProductModel.php");
require_once ("app/models/CategoryModel.php");

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function index()
    {
        header("content-type: application/json");
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    public function show($id)
    {
        header("content-type: application/json");
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product is not found."]);
        }
    }

    public function store()
    {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ??'';
        $category_id = $data['category_id'] ?? null;
        
        $result = $this->productModel->addProduct($name, $description, $price, $category_id, null);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(["errors" => $result]);
            exit();
        } else {
            http_response_code(201);
            echo json_encode(["message" => "Product created successfully."]);
            exit();
        }
    }

    public function update($id)
    {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, null);
        if ($result) {
            echo json_encode(["message" => "Product updated successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product update failed."]);
        }
    }

    public function destroy($id)
    {
        header("Content-Type: application/json");
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(["message" => "Product deleted successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product deletion failed."]);
        }
    }
}
?>