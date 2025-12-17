<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use absolute paths from the controller directory
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/user_model.php';

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

class UserController {
    private $conn;
    private $recaptchaSecretKey = '6LdiwxwsAAAAAHqn32uZh3KzzHorRZ6w9Zyerwmq';
    
    public function __construct() {
        $this->conn = config::getConnexion();
    }
    
    private function verifyRecaptcha($recaptchaResponse) {
        if (empty($recaptchaResponse)) {
            return false;
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultJson = json_decode($result);
        
        return $resultJson->success;
    }
    
    private function generate2FACode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    private function send2FAEmail($email, $code, $username) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'aminelandolsi5000@gmail.com';
            $mail->Password   = 'hfvq kqny lcci czok';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom('aminelandolsi5000@gmail.com', 'Ludology Vault');
            $mail->addAddress($email, $username);
            
            $mail->isHTML(true);
            $mail->Subject = 'Code de Vérification 2FA - Ludology Vault';
            
            $mail->Body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #0a0a0a;
                            color: #ffffff;
                            padding: 20px;
                            margin: 0;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            background-color: #1a1a1a;
                            border: 2px solid #00FF41;
                            padding: 30px;
                            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
                        }
                        h1 {
                            color: #00FF41;
                            text-align: center;
                            text-shadow: 0 0 10px #00FF41;
                            margin-top: 0;
                        }
                        .code-box {
                            background: rgba(0, 255, 65, 0.1);
                            border: 3px solid #00FF41;
                            padding: 20px;
                            margin: 30px 0;
                            text-align: center;
                        }
                        .code {
                            font-size: 48px;
                            font-weight: bold;
                            color: #00FF41;
                            letter-spacing: 10px;
                            text-shadow: 0 0 20px #00FF41;
                        }
                        p {
                            line-height: 1.6;
                            margin-bottom: 20px;
                            color: #ffffff;
                        }
                        .warning {
                            background-color: rgba(255, 215, 0, 0.1);
                            border: 1px solid #FFD700;
                            padding: 15px;
                            margin: 20px 0;
                            color: #FFD700;
                        }
                        .footer {
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 1px solid #333;
                            font-size: 12px;
                            color: #888;
                            text-align: center;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>🎮 LUDOLOGY VAULT 🎮</h1>
                        <p>Bonjour <strong>' . htmlspecialchars($username) . '</strong>,</p>
                        <p>Voici votre code de vérification pour finaliser votre connexion :</p>
                        
                        <div class="code-box">
                            <div class="code">' . $code . '</div>
                        </div>
                        
                        <div class="warning">
                            <strong>⚠️ IMPORTANT :</strong> Ce code expirera dans 5 minutes.
                        </div>
                        
                        <p>Si vous n\'avez pas demandé ce code, ignorez cet email et assurez-vous que votre compte est sécurisé.</p>
                        
                        <div class="footer">
                            <p>© 2025 Ludology Vault - Le coffre-fort des jeux vidéo rétro</p>
                        </div>
                    </div>
                </body>
                </html>
            ';
            
            $mail->AltBody = "Bonjour $username,\n\n" .
                            "Votre code de vérification : $code\n\n" .
                            "Ce code expire dans 5 minutes.\n\n" .
                            "Ludology Vault";
            
            $mail->send();
            return ['success' => true];
        } catch (Exception $e) {
            error_log("2FA Email Error: " . $mail->ErrorInfo);
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }
    
    public function register($user) {
        try {
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['username' => $user->getUsername()]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['email' => $user->getEmail()]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone_number, date_of_birth, role, status) 
                    VALUES (:username, :email, :password, :first_name, :last_name, :phone_number, :date_of_birth, :role, :status)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'password' => $hashedPassword,
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'phone_number' => $user->getPhoneNumber(),
                'date_of_birth' => $user->getDateOfBirth(),
                'role' => 'user',
                'status' => 'active'
            ]);
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function login($username, $password, $recaptchaResponse = null) {
        try {
            if (!$this->verifyRecaptcha($recaptchaResponse)) {
                return ['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'];
            }
            
            $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['username' => $username]);
            
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetch();
                
                if ($userData['status'] === 'banned') {
                    return ['success' => false, 'message' => 'Your account has been banned'];
                }
                
                if (password_verify($password, $userData['password'])) {
                    // Generate 2FA code
                    $code = $this->generate2FACode();
                    $expiresAt = date('Y-m-d H:i:s', time() + 300); // 5 minutes
                    
                    // Store code in database
                    $sql = "UPDATE users SET two_fa_code = :code, two_fa_expires_at = :expires_at WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([
                        'code' => $code,
                        'expires_at' => $expiresAt,
                        'id' => $userData['id']
                    ]);
                    
                    // Send 2FA email
                    $emailResult = $this->send2FAEmail($userData['email'], $code, $userData['username']);
                    
                    if ($emailResult['success']) {
                        // Store user info in session for 2FA verification
                        $_SESSION['2fa_user_id'] = $userData['id'];
                        $_SESSION['2fa_pending'] = true;
                        
                        return ['success' => true, 'message' => 'Code sent to email', 'require_2fa' => true];
                    } else {
                        return ['success' => false, 'message' => 'Failed to send verification code'];
                    }
                } else {
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                return ['success' => false, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function verify2FA($code) {
        try {
            if (!isset($_SESSION['2fa_user_id']) || !isset($_SESSION['2fa_pending'])) {
                return ['success' => false, 'message' => 'Invalid session'];
            }
            
            $userId = $_SESSION['2fa_user_id'];
            
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Check if code expired
            if (strtotime($userData['two_fa_expires_at']) < time()) {
                return ['success' => false, 'message' => 'Code expired. Please login again.'];
            }
            
            // Verify code
            if ($userData['two_fa_code'] !== $code) {
                return ['success' => false, 'message' => 'Invalid code'];
            }
            
            // Clear 2FA data
            $sql = "UPDATE users SET two_fa_code = NULL, two_fa_expires_at = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            
            // Complete login
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['profile_picture'] = $userData['profile_picture_url'];
            $_SESSION['logged_in'] = true;
            
            // Clear 2FA session variables
            unset($_SESSION['2fa_user_id']);
            unset($_SESSION['2fa_pending']);
            
            return ['success' => true, 'message' => 'Login successful', 'role' => $userData['role']];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function resend2FA() {
        try {
            if (!isset($_SESSION['2fa_user_id']) || !isset($_SESSION['2fa_pending'])) {
                return ['success' => false, 'message' => 'Invalid session'];
            }
            
            $userId = $_SESSION['2fa_user_id'];
            
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Generate new code
            $code = $this->generate2FACode();
            $expiresAt = date('Y-m-d H:i:s', time() + 300);
            
            $sql = "UPDATE users SET two_fa_code = :code, two_fa_expires_at = :expires_at WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'code' => $code,
                'expires_at' => $expiresAt,
                'id' => $userId
            ]);
            
            // Send email
            $emailResult = $this->send2FAEmail($userData['email'], $code, $userData['username']);
            
            if ($emailResult['success']) {
                return ['success' => true, 'message' => 'New code sent'];
            } else {
                return ['success' => false, 'message' => 'Failed to send code'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function googleLogin($googleData) {
        try {
            // Check if user exists by google_id
            $sql = "SELECT * FROM users WHERE google_id = :google_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['google_id' => $googleData['id']]);
            
            if ($stmt->rowCount() > 0) {
                // Existing Google user
                $userData = $stmt->fetch();
                
                if ($userData['status'] === 'banned') {
                    return ['success' => false, 'message' => 'Your account has been banned'];
                }
            } else {
                // Check if email exists (link Google account)
                $sql = "SELECT * FROM users WHERE email = :email";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['email' => $googleData['email']]);
                
                if ($stmt->rowCount() > 0) {
                    // Link existing account
                    $userData = $stmt->fetch();
                    
                    $sql = "UPDATE users SET google_id = :google_id WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([
                        'google_id' => $googleData['id'],
                        'id' => $userData['id']
                    ]);
                } else {
                    // Create new user
                    $username = explode('@', $googleData['email'])[0];
                    $baseUsername = $username;
                    $counter = 1;
                    
                    // Make sure username is unique
                    while (true) {
                        $sql = "SELECT id FROM users WHERE username = :username";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute(['username' => $username]);
                        
                        if ($stmt->rowCount() === 0) break;
                        
                        $username = $baseUsername . $counter;
                        $counter++;
                    }
                    
                    $sql = "INSERT INTO users (username, email, password, first_name, last_name, google_id, role, status) 
                            VALUES (:username, :email, :password, :first_name, :last_name, :google_id, :role, :status)";
                    
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([
                        'username' => $username,
                        'email' => $googleData['email'],
                        'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                        'first_name' => $googleData['given_name'] ?? '',
                        'last_name' => $googleData['family_name'] ?? '',
                        'google_id' => $googleData['id'],
                        'role' => 'user',
                        'status' => 'active'
                    ]);
                    
                    $userData = [
                        'id' => $this->conn->lastInsertId(),
                        'username' => $username,
                        'email' => $googleData['email'],
                        'role' => 'user',
                        'profile_picture_url' => $googleData['picture'] ?? null
                    ];
                }
            }
            
            // Set session (no 2FA for Google login)
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['profile_picture'] = $userData['profile_picture_url'];
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'message' => 'Google login successful', 'role' => $userData['role']];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function viewProfile($userId) {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'user' => $stmt->fetch()];
            } else {
                return ['success' => false, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function updateProfile($userId, $data, $file = null) {
        try {
            if (isset($data['username'])) {
                $sql = "SELECT id FROM users WHERE username = :username AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['username' => $data['username'], 'id' => $userId]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'Username already exists'];
                }
            }
            
            if (isset($data['email'])) {
                $sql = "SELECT id FROM users WHERE email = :email AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['email' => $data['email'], 'id' => $userId]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'Email already exists'];
                }
            }
            
            $profilePicturePath = null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed.'];
                }
                
                $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $profilePicturePath = $newFileName;
                    
                    $oldPicture = $this->viewProfile($userId);
                    if ($oldPicture['success'] && $oldPicture['user']['profile_picture_url']) {
                        $oldPath = $uploadDir . $oldPicture['user']['profile_picture_url'];
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                } else {
                    return ['success' => false, 'message' => 'Failed to upload profile picture'];
                }
            }
            
            $updateFields = [];
            $params = ['id' => $userId];
            
            if (isset($data['username'])) {
                $updateFields[] = 'username = :username';
                $params['username'] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $updateFields[] = 'email = :email';
                $params['email'] = $data['email'];
            }
            
            if (isset($data['first_name'])) {
                $updateFields[] = 'first_name = :first_name';
                $params['first_name'] = $data['first_name'];
            }
            
            if (isset($data['last_name'])) {
                $updateFields[] = 'last_name = :last_name';
                $params['last_name'] = $data['last_name'];
            }
            
            if (isset($data['phone_number'])) {
                $updateFields[] = 'phone_number = :phone_number';
                $params['phone_number'] = $data['phone_number'];
            }
            
            if (isset($data['date_of_birth'])) {
                $updateFields[] = 'date_of_birth = :date_of_birth';
                $params['date_of_birth'] = $data['date_of_birth'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $updateFields[] = 'password = :password';
                $params['password'] = $hashedPassword;
            }
            
            if ($profilePicturePath) {
                $updateFields[] = 'profile_picture_url = :profile_picture';
                $params['profile_picture'] = $profilePicturePath;
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No fields to update'];
            }
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                if (isset($data['username'])) {
                    $_SESSION['username'] = $data['username'];
                }
                if (isset($data['email'])) {
                    $_SESSION['email'] = $data['email'];
                }
                if ($profilePicturePath) {
                    $_SESSION['profile_picture'] = $profilePicturePath;
                }
                return ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update profile'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            $sql = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
            
            if (!password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                'password' => $hashedPassword,
                'id' => $userId
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to change password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function deactivateAccount($userId) {
        try {
            $sql = "UPDATE users SET status = 'inactive' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute(['id' => $userId]);
            
            if ($result) {
                $this->logout();
                return ['success' => true, 'message' => 'Account deactivated'];
            } else {
                return ['success' => false, 'message' => 'Failed to deactivate account'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, role, status, date_of_birth, phone_number FROM users ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function banUser($userId) {
        try {
            $sql = "UPDATE users SET status = 'banned' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute(['id' => $userId]);
            
            return ['success' => $result, 'message' => $result ? 'User banned successfully' : 'Failed to ban user'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function deleteUser($userId) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute(['id' => $userId]);
            
            return ['success' => $result, 'message' => $result ? 'User deleted successfully' : 'Failed to delete user'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register':
                $user = new User(
                    null,
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['first_name'] ?? '',
                    $_POST['last_name'] ?? '',
                    $_POST['phone_number'] ?? '',
                    $_POST['date_of_birth'] ?? null
                );
                $result = $controller->register($user);
                echo json_encode($result);
                break;
                
            case 'login':
                $recaptchaResponse = $_POST['g-recaptcha-response'] ?? null;
                $result = $controller->login($_POST['username'], $_POST['password'], $recaptchaResponse);
                echo json_encode($result);
                break;
                
            case 'verify2FA':
                $result = $controller->verify2FA($_POST['code']);
                echo json_encode($result);
                break;
                
            case 'resend2FA':
                $result = $controller->resend2FA();
                echo json_encode($result);
                break;
                
            case 'googleLogin':
                $googleData = json_decode($_POST['google_data'], true);
                $result = $controller->googleLogin($googleData);
                echo json_encode($result);
                break;
                
            case 'viewProfile':
                $userId = $_POST['user_id'] ?? $_SESSION['user_id'];
                $result = $controller->viewProfile($userId);
                echo json_encode($result);
                break;
                
            case 'updateProfile':
                $file = isset($_FILES['profile_picture']) ? $_FILES['profile_picture'] : null;
                $result = $controller->updateProfile($_SESSION['user_id'], $_POST, $file);
                echo json_encode($result);
                break;
                
            case 'changePassword':
                $result = $controller->changePassword($_SESSION['user_id'], $_POST['current_password'], $_POST['new_password']);
                echo json_encode($result);
                break;
                
            case 'deactivateAccount':
                $result = $controller->deactivateAccount($_SESSION['user_id']);
                echo json_encode($result);
                break;
                
            case 'logout':
                $result = $controller->logout();
                echo json_encode($result);
                break;
                
            case 'banUser':
                if ($controller->isAdmin()) {
                    $result = $controller->banUser($_POST['user_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                }
                break;
                
            case 'deleteUser':
                if ($controller->isAdmin()) {
                    $result = $controller->deleteUser($_POST['user_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                }
                break;
        }
    }
}
?>