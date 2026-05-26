<?php

require_once 'app/config/VNPayConfig.php';

class VNPayService {

    public static function createPaymentUrl($orderId, $amount, $orderInfo, $returnUrl = null) {
        $vnp_TmnCode = VNPayConfig::getTmnCode();
        $vnp_HashSecret = VNPayConfig::getHashSecret();
        $vnp_Url = VNPayConfig::getApiUrl();
        $vnp_ReturnUrl = $returnUrl ?? VNPayConfig::getReturnUrl();
        $vnp_Amount = $amount * 100; // VNPay requires amount in cents
        $vnp_Locale = VNPayConfig::getLocale();
        $vnp_CurrCode = VNPayConfig::getCurrency();
        $vnp_Version = VNPayConfig::getVersion();
        $vnp_Command = VNPayConfig::getCommand();

        $inputData = array(
            "vnp_Version" => $vnp_Version,
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => $vnp_Command,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => $vnp_CurrCode,
            "vnp_IpAddr" => self::getClientIp(),
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $orderId,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= "&" . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    public static function verifyPaymentResponse($data) {
        $vnp_HashSecret = VNPayConfig::getHashSecret();
        
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        
        // 1. Lọc TẤT CẢ các rác (như biến 'url' của Router) ra khỏi mảng dữ liệu
        // Chỉ giữ lại những tham số bắt đầu bằng "vnp_"
        $vnpayData = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $vnpayData[$key] = $value;
            }
        }

        // 2. Loại bỏ 2 biến Hash ra khỏi mảng để chuẩn bị mã hóa các biến còn lại
        unset($vnpayData['vnp_SecureHash']);
        unset($vnpayData['vnp_SecureHashType']);

        ksort($vnpayData);
        $i = 0;
        $hashData = "";
        foreach ($vnpayData as $key => $value) {
            if ($i == 1) {
                $hashData .= "&" . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // 3. Kiểm tra chữ ký bảo mật
        if ($secureHash != $vnp_SecureHash) {
            return [
                'success' => false,
                'message' => 'Lỗi bảo mật: Sai chữ ký xác thực (Invalid secure hash)',
                'data' => $data
            ];
        }

        // 4. Kiểm tra mã phản hồi nếu chữ ký hợp lệ
        $vnp_ResponseCode = $data['vnp_ResponseCode'] ?? '99';
        
        if ($vnp_ResponseCode == '00') {
            return [
                'success' => true,
                'message' => 'Payment successful',
                'data' => $data
            ];
        } else {
            $errorMessage = self::getResponseMessage($vnp_ResponseCode);
            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $data
            ];
        }
    }

    public static function queryTransaction($orderId, $transactionDate) {
        $vnp_TmnCode = VNPayConfig::getTmnCode();
        $vnp_HashSecret = VNPayConfig::getHashSecret();
        $vnp_Api_Query_Url = VNPayConfig::getQueryUrl();

        $inputData = array(
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_TxnRef" => $orderId,
            "vnp_TransactionDate" => $transactionDate,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_IpAddr" => self::getClientIp(),
            "vnp_Version" => VNPayConfig::getVersion(),
        );

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= "&" . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        $url = $vnp_Api_Query_Url . "?" . http_build_query($inputData) . "&vnp_SecureHash=" . $vnpSecureHash;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getResponseMessage($code) {
        $messages = array(
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Tài khoản của Quý khách bị trừ tiền, nhưng giao dịch không được ghi nhận do Website của Quý khách không kết nối được với VNPAY. Quý khách vui lòng kiểm tra kết nối internet và thực hiện lại giao dịch.',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của customer bị khóa.',
            '10' => 'Giao dịch không thành công do: Địa chỉ IP/listing page Merchant được kê khai không phù hợp.',
            '11' => 'Giao dịch không thành công do: Mã xác thực không đúng lần thứ 1.',
            '12' => 'Giao dịch không thành công do: Đơn vị strangely chiều dài của doạn Alphanumeric Password quá nhiều ký tự.',
            '13' => 'Giao dịch không thành công do: KY HẠNG CỦA KHÁCH HÀNG đang bị tạm khóa do quá nhiều lần nhập sai mật khẩu.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch.',
            '51' => 'Giao dịch không thành công do: Tài khoản của bạn đã vượt quá hạn mức.',
            '65' => 'Giao dịch không thành công do: Tài khoản Merchant VNPay Bị khóa.',
            '75' => 'Ngân hàng từ chối giao dịch.',
            '79' => 'KY HẠN CỦA KHÁCH HÀNG ĐÃ HẾT',
            '99' => 'Các lỗi khác (lỗi chưa được xác định).',
        );

        return $messages[$code] ?? 'Lỗi không xác định';
    }

    public static function generateOrderId() {
        return VNPayConfig::ORDER_PREFIX . date(VNPayConfig::ORDER_DATE_FORMAT) . mt_rand(1000, 9999);
    }
}

?>
