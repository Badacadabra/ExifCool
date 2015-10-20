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
							'upload' => "uploadAction"
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
	 * Action de la page de détails d'une image
	 * */
	 public function detailAction()
	 {
		 return TemplateRender::render('views/details.html',$res=array());
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
