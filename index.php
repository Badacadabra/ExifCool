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
				$title = "Carte";
				$meta_title = "Carte interactive";
				$meta_description = "Visualisation d'images sur une carte, à partir des coordonnées GPS fournies dans les métadonnées.";
				$meta_image = "";
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
			case "upload":
				$title = "Upload";
				$meta_title = "Upload d'images";
				$meta_description = "Uploadez vos images pour en extraire les métadonnées !";
				$meta_image = "";
				$meta_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				break;
			case "about":
				$title = "À propos";
				$meta_title = "À propos de l'application";
				$meta_description = "Détails techniques sur la mise en place de l'application.";
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

