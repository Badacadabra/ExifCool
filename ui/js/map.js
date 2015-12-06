/**
 * Initialisation de la carte
 * */
var map = L.map('map').setView([48.8534100, 2.3488000], 2);
L.tileLayer('https://api.mapbox.com/v4/mapbox.light/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYmFkYWNhZGFicmEiLCJhIjoiY2lmd243aHNjMDI2enRjbTBiMndsbTMxYyJ9.t6qub-xAwB9RnfP4dE3DXw', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    minZoom: 2,
    id: 'badacadabra.cifwn7his023htrlzshz6jfmz',
    accessToken: 'pk.eyJ1IjoiYmFkYWNhZGFicmEiLCJhIjoiY2lmd243aHNjMDI2enRjbTBiMndsbTMxYyJ9.t6qub-xAwB9RnfP4dE3DXw'
}).addTo(map);

/**
 * Ajout des marqueurs à partir de données au format JSON
 * */
$.get('?a=ajaxMap').done(function(res) {
    if (res.code == 200) {
         $( ".ui.active.page.dimmer" ).hide();
        if (res.data.markers.length > 0) {
            var markers = res.data.markers;
            for (i in markers) {
                var marker = L.marker([markers[i].latitude, markers[i].longitude]).addTo(map);
                marker.bindPopup("<img src=\"" + markers[i].image + "\">");
                //~ $( "body" ).append( "<a id=\"link\" href=\"" + markers[i].image + "\" data-lightbox=\"image\" data-title=\"Toto\"></a>" );
                //~ marker.on('click', function(){
                    //~ $( "#link" ).trigger( "click" );
                //~ });
            }
        } else
            // alert("Aucune image disponible pour affichage");
            $('#no-image').modal('show');
    }
});
