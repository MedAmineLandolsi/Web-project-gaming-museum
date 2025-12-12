<?php
class Commande {

    private $id;
    private $produit_id;
    private $total;
    private $quantity;
    private $date;

    public function __construct($produit_id, $total, $quantity, $date, $id = null) {
        $this->id = $id;
        $this->produit_id = $produit_id;
        $this->total = $total;
        $this->quantity = $quantity;
        $this->date = $date;
    }

    public function getId() { return $this->id; }
    public function getProduitId() { return $this->produit_id; }
    public function getTotal() { return $this->total; }
    public function getQuantity() { return $this->quantity; }
    public function getDate() { return $this->date; }
}
