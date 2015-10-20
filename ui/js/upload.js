$( document ).ready(function() {
	$( "#file" ).change(function () {
		$("#form-wrapper").fadeIn();
		$("#selected-image").css("margin-left", "150px");
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
		} else {
			alert("Votre navigateur ne gère pas FileReader.");
		}
	});

});
