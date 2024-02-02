<?php
require_once("view/View.php");
require_once("control/Controller.php");
require_once("model/AnimalBuilder.php");

class Router {

	public function POSTredirect($url, $feedback) {
		// Stocke le feedback dans la session
		$_SESSION['feedback'] = $feedback;

		// Effectue la redirection
		header("HTTP/1.1 303 See Other");
		header("Location: $url");
		die();
	}

	public function main($animalStorage) {
	    // Récupère le feedback de la session
	    $feedback = key_exists('feedback', $_SESSION) ? $_SESSION['feedback'] : null;
		unset($_SESSION['feedback']);

	    $view = new View("", "", $this, $feedback);
        $controller = new Controller($view, $animalStorage);

		// Analyse de l'URL
		$id = key_exists('id', $_GET)? trim($_GET['id']): null;
		$action = key_exists('action', $_GET)? trim($_GET['action']) : null;
		$liste = key_exists('liste', $_GET) ? trim($_GET['liste']) : null;

		if ($action === null) {
			// Définit l'action par défaut
			if ($id !== null) {
				$action = "infos";
			} elseif ($liste !== null) {
				$action = "liste";
			} else {
				$action = "accueil";
			}
		}

        try {
           	switch ($action) {
				case "accueil":
           	        $view->homePage();
           	        break;
           	    case "infos":
					if ($id === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->showInformation($id);
					}
           	        break;
				case "liste":
					if ($liste === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->showList();
					}
					break;
           	    case "nouveau":
                   	$view->prepareAnimalCreationPage(new AnimalBuilder($_POST, []));
           	        break;
           	    case "sauverNouveau":
           	        $controller->saveNewAnimal($_POST);
           	        break;
				case "supprimer":
					if ($id === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->deleteAnimal($id);
					}
					break;
				case "confirmerSuppression":
					if ($id === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->confirmAnimalDeletion($id);
					}
					break;
				case "modifier":
					if ($id === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->modifyAnimal($id);
					}
					break;

				case "sauverModifs":
					if ($id === null) {
						$view->makeUnknownActionPage();
					} else {
						$controller->saveAnimalModifications($id, $_POST);
					}
					break;
           	    default:
					// L'internaute a demandé une action non prévue
					$view->makeUnknownActionPage();
					break;
           	}
		} catch (Exception $e) {
			// Gestion des exceptions imprévues
			$view->makeUnexpectedErrorPage($e);
		}
		/* Enfin, on affiche la page préparée */
        $view->render();
    }

	/* URL de la page d'accueil */
	public function homePage() {
		return "site.php";
	}

	/* URL de la page de la l'animal d'identifiant $id */
	public function getAnimalURL($animalId) {
		return "site.php?id=$animalId";
	}

	/* URL de la page avec tous les animaux */
	public function listeAnimauxPage() {
		return "site.php?liste=animaux";
	}

	/* URL de le formulaire de création d'un animal */
    public function getAnimalCreationURL() {
        return 'site.php?action=nouveau';
    }

	/* URL d'enregistrement d'un nouvelle animal
	 * (champ 'action' du formulaire) */
	public function getAnimalSaveURL() {
        return 'site.php?action=sauverNouveau';
    }

	/* URL de la page demandant la confirmation
	 * de la suppression d'un animal */
	public function animalDeletionPage($animalId) {
		return "site.php?id=$animalId&amp;action=supprimer";
	}

	/* URL de suppression effective d'un animal
	 * (champ 'action' du formulaire) */
	public function confirmAnimalDeletion($animalId) {
		return "site.php?id=$animalId&amp;action=confirmerSuppression";
	}

	/* URL de la page d'édition d'un animal existant */
	public function animalModifPage($animalId) {
		return "site.php?id=$animalId&amp;action=modifier";
	}

	/* URL d'enregistrement des modifications sur un
	 * animal (champ 'action' du formulaire) */
	public function updateModifiedAnimal($animalId) {
		return "site.php?id=$animalId&amp;action=sauverModifs";
	}
}
?>