<?php
include_once __DIR__ . '/../models/Participation.php';
include_once __DIR__ . '/../models/Evenement.php';

class ParticipationController {
    private $participationModel;
    private $evenementModel;

    public function __construct($db) {
        $this->participationModel = new Participation($db);
        $this->evenementModel = new Evenement($db);
    }

    public function index() {
        $stmt = $this->participationModel->read();
        $participations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $participations;
    }

    public function getByEvenement($id_evenement) {
        $this->participationModel->id_evenement = $id_evenement;
        $stmt = $this->participationModel->readByEvenement();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // Vérifier si l'email existe déjà pour cet événement
        $this->participationModel->email = $data['email'];
        $this->participationModel->id_evenement = $data['id_evenement'];
        
        if($this->participationModel->emailExists()) {
            return "email_exists";
        }

        // Vérifier les places disponibles
        $this->evenementModel->id_evenement = $data['id_evenement'];
        if($this->evenementModel->readOne()) {
            $participationsCount = $this->evenementModel->countParticipations();
            if($participationsCount >= $this->evenementModel->places_max) {
                return "no_places";
            }
        }

        $this->participationModel->id_evenement = $data['id_evenement'];
        $this->participationModel->nom_participant = $data['nom_participant'];
        $this->participationModel->email = $data['email'];
        $this->participationModel->telephone = $data['telephone'];

        if($this->participationModel->create()) {
            return "success";
        }
        return "error";
    }

    public function delete($id) {
        $this->participationModel->id_participation = $id;
        if($this->participationModel->delete()) {
            return true;
        }
        return false;
    }
}
?>