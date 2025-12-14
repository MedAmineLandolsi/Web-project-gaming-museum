<?php

class jeux{
    private ?int $id;
    private ?string $nom;
    private ?string $description;
    private ?float $prix;
    private ?int $stock;
    private ?string $categorie;
    private ?string $image;
    private ?string $trailer_url;

    private ?string $rarity;
    private ?string $condition;
    private ?string $edition;
    private ?float $estimated_value;
    private ?string $collector_notes;
    
    

    public function __construct(?int $id, ?string $nom, ?string $description, ?float $prix,?int $stock, ?string $categorie = null, ?string $image = null, ?string $trailer_url = null, ?string $rarity = 'common', ?string $condition = 'very_good', ?string $edition = null, ?float $estimated_value = null, ?string $collector_notes = null) {
    $this->id = $id;
    $this->nom = $nom;
    $this->description = $description;
    $this->prix = $prix;
    $this->stock = $stock;
    $this->categorie = $categorie;
    $this->image = $image;
    $this->trailer_url = $trailer_url;
    $this->rarity = $rarity;
    $this->condition = $condition;
    $this->edition = $edition;
    $this->estimated_value = $estimated_value;
    $this->collector_notes = $collector_notes;
}

    // Getters and Setters

   

public function getRarity(): ?string {
    return $this->rarity;
}

public function setRarity(?string $rarity): void {
    $this->rarity = $rarity;
}

public function getCondition(): ?string {
    return $this->condition;
}

public function setCondition(?string $condition): void {
    $this->condition = $condition;
}

public function getEdition(): ?string {
    return $this->edition;
}

public function setEdition(?string $edition): void {
    $this->edition = $edition;
}

public function getEstimatedValue(): ?float {
    return $this->estimated_value;
}

public function setEstimatedValue(?float $estimated_value): void {
    $this->estimated_value = $estimated_value;
}

public function getCollectorNotes(): ?string {
    return $this->collector_notes;
}

public function setCollectorNotes(?string $collector_notes): void {
    $this->collector_notes = $collector_notes;
}
    public function getTrailerUrl(): ?string {
    return $this->trailer_url;
}

public function setTrailerUrl(?string $trailer_url): void {
    $this->trailer_url = $trailer_url;
}
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
