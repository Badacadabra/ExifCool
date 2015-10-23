<?php

require_once "src/TemplateRender.php";

/**
 * Class Controller : contrôleur principal de l'application
 * **/
class Controller
{
	/**
	 * @var $listActions
	 * Permet de contenir l'ensemble des actions de l'appli
	 * */
	private $listActions;
	/**
	 * @var $action
	 * L'action envoyée par le client
	 * */
	private $action;
	/**
	 * @var $response
	 * La réponse à renvoyer au client
	 * */
	private $response = array();
	/**
	 * Le constructeur de la classe
	 * */
	public function __construct($action=null)
	{
		$this->listActions = array (
							'home' => 'homeAction', 
							'detail' => "detailAction",
							'map' => "mapAction",
							'about' => "aboutAction",
							'upload' => "uploadAction",
							'ajaxHome' => "ajaxHomeAction",
							'uploadImgInfo' => "uploadImgInfoAction",
							'validateImg' => "validateUploadImgAction",
							'cancel' => "cancelUploadingAction",
							);
		$this->action = $action;
		$this->currentImg = null;
	}
	/**
	 * Permet de faire le dispatching enfin d'exécuter la bonne action
	 * @return mixed
	 * **/
	public function dispatch()
	{
		if ($this->action!==null) {
			if (array_key_exists($this->action,$this->listActions)) {
				if (method_exists($this,$this->listActions[$this->action])) {
					$action = $this->listActions[$this->action];
					return $this->$action();
				} else
					throw new Exception("Impossible d'exécuter l'action, paramètre erroné.");
			} else 
				throw new Exception("Action non existante");
		} else 
			return $this->homeAction();
	}
	/**
	 * Renvoie les métadonnées d'une image donnée
	 * @param $image : l'image 
	 * @return json
	 * **/
	public function getMedataData($image)
	{
		if (file_exists($image)) {
			$data = array();
			exec("exiftool -g0 -json {$image}", $data);
			return (json_decode(implode($data),true));
		 } else 
			 throw new Exception("Image introuvable, vérifier bien la valeur du paramètre <b>q</b>");
	}
	/**
	 * Action de la page d'acceuil
	 * @return TemplateRender
	 * */
	 public function homeAction()
	 {
		return TemplateRender::render('views/index.html',$res=array());
	 }
	 /**
	  * Récupération des infos de métadata des images via ajax
	  * @return Response
	  * */
	 public function ajaxHomeAction()
	 {
		$files = glob("ui/images/photos/*.*");
		$supported_file = array('gif','jpg','jpeg','png');
		$res = array();
		if (sizeof($files) > 0) {
			foreach($files as $image)
			{
				$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
				if (in_array($ext, $supported_file)) {
					$line = array();
					$row = array();
					$line = $this->getMedataData($image);
					$row['title'] = $line[0]['XMP']['Title'];
					$row['subtitle'] = $line[0]['XMP']['Creator'].","." ".$line[0]['XMP']['Country'];
					$row['url'] = "?a=detail&q=".pathinfo($image)['filename'].".".$ext;
					$row['img'] = $image;
					$res[] = $row;
				}
			}
			$this->setResponse(200,"Opération terminée avec succès !",$res);
		} else
			$this->setResponse(400,"Erreur survenue lors du chargément des images !");
			
		$this->sendResponse();
	 }
	 /**
	 * Action de la page de détails d'une image
	 * @return TemplateRender
	 * */
	 public function detailAction()
	 {
		 $res = array();
		 if (isset($_GET['q']) && !empty($_GET['q'])) {
			 $image = "ui/images/photos/{$_GET['q']}";
			 $res = $this->getMedataData($image);
			 exec("exiftool -xmp -b {$image} -o ui/images/photos/xmp/".pathinfo($image)['filename'].".xmp");
		 }
		 return TemplateRender::render('views/details.html',$res);
	 }
	 /**
	 * Action de la page de téléchargement
	 * @return TemplateRender
	 * */
	 public function uploadAction()
	 {
		 return TemplateRender::render('views/upload.html',$res=array());
	 }
	 /**
	 * Permet d'enregistrer une image et renvoyer ses métadonnées
	 * par la suite.
	 * @return Response
	 * */
	 public function uploadImgInfoAction()
	 {
		 if(isset($_FILES['uploadImg']) && $_FILES['uploadImg']['name']!='') {
			$realName = $_FILES['uploadImg']['name'];
			$ext = pathinfo($realName, PATHINFO_EXTENSION);
			$tmp_name = $_FILES['uploadImg']['tmp_name'];
			$name = sha1(uniqid(mt_rand(), true)).'.'.$ext;
			move_uploaded_file($tmp_name,'ui/images/photos/'.$name);
			$image = "ui/images/photos/".$name;
			$_SESSION['img'] = $image;
			$row = $this->getMedataData($image);
			$res = array();
			$res['title'] = $row[0]['XMP']['Title'];
			$res['author'] = $row[0]['XMP']['Creator'];
			$res['right'] = $row[0]['XMP']['Rights'];
			$res['createdDate'] = $row[0]['XMP']['CreateDate'];
			$res['city'] = $row[0]['XMP']['City'];
			
			$this->setResponse(200,"Opération terminée avec succès !",$res);
		} else
			$this->setResponse(400,"Erreur survenue lors du téléchargement, aucune image reçue !");
			
		$this->sendResponse();
	 }
	 /**
	 * Permet de valider les métadonnées de l'image 
	 * télécharger.
	 * @return Response
	 * */
	 public function validateUploadImgAction()
	 {
		 $res = array();
		 if (isset($_SESSION['img']) && !empty($_SESSION['img'])) {
			if (isset($_POST['title']) && isset($_POST['author'])
				&& isset($_POST['right']) && isset($_POST['createDate'])
				&& isset($_POST['city'])) {
				$image = $_SESSION['img'];
				$data = array(array("SourceFile" => $image,
										"XMP:Title" => $_POST['title'],
										"XMP:Rights" => $_POST['right'],
										"XMP:Creator" => $_POST['author'],
										"XMP:City" => $_POST['city'],
										"EXIF:CreateDate" => $_POST['createDate']
								));
				file_put_contents('ui/images/photos/tmp.json', json_encode($data));
				exec("exiftool -json=ui/images/photos/tmp.json {$image}");
				unlink('ui/images/photos/tmp.json');
				unlink($image.'_original');
				$this->setResponse(200,"Opération terminée avec succès !",pathinfo($image)['basename']);
				unset($_SESSION['img']);
			} else
				$this->setResponse(400,"Erreur survenue lors de la validation, aucune image reçue");
		 } else
			 $this->setResponse(400,"Erreur survenue lors de la validation, aucune image reçue");
		
		$this->sendResponse();
	 }
	 /**
	  * Annulation du téléchargement
	  * **/
	  public function cancelUploadingAction ()
	  {
		  if (unlink($_SESSION['img']))
				$this->setResponse(200,"Opération terminée avec succès !");
		   else
				$this->setResponse(200,"Erreur survenue lors de l'annulation !");
				
		$this->sendResponse();
	  }
	 /**
	 * Action de la page d'affichage de la carte
	 * @return TemplateRender
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
	  * Permet de construire la réponse à renvoyer au client
	  * @param int $code : le code de retour
	  * @param string $message : le message de retour associé au code
	  * @param string $data : les données à renvoyer
	  * @return void
	  ***/ 
	  public function setResponse($code,$message,$data="")
	  {
		  $this->response['code'] = $code;
		  $this->response['message'] = $message;
		  $this->response['data'] = $data;
	  }
	  /**
	  * Permet de renvoyer la réponse au client
	  * @param string $type : le type de retour
	  * @return mixed
	  ***/ 
	  public function sendResponse($type='json')
	  {
			header("Content-Type: application/{$type}");
			if($type =='json') {
				echo json_encode($this->response);die;
			} else
				echo $this->response;die;
				  
	  }
}
