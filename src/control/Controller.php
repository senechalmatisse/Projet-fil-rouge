<?php
require_once("model/Animal.php");

/*** Contrôleur du site des animaux. ***/
class Controller {

    private View $view;
    private AnimalStorage $animalStorage;

    public function __construct(View $view, AnimalStorage $animalStorage) {
        $this->view = $view;
        $this->animalStorage = $animalStorage;
    }

    public function showInformation($id) {
        // Un animal est demandé, on le récupère en BD
        $animal = $this->animalStorage->read($id);

        // Prépare la page en fonction de la présence ou non de l'animal
        $this->view->{$animal ? 'prepareAnimalPage' : 'prepareUnknownAnimalPage'}($animal, $id);
    }

    public function showList() {
        $animals = $this->animalStorage->readAll();
        $this->view->prepareListPage($animals);
    }

    public function saveNewAnimal(array $data) {
        $animalBuilder = new AnimalBuilder($data);

        if (!$animalBuilder->isValid()) {
			$_SESSION['feedback'] = "Erreur ! Veuillez saisir des données valides.";
            $this->view->prepareAnimalCreationPage($animalBuilder);
        } else {
            $animal = $animalBuilder->createAnimal();
            $newAnimalId = $this->animalStorage->create($animal);

            // Affiche un message de succès et redirige vers la page de l'animal créé
            $this->view->displayAnimalCreationSuccess($newAnimalId);
        }
    }

    public function deleteAnimal($id) {
		// On récupère l'animal en BD
		$animal = $this->animalStorage->read($id);

		if ($animal === null) {
			// L'animal n'existe pas en BD
			$this->view->makeUnknownActionPage();
		} else {
			// L'animal existe, on prépare la page
			$this->view->makeAnimalDeletionPage($id, $animal);
		}
	}

    public function confirmAnimalDeletion($id) {
		// L'utilisateur confirme vouloir supprimer l'animal
		$ok = $this->animalStorage->delete($id);
		if (!$ok) {
			// L'animal n'existe pas en BD
			$this->view->prepareUnknownAnimalPage();
		} else {
			// Tout s'est bien passé
			$this->view->makeAnimalDeletedPage();
		}
	}

    public function modifyAnimal($id) {
        // On récupère l'animal à modifier en BD
		$animal = $this->animalStorage->read($id);

		if ($animal === null) {
			$this->view->makeUnknownAnimalPage();
		} else {
			// Extraction des données modifiables
			$animalBuilder = AnimalBuilder::buildFromAnimal($animal);
			// Préparation de la page de formulaire
			$this->view->makeAnimalModifPage($id, $animalBuilder);
		}
	}

	public function saveAnimalModifications($id, array $data) {
		// On récupère en BD l'animal à modifier
		$animal = $this->animalStorage->read($id);
		if ($animal === null) {
			// L'animal n'existe pas en BD
			$this->view->makeUnknownAnimalPage();
		} else {
			$animalBuilder = new AnimalBuilder($data);

			// Validation des données
			if (!$animalBuilder->isValid()) {
				$_SESSION['feedback'] = "Erreur ! Veuillez saisir des données valides.";
				$this->view->makeAnimalModifPage($id, $animalBuilder);
			} else {
				// Modification de l'animal
				$animalBuilder->updateAnimal($animal);
				// On essaie de mettre à jour en BD
				$ok = $this->animalStorage->update($id, $animal);
				if (!$ok)
					throw new Exception("Identifier has disappeared?!");

				// Préparation de la page de l'animal
				$this->view->displayAnimalCreationSuccess($id);
			}
		}
	}
}
?>