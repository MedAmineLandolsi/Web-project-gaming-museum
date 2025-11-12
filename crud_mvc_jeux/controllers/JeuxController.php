<?php
require_once "config/Database.php";
require_once "models/Jeux.php";

class JeuxController {
    private $model;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->model = new Jeux($db);
    }

    public function index() {
        $stmt = $this->model->readAll();
        include "views/jeux/list.php";
    }

    public function create() {
        $error = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nom']) || empty($_POST['prix']) || empty($_POST['categorie'])) {
                $error = "Tous les champs obligatoires doivent être remplis.";
            } else {
                $this->model->nom = $_POST['nom'];
                $this->model->description = $_POST['description'];
                $this->model->prix = $_POST['prix'];
                $this->model->stock = $_POST['stock'];
                $this->model->categorie = $_POST['categorie'];
                if ($this->model->create()) {
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout.";
                }
            }
        }
        include "views/jeux/add.php";
    }

    public function edit($id) {
        $this->model->id = $id;
        $jeu = $this->model->readOne();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->nom = $_POST['nom'];
            $this->model->description = $_POST['description'];
            $this->model->prix = $_POST['prix'];
            $this->model->stock = $_POST['stock'];
            $this->model->categorie = $_POST['categorie'];
            $this->model->id = $id;

            if ($this->model->update()) {
                header("Location: index.php");
                exit;
            }
        }
        include "views/jeux/edit.php";
    }

    public function delete($id) {
        $this->model->id = $id;
        $this->model->delete();
        header("Location: index.php");
    }
}
?>