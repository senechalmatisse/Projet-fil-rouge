<?php
interface AnimalStorage {

    /* Renvoie l'animal d'identifiant $id, ou null
	 * si l'identifiant ne correspond à aucun animal. */
    public function read($id): ?Animal;

    /* Renvoie un tableau associatif id => Animal
	 * contenant tous les animaux de la base. */
    public function readAll(): array;

    /* Insère un nouvel animal dans la base. Renvoie l'identifiant
	 * du nouvel animal. */
    public function create(Animal $animal): string;

    /* Supprime un animal. Renvoie
	 * true si la suppression a été effectuée, false
	 * si l'identifiant ne correspond à aucun animal. */
    public function delete(string $id): bool;

    /* Met à jour un animal dans la base. Renvoie
	 * true si la modification a été effectuée, false
	 * si l'identifiant ne correspond à aucun animal. */
    public function update(string $id, Animal $animal): bool;

}
?>