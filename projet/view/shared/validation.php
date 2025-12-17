<?php
class Validation {
    public function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function isAlpha($string) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $string);
    }
    
    public function isNumeric($value) {
        return is_numeric($value);
    }
    
    public function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public function isLengthBetween($string, $min, $max) {
        $length = strlen(trim($string));
        return $length >= $min && $length <= $max;
    }
    
    public function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}
?>