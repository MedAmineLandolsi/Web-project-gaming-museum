<?php
require_once __DIR__ . '/../config.php';

class CommandeController {

    public function listCommandes() {
        $db = config::connect();
        $sql = "SELECT c.*, j.nom 
                FROM commande c
                JOIN jeux j ON c.Produit_id = j.id";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCommande($cmd) {
        $db = config::connect();
        $sql = "INSERT INTO commande (Produit_id, Total, quantity, Date)
                VALUES (:game, :total, :qty, :date)";
        $query = $db->prepare($sql);

        $query->bindValue(':game', $cmd->getProduitId());
        $query->bindValue(':total', $cmd->getTotal());
        $query->bindValue(':qty', $cmd->getQuantity());
        $query->bindValue(':date', $cmd->getDate());

        $query->execute();
    }

    public function deleteCommande($id) {
        $db = config::connect();
        $sql = "DELETE FROM commande WHERE ID = :id";
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        $query->execute();
    }
    public function getCommande($id)
    {
        $db = config::connect();

        try {
            $query = $db->prepare("
                SELECT c.*, j.nom AS jeu
                FROM commande c
                JOIN jeux j ON c.Produit_id = j.id
                WHERE c.id = :id
            ");

            $query->execute(['id' => $id]);
            $commande = $query->fetch(PDO::FETCH_ASSOC);

            return $commande;

        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    public function updateCommande($commande)
{
    $db = config::connect();

    try {
        $query = $db->prepare("
            UPDATE commande 
            SET Produit_id = :Produit_id,
                quantity = :quantity,
                Total = :Total,
                Date = :Date
            WHERE ID = :ID
        ");

        $query->execute([
            'Produit_id' => $commande['Produit_id'],
            'quantity'   => $commande['quantity'],
            'Total'      => $commande['Total'],
            'Date'       => $commande['Date'],
            'ID'         => $commande['ID']
        ]);

        return true;

    } catch (PDOException $e) {
        die('Erreur update Commande : ' . $e->getMessage());
    }
}


}