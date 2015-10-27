/**
 * Class Upload, gère l'upload des images
 * **/
 function Upload() {
	 
	 $( "#file" ).on('change',start);
	 $( "#save" ).on('click',sendImgInfo);
	 $( "#cancel" ).on('click',cancelUploading);
	 function start () {
        // Visualisation de l'image avant upload
        var fd = new FormData($("#upload-form")[0]);
		var file = $("#file")[0].files[0],
			fileName = file.name,
			fileExt = (file.name).split('.').pop(),
			acceptedExtensions = ["jpg","png","jpeg","svg"];
			if ($.inArray(fileExt,acceptedExtensions) <= -1) {
				alert("Le fichier que vous essayer de télécharger n'est pas pris en compte par le système.\n\n\
					Fichiers acceptés : jpg, png, jpeg, svg.");
				return false;
			}
		$("#form-wrapper").fadeIn();
		if (typeof (FileReader) != "undefined") {
				var selectedImage = $("#selected-image");
				selectedImage.empty();
				var reader = new FileReader();
				reader.onload = function (e) {
					$("<img />", {
						"src": e.target.result
					}).appendTo(selectedImage);
				}
				selectedImage.show();
				reader.readAsDataURL($( this )[0].files[0]);
				/*****Envoi de l'image au serveur***/
				sendImg(fd);
		} else {
			alert("Votre navigateur ne gère pas FileReader.");
		}
        // Ajustements ergonomiques
        $( "#selected-image" ).css({
            "margin-left" : "150px",
            "border" : "solid 8px black",
            "box-shadow" : "7px 7px 5px gray"
        });
	}
	/**
	 * Permet de parser les infos d'une image donnée
	 **/
	function parseImgInfo (res) {
		console.log(res);
        $( "#upload-loader" ).fadeOut();
		if (res.code == 200) {
			$( "#title" ).val(res.data.title);
			$( "#author" ).val(res.data.author);
			$( "#right" ).val(res.data.right);
			$( "#create-date" ).val(res.data.createdDate);
			$( "#headline" ).val(res.data.headline);
			$( "#country" ).val(res.data.country);
			$( "#city" ).val(res.data.city);
			$( "#desc" ).val(res.data.desc);
			$( "#authorURL" ).val(res.data.creatorWorkURL);
			$( "#usageTerms" ).val(res.data.usageTerms);
			$( "#credit").val(res.data.credit);
			$( "#source").val(res.data.source);
		} else {
			alert(res.message);
		}
		
	}
	/****
	 * Permet d'envoyer les images au serveur.
	 * @param Object fileData : le fichier image à envoyer
	 * **/
	function sendImg(fileData) {
        $( "#upload-loader" ).fadeIn();
		$.ajax({
			  url: "?a=uploadImgInfo",
			  type: "POST",
			  data: fileData,
			  processData: false,
			  contentType: false 
			}).success(parseImgInfo);
	}
    
    /***
     * Validation du formulaire
     * **/
    //~ function validateForm() {
        //~ $('.ui.large.form')
          //~ .form({
            //~ fields: {
              //~ title: {
                //~ identifier: 'title',
                //~ rules: [
                  //~ {
                    //~ type   : 'empty',
                    //~ prompt : 'Vous devez entrer un titre.'
                  //~ }
                //~ ]
              //~ }
            //~ },
            //~ author: {
              //~ identifier: 'author',
              //~ rules: [
                //~ {
                  //~ type   : 'empty',
                  //~ prompt : 'Vous devez entrer un auteur.'
                //~ }
              //~ ]
            //~ }
          //~ },
          //~ {
            //~ onSuccess: sendImgInfo,
            //~ onFailure: function() {
                //~ return false;
            //~ }
          //~ }
        //~ );
    //~ }
    
	/**
	 * Permet d'envoyer les nouvelles données des images
	 * au serveur.
	 * **/
	function sendImgInfo(ev) {
		
		if ($( "#title" ).val() == "") {
            $( "#form-wrapper" ).addClass( "error" );
            $( "#title-field" ).addClass( "error" );
            $( "#title-error" ).fadeIn();
            $( "html,body" ).mCustomScrollbar("scrollTo", 0);
            return false;
		}
        if ($( "#author" ).val() == "") {
            $( "#author" ).val( "Inconnu" );
		}
        
		var data = {
				title : $("#title").val(),
				author : $("#author").val(),
				right : $("#right").val(),
				createDate : $("#create-date").val(),
				city : $("#city").val(),
				country : $("#country").val(),
				headline : $("#headline").val(),
				desc : $("#desc").val(),
				authorUrl : $( "#authorURL" ).val(),
				usageTerms : $("#usageTerms" ).val(),
				credit : $( "#credit").val(),
				source : $( "#source").val()
			};
			
		$.post("?a=validateImg",data).done(function(res) {
			if (res.code == 200) {
                $('#confirm-upload').modal({
                    onApprove: function () {
                        location.href = "?a=upload";
                    },
                    onDeny: function() {
                        location.href = '?a=detail&q='+res.data;
                    }
                }).modal('show');
			} else
				alert(res.message);
		  });
          
        ev.preventDefault();
	}
    
	function cancelUploading (ev) {
        $('#cancel-upload').modal({
            onApprove: function () {
                $.post("?a=cancel").done(function(res) {
                    if (res.code != 200) alert(res.message); else location.href = "?a=upload";
                });
            }
        }).modal('show');
		ev.preventDefault();
	}
 }
 
$( document ).ready(function() {
	upload = new Upload();
});
