# ExifCool

ExifCool est une application web développée par Macky Dieng (dieng444) et Baptiste Vannesson (Badacadabra) dans le cadre du M2-DNR2I à l'université de Caen Normandie. Le but du projet était de concevoir une galerie photo « sémantique » sans utiliser de base de données. Naturellement, cette application s'appuie sur l'excellent programme [ExifTool](http://www.sno.phy.queensu.ca/~phil/exiftool/) développé par Phil Harvey.

## Présentation fonctionnelle de l'application

En dehors du « À propos » qui reprend ce README à l'identique, ExifCool s'articule autour de quatre pages principales :

* La page d'accueil qui affiche, sous forme de galerie photo, toutes les images présentes dans le dossier « images/photos » de l'application.
* La page de détails qui affiche les métadonnées de l'image sélectionnée, et qui propose un téléchargement de cette image, un téléchargement de son fichier XMP Sidecar, et des recherches associées sur Flickr.
* La page de cartographie qui affiche, sous forme de carte interactive, les images présentes dans le dossier « images/photos ».
* La page d'upload qui permet d'envoyer sa propre image dans l'application, avec une extraction à la volée des métadonnées.

## Présentation technique de l'application

Comme chaque page correspond plus ou moins à une fonctionnalité, nous allons ici présenter les détails techniques en reprenant la structure évoquée plus haut.
Mais avant cela, quelques remarques...

### Remarques préliminaires

ExifCool s'appuie sur une architecture MVC ; ou plutôt VC... Il y a en effet une séparation claire entre le contrôleur PHP et les vues en HTML. Il n'y a, en revanche, pas réellement de modèle puisque la base de données est volontairement inexistante.

Dans cette application, nous avons utilisé PHP pour pouvoir manipuler l'exécutable « exiftool » avec un certain confort. Nous voulions initialement programmer l'ensemble du projet en JavaScript, mais nous n'avons pas trouvé de portage réellement complet du « exiftool » que nous connaissons en ligne de commande. Nous avons notamment testé la bibliothèque « [exif-js](https://github.com/exif-js/exif-js) » qui, même si elle fonctionne très bien, ne renvoie pas les métadonnées XMP ou encore IPTC (seules les métadonnées EXIF sont extraites). De même, l'intéressante bibliothèque « [exiftool](https://github.com/nathanpeck/exiftool) » disponible en Node.js via npm, ne semblait pas aussi complète que l'exécutable.

Par ailleurs, nous avons utilisé [Semantic UI](http://semantic-ui.com/), déjà parce que le nom allait bien avec le concept du projet, mais aussi et surtout parce que c'est un framework qui permet de faire des choses propres en un temps raisonnable.

À noter aussi que le site est une application desktop qui prend en charge les écrans à partir de 1024x768, mais l'appli est particulièrement optimisée pour les hautes définitions (notamment full HD). ExifCool est donc en partie responsive, mais ne propose pas de version mobile. Il s'agit avant tout d'une application conçue selon le paradigme desktop-first.

### Page d'accueil

Lorsque l'utilisateur arrive sur la page d'accueil, il est généralement confronté à un loader qui le fait patienter quelques instants. Ce temps d'attente est parfaitement normal car, en arrière-plan, il se passe pas mal de choses... Tout d'abord, notre script client en JavaScript fait appel à un web worker qui a pour mission principale de lancer une requête AJAX pour charger les données. Derrière les rideaux, PHP reçoit des instructions en mode asynchrone, fait le listing de toutes les images dans le répertoire « images/photos », puis en extrait les métadonnées utiles (ici le titre et l'auteur de chacune des images dans le répertoire précisé plus haut). Bien sûr, lorsque cette requête aboutit, le web worker en informe le script client avec un « postMessage ». À l'issue de la procédure, un objet JSON spécifique est formé avec les données récupérées pour alimenter le plugin [Photocols](https://github.com/2CodersTeam/jquery.photocols) qui permet de présenter les images d'une façon très élégante. Le plugin étant néanmoins contraignant sur le plan de la structure, les métadonnées (microdata) de chaque image devaient être injectées en JavaScript a posteriori. Elles ne sont donc pas directement visibles dans le code source de la page d'accueil mais existent bel et bien dans le DOM.

![Microdata pour la page d'accueil](https://21411850.users.info.unicaen.fr/ExifCool/ui/images/microdata.jpg)

À propos du web worker, il nous semblait ici judicieux d'effectuer tous ces traitements lourds (non DOM) dans un thread à part ; ceci afin de ne pas saturer le navigateur.

### Page de détails

La page de détails d'une image est accessible de plusieurs façons :

* En cliquant sur une image de la page d'accueil
* En cliquant sur un résultat donné par le moteur de recherche
* En cliquant sur « Non » dans la pop-up qui s'affiche après avoir uploadé une image (redirection automatique)

Quand l'utilisateur arrive sur la page de détails d'une image, une miniature s'affiche (dans un élément « figure ») avec sa légende (« figcaption ») et toutes les métadonnées correspondantes (EXIF, XMP, IPTC). Ces métadonnées sont extraites par PHP avec un « exec » sur « exiftool ». Pour des raisons de lisibilité, ces métadonnées sont présentées sont forme d'accordéon. En outre, en cliquant sur une miniature, l'utilisateur peut voir l'image en grand dans une [lightbox](https://github.com/lokesh/lightbox2/).

Sur cette page, un petit menu est également disponible. Il permet de télécharger l'image (grâce à la propriété « download » de l'élément « a »), mais également le fichier XMP Sidecar (généré grâce à l'option « -xmp » de l'exécutable « exiftool »). Il est également possible d'envoyer une requête vers l'API de Flickr, en choisissant ses mots-clés dans une liste déroulante. Naturellement, ces mots-clés sont issus des métadonnées de l'image courante, précédemment extraites par PHP via « exiftool ».

### Page de cartographie

Comme pour la page d'accueil, l'utilisateur est généralement confronté à un loader lorsqu'il arrive sur la page de cartographie, et c'est là aussi dû à la lourdeur des traitements. Dans un premier temps, pour afficher la carte, nous avons utilisé Leaflet, une bibliothèque JavaScript très populaire qui a d'ailleurs servi à développer la [carte photo de Flickr](https://www.flickr.com/map). En complément, nous avons utilisé [Mapbox](https://www.mapbox.com/) pour le design de la carte.

Au chargement de la page, une requête AJAX est envoyée vers un script PHP pour récupérer les métadonnées des images du répertoire « images/photos », en particulier la latitude et la longitude (lorsque les images contiennent bien des métadonnées de géolocalisation). Si la requête est un succès, alors on boucle sur le résultat renvoyé et on construit, selon la syntaxe de Leaflet, les différents marqueurs de la carte en utilisant la latitude et la longitude de chaque image contenant des métadonnées GPS. Il va de soi que le chemin de chaque image est aussi injecté dans la carte, afin que les miniatures soient visibles par un simple clic sur le marqueur correspondant.

### Page d'upload

Sur la page d'upload, l'utilisateur a la possibilité d'ajouter n'importe quelle image à l'application. Pour des raisons pratiques, car il s'agit avant tout d'un exercice, il n'y a pas d'espace membre sur ExifCool, et donc aucun contrôle de droits ou d'accès. En revanche, un test est effectué sur le type de fichier pour vérifier qu'il s'agit bien d'une image.

Lorsque l'utilisateur choisit une image à uploader, un formulaire s'affiche ainsi qu'une prévisualisation de l'image. Pour le formulaire, il s'agit simplement d'une div cachée qui se révèle grâce à jQuery. Quant à la prévisualisation, et compte tenu du fait qu'il n'est plus possible de modifier directement un chemin local pour des raisons de sécurité (cf. « fakepath »), il a fallu passer par un objet FileReader fourni par HTML5.

On remarquera que le processus d'extraction des métadonnées de l'image sélectionnée se fait au même moment. Là encore, une requête AJAX est envoyée vers un script PHP, et au succès, on préremplit les champs du formulaire afin que l'utilisateur puisse modifier ou valider l'upload plus facilement. À noter que le champ titre est obligatoire pour que notre moteur de recherche serve à quelque chose. Si ce champ n'est pas renseigné, une erreur sera renvoyée, le formulaire ne sera pas validé, et fatalement l'image ne sera pas uploadée.

À la fin d'un upload, une pop-up demande à l'utilisateur s'il souhaite uploader une nouvelle image. S'il clique sur « Oui », une redirection JavaScript le ramène à la page d'upload. S'il clique sur « Non », une redirection JavaScript l'envoie sur la page de détails de l'image tout juste uploadée.

## Annexes

![Exemple de rendu « Open Graph »](https://21411850.users.info.unicaen.fr/ExifCool/ui/images/open-graph.jpg)

![Exemple de rendu « Twitter Cards »](https://21411850.users.info.unicaen.fr/ExifCool/ui/images/twitter-card.jpg)
