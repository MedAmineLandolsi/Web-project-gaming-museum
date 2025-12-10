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
        // AJOUTÉ : Vérifier si l'utilisateur est déjà inscrit
        if(isset($_SESSION['user_id'])) {
            $this->participationModel->user_id = $_SESSION['user_id'];
            $this->participationModel->id_evenement = $data['id_evenement'];
            
            if($this->participationModel->userAlreadyRegistered()) {
                return "already_registered";
            }
        }

        // MODIFIÉ : Vérifier si l'email existe déjà pour cet événement
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
        
        // AJOUTÉ : Associer l'ID utilisateur si connecté
        if(isset($_SESSION['user_id'])) {
            $this->participationModel->user_id = $_SESSION['user_id'];
        } else {
            $this->participationModel->user_id = null; // Pour les non-connectés
        }

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
    
    // AJOUTÉ : Récupérer les participations d'un utilisateur
    public function getByUser($user_id) {
        $stmt = $this->participationModel->readByUser($user_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // AJOUTÉ : Vérifier si l'utilisateur peut annuler sa participation
    public function canCancel($participation_id, $user_id) {
        // Récupérer la participation
        $participations = $this->index();
        foreach($participations as $participation) {
            if($participation['id_participation'] == $participation_id) {
                // L'utilisateur peut annuler s'il est le participant OU un admin
                if($participation['User_ID'] == $user_id || $_SESSION['role'] == 'admin') {
                    return true;
                }
                return false;
            }
        }
        return false;
    }
}
?>