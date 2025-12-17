<?php
require_once __DIR__ . '/../config.php';

class JeuxController
{

    /**
 * Update game stock
 */
public function updateStock($game_id, $quantity_change) {
    $db = config::connect();
    
    try {
        // Get current stock
        $sql = "SELECT stock FROM jeux WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute(['id' => $game_id]);
        $current = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            return false;
        }
        
        // Calculate new stock
        $new_stock = $current['stock'] + $quantity_change;
        
        // Ensure stock doesn't go negative
        if ($new_stock < 0) {
            $new_stock = 0;
        }
        
        // Update stock
        $sql = "UPDATE jeux SET stock = :stock WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute([
            'stock' => $new_stock,
            'id' => $game_id
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating stock: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if game has sufficient stock
 */
public function checkStock($game_id, $requested_quantity) {
    $db = config::connect();
    
    try {
        $sql = "SELECT stock FROM jeux WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute(['id' => $game_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        return $result['stock'] >= $requested_quantity;
    } catch (PDOException $e) {
        error_log("Error checking stock: " . $e->getMessage());
        return false;
    }
}
    public function listjeux($search = null, $sortBy = null, $order = null)
{
    $db = config::getConnexion();

    try {
        $sql = "SELECT * FROM jeux";
        $params = [];

        if ($search) {
            $sql .= " WHERE nom LIKE :search OR categorie LIKE :search OR description LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $validColumns = ['prix', 'nom', 'date'];
        $validOrder = ['asc', 'desc'];

        if ($sortBy && in_array(strtolower($sortBy), $validColumns)) {
            $sql .= " ORDER BY " . $sortBy;
            $sql .= " " . (in_array(strtolower($order), $validOrder) ? strtoupper($order) : "ASC");
        }

        $query = $db->prepare($sql);
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        die('Error:' . $e->getMessage());
    }
}



  
   function addgame(jeux $game)
{
    $sql = "INSERT INTO jeux (nom, description, prix, stock, categorie, image, trailer_url, rarity, `condition`, edition, estimated_value, collector_notes) 
            VALUES (:nom, :description, :prix, :stock, :categorie, :image, :trailer_url, :rarity, :condition, :edition, :estimated_value, :collector_notes)";

    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute([
            'nom'             => $game->getnom(),
            'description'     => $game->getdescription(),
            'prix'            => $game->getprix(),
            'stock'           => $game->getstock(),      
            'categorie'       => $game->getcategorie(),
            'image'           => $game->getImage(),
            'trailer_url'     => $game->getTrailerUrl(),
            'rarity'          => $game->getRarity(),
            'condition'       => $game->getCondition(),
            'edition'         => $game->getEdition(),
            'estimated_value' => $game->getEstimatedValue(),
            'collector_notes' => $game->getCollectorNotes()
        ]);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
     function updategame($game, $id)
{
    try {
        $db = config::getConnexion();

        $query = $db->prepare(
            'UPDATE jeux SET 
                nom = :nom,
                description = :description,
                prix = :prix,
                categorie = :categorie,
                trailer_url = :trailer_url,
                rarity = :rarity,
                `condition` = :condition,
                edition = :edition,
                estimated_value = :estimated_value,
                collector_notes = :collector_notes
            WHERE id = :id'
        );

        $query->execute([
            'id'              => $id,
            'nom'             => $game->getnom(),
            'description'     => $game->getdescription(),
            'prix'            => $game->getprix(),
            'categorie'       => $game->getcategorie(),
            'trailer_url'     => $game->getTrailerUrl(),
            'rarity'          => $game->getRarity(),
            'condition'       => $game->getCondition(),
            'edition'         => $game->getEdition(),
            'estimated_value' => $game->getEstimatedValue(),
            'collector_notes' => $game->getCollectorNotes()
        ]);

        echo $query->rowCount() . " records UPDATED successfully <br>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); 
    }
}
public function getGameScreenshots($game_id) {
    $db = config::getConnexion();
    
    try {
        $sql = "SELECT * FROM game_screenshots WHERE game_id = :game_id ORDER BY sort_order ASC";
        $query = $db->prepare($sql);
        $query->execute(['game_id' => $game_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching screenshots: " . $e->getMessage());
        return [];
    }
}

public function getRarityDisplay($rarity) {
    $rarities = [
        'common' => 'Commun',
        'rare' => 'Rare',
        'very_rare' => 'Très Rare',
        'collector' => "Édition Collector",
        'museum_piece' => 'Pièce de Musée'
    ];
    
    return $rarities[$rarity] ?? $rarity;
}

public function getConditionDisplay($condition) {
    $conditions = [
        'new' => 'Neuf (scellé)',
        'like_new' => 'Comme neuf',
        'very_good' => 'Très bon état',
        'good' => 'Bon état',
        'acceptable' => 'État acceptable'
    ];
    
    return $conditions[$condition] ?? $condition;
}
public function extractYoutubeId($url) {
    if (empty($url)) return '';
    
    // Handle various YouTube URL formats
    $patterns = [
        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
        '/youtube\.com\/embed\/([^"&?\/\s]{11})/',
        '/youtube\.com\/v\/([^"&?\/\s]{11})/',
        '/youtu\.be\/([^"&?\/\s]{11})/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    return '';
}

    function deletegame($id)
    {
        $sql = "DELETE FROM jeux WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
    public function getGameById($id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare("SELECT * FROM jeux WHERE id = :id");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public function countGames()
    {
        $sql = "SELECT COUNT(*) as total FROM jeux";
        $db = config::getConnexion();

        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    function showjeux($id)
{
    $sql = "SELECT * FROM jeux WHERE id = :id";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute([
            'id' => $id
        ]);

        return $query->fetch();  // returns 1 game
    } catch (PDOException $e) {
        $e->getMessage();
    }
}




   

    
}
