<?php

class VNPayConfig {
    // VNPay Merchant Configuration
    const TMN_CODE = "K69WZ4V9"; // Thay đổi mã merchant của bạn
    const HASH_SECRET = "W3Q7V76Q9SAWGGFHI3MBGA7U75NC0MBE"; // Thay đổi secret key của bạn
    const API_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // Sandbox URL
    const API_URL_RETURN = "http://localhost:8080/checkout/paymentReturn";
    const API_URL_NOTIFY = "http://localhost:8080/checkout/paymentCallback";
    
    // Production URLs (uncomment when going live)
    // const API_URL = "https://payment.vnpayment.vn/paymentv2/vpcpay.html";
    
    const API_QUERY_DR = "https://sandbox.vnpayment.vn/merchant_weblog/QueryDR.php";
    // const API_QUERY_DR = "https://api.vnpayment.vn/merchant_weblog/QueryDR.php"; // Production

    const CURRENCY = "VND";
    const LOCALE = "vn";
    const VERSION = "2.1.0";
    const COMMAND = "pay";

    // Order codes configuration
    const ORDER_PREFIX = "DH";
    const ORDER_DATE_FORMAT = "YmdHis";

    public static function getTmnCode() {
        return self::TMN_CODE;
    }

    public static function getHashSecret() {
        return self::HASH_SECRET;
    }

    public static function getApiUrl() {
        return self::API_URL;
    }

    public static function getReturnUrl() {
        return self::API_URL_RETURN;
    }

    public static function getNotifyUrl() {
        return self::API_URL_NOTIFY;
    }

    public static function getQueryUrl() {
        return self::API_QUERY_DR;
    }

    public static function getCurrency() {
        return self::CURRENCY;
    }

    public static function getLocale() {
        return self::LOCALE;
    }

    public static function getVersion() {
        return self::VERSION;
    }

    public static function getCommand() {
        return self::COMMAND;
    }
}

?>
