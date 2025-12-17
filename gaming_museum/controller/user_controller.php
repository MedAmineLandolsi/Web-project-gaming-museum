<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use absolute paths from the controller directory
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/user_model.php';

class UserController {
    private $conn;
    
    public function __construct() {
        $this->conn = config::getConnexion();
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
    
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['username' => $username]);
            
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetch();
                
                if ($userData['status'] === 'banned') {
                    return ['success' => false, 'message' => 'Your account has been banned'];
                }
                
                if (password_verify($password, $userData['password'])) {
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['email'] = $userData['email'];
                    $normalizedRole = strtolower(trim((string)($userData['role'] ?? '')));
                    $_SESSION['role'] = $normalizedRole;
                    $_SESSION['profile_picture'] = $userData['profile_picture_url'];
                    $_SESSION['logged_in'] = true;

                    // Bridge sessions for the "projet" module (communautés/publications)
                    // so a single login works across the whole workspace.
                    $_SESSION['user_prenom'] = $userData['first_name'] ?? '';
                    $_SESSION['user_nom'] = $userData['last_name'] ?? '';
                    $_SESSION['is_admin'] = $normalizedRole === 'admin';

                    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
                    $gamingBaseUrl = rtrim(dirname($scriptName, 3), '/');
                    if ($gamingBaseUrl === '/' || $gamingBaseUrl === '.') {
                        $gamingBaseUrl = '';
                    }
                    $pp = $userData['profile_picture_url'] ?? '';
                    $_SESSION['user_avatar'] = $pp !== '' ? ($gamingBaseUrl . '/uploads/' . $pp) : '';
                    
                    return ['success' => true, 'message' => 'Login successful', 'role' => $normalizedRole];
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
            // Check if username is being changed and if it already exists
            if (isset($data['username'])) {
                $sql = "SELECT id FROM users WHERE username = :username AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['username' => $data['username'], 'id' => $userId]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'Username already exists'];
                }
            }
            
            // Check if email is being changed and if it already exists
            if (isset($data['email'])) {
                $sql = "SELECT id FROM users WHERE email = :email AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['email' => $data['email'], 'id' => $userId]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'Email already exists'];
                }
            }
            
            // Handle profile picture upload
            $profilePicturePath = null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                
                // Create uploads directory if it doesn't exist
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
                    
                    // Delete old profile picture if exists
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
            
            // Build the SQL query dynamically based on what's being updated
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
            
            // Handle password update if provided
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
                // Update session variables
                if (isset($data['username'])) {
                    $_SESSION['username'] = $data['username'];
                }
                if (isset($data['email'])) {
                    $_SESSION['email'] = $data['email'];
                }
                if ($profilePicturePath) {
                    $_SESSION['profile_picture'] = $profilePicturePath;
                }

                // Keep bridge session variables up-to-date
                if (isset($data['first_name'])) {
                    $_SESSION['user_prenom'] = $data['first_name'];
                }
                if (isset($data['last_name'])) {
                    $_SESSION['user_nom'] = $data['last_name'];
                }
                if ($profilePicturePath) {
                    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
                    $gamingBaseUrl = rtrim(dirname($scriptName, 3), '/');
                    if ($gamingBaseUrl === '/' || $gamingBaseUrl === '.') {
                        $gamingBaseUrl = '';
                    }
                    $_SESSION['user_avatar'] = $gamingBaseUrl . '/uploads/' . $profilePicturePath;
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
        $role = strtolower(trim((string)($_SESSION['role'] ?? '')));
        return $role === 'admin';
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
                $result = $controller->login($_POST['username'], $_POST['password']);
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