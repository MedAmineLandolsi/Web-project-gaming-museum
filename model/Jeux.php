<?php

class jeux{
    private ?int $id;
    private ?string $nom;
    private ?string $description;
    private ?float $prix;
    private ?int $stock;
    private ?string $categorie;
    private ?string $image;
    
    

    // Constructor
    public function __construct(?int $id, ?string $nom, ?string $description, ?float $prix,?int $stock, ?string $categorie = null, ?string $image = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->stock = $stock;
        $this->categorie = $categorie;
        $this->image = $image;
        
       
    }

    // Getters and Setters

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getnom(): ?string {
        return $this->nom;
    }

    public function setnom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getdescription(): ?string {
        return $this->description;
    }

    public function setdescription(?string $description): void {
        $this->description = $description;
    }

   

    public function getprix(): ?float {
        return $this->prix;
    }

    public function setprix(float $prix): void {
        $this->prix = $prix;
    }
     public function getstock(): ?int {
        return $this->stock;
    }

    public function setstock(?int $stock): void {
        $this->stock = $stock;
    }


    
    public function getcategorie(): ?string {
        return $this->categorie;
    }

    public function setcategorie(?string $categorie): void {
        $this->categorie = $categorie;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image): void {
        $this->image = $image;
    }
   
}

?>
