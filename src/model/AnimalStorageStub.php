<?php
require_once("AnimalStorage.php");

class AnimalStorageStub implements AnimalStorage {
    private $animalsTab;

    public function __construct() {
        $this->animalsTab = array(
            'medor' => new Animal('Médor', 'chien', 5),
            'felix' => new Animal('Félix', 'chat', 3),
            'denver' => new Animal('Denver', 'dinosaure', 65),
        );
    }

    public function read($id): ?Animal {
        // Retourne l'animal avec l'identifiant spécifié ou null s'il n'existe pas
        return isset($this->animalsTab[$id]) ? $this->animalsTab[$id] : null;
    }

    public function readAll(): array {
        // Retourne le tableau des animaux
        return $this->animalsTab;
    }

    public function create(Animal $a): string {
        throw new Exception("Operation not supported: create");
    }

    public function update(string $id, Animal $a): bool {
        throw new Exception("Operation not supported: update");
    }

    public function delete(string $id): bool {
        throw new Exception("Operation not supported: delete");
    }
}
?>