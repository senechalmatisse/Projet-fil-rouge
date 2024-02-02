<?php

// Représente un animal.
class Animal {
    private $name, $species, $age;

	// Construit un animal.
    public function __construct($name, $species, $age) {
        $this->name = $name;
        $this->species = $species;
        $this->age = $age;
    }

    // Renvoie le nom de l'animal
    public function getName() { return $this->name; }

    // Renvoie l'espèce de l'animal
    public function getSpecies() { return $this->species; }

    // Renvoie l'âge de l'animal
    public function getAge() { return $this->age; }

    /* Modifie le nom de l'animal. Le nouveau nom doit
	 * être valide au sens de isNameValid, sinon
	 * une exception est levée. */
	public function setName($name) {
		$this->name = $name;
	}

    /* Modifie l'espèce de l'animal.
	 * Le nouveau code doit
	 * être valide au sens de isSpeciesValid, sinon
	 * une exception est levée. */
	public function setSpecies($species) {
		$this->species = $species;
	}

    /* Modifie l'âge de l'animal.
	 * Le nouveau code doit
	 * être valide au sens de isAgeValid, sinon
	 * une exception est levée. */
	public function setAge($age) {
		$this->age = $age;
	}
}
?>