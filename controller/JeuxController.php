<?php
require_once __DIR__ . '/../config.php';

class JeuxController
{
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
        $sql = "INSERT INTO jeux (nom, description, prix, stock, categorie, image) 
            VALUES (:nom, :description, :prix, :stock, :categorie, :image)";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom'         => $game->getnom(),
                'description' => $game->getdescription(),
                'prix'        => $game->getprix(),
                'stock'       => $game->getstock(),      
                'categorie'   => $game->getcategorie(),
                'image'       => $game->getImage(),
        ]);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    }
     function updategame($game, $id)
    {
        var_dump($game);
        try {
            $db = config::getConnexion();

         $query = $db->prepare(
            'UPDATE jeux SET 
                nom = :nom,
                description = :description,
                prix = :prix,
                categorie = :categorie
            WHERE id = :id'
        );

        $query->execute([
            'id' => $id,
            'nom' => $game->getnom(),
            'description' => $game->getdescription(),
            'prix' => $game->getprix(),
            'categorie' => $game->getcategorie(),
        ]);

        echo $query->rowCount() . " records UPDATED successfully <br>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); 
    }
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
