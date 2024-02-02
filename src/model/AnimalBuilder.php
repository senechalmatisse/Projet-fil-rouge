<?php

// Fonctions de manipulation des animaux via des formulaires
class AnimalBuilder {
    const NAME_REF = "nom";
    const SPECIES_REF = "espece";
    const AGE_REF = "age";

    private array $data;
    private $error;

    /* Crée une nouvelle instance, avec les données passées en argument
     * et les erreurs initialisées à null. */
    public function __construct(array $data) {
        $this->data = $data;
        $this->error = array();
    }

    // Renvoie les données d'un animal.
    public function getData(): array { return $this->data; }

    // Renvoie les erreurs associées aux champs d'un animal.
    public function getError(): ?array { return $this->error; }

    /* Crée une nouvelle instance de Animal avec les données
	 * fournies. Si toutes ne sont pas présentes, une exception
	 * est lancée. */
    public function createAnimal(): Animal {
        if (!key_exists("nom", $this->data) || !key_exists("espece", $this->data) || !key_exists("age", $this->data)) {
            throw new Exception("Missing fields for animal creation");
        }

        $name = View::htmlesc(trim($this->data["nom"]));
        $species = View::htmlesc(trim($this->data["espece"]));
        $age = intval(trim($this->data["age"]));

        return new Animal($name, $species, $age);
    }

    /* Renvoie une nouvelle instance de AnimalBuilder avec les données
 	 * modifiables de l'animal passée en argument. */
	public static function buildFromAnimal(Animal $animal) {
		return new AnimalBuilder(array(
			"nom" => $animal->getName(),
            "espece" => $animal->getSpecies(),
            "age" => $animal->getAge(),
		));
	}

    /* Vérifie la validité des données envoyées par le client,
	 * et renvoie un tableau des erreurs à corriger. */
	public function isValid(): bool {
        $this->error = array();
        if (!key_exists("nom", $this->data) || trim($this->data["nom"]) === "") {
            $this->error["nom"] = "Vous devez entrer un nom.";
        } else if (mb_strlen($this->data["nom"], 'UTF-8') >= 30) {
            $this->error["nom"] = "Le nom doit faire moins de 30 caractères.";
        }
        if (!key_exists("espece", $this->data) || trim($this->data["espece"]) === "") {
            $this->error["espece"] = "Vous devez entrer une espèce.";
        } else if (mb_strlen($this->data["espece"], 'UTF-8') >= 30) {
            $this->error["espece"] = "L'espèce doit faire moins de 30 caractères.";
        }
        if (!key_exists("age", $this->data) || !is_numeric($this->data["age"]) || $this->data["age"] <= 0) {
            $this->error["age"] = "Vous devez rentrer un âge valide et positif.";
        }
        return count($this->error) === 0;
    }

    // Met à jour une instance de Animal avec les données fournies.
	public function updateAnimal(Animal $animal) {
		if (key_exists("nom", $this->data)) {
            $animal->setName(View::htmlesc(trim($this->data["nom"])));
        }
		if (key_exists("espece", $this->data)) {
            $animal->setSpecies(View::htmlesc(trim($this->data["espece"])));
        }
        if (key_exists("age", $this->data)) {
            $animal->setAge(intval($this->data["age"]));
        }
	}
}
?>