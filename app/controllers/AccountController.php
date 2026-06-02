<?php 
require_once('app/config/database.php'); 
require_once('app/models/AccountModel.php'); 
class AccountController { 
    private $accountModel; 
    private $db;

    public function __construct() { 
        $this->db = (new Database())->getConnection(); 
        $this->accountModel = new AccountModel($this->db); 
    }

    public function register() { 
        include_once 'app/views/account/register.php'; 
    }

    public function login() { 
        include_once 'app/views/account/login.php'; 
    }

    public function googleLogin() {
        $config = require __DIR__ . '/../config/google.php';

        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
    
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    
        header("Location: $url");
        exit;
    }

    public function googleCallback()
    {
        $config = require __DIR__ . '/../config/google.php';

        if (!isset($_GET['code'])) {
            die("Google login failed");
        }

        $code = $_GET['code'];

        // 🔹 BƯỚC 1: đổi code lấy access token
        $tokenUrl = 'https://oauth2.googleapis.com/token';

        $postData = [
            'code' => $code,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            die("Cannot get access token");
        }

        // 🔹 BƯỚC 2: lấy info user
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token='
            . $tokenData['access_token'];

        $userInfo = file_get_contents($userInfoUrl);
        $user = json_decode($userInfo, true);

        // 🔹 BƯỚC 3: lưu session login
        $email = $user['email'];
        $username = "google_" .$email;
        $fullname = $user['name'];


        // Tìm account theo username = email
        $stmt = $this->db->prepare(
            "SELECT * FROM account WHERE username = ?"
        );
        
        $stmt->execute([$username]);
        
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Nếu chưa có thì tạo mới
        if (!$account) {
        
            $randomPassword = password_hash(
                bin2hex(random_bytes(16)),
                PASSWORD_DEFAULT
            );
        
            $stmt = $this->db->prepare(
                "INSERT INTO account (username, fullname, password, role)
                 VALUES (?, ?, ?, 'user')"
            );
        
            $stmt->execute([
                $username,
                $fullname,
                $randomPassword
            ]);
        
            $accountId = $this->db->lastInsertId();
        
            $stmt = $this->db->prepare(
                "SELECT * FROM account WHERE id = ?"
            );
        
            $stmt->execute([$accountId]);
        
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Login bằng session
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['username'] = $account['username'];
        $_SESSION['fullname'] = $account['fullname'];
        $_SESSION['role'] = $account['role'];
        
        header("Location: /Product/");
        exit;   
    }

    public function githubLogin()
    {
        $config = require __DIR__ . '/../config/github.php';

        $url = "https://github.com/login/oauth/authorize?" . http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => 'user:email',
            'prompt' => 'select_account'
        ]);

        header("Location: " . $url);
        exit;
    }

    public function githubCallback()
    {
    $config = require __DIR__ . '/../config/github.php';

    if (!isset($_GET['code'])) {
        die("GitHub login failed");
    }

    $code = $_GET['code'];

    // BƯỚC 1: Lấy access token
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://github.com/login/oauth/access_token",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'redirect_uri' => $config['redirect_uri']
        ]),
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (!isset($tokenData['access_token'])) {
        die("Cannot get GitHub access token");
    }

    $accessToken = $tokenData['access_token'];

    // BƯỚC 2: Lấy thông tin user
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.github.com/user",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "User-Agent: MyStore-App",
            "Accept: application/vnd.github+json"
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $githubUser = json_decode($response, true);

    // BƯỚC 3: Lấy email
    $email = $githubUser['email'] ?? null;
    $username = 'github_' . $githubUser['login'];

    if (!$email) {

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.github.com/user/emails",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $accessToken",
                "User-Agent: MyStore-App",
                "Accept: application/vnd.github+json"
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $emails = json_decode($response, true);

        foreach ($emails as $item) {
            if ($item['primary']) {
                $email = $item['email'];
                break;
            }
        }
    }

    if (!$email) {
        die("Cannot get GitHub email");
    }

    $fullname = $githubUser['name'] ?: $githubUser['login'];

    // BƯỚC 4: Tìm account
    $stmt = $this->db->prepare(
        "SELECT * FROM account WHERE username = ?"
    );

    $stmt->execute([$username]);

    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    // BƯỚC 5: Nếu chưa có thì tạo mới
    if (!$account) {

        $randomPassword = password_hash(
            bin2hex(random_bytes(16)),
            PASSWORD_DEFAULT
        );

        $stmt = $this->db->prepare(
            "INSERT INTO account(username, fullname, password, role)
             VALUES (?, ?, ?, 'user')"
        );

        $stmt->execute([
            $username,
            $fullname,
            $randomPassword
        ]);

        $accountId = $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            "SELECT * FROM account WHERE id = ?"
        );

        $stmt->execute([$accountId]);

        $account = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // BƯỚC 6: Login
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['username'] = $account['username'];
        $_SESSION['fullname'] = $account['fullname'];
        $_SESSION['role'] = $account['role'];

        header("Location: /Product/");
        exit;
    }

    public function save() { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
            $username = $_POST['username'] ?? ''; 
            $fullName = $_POST['fullname'] ?? ''; 
            $password = $_POST['password'] ?? ''; 
            $confirmPassword = $_POST['confirmpassword'] ?? ''; 
            $role = $_POST['role'] ?? 'user'; 

            $errors = []; 
            if (empty($username)) $errors['username'] = "Vui lòng nhập username!"; 
            if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!"; 
            if (empty($password)) $errors['password'] = "Vui lòng nhập password!"; 
            if ($password != $confirmPassword) $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!"; 
            if (!in_array($role, ['admin', 'user'])) $role = 'user'; 
            if ($this->accountModel->getAccountByUsername($username)) { 
            $errors['account'] = "Tài khoản này đã được đăng ký!"; 
            } 
            if (count($errors) > 0) { 
                include_once 'app/views/account/register.php'; 
            } else { 
                $result = $this->accountModel->save($username, $fullName, $password, $role); 
                if ($result) { 
                    header('Location: /account/login');
                    exit;
                } 
            } 
        } 
    } 
            
    public function logout() {
        session_start(); 
        unset($_SESSION['username']); 
        unset($_SESSION['role']); 
        header('Location: /Product'); 
        exit; 
    } 

    public function checkLogin() { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
            $username = $_POST['username'] ?? ''; 
            $password = $_POST['password'] ?? '';

            $account = $this->accountModel->getAccountByUsername($username); 
            if ($account && password_verify($password, $account->password)) { 
                session_start(); 
                if (!isset($_SESSION['username'])) { 
                $_SESSION['username'] = $account->username; 
                $_SESSION['role'] = $account->role; 
                }
                header('Location: /Product'); 
            exit; 
            } else { 
                $error = $account ? "Mật khẩu không đúng!" : "Không tìm thấy tài khoản";
                include_once 'app/views/account/login.php'; 
                exit;
            } 
        }
    }

    public function manageRole()
    {
        $accounts = $this->accountModel->getAllAccounts();
        include "app/views/account/manage_role.php";
    }

    public function updateRole()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $role = $_POST['role'];
            if ($this->accountModel->updateRole($username, $role)) {

                header("Location: /account/manageRole");
                exit();
            }
            echo "Cập nhật role thất bại";
        }
    }
}
?>