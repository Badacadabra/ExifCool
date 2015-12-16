<?php

require_once "src/TemplateRender.php";

/**
 * Class Controller : contrôleur principal de l'application
 * **/
class Controller
{
	/**
	 * @var $listActions
	 * Permet de contenir l'ensemble des actions de l'application
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
							'ajaxMap' => "ajaxMapAction",
							'search' => "searchAction",
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
	 * Renvoie les métadonnées d'une image donnée
	 * @param $image : l'image
	 * @return json
	 * **/
	public function getImageTitle($image)
	{
		if (file_exists($image)) {
			$data = array();
			exec("exiftool -xmp:Title -json {$image}", $data);
			return json_decode(implode($data),true);
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
					$row['url'] = "?a=detail&q=".pathinfo($image)['filename'].".".$ext."&t=".$line[0]['XMP']['Title'];
					$row['img'] = $image;
					$res[] = $row;
				}
			}
			$this->setResponse(200,"Opération terminée avec succès !",$res);
		} else
			$this->setResponse(400,"Erreur survenue lors du chargement des images !");

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
			$name = uniqid(mt_rand(), true).'.'.$ext;
			move_uploaded_file($tmp_name,'ui/images/photos/'.$name);
			$image = "ui/images/photos/".$name;
			$_SESSION['img'] = $image;
			$row = $this->getMedataData($image);
			$res = array();
			$title = null;
			$author = null;
			$right = null;
			$createDate = null;
			$city = null;
			$desc = null;
			$country = null;
			$headline = null;
			$creatorWorkURL = null;
			$usageTerms = null;
			$source = null;
			$credit = null;


			if (array_key_exists('XMP',$row[0]) || array_key_exists('IPTC',$row[0])) {
				if (array_key_exists('Title',$row[0]['XMP']))
					$title = $row[0]['XMP']['Title'];
				elseif (array_key_exists('Title',$row[0]['IPTC']))
					$title = $row[0]['IPTC']['Title'];
				else
					$tile = null;
				if (array_key_exists('Creator',$row[0]['XMP']))
					$author = $row[0]['XMP']['Creator'];
				elseif (array_key_exists('Creator',$row[0]['IPTC']))
					$author = $row[0]['IPTC']['Creator'];
				else
					$author = null;
				if (array_key_exists('Rights',$row[0]['XMP']))
					$right = $row[0]['XMP']['Rights'];
				elseif (array_key_exists('Rights',$row[0]['IPTC']))
					$right = $row[0]['IPTC']['Rights'];
				else
					$right = null;
				if (array_key_exists('CreateDate',$row[0]['XMP']))
					$createDate = $row[0]['XMP']['CreateDate'];
				elseif (array_key_exists('CreateDate',$row[0]['IPTC']))
					$createDate = $row[0]['IPTC']['CreateDate'];
				else
					$createDate = null;
				if (array_key_exists('City',$row[0]['XMP']))
					$city = $row[0]['XMP']['City'];
				elseif (array_key_exists('City',$row[0]['IPTC']))
					$city = $row[0]['IPTC']['City'];
				else
					$city = null;
				if (array_key_exists('Description',$row[0]['XMP']))
					$desc = $row[0]['XMP']['Description'];
				elseif (array_key_exists('Description',$row[0]['IPTC']))
					$desc = $row[0]['IPTC']['Description'];
				else
					$desc = null;
				if (array_key_exists('Country',$row[0]['XMP']))
					$country = $row[0]['XMP']['Country'];
				elseif (array_key_exists('Country',$row[0]['IPTC']))
					$country = $row[0]['IPTC']['Country'];
				else
					$country = null;
				if (array_key_exists('Headline',$row[0]['XMP']))
					$headline = $row[0]['XMP']['Headline'];
				elseif (array_key_exists('Headline',$row[0]['IPTC']))
					$headline = $row[0]['IPTC']['Headline'];
				else
					$headline = null;
				if (array_key_exists('CreatorWorkURL',$row[0]['XMP']))
					$creatorWorkURL = $row[0]['XMP']['CreatorWorkURL'];
				elseif (array_key_exists('CreatorWorkURL',$row[0]['IPTC']))
					$creatorWorkURL = $row[0]['IPTC']['CreatorWorkURL'];
				else
					$creatorWorkURL = null;
				if (array_key_exists('UsageTerms',$row[0]['XMP']))
					$usageTerms = $row[0]['XMP']['UsageTerms'];
				elseif (array_key_exists('UsageTerms',$row[0]['IPTC']))
					$usageTerms = $row[0]['IPTC']['UsageTerms'];
				else
					$usageTerms = null;
				if (array_key_exists('Source',$row[0]['XMP']))
					$source = $row[0]['XMP']['Source'];
				elseif (array_key_exists('Source',$row[0]['IPTC']))
					$source = $row[0]['IPTC']['Source'];
				else
					$source = null;
				if (array_key_exists('Credit',$row[0]['XMP']))
					$credit = $row[0]['XMP']['Credit'];
				elseif (array_key_exists('Credit',$row[0]['IPTC']))
					$credit = $row[0]['IPTC']['Credit'];
				else
					$credit = null;
			}
			$res['title'] = $title;
			$res['author'] = $author;
			$res['right'] = $right;
			$res['createdDate'] = $createDate;
			$res['city'] = $city;
			$res['desc'] = $desc;
			$res['country'] = $country;
			$res['headline'] = $headline;
			$res['creatorWorkURL'] = $creatorWorkURL;
			$res['usageTerms'] = $usageTerms;
			$res['source'] = $source;
			$res['credit'] = $credit;

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
				&& isset($_POST['city']) && isset($_POST['authorUrl'])
				&& isset($_POST['usageTerms']) && isset($_POST['credit'])
				&& isset($_POST['source'])) {
				$image = $_SESSION['img'];
				$right = !empty($_POST['right']) ? $_POST['right'] : "_none";
				$city = !empty($_POST['city']) ? $_POST['city'] : "_none";
				$country = !empty($_POST['country']) ? $_POST['country'] : "_none";
				$headline = !empty($_POST['headline']) ? $_POST['headline'] : "_none";
				$desc = !empty($_POST['desc']) ? $_POST['desc'] : "_none";
				$createDate = !empty($_POST['createDate']) ? $_POST['createDate'] : "_none";
				$authorUrl = !empty($_POST['authorUrl']) ? $_POST['authorUrl'] : "_none";
				$usageTerms = !empty($_POST['usageTerms']) ? $_POST['usageTerms'] : "_none";
				$credit = !empty($_POST['credit']) ? $_POST['credit'] : "_none";
				$source = !empty($_POST['source']) ? $_POST['source'] : "_none";

				$data = array(array("SourceFile" => $image,
										"XMP:Title" => $_POST['title'],
										"XMP:Rights" => $right,
										"XMP:Creator" => $_POST['author'],
										"XMP:City" => $city,
										"XMP:Country" => $country,
										"XMP:Headline" => $headline,
										"XMP:Description" => $desc,
										"XMP:CreatorWorkerURL" => $authorUrl,
										"XMP:UsageTerms" => $usageTerms,
										"IPTC:Credit" => $credit,
										"IPTC:Source" => $createDate
								));
				file_put_contents('ui/images/photos/tmp.json', json_encode($data));
				exec("exiftool -json=ui/images/photos/tmp.json {$image}");
				unlink('ui/images/photos/tmp.json');
				unlink($image.'_original');
				$res['image'] = pathinfo($image)['basename'];
				$res['title'] = $_POST['title'];
				$this->setResponse(200,"Opération terminée avec succès !",$res);
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
		  if (isset($_SESSION['img'])) {
				unlink($_SESSION['img']);
				$this->setResponse(200,"Opération terminée avec succès !");
			}
		   else
				$this->setResponse(200,"Erreur survenue lors de l'annulation !");

		$this->sendResponse();
	  }
	 /**
	 * A
	 * */
	 public function ajaxMapAction()
	 {
		$files = glob("ui/images/photos/*.*");
		$res = array();
		$data = array();
		if (sizeof($files) > 0) {
			foreach($files as $image)
			{
				$line = $this->getMedataData($image);
				if (array_key_exists('GPSLatitude',$line[0]['Composite'])
					&& array_key_exists('GPSLongitude',$line[0]['Composite'])) {
					$gpsLat = explode(" ",$line[0]['Composite']['GPSLatitude']);
					$gpsLong = explode(" ",$line[0]['Composite']['GPSLongitude']);


					$longCardinate = $gpsLong[sizeof($gpsLong) - 1];
					$latCardinate = $gpsLat[sizeof($gpsLat) - 1];
					//Les différentes parties de la latitude
                    $latDegree = (int) $gpsLat[0];
					$latMinute = (int) str_replace("'","",$gpsLat[2]);
					$latSeconds = (float) str_replace("\"","",$gpsLat[3]);
					//Les différentes parties de la longitude
					$longDegree = (int) $gpsLong[0];
					$longMinute = (int) str_replace("'","",$gpsLong[2]);
					$longSeconds = (float) str_replace("'","",$gpsLong[3]);
					//Decimal value = Degrees + (Minutes/60) + (Seconds/3600)
					$latitude = round($latDegree + ($latMinute/60) + ($latSeconds/3600),6);
					$longitude = round($longDegree + ($longMinute/60) + ($longSeconds/3600),6);
					if ($latCardinate == "S")
						$latitude = -$latitude;
					if ($longCardinate == "W")
						$longitude = -$longitude;

					$data[] = array ('latitude' => $latitude,
									 'longitude' => $longitude,
									 'image' => "ui/images/photos/".pathinfo($image)['basename']
									);
				}
			}
			$res['markers'] = $data;

			$this->setResponse(200,"Opération terminée avec succès !",$res);
		} else
			$this->setResponse(400,"Aucune image trouvée!",$res);

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
	 /***
	  * Action du moteur de recherche
	  * */
	  public function searchAction ()
	  {
		  if (isset($_GET['q']) && !empty($_GET['q'])) {
              $files = glob("ui/images/photos/*.*");
			  $supported_file = array('gif','jpg','jpeg','png');
			  $res = array();
			  if (sizeof($files) > 0) {
                    foreach($files as $image) {
                        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                        $line = $this->getImageTitle($image);
                        $title = $line[0]['Title'];
                        if (strstr(strtolower($title),strtolower($_GET['q']))) {
                            $row['title'] = $line[0]['Title'];
                            $row['url'] = "?a=detail&q=".pathinfo($image)['filename'].".".$ext."&t=".$line[0]['Title'];
                            $res[] = $row;
                        }
                    }
				  $this->setResponse(200,"Opération terminée avec succès !",$res);
			  } else
					$this->setResponse(400,"Erreur survenue aucours de la récupération des données ! ",$res);

			$this->sendResponse();
		  }
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
