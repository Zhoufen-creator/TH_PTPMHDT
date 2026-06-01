<?php

require_once 'app/config/database.php';
require_once 'app/config/VNPayConfig.php';
require_once 'app/models/CartService.php';
require_once 'app/Services/VNPayService.php';
require_once 'app/models/OrderModel.php';

class CheckoutController {
    private $db;
    private $cartService;
    private $orderModel;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->cartService = new CartService($this->db);
        $this->orderModel = new OrderModel($this->db);
    }

    public function index() {
        $cartDetails = $this->cartService->getCartDetails();
        
        if (empty($cartDetails['items'])) {
            header('Location: /Cart/index');
            exit;
        }

        include 'app/views/checkout_index.php';
    }

    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Validate cart
        $cartDetails = $this->cartService->getCartDetails();
        if (empty($cartDetails['items'])) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
            exit;
        }

        // Get form data
        $userName = trim($_POST['user_name'] ?? '');
        $userEmail = trim($_POST['user_email'] ?? '');
        $userPhone = trim($_POST['user_phone'] ?? '');
        $userAddress = trim($_POST['user_address'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'vnpay');

        // Validate form data
        $errors = [];
        if (empty($userName)) $errors[] = 'Tên khách hàng không được để trống';
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (empty($userPhone) || !preg_match('/^[0-9]{9,11}$/', str_replace([' ', '-', '+84'], '', $userPhone))) $errors[] = 'Số điện thoại không hợp lệ';
        if (empty($userAddress)) $errors[] = 'Địa chỉ không được để trống';

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        // Tạo mã giao dịch
        $orderCode = VNPayService::generateOrderId();
        $totalPrice = $cartDetails['totalPrice'];

        $orderData = [
            'name' => $userName,
            'phone' => $userPhone,
            'address' => $userAddress
        ];

        $orderId = $this->orderModel->createOrder($orderData);
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo đơn hàng']);
            exit;
        }


        foreach ($cartDetails['items'] as $item) {
            $this->orderModel->addOrderItem($orderId, $item['id'], $item['quantity'], $item['price']);
        }

        // Store order code in session for verification later
        $_SESSION['pending_order_code'] = $orderCode;
        $_SESSION['pending_order_id'] = $orderId;

        // Handle payment based on method
        if ($paymentMethod === 'vnpay') {
            // Generate VNPay payment URL
            $paymentUrl = VNPayService::createPaymentUrl(
                $orderCode,
                $totalPrice,
                'Thanh toan don hang ' . $orderCode
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Chuyển hướng đến VNPay',
                'payment_url' => $paymentUrl,
                'order_code' => $orderCode
            ]);
        } else if ($paymentMethod === 'cod') {
            // COD - Đơn hàng đã được tạo, xóa giỏ hàng
            $this->cartService->clearCart();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Đơn hàng đã được tạo thành công',
                'order_code' => $orderCode,
                'redirect' => '/Checkout/success/' . $orderCode
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Phương thức thanh toán không hợp lệ']);
        }

        exit;
    }

    public function paymentCallback() {
        // Handle VNPay IPN callback
        $vnpay_data = $_GET;
        $result = VNPayService::verifyPaymentResponse($vnpay_data);

        if (!$result['success']) {
            $this->logPaymentResponse($vnpay_data, false);
            http_response_code(400);
            return;
        }

        // Vì không có Database nên bỏ qua bước check Order. Trả về OK luôn cho VNPay.
        $this->logPaymentResponse($vnpay_data, true);
        http_response_code(200);
        echo "OK";
    }

    public function paymentReturn() {
        // Handle return from VNPay payment gateway
        $vnpay_data = $_GET;
        $result = VNPayService::verifyPaymentResponse($vnpay_data);
        $responseCode = $vnpay_data['vnp_ResponseCode'] ?? '99';

        if (!$result['success']) {
            $order_code = $vnpay_data['vnp_TxnRef'] ?? '';
            $errorMessage = VNPayService::getResponseMessage($responseCode);
            include 'app/views/checkout_failed.php';
            return;
        }

        $vnp_TxnRef = $vnpay_data['vnp_TxnRef'] ?? '';

        // ✅ THANH TOÁN THÀNH CÔNG -> XÓA GIỎ HÀNG
        $this->cartService->clearCart();

        // Redirect to success page
        header('Location: /Checkout/success/' . $vnp_TxnRef);
        exit;
    }

    public function success($orderCode) {
        // Hiển thị trang thành công với mã đơn hàng
        echo "<script>
            alert('Thanh toán thành công! Mã đơn hàng: " . htmlspecialchars($orderCode) . "');
            window.location.href = '/Product';
        </script>";
        exit;
    }

    private function logPaymentResponse($data, $isSuccess) {
        $logDir = 'logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . 'vnpay_' . date('Y-m-d') . '.log';
        $logMessage = date('Y-m-d H:i:s') . ' | ' . ($isSuccess ? 'SUCCESS' : 'FAILED') . ' | ';
        $logMessage .= json_encode($data) . "\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
?>