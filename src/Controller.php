<?php

require_once "src/TemplateRender.php";

class Controller
{
	private $listActions;
	private $action;
			
	public function __construct($action=null)
	{
		$this->listActions = array (
							'home' => 'homeAction', 
							'detail' => "detailAction",
							'map' => "mapAction",
							'about' => "aboutAction",
							'upload' => "uploadAction",
							'ajaxHome' => "ajaxHomeAction"
							);
		$this->action = $action;
	}
	public function dispatch()
	{
		if ($this->action!==null) {
			if (array_key_exists($this->action,$this->listActions)) {
				if (method_exists($this,$this->listActions[$this->action])) {
					$action = $this->listActions[$this->action];
					return $this->$action();
				} else {
					throw new Exception("Impossible d'exécuter l'action, paramètre erroné.");
				}
			} else {
				throw new Exception("Action non existante");
			}
		} else {
			return $this->homeAction();
		}
	}
	/**
	 * Action de la page d'acceuil
	 * */
	 public function homeAction()
	 {
		return TemplateRender::render('views/index.html',$res=array());
	 }
	 /**
	  * Récupération des infos de métadata des images via ajax
	  * */
	 public function ajaxHomeAction()
	 {
		$files = glob("ui/images/photos/*.*");
		$supported_file = array('gif','jpg','jpeg','png');
		$res = array();
		foreach($files as $image)
		{
			$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
			if (in_array($ext, $supported_file)) {
				$data = array();
				$line = array();
				$row = array();
				exec("exiftool -g0 -json {$image}", $data);
				$line = (json_decode(implode($data),true));
				//$data[] = $row;
				//var_dump($row[0]);die;
				$row['title'] = $line[0]['XMP']['Title'];
				$row['subtitle'] = $line[0]['XMP']['Creator'].","." ".$line[0]['XMP']['Country'];
				$row['url'] = "?a=detail&q=".pathinfo($image)['filename'].".".$ext;
				$row['img'] = $image;
				$res[] = $row;
			}
		}
		
		header('Content-Type: application/json');
		
		echo json_encode($res);die;
	 }
	 /**
	 * Action de la page de détails d'une image
	 * */
	 public function detailAction()
	 {
		 $res = array();
		 if (isset($_GET['q']) && !empty($_GET['q'])) {
			 $image = "ui/images/photos/{$_GET['q']}";
			 if (file_exists($image)) {
				$row = array();
				$info = array();
				exec("exiftool -g0 -json {$image}", $row);
				$res = (json_decode(implode($row),true));
			 } else {
				 throw new Exception("Image introuvable, vérifier bien la valeur du paramètre <b>q</b>");
			 }
		 }
		 
		 return TemplateRender::render('views/details.html',$res);
	 }
	 /**
	 * Action de la page d'affichage de la carte
	 * */
	 public function mapAction()
	 {
		 return TemplateRender::render('views/carte.html',$res=array());
	 }
	 /**
	 * Action de la page de à propos
	 * */
	 public function aboutAction()
	 {
		 return TemplateRender::render('views/a-propos.html',$res=array());
	 }
	 /**
	 * Action de la page de téléchargement
	 * */
	 public function uploadAction()
	 {
		 return TemplateRender::render('views/upload.html',$res=array());
	 }
}
