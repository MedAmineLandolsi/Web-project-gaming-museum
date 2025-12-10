<?php
include_once __DIR__ . '/../models/Evenement.php';
class EvenementController {
    private $evenementModel;

    public function __construct($db) {
        $this->evenementModel = new Evenement($db);
    }

    public function index() {
        $stmt = $this->evenementModel->read();
        $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $evenements;
    }

    public function show($id) {
        $this->evenementModel->id_evenement = $id;
        if($this->evenementModel->readOne()) {
            return $this->evenementModel;
        }
        return null;
    }

    public function create($data) {
        $this->evenementModel->nom = $data['nom'];
        $this->evenementModel->description = $data['description'];
        $this->evenementModel->date_debut = $data['date_debut'];
        $this->evenementModel->date_fin = $data['date_fin'];
        $this->evenementModel->lieu = $data['lieu'];
        $this->evenementModel->jeu = $data['jeu'];
        $this->evenementModel->places_max = $data['places_max'];
        $this->evenementModel->prix = $data['prix'];
        
        // AJOUTÉ : gérer l'organisateur
        if(isset($data['organisateur_id'])) {
            $this->evenementModel->organisateur_id = $data['organisateur_id'];
        } else {
            // Utiliser l'ID de l'utilisateur connecté par défaut
            $this->evenementModel->organisateur_id = $_SESSION['user_id'] ?? null;
        }
        
        // AJOUTÉ : gérer l'image si elle existe
        if(isset($data['image']) && !empty($data['image'])) {
            $this->evenementModel->image = $data['image'];
        }

        if($this->evenementModel->create()) {
            return true;
        }
        return false;
    }

    public function update($id, $data) {
        $this->evenementModel->id_evenement = $id;
        $this->evenementModel->nom = $data['nom'];
        $this->evenementModel->description = $data['description'];
        $this->evenementModel->date_debut = $data['date_debut'];
        $this->evenementModel->date_fin = $data['date_fin'];
        $this->evenementModel->lieu = $data['lieu'];
        $this->evenementModel->jeu = $data['jeu'];
        $this->evenementModel->places_max = $data['places_max'];
        $this->evenementModel->prix = $data['prix'];
        
        // AJOUTÉ : gérer l'image si elle existe
        if(isset($data['image']) && !empty($data['image'])) {
            $this->evenementModel->image = $data['image'];
        }

        if($this->evenementModel->update()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->evenementModel->id_evenement = $id;
        if($this->evenementModel->delete()) {
            return true;
        }
        return false;
    }
    
    // AJOUTÉ : méthode pour vérifier si l'utilisateur peut modifier/supprimer
    public function canManage($event_id, $user_id) {
        $this->evenementModel->id_evenement = $event_id;
        return $this->evenementModel->isOrganizer($user_id);
    }
    
    // AJOUTÉ : méthode pour compter les participants
    public function countParticipants($event_id) {
        $this->evenementModel->id_evenement = $event_id;
        return $this->evenementModel->countParticipations();
    }
}
?>