<?php
require_once __DIR__ . '/../connection.php';
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $phone_number;
    public $date_of_birth;
    public $role;
    public $status;

    public function __construct() {
        $this->conn = config::getConnexion();
        
        // Debug: Check if connection is established
        if ($this->conn === null) {
            die("❌ Database connection is null in User constructor");
        }
    }

    public function create() {
        // Debug connection
        if ($this->conn === null) {
            die("❌ Database connection is null in create() method");
        }

        $query = "INSERT INTO " . $this->table_name . " 
                SET username=:username, email=:email, password=:password, 
                    first_name=:first_name, last_name=:last_name, 
                    phone_number=:phone_number, date_of_birth=:date_of_birth, 
                    role=:role";

        echo "🔧 DEBUG: Preparing query: $query<br>";
        
        $stmt = $this->conn->prepare($query);

        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);
        $stmt->bindParam(":role", $this->role);

        echo "🔧 DEBUG: Executing query...<br>";
        
        if($stmt->execute()) {
            echo "✅ User created successfully!<br>";
            return true;
        } else {
            echo "❌ Query execution failed<br>";
            $errorInfo = $stmt->errorInfo();
            echo "🔧 DEBUG: SQL Error: " . $errorInfo[2] . "<br>";
            return false;
        }
    }

    public function emailExists() {
        $query = "SELECT id, username, password, role, status 
                FROM " . $this->table_name . " 
                WHERE email = ? 
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->phone_number = $row['phone_number'];
            $this->date_of_birth = $row['date_of_birth'];
            $this->profile_picture_url = $row['profile_picture_url'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET first_name=:first_name, last_name=:last_name, 
                    phone_number=:phone_number, date_of_birth=:date_of_birth
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function changePassword() {
        $query = "UPDATE " . $this->table_name . " SET password=:password WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deactivate() {
        $query = "UPDATE " . $this->table_name . " SET status='inactive' WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>