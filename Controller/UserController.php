<?php
require_once __DIR__ . '/../connection.php';    
require_once __DIR__ . '/../Model/User.php';
class UserController {
    private $userModel;
    private $db;

    public function __construct() {
        $this->userModel = new User($this->db);
    }

    public function register() {
        if($_POST) {
            $this->userModel->username = $_POST['username'];
            $this->userModel->email = $_POST['email'];
            $this->userModel->password = $_POST['password'];
            $this->userModel->first_name = $_POST['first_name'];
            $this->userModel->last_name = $_POST['last_name'];
            $this->userModel->phone_number = $_POST['phone_number'];
            $this->userModel->date_of_birth = $_POST['date_of_birth'];
            $this->userModel->role = 'user';

            if($this->userModel->create()) {
                header("Location: login.php?message=Registration successful");
                exit;
            } else {
                $error = "Registration failed";
            }
        }
        include 'View/FrontOffice/signup.php';
    }

    public function login() {
        if($_POST) {
            $error='';
            $this->userModel->email = $_POST['email'];
            $email_exists = $this->userModel->emailExists();

            if($email_exists && password_verify($_POST['password'], $this->userModel->password)) {
                if($this->userModel->status == 'active') {
                    session_start();
                    $_SESSION['user_id'] = $this->userModel->id;
                    $_SESSION['username'] = $this->userModel->username;
                    $_SESSION['role'] = $this->userModel->role;
                    
                    header("Location: profile.php");
                    exit;
                } else {
                    $error = "Account is inactive";
                }
            } else {
                $error = "Invalid email or password";
            }
        }
        include 'View/FrontOffice/login.php';
    }

    public function viewProfile() {
        session_start();
        if(!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        $this->userModel->id = $_SESSION['user_id'];
        if($this->userModel->readOne()) {
            $user = $this->userModel;
            include 'View/FrontOffice/profile.php';
        } else {
            header("Location: login.php?error=User not found");
            exit;
        }
    }

    public function updateProfile() {
        session_start();
        if(!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        if($_POST) {
            $this->userModel->id = $_SESSION['user_id'];
            $this->userModel->first_name = $_POST['first_name'];
            $this->userModel->last_name = $_POST['last_name'];
            $this->userModel->phone_number = $_POST['phone_number'];
            $this->userModel->date_of_birth = $_POST['date_of_birth'];

            if($this->userModel->update()) {
                $message = "Profile updated successfully";
            } else {
                $error = "Profile update failed";
            }
        }
        $this->viewProfile();
    }

    public function changePassword() {
        session_start();
        if(!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        if($_POST) {
            $this->userModel->id = $_SESSION['user_id'];
            $this->userModel->password = $_POST['new_password'];
            
            if($this->userModel->changePassword()) {
                $message = "Password changed successfully";
            } else {
                $error = "Password change failed";
            }
        }
        include 'View/FrontOffice/changePassword.php';
    }

    public function deactivateAccount() {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    
    if(isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
        $this->userModel->id = $_SESSION['user_id'];
        if($this->userModel->deactivate()) {
            session_destroy();
            header("Location: login.php?message=Account deactivated");
            exit;
        } else {
            $error = "Account deactivation failed";
        }
    }
    
    include 'View/FrontOffice/deactivate.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>