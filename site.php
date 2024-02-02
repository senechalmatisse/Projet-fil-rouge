<?php
/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

// Inclusion des classes et autres dépendances nécessaires
require("Router.php");
require_once("model/AnimalStorageSession.php");

// Démarrage de la session
session_start();

// Création d'une instance de AnimalStorageSession
$animalStorage = new AnimalStorageSession();

// Création d'une instance du routeur et appel de sa méthode principale
$router = new Router();
$router->main($animalStorage);
?>
