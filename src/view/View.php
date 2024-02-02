<?php
class View {
    private $title, $content, $router, $feedback;
    private array $menu;

    public function __construct($title, $content, $router, $feedback) {
        $this->title = $title;
        $this->content = $content;
        $this->router = $router;
        $this->feedback = $feedback;
        $this->menu = array(
            $this->router->homePage() => 'Accueil',
            $this->router->listeAnimauxPage() => 'Liste des Animaux',
            $this->router->getAnimalCreationURL() => 'Nouvel animal',
        );
    }

    public function homePage() {
		$this->title = "Proposez vos animaux !";
		$this->content = "Bienvenue sur ce site de partage d'animaux.";
	}

    public function prepareAnimalPage(Animal $animal, $id) {
        $this->title = "Page sur " . $animal->getName();
        $this->content = $animal->getName() . " est un animal de l'espèce " . $animal->getSpecies() . " et a " . $animal->getAge() . " ans.";
        $this->content .= '<ul><li><a href="'.$this->router->animalModifPage($id).'">Modifier</a></li>'."\n";
        $this->content .= '<li><a href="'.$this->router->animalDeletionPage($id).'">Supprimer</a></li></ul>'."\n";
    }

    public function prepareAnimalCreationPage(AnimalBuilder $animalBuilder) {  
        $saveURL = $this->router->getAnimalSaveURL();
    
        $this->title = 'Ajouter votre animal';
        $this->content = $this->getFormulaire($animalBuilder, $saveURL);
        $this->content .= "<input type='submit' value='Créer'></form>";
    }

    public function makeAnimalDeletionPage($id, Animal $animal) {
		$animalName = self::htmlesc($animal->getName());

		$this->title = "Suppression de l'animal $animalName";
		$this->content = "<p>L'animal « {$animalName} » va être supprimé.</p>\n";
		$this->content .= '<form action="'. $this->router->confirmAnimalDeletion($id) .'" method="POST">'."\n";
		$this->content .= "<button>Confirmer</button>\n</form>\n";
	}

    public function makeAnimalDeletedPage() {
		$this->title = "Suppression effectuée";
		$this->content = "<p>L'animal a été correctement supprimé.</p>";
	}

    public function makeAnimalModifPage($id, AnimalBuilder $animalBuilder) {
        $updateURL = $this->router->updateModifiedAnimal($id);

        $this->title = "Modifier l'animal";
        $this->content = $this->getFormulaire($animalBuilder, $updateURL);
        $this->content .= "<input type='submit' value='Modifier'></form>";
	}

    public function prepareListPage($animals) {
        $this->title = "Liste des animaux";
        $animalList = '';

        if (!empty($animals)) {
            $animalList = "<ul class='liste'>";
            foreach ($animals as $animalId => $animal) {
                // Obtient l'URL de la page de l'animal
                $animalURL = $this->router->getAnimalURL($animalId);
                // Chaque nom est un lien vers la page de l'animal
                $animalList .= "<li><a href='$animalURL'><h3>" . $animal->getName() . "</h3></a></li>";
            }
            $animalList .= '</ul>';
        } else {
            $animalList = 'Aucun animal à afficher.';
        }

        $this->content = $animalList;
    }

    public function displayAnimalCreationSuccess($id) {
        $url = $this->router->getAnimalURL($id);
        $this->router->POSTredirect($url, "Succès ! L'animal a été ajouté.");
    }

    public function prepareUnknownAnimalPage() {
        $this->title = "Animal inconnu";
        $this->content = "L'animal demandé n'existe pas.";
    }

    public function makeUnknownActionPage() {
		$this->title = "Erreur";
		$this->content = "La page demandée n'existe pas.";
	}

    /* Génère une page d'erreur inattendue. Peut optionnellement
	 * prendre l'exception qui a provoqué l'erreur
	 * en paramètre, mais n'en fait rien pour l'instant. */
	public function makeUnexpectedErrorPage(Exception $e=null) {
		$this->title = "Erreur";
		$this->content = "Une erreur inattendue s'est produite." . $e;
	}

    /******************************************************************************/
	/* Méthodes utilitaires                                                       */
	/******************************************************************************/

    /* Une fonction pour échapper les caractères spéciaux de HTML,
	* car celle de PHP nécessite trop d'options. */
	public static function htmlesc($str) {
		return htmlspecialchars($str,
			/* on échappe guillemets _et_ apostrophes : */
			ENT_QUOTES
			/* les séquences UTF-8 invalides sont
			* remplacées par le caractère �
			* au lieu de renvoyer la chaîne vide…) */
			| ENT_SUBSTITUTE
			/* on utilise les entités HTML5 (en particulier &apos;) */
			| ENT_HTML5,
			'UTF-8');
	}

    private function getFormulaire(AnimalBuilder $animalBuilder, $url) {
        $data = $animalBuilder->getData();
        $errorMessages = $animalBuilder->getError();
    
        $nameLabel = "Nom de l'animal";
        $speciesLabel = "Espèce de l'animal";
        $ageLabel = "Âge de l'animal";
    
        $name = key_exists(AnimalBuilder::NAME_REF, $data) ? self::htmlesc($data[AnimalBuilder::NAME_REF]) : '';
        $species = key_exists(AnimalBuilder::SPECIES_REF, $data) ? self::htmlesc($data[AnimalBuilder::SPECIES_REF]) : '';
        $age = key_exists(AnimalBuilder::AGE_REF, $data) ? $data[AnimalBuilder::AGE_REF] : '';
    
        $errorMessageName = key_exists(AnimalBuilder::NAME_REF, $errorMessages) ? self::htmlesc($errorMessages[AnimalBuilder::NAME_REF]) : '';
        $errorMessageSpecies = key_exists(AnimalBuilder::SPECIES_REF, $errorMessages) ? self::htmlesc($errorMessages[AnimalBuilder::SPECIES_REF]) : '';
        $errorMessageAge = key_exists(AnimalBuilder::AGE_REF, $errorMessages) ? $errorMessages[AnimalBuilder::AGE_REF] : '';

        return  "
            <form action='{$url}' method='POST'>
                <p>
                    <label>{$nameLabel} :
                        <input type='text' name='" . AnimalBuilder::NAME_REF . "' value='{$name}'>
                        <span class='error'>{$errorMessageName}</span>
                    </label>
                </p>
                <p>
                    <label>{$speciesLabel} :
                        <input type='text' name='" . AnimalBuilder::SPECIES_REF . "' value='{$species}'>
                        <span class='error'>{$errorMessageSpecies}</span>
                    </label>
                </p>
                <p>
                    <label>{$ageLabel} :
                        <input type='text' name='" . AnimalBuilder::AGE_REF . "' value='{$age}'>
                        <span class='error'>{$errorMessageAge}</span>
                    </label>
                </p>";
    }

    /******************************************************************************/
	/* Rendu de la page                                                           */
	/******************************************************************************/

    // Méthode render pour afficher la page HTML
    public function render() {
        if ($this->title !== null && $this->content !== null) {
            include("squelette.php");
        } else {
            $this->makeUnexpectedErrorPage();
        }
    }
}
?>