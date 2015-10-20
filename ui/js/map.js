// Initialisation de la carte
var map = L.map('map').setView([48.8534100, 2.3488000], 3);
L.tileLayer('https://api.mapbox.com/v4/mapbox.light/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYmFkYWNhZGFicmEiLCJhIjoiY2lmd243aHNjMDI2enRjbTBiMndsbTMxYyJ9.t6qub-xAwB9RnfP4dE3DXw', {
	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
	minZoom: 2,
	id: 'badacadabra.cifwn7his023htrlzshz6jfmz',
	accessToken: 'pk.eyJ1IjoiYmFkYWNhZGFicmEiLCJhIjoiY2lmd243aHNjMDI2enRjbTBiMndsbTMxYyJ9.t6qub-xAwB9RnfP4dE3DXw'
}).addTo(map);

// Ajout des marqueurs à partir de données au format JSON
var data = {
	markers: [
		{ "latitude":60.1756, "longitude":24.9342, "image":"../ui/images/baptiste-vannesson.jpg" },
		{ "latitude":48.8534100, "longitude":2.3488000, "image":"../ui/images/macky-dieng.jpg" },
	]
}

for (var i = 0; i < data.markers.length; i++) {
	var marker = L.marker([data.markers[i].latitude, data.markers[i].longitude]).addTo(map);
	marker.bindPopup("<img src=\"" + data.markers[i].image + "\">");
}
