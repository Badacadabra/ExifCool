<?php
require_once "src/Controller.php";

session_start();

try {
	if (isset($_GET['a']) && !empty($_GET['a'])) {
		$action = trim($_GET['a']);
		$ctrl = new Controller($action);
		$res = $ctrl->dispatch();
		switch($action) {
			case "map" : 
				$title = "La carte";
				$meta_title = "Visualisation des images sur une carte";
				$meta_description = "Visualisation des images sur une carte";
				$meta_image = "";
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
			case "upload":
				$title = "Ajouter une image";
				$meta_title = "Ajout des images à l'application";
				$meta_description = "Plate-forme d'upload d'images sur l'application";
				$meta_image = "";
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
			case "about":
				$title = "À propos";
				$meta_title = "Fiche technique sur l'implémentation de l'application";
				$meta_description = "Détail technique de la mise en place de l'application";
				$meta_image = "";
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
			case "detail":
				$title = $_GET['t'];
				$meta_title = $_GET['t'];
				$meta_description = $_GET['t'];
				$meta_image = $_SERVER['SERVER_NAME']."/ui/images/photos/".$_GET['q'];
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
		}	
	} else {
		$ctrl = new Controller();
		$res = $ctrl->dispatch();
		$title = "Accueil";
		$meta_title = "Galerie d'images";
		$meta_description = "Une galerie d'images pour le module UMDN3C";
		$meta_image = $_SERVER['SERVER_NAME']."/ui/images/photographer.jpg";
		$meta_url = $_SERVER['SERVER_NAME'];
	}
	require_once "includes/header.html";
	echo $res;
	require_once "includes/footer.html";
} catch (Exception $e) {
	header('Content-Type: text/html; charset=utf-8');
    echo $e->getMessage();
}

