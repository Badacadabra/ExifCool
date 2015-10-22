/**
 * Class Upload, gère l'upload des images
 * **/
 function Upload() {
	 
	 $( "#file").on('change',start);
	 function start () {
		$("#selected-image").css("margin-left", "150px");
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
				/*****Envoie de l'image au serveur***/
				send();
		} else {
			alert("Votre navigateur ne gère pas FileReader.");
		}
	}
	/**
	 * Permet de parser les infos d'une image donnée
	 **/
	function parseImgInfo (result) {
		console.log(result);
		$("#title").val(result.title);
		$("#author").val(result.author);
		$("#right").val(result.right);
		$("#create-date").val(result.createdDate);
		$("#city").val(result.city);
	}
	/**
	 * Permet d'envoyer les images au serveur.
	 * **/
	function send() {
		var fd = new FormData($("#upload-form")[0]);
		$.ajax({
			  url: "?a=uploadImgInfo",
			  type: "POST",
			  data: fd,
			  processData: false,
			  contentType: false 
			}).success(parseImgInfo);
	}
 }
 
$(document).ready(function() {
	upload = new Upload();
});
