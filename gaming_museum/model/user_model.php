<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $first_name;
    private $last_name;
    private $phone_number;
    private $date_of_birth;
    private $profile_picture_url;
    private $role;
    private $status;
    
    // Constructor
    public function __construct($id = null, $username = null, $email = null, $password = null, 
                                $first_name = null, $last_name = null, $phone_number = null, 
                                $date_of_birth = null, $profile_picture_url = null, 
                                $role = 'user', $status = 'active') {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone_number = $phone_number;
        $this->date_of_birth = $date_of_birth;
        $this->profile_picture_url = $profile_picture_url;
        $this->role = $role;
        $this->status = $status;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function getFirstName() {
        return $this->first_name;
    }
    
    public function getLastName() {
        return $this->last_name;
    }
    
    public function getPhoneNumber() {
        return $this->phone_number;
    }
    
    public function getDateOfBirth() {
        return $this->date_of_birth;
    }
    
    public function getProfilePictureUrl() {
        return $this->profile_picture_url;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function setFirstName($first_name) {
        $this->first_name = $first_name;
    }
    
    public function setLastName($last_name) {
        $this->last_name = $last_name;
    }
    
    public function setPhoneNumber($phone_number) {
        $this->phone_number = $phone_number;
    }
    
    public function setDateOfBirth($date_of_birth) {
        $this->date_of_birth = $date_of_birth;
    }
    
    public function setProfilePictureUrl($profile_picture_url) {
        $this->profile_picture_url = $profile_picture_url;
    }
    
    public function setRole($role) {
        $this->role = $role;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
}
?>
