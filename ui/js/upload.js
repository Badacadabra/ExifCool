/**
 * Class Upload, gère l'upload des images
 * **/
 function Upload() {
	 
	 $( "#file" ).on('change',start);
	 $( "#save" ).on('click',sendImgInfo);
	 $( "#cancel" ).on('click',cancelUploading);
	 function start () {
        // Visualisation de l'image avant upload
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
				sendImg();
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
		} else {
			alert(res.message);
		}
		
	}
	/****
	 * Permet d'envoyer les images au serveur.
	 * **/
	function sendImg() {
        $( "#upload-loader" ).fadeIn();
		var fd = new FormData($("#upload-form")[0]);
		$.ajax({
			  url: "?a=uploadImgInfo",
			  type: "POST",
			  data: fd,
			  processData: false,
			  contentType: false 
			}).success(parseImgInfo);
	}
	/**
	 * Permet d'envoyer les nouvelles données des images
	 * au serveur.
	 * **/
	function sendImgInfo(ev) {
		var data = {
				title : $("#title").val(),
				author : $("#author").val(),
				right : $("#right").val(),
				createDate : $("#create-date").val(),
				city : $("#city").val(),
				country : $("#country").val(),
				headline : $("#headline").val(),
				desc : $("#desc").val()
			};
			
		$.post("?a=validateImg",data).done(function(res) {
			if (res.code == 200) {
				// var msg = confirm(res.message+'\nVoulez-vous ajouter une nouvelle image ?\n\n Annuler : Aller sur la page de détail de l\'image\n OK : Ajouter une nouvelle image');
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
