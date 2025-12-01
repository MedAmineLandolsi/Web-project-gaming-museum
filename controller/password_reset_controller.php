<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use absolute paths from the controller directory
require_once __DIR__ . '/../config.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

class PasswordResetController {
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
    
    private function generateResetToken() {
        // Generate a secure random token - KEEP AT 32 BYTES
        return bin2hex(random_bytes(32)); // This creates a 64-character token
    }
    
    private function sendResetEmail($email, $token, $username) {
        $mail = new PHPMailer(true);
        
        try {
            // Enable verbose debug output (remove in production)
            $mail->SMTPDebug = 0; // Set to 2 for debugging
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer: $str");
            };
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'aminelandolsi5000@gmail.com';
            $mail->Password   = 'hfvq kqny lcci czok';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Set charset to UTF-8
            $mail->CharSet = 'UTF-8';
            
            // Recipients
            $mail->setFrom('aminelandolsi5000@gmail.com', 'Ludology Vault');
            $mail->addAddress($email, $username);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de mot de passe - Ludology Vault';
            
            // Get the correct base URL and construct proper path
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            
            // Construct the full reset link with correct path to GAMING_MUSEUM project
            $resetLink = $protocol . '://' . $host . '/GAMING_MUSEUM/view/frontoffice/reset_password.php?token=' . $token;
            
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
                        p {
                            line-height: 1.6;
                            margin-bottom: 20px;
                            color: #ffffff;
                        }
                        .button-container {
                            text-align: center;
                            margin: 30px 0;
                        }
                        .button {
                            display: inline-block;
                            padding: 15px 30px;
                            background-color: #00FF41;
                            color: #0a0a0a !important;
                            text-decoration: none;
                            font-weight: bold;
                            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
                        }
                        .link {
                            word-break: break-all;
                            color: #00FF41;
                            font-size: 14px;
                        }
                        .footer {
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 1px solid #333;
                            font-size: 12px;
                            color: #888;
                            text-align: center;
                        }
                        .warning {
                            background-color: rgba(255, 215, 0, 0.1);
                            border: 1px solid #FFD700;
                            padding: 15px;
                            margin: 20px 0;
                            color: #FFD700;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>🎮 LUDOLOGY VAULT 🎮</h1>
                        <p>Bonjour <strong>' . htmlspecialchars($username) . '</strong>,</p>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                        <div class="button-container">
                            <a href="' . htmlspecialchars($resetLink) . '" class="button">RÉINITIALISER MON MOT DE PASSE</a>
                        </div>
                        <p>Ou copiez et collez ce lien dans votre navigateur :</p>
                        <p class="link">' . htmlspecialchars($resetLink) . '</p>
                        <div class="warning">
                            <strong>⚠️ IMPORTANT :</strong> Ce lien expirera dans 1 heure.
                        </div>
                        <p>Si vous n\'avez pas demandé cette réinitialisation, ignorez simplement cet email. Votre mot de passe restera inchangé.</p>
                        <div class="footer">
                            <p>© 2025 Ludology Vault - Le coffre-fort des jeux vidéo rétro</p>
                        </div>
                    </div>
                </body>
                </html>
            ';
            
            $mail->AltBody = "Bonjour $username,\n\n" .
                            "Vous avez demandé la réinitialisation de votre mot de passe.\n\n" .
                            "Cliquez sur ce lien pour réinitialiser votre mot de passe :\n" .
                            "$resetLink\n\n" .
                            "Ce lien expirera dans 1 heure.\n\n" .
                            "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n" .
                            "Cordialement,\n" .
                            "L'équipe Ludology Vault";
            
            $mail->send();
            return ['success' => true, 'message' => 'Email sent successfully'];
        } catch (Exception $e) {
            // Log the full error for debugging
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return ['success' => false, 'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo];
        }
    }
    
    public function forgotPassword($email, $recaptchaResponse = null) {
        try {
            // Verify reCAPTCHA
            if (!$this->verifyRecaptcha($recaptchaResponse)) {
                return ['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'];
            }
            
            // Check if email exists
            $sql = "SELECT id, username FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['email' => $email]);
            
            if ($stmt->rowCount() === 0) {
                // Don't reveal if email doesn't exist for security reasons
                return ['success' => true, 'message' => 'Si un compte avec cet email existe, un lien de réinitialisation a été envoyé.'];
            }
            
            $user = $stmt->fetch();
            $userId = $user['id'];
            $username = $user['username'];
            
            // Generate reset token (64 characters)
            $token = $this->generateResetToken();
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
            
            // Store token in database
            $sql = "UPDATE users SET reset_token_hash = :token_hash, reset_token_expires_at = :expires_at WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt,
                'id' => $userId
            ]);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to generate reset token.'];
            }
            
            // Send reset email
            $emailResult = $this->sendResetEmail($email, $token, $username);
            
            if ($emailResult['success']) {
                return ['success' => true, 'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'];
            } else {
                // Return the specific error for debugging
                return ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email: ' . $emailResult['message']];
            }
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function verifyToken($token) {
        try {
            $tokenHash = hash('sha256', $token);
            
            $sql = "SELECT id, reset_token_expires_at FROM users WHERE reset_token_hash = :token_hash";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['token_hash' => $tokenHash]);
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Token de réinitialisation invalide.'];
            }
            
            $user = $stmt->fetch();
            $expiresAt = strtotime($user['reset_token_expires_at']);
            
            if ($expiresAt < time()) {
                return ['success' => false, 'message' => 'Le token de réinitialisation a expiré. Veuillez en demander un nouveau.'];
            }
            
            return ['success' => true, 'user_id' => $user['id']];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function resetPassword($token, $newPassword) {
        try {
            // Verify token
            $tokenVerification = $this->verifyToken($token);
            
            if (!$tokenVerification['success']) {
                return $tokenVerification;
            }
            
            $userId = $tokenVerification['user_id'];
            
            // Validate password
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères.'];
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password and clear reset token
            $sql = "UPDATE users SET password = :password, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                'password' => $hashedPassword,
                'id' => $userId
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès!'];
            } else {
                return ['success' => false, 'message' => 'Échec de la réinitialisation du mot de passe.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $controller = new PasswordResetController();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'forgot_password':
                $recaptchaResponse = $_POST['g-recaptcha-response'] ?? null;
                $result = $controller->forgotPassword($_POST['email'], $recaptchaResponse);
                echo json_encode($result);
                break;
                
            case 'reset_password':
                $result = $controller->resetPassword($_POST['token'], $_POST['password']);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }
    exit();
}
?>