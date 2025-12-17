<?php


class config
{
    private static $pdo = null;
    
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=localhost;dbname=gaming_museum;charset=utf8mb4",
                    "root",
                    ""
                );
                
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
               
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
    
    // Alternative method for CRUD-COMMANDE compatibility
    public static function connect()
    {
        return self::getConnexion();
    }
}

// Optional: Auto-instantiate for backward compatibility
// config::getConnexion();
?>