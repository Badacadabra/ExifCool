<?php
require_once "src/Controller.php";

session_start();

try {
	if (isset($_GET['a']) && !empty($_GET['a'])) {
		$action = trim($_GET['a']);
		$ctrl = new Controller($action);
		$res = $ctrl->dispatch();
	} else {
		$ctrl = new Controller();
		$res = $ctrl->dispatch();
	}
	require_once "includes/header.html";
	echo $res;
	require_once "includes/footer.html";
} catch (Exception $e) {
	header('Content-Type: text/html; charset=utf-8');
    echo $e->getMessage();
}

