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
}
?>