var search = function(e) {

    $( "#flickr-loader" ).fadeIn();
    
	// console.log( $("#imageFlickr").val());
    var urlFlickr = "https://api.flickr.com/services/rest/";
    var keywords = $('#flickr-selection').dropdown('get value');
    var params =  {
        "method": "flickr.photos.search",
        "api_key": "14d6906508dab0d8cc63498536cf07a8",
        "tags": keywords,
        "license": 2, // Creative Commons
        "per_page": 30,
        "format": "json"
    };

    $.ajax({
        url: urlFlickr,
        jsonp: "jsoncallback",
        dataType: "jsonp",
        data: params
    }).done(parseFlickr);

}

var parseFlickr = function(response) {
    // console.log(response);
    template = " ";
    for (var i=0; i < response.photos.photo.length; i++) {
        var url = "https://farm" + response.photos.photo[i].farm + ".staticflickr.com/" + response.photos.photo[i].server + "/" + response.photos.photo[i].id + "_" + response.photos.photo[i].secret + "_b.jpg";
			template+="<a href=\"" + url + "\" data-lightbox=\"image-" + i + "\"><img id=\"" + response.photos.photo[i].id + "\" src=\"https://farm" + response.photos.photo[i].farm + ".staticflickr.com/" + response.photos.photo[i].server + "/" + response.photos.photo[i].id + "_" + response.photos.photo[i].secret + "_q.jpg\" alt=\"\"></a>";
        //~ $( "#" + response.photos.photo[i].id ).click(function() {
            //~ $( "html, body" ).animate( { scrollTop: 0 }, 500 );
        //~ });
    }
    $("#flickr-response").html(template);
    // Ajustements ergonomiques
    $( "#flickr-loader" ).fadeOut();
}
